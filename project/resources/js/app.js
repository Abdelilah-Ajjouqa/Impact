import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // CSRF header for Axios if meta tag exists
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta && window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');
    }

    // Global Follow / Unfollow interactive button handler
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
