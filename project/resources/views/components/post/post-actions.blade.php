@props(['post'])

@php
    $isLiked = Auth::check() && $post->isLikedBy(Auth::user());
    $likesCount = $post->likes_count ?? $post->likes()->count();
    $isSaved = Auth::check() && \App\Models\SavedPost::where('user_id', Auth::id())->where('post_id', $post->id)->exists();
@endphp

<div class="w-full flex justify-between items-center px-2 py-4 sticky top-0 bg-white/95 backdrop-blur z-20 border-b border-gray-100">
    <div class="flex items-center space-x-2.5">
        <!-- Interactive Like Button with Live Counter -->
        <button type="button"
            class="like-btn flex items-center gap-2 px-3.5 py-2 rounded-full font-semibold text-sm transition {{ $isLiked ? 'bg-red-50 text-red-500 hover:bg-red-100' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            data-post-id="{{ $post->id }}"
            title="Like this post">
            <i class="like-icon {{ $isLiked ? 'fas fa-heart text-red-500' : 'far fa-heart' }} text-base"></i>
            <span class="like-count">{{ $likesCount }}</span>
        </button>

        @auth
            <!-- Bookmark / Save Button -->
            <button type="button"
                class="save-btn bg-sky-50 hover:bg-sky-100 text-sky-600 font-semibold py-2 px-4 rounded-full text-sm transition flex items-center"
                data-post-id="{{ $post->id }}" data-saved="{{ $isSaved ? 'true' : 'false' }}">
                <i class="bookmark-icon {{ $isSaved ? 'fas' : 'far' }} fa-bookmark mr-1.5"></i>
                <span class="bookmark-text">{{ $isSaved ? 'Saved' : 'Save' }}</span>
            </button>
        @endauth
    </div>

    <div class="flex items-center space-x-2">
        <!-- One-Click Quick Share Button -->
        <button type="button"
            class="share-trigger-btn flex justify-center items-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium w-9 h-9 rounded-full transition shadow-sm"
            data-url="{{ route('posts.show', $post->id) }}"
            data-title="{{ $post->title }}"
            title="Share post">
            <i class="fas fa-share-nodes text-sm"></i>
        </button>

        @auth
            <!-- Add to Album Button -->
            <button id="add-to-album-btn"
                class="flex justify-center items-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium w-9 h-9 rounded-full transition shadow-sm"
                title="Add to Album">
                <i class="fas fa-folder-plus text-sm"></i>
            </button>
        @endauth

        @if (Auth::check() && Auth::id() == $post->user_id)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-600 hover:bg-gray-100 w-9 h-9 flex items-center justify-center rounded-full transition">
                    <i class="fas fa-ellipsis"></i>
                </button>
                <div x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-1 z-30 border border-gray-100 text-sm">
                    <a href="{{ route('posts.edit', $post->id) }}"
                        class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-edit w-4 mr-2.5 text-gray-400"></i> Edit post
                    </a>
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-trash-alt w-4 mr-2.5"></i> Delete post
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if (Auth::check() && Auth::user()->role == 'admin' && Auth::id() != $post->user_id)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-600 hover:bg-gray-100 w-9 h-9 flex items-center justify-center rounded-full transition">
                    <i class="fas fa-ellipsis"></i>
                </button>
                <div x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-1 z-30 border border-gray-100 text-sm">
                    <form action="{{ route('admin.posts.archive', $post->id) }}" method="post"
                        onsubmit="return confirm('Are you sure you want to archive this post?');">
                        @csrf
                        <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fa-solid fa-box-archive w-4 mr-2.5 text-gray-400"></i> Archive Post
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
