import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Setup CSRF header for Axios if meta tag exists
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta && window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');
    }

    // ---------------------------------------------------------
    // 2. Interactive Like System & Heart Animations
    // ---------------------------------------------------------
    async function togglePostLike(postId, options = {}) {
        if (!postId) return;

        // Prevent spamming
        const buttons = document.querySelectorAll(`.like-btn[data-post-id="${postId}"]`);
        buttons.forEach(btn => btn.disabled = true);

        try {
            const response = await window.axios.post(`/posts/${postId}/like`);
            if (response.data && response.data.status === 'success') {
                const isLiked = response.data.liked;
                const likesCount = response.data.likes_count;

                // Synchronize all like buttons and counts for this post on the page
                buttons.forEach(btn => {
                    const icon = btn.querySelector('.like-icon');
                    const countSpan = btn.querySelector('.like-count');

                    if (icon) {
                        if (isLiked) {
                            icon.className = 'like-icon fas fa-heart text-red-500 heart-pulse';
                        } else {
                            icon.className = 'like-icon far fa-heart';
                        }
                    }

                    if (countSpan) {
                        countSpan.textContent = likesCount;
                    }

                    // Update button classes for post-actions component styling
                    if (btn.classList.contains('bg-red-50') || btn.classList.contains('bg-gray-100')) {
                        if (isLiked) {
                            btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                            btn.classList.add('bg-red-50', 'text-red-500', 'hover:bg-red-100');
                        } else {
                            btn.classList.remove('bg-red-50', 'text-red-500', 'hover:bg-red-100');
                            btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        }
                    } else if (btn.classList.contains('text-gray-500') || btn.classList.contains('text-red-500')) {
                        if (isLiked) {
                            btn.classList.remove('text-gray-500', 'hover:text-red-500');
                            btn.classList.add('text-red-500');
                        } else {
                            btn.classList.remove('text-red-500');
                            btn.classList.add('text-gray-500', 'hover:text-red-500');
                        }
                    }
                });
                return { isLiked, likesCount };
            }
        } catch (error) {
            if (error.response && error.response.status === 401) {
                if (window.Swal) {
                    window.Swal.fire({
                        title: 'Log in to Like',
                        text: 'Create a free account or log in to like posts and curate your feed.',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#0ea5e9',
                        confirmButtonText: 'Log In',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/login';
                        }
                    });
                } else if (confirm('Please log in to like this idea. Go to login page?')) {
                    window.location.href = '/login';
                }
            } else {
                console.error('Like toggle error:', error);
            }
        } finally {
            buttons.forEach(btn => btn.disabled = false);
        }
    }

    // Trigger floating heart burst animation
    function triggerHeartBurst(container) {
        if (!container) return;
        const burstEl = container.querySelector('.heart-burst');
        if (!burstEl) return;

        burstEl.classList.remove('animate-burst');
        // Force reflow
        void burstEl.offsetWidth;
        burstEl.classList.add('animate-burst');

        setTimeout(() => {
            burstEl.classList.remove('animate-burst');
        }, 900);
    }

    // Event Delegation: Click on Like buttons
    document.addEventListener('click', (e) => {
        const likeBtn = e.target.closest('.like-btn');
        if (likeBtn) {
            e.preventDefault();
            e.stopPropagation();
            const postId = likeBtn.dataset.postId;
            togglePostLike(postId);
        }
    });

    // Event Delegation: Double Click on Media / Card containers for Heart Burst
    document.addEventListener('dblclick', (e) => {
        const container = e.target.closest('.double-tap-container');
        if (container) {
            e.preventDefault();
            const postId = container.dataset.postId;
            triggerHeartBurst(container);
            if (postId) {
                togglePostLike(postId);
            }
        }
    });

    // Touch device double-tap handler
    let lastTapTime = 0;
    document.addEventListener('touchend', (e) => {
        const container = e.target.closest('.double-tap-container');
        if (!container) return;

        const currentTime = new Date().getTime();
        const tapGap = currentTime - lastTapTime;

        if (tapGap > 0 && tapGap < 350) {
            e.preventDefault();
            const postId = container.dataset.postId;
            triggerHeartBurst(container);
            if (postId) {
                togglePostLike(postId);
            }
            lastTapTime = 0;
        } else {
            lastTapTime = currentTime;
        }
    });

    // ---------------------------------------------------------
    // 3. Quick-Share Modal Trigger
    // ---------------------------------------------------------
    document.addEventListener('click', (e) => {
        const shareBtn = e.target.closest('.share-trigger-btn');
        if (shareBtn) {
            e.preventDefault();
            e.stopPropagation();
            const url = shareBtn.dataset.url || window.location.href;
            const title = shareBtn.dataset.title || document.title;
            window.dispatchEvent(new CustomEvent('open-share-modal', {
                detail: { url, title }
            }));
        }
    });

    // ---------------------------------------------------------
    // 4. Infinite Scroll with IntersectionObserver
    // ---------------------------------------------------------
    const sentinel = document.getElementById('infinite-scroll-sentinel');
    const masonryGrid = document.getElementById('masonry-grid');
    const noMoreIndicator = document.getElementById('no-more-posts-indicator');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const spinner = document.getElementById('infinite-scroll-spinner');

    if (sentinel && masonryGrid) {
        let isFetching = false;

        async function loadMorePosts() {
            if (isFetching) return;

            const nextPageUrl = sentinel.dataset.nextPage;
            if (!nextPageUrl) {
                sentinel.classList.add('hidden');
                if (noMoreIndicator) noMoreIndicator.classList.remove('hidden');
                return;
            }

            isFetching = true;
            if (spinner) spinner.classList.remove('hidden');
            if (loadMoreBtn) loadMoreBtn.classList.add('hidden');

            try {
                const response = await window.axios.get(nextPageUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.data && response.data.html) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = response.data.html;

                    // Append items with fade-in animation
                    const newItems = Array.from(tempDiv.children);
                    newItems.forEach(item => {
                        item.classList.add('card-enter');
                        masonryGrid.appendChild(item);
                    });

                    // Update pagination state
                    if (response.data.hasMorePages && response.data.nextPageUrl) {
                        sentinel.dataset.nextPage = response.data.nextPageUrl;
                        sentinel.classList.remove('hidden');
                    } else {
                        sentinel.dataset.nextPage = '';
                        sentinel.classList.add('hidden');
                        if (noMoreIndicator) noMoreIndicator.classList.remove('hidden');
                        if (observer) observer.disconnect();
                    }
                }
            } catch (error) {
                console.error('Infinite scroll error:', error);
                // Fallback to manual load button on error
                if (spinner) spinner.classList.add('hidden');
                if (loadMoreBtn) loadMoreBtn.classList.remove('hidden');
            } finally {
                isFetching = false;
            }
        }

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                loadMorePosts();
            });
        }

        // Initialize IntersectionObserver
        let observer = null;
        if ('IntersectionObserver' in window) {
            observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !isFetching) {
                        loadMorePosts();
                    }
                });
            }, {
                rootMargin: '300px 0px 300px 0px', // Preload ahead of scrolling
                threshold: 0.1
            });

            observer.observe(sentinel);
        } else if (loadMoreBtn) {
            // Fallback for older browsers without IntersectionObserver
            loadMoreBtn.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
        }
    }

    // ---------------------------------------------------------
    // 5. Global Follow / Unfollow interactive button handler
    // ---------------------------------------------------------
    document.querySelectorAll('.btn-follow').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const userId = button.dataset.userId;
            if (!userId) return;

            button.disabled = true;
            try {
                const response = await window.axios.post(`/users/${userId}/follow`);
                if (response.data && response.data.status === 'success') {
                    const isFollowing = response.data.following;
                    const followersCount = response.data.followers_count;

                    // Synchronize all follow buttons on the page for this user
                    document.querySelectorAll(`.btn-follow[data-user-id="${userId}"]`).forEach(btn => {
                        btn.dataset.following = isFollowing ? 'true' : 'false';
                        if (isFollowing) {
                            btn.textContent = 'Following';
                            btn.classList.remove('bg-red-600', 'hover:bg-red-700', 'text-white');
                            btn.classList.add('bg-gray-100', 'hover:bg-gray-200', 'text-gray-800');
                        } else {
                            btn.textContent = 'Follow';
                            btn.classList.remove('bg-gray-100', 'hover:bg-gray-200', 'text-gray-800');
                            btn.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white');
                        }
                    });

                    // Update live follower count displays on the page
                    document.querySelectorAll(`[data-follower-count-for="${userId}"]`).forEach(el => {
                        el.textContent = followersCount;
                    });
                }
            } catch (error) {
                console.error('Follow error:', error);
                const message = error.response?.data?.error || 'Failed to update follow status.';
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert(message);
                }
            } finally {
                button.disabled = false;
            }
        });
    });
});
