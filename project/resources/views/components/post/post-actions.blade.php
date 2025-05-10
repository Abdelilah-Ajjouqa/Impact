@props(['post'])

<div class="w-[106%] flex justify-between items-center px-2 py-4 sticky top-0 bg-white">
    <div class="flex space-x-2">
        @auth
            @php
                // Check if the post is saved
                $isSaved = \App\Models\SavedPost::where('user_id', Auth::id())->where('post_id', $post->id)->exists();
            @endphp
            <button type="button"
                class="save-btn bg-sky-100 hover:bg-sky-200 text-sky-600 font-medium py-2 px-4 rounded-full"
                data-post-id="{{ $post->id }}" data-saved="{{ $isSaved ? 'true' : 'false' }}">
                <i class="bookmark-icon {{ $isSaved ? 'fas' : 'far' }} fa-bookmark mr-1"></i>
                <span class="bookmark-text">{{ $isSaved ? 'Saved' : 'Save' }}</span>
            </button>
        @endauth
    </div>

    <div class="flex space-x-3">
        @if (Auth::check() && Auth::id() == $post->user_id)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-600 hover:bg-gray-100 p-2 rounded-full">
                    <i class="fas fa-ellipsis-h text-xl"></i>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                    <a href="{{ route('posts.edit', $post->id) }}"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 ">
                        <i class="fas fa-edit mr-2"></i> Edit post
                    </a>
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                            <i class="fas fa-trash-alt mr-2"></i> Delete post
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if (Auth::check() && Auth::user()->role == 'admin')
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-600 hover:bg-gray-100 p-2 rounded-full">
                    <i class="fas fa-ellipsis-h text-xl"></i>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                    <form action="{{ route('admin.posts.archive', $post->id) }}" method="post"
                        onsubmit="return confirm('Are you sure you want to archive this post ?');">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-gray-600 hover:bg-gray-100">
                            <i class="fa-solid fa-box-archive"></i> Archive Post
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @auth
            <button id="add-to-album-btn"
                class="flex justify-center items-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-2 rounded-full">
                <i class="fas fa-folder-plus"></i>
            </button>
        @endauth
    </div>
</div>
