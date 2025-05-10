@props(['post'])

<div id="add-to-album-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 ">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex justify-between items-center border-b p-4">
            <h3 class="text-lg font-semibold">Add to Album</h3>
            <button id="close-album-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4" id="user-albums-container">
            @auth
                @if (Auth::user()->albums->isEmpty())
                    <div class="text-center py-6">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-folder-open text-4xl"></i>
                        </div>
                        <p class="text-gray-600 mb-4">You don't have any albums yet</p>
                        <a href="{{ route('albums.create') }}"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-full inline-block">
                            Create Album
                        </a>
                    </div>
                @else
                    <div class="grid gap-2">
                        @foreach (Auth::user()->albums as $album)
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 overflow-hidden mr-3">
                                        @if ($album->cover_image)
                                            <img src="{{ asset('storage/' . $album->cover_image) }}"
                                                alt="{{ $album->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-images text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-medium">{{ $album->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ $album->posts->count() }} posts</p>
                                    </div>
                                </div>
                                <button
                                    class="add-to-album-btn bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm"
                                    data-album-id="{{ $album->id }}" data-post-id="{{ $post->id }}">
                                    Add
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addToAlbumBtn = document.getElementById('add-to-album-btn');
            const addToAlbumModal = document.getElementById('add-to-album-modal');
            const closeAlbumModal = document.getElementById('close-album-modal');
            const userAlbumsContainer = document.getElementById('user-albums-container');

            if (addToAlbumBtn) {
                addToAlbumBtn.addEventListener('click', function() {
                    addToAlbumModal.classList.remove('hidden');

                    // Fetch user albums
                    fetch(`{{ route('users.albums', Auth::id()) }}?post_id={{ $post->id }}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            userAlbumsContainer.innerHTML = '';

                            if (data.albums.length === 0) {
                                userAlbumsContainer.innerHTML = `
                            <div class="text-center py-6">
                                <p class="text-gray-500 mb-4">You don't have any albums yet.</p>
                                <a href="{{ route('albums.create') }}" class="bg-black hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-full">
                                    Create an album
                                </a>
                            </div>
                        `;
                                return;
                            }

                            const albumsList = document.createElement('div');
                            albumsList.className = 'space-y-2';

                            data.albums.forEach(album => {
                                const albumElement = document.createElement('div');
                                albumElement.className =
                                    'flex items-center justify-between p-3 border rounded-md hover:bg-gray-50';

                                let coverHtml =
                                    `<div class="bg-gray-200 w-12 h-12 rounded-md flex items-center justify-center"><i class="fas fa-images text-gray-400"></i></div>`;
                                if (album.cover_image) {
                                    coverHtml =
                                        `<img src="/storage/${album.cover_image}" alt="${album.title}" class="w-12 h-12 object-cover rounded-md">`;
                                }

                                let buttonHtml = `
                            <button class="add-to-album-btn bg-sky-500 hover:bg-sky-600 text-white font-medium py-1 px-3 rounded-full text-sm" data-album-id="${album.id}">
                                Add
                            </button>
                        `;

                                if (album.has_post) {
                                    buttonHtml = `
                                <button class="bg-gray-300 text-gray-600 font-medium py-1 px-3 rounded-full text-sm" disabled>
                                    Added
                                </button>
                            `;
                                }

                                albumElement.innerHTML = `
                            <div class="flex items-center">
                                ${coverHtml}
                                <div class="ml-3">
                                    <h4 class="font-medium">${album.title}</h4>
                                    <p class="text-xs text-gray-500">${album.posts_count} posts</p>
                                </div>
                            </div>
                            ${buttonHtml}
                        `;

                                albumsList.appendChild(albumElement);
                            });

                            userAlbumsContainer.appendChild(albumsList);

                            // Add event listeners to the "Add" buttons
                            document.querySelectorAll('.add-to-album-btn').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const albumId = this.getAttribute('data-album-id');
                                    addPostToAlbum(albumId, this);
                                });
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching albums:', error);
                            userAlbumsContainer.innerHTML = `
                        <div class="text-center py-6">
                            <p class="text-red-500">Error loading albums: ${error.message}</p>
                        </div>
                    `;
                        });
                });
            }

            if (closeAlbumModal) {
                closeAlbumModal.addEventListener('click', function() {
                    addToAlbumModal.classList.add('hidden');
                });
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === addToAlbumModal) {
                    addToAlbumModal.classList.add('hidden');
                }
            });

            function addPostToAlbum(albumId, buttonElement) {
                const originalText = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                buttonElement.disabled = true;

                fetch(`/albums/${albumId}/posts`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            post_id: {{ $post->id }}
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Server responded with status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            buttonElement.innerHTML = '<i class="fas fa-check"></i>';
                            buttonElement.classList.remove('bg-sky-500', 'hover:bg-sky-600');
                            buttonElement.classList.add('bg-green-500', 'hover:bg-green-600');

                            // Disable the button after successful addition
                            setTimeout(() => {
                                buttonElement.innerHTML = 'Added';
                                buttonElement.classList.add('bg-gray-300');
                                buttonElement.classList.remove('bg-green-500', 'hover:bg-green-600');
                                buttonElement.classList.add('text-gray-600');
                                buttonElement.disabled = true;
                            }, 1000);
                        } else {
                            buttonElement.innerHTML = originalText;
                            buttonElement.disabled = false;
                            alert(data.message || 'Failed to add post to album');
                        }
                    })
                    .catch(error => {
                        console.error('Error adding post to album:', error);
                        buttonElement.innerHTML = originalText;
                        buttonElement.disabled = false;
                        alert('An error occurred. Please try again: ' + error.message);
                    });
            }
        });
    </script>
@endpush
