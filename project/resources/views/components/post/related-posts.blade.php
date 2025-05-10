@props(['relatedPosts' => null])

<div class="mt-12">
    <h2 class="text-xl font-bold mb-6">More like this</h2>
    <div class="masonry-grid">
        @if ($relatedPosts && $relatedPosts->count() > 0)
            @foreach ($relatedPosts as $item)
                <div class="masonry-item">
                    <div class="bg-gray-50 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                        <a href="{{ route('posts.show', $item->id) }}"
                            class="block hover:opacity-85 overflow-hidden rounded-t-lg">
                            @if ($item->mediaContent->first() && str_contains($item->mediaContent->first()->type, 'video'))
                                <video class="w-full object-cover" autoplay muted loop playsinline>
                                    <source src="{{ asset('storage/' . $item->mediaContent->first()->path) }}"
                                        type="{{ $item->mediaContent->first()->type }}">
                                    Your browser does not support the video tag.
                                </video>
                            @elseif ($item->mediaContent->first())
                                <img src="{{ asset('storage/' . $item->mediaContent->first()->path) }}"
                                    alt="{{ $item->title }}" class="w-full object-cover">
                            @else
                                <div class="bg-gray-200 w-full" style="aspect-ratio: 4/3;"></div>
                            @endif
                        </a>
                        <div class="p-2">
                            <h3 class="font-semibold text-lg truncate cursor-pointer">{{ $item->title }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-2 mt-0.5 cursor-pointer">{{ $item->description }}
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <a href="{{ route('users.show', $item->user_id) }}"
                                    class="flex items-center duration-300 hover:drop-shadow-2xl hover:text-red-500">
                                    <img src="{{ $item->user->avatar ?? 'https://placehold.co/40' }}" alt="User"
                                        class="w-8 h-8 rounded-full mr-2">
                                    <span class="text-sm font-medium">{{ $item->user->username }}</span>
                                </a>
                                <div class="flex gap-x-1">
                                    @auth
                                        @php
                                            $isSaved = \App\Models\SavedPost::where('user_id', Auth::id())
                                                ->where('post_id', $item->id)
                                                ->exists();
                                        @endphp
                                        <button
                                            class="unsave-btn text-gray-600 hover:text-red-600 p-1 rounded-full hover:bg-gray-100"
                                            data-post-id="{{ $item->id }}">
                                            <i class="{{ $isSaved ? 'fas' : 'far' }} fa-bookmark"></i>
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">No related posts found</p>
            </div>
        @endif
    </div>
</div>

<style>
    .masonry-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        grid-gap: 1.5rem;
        grid-auto-rows: 10px;
    }

    .masonry-item {
        grid-row-end: span 30;
    }

    .masonry-item img,
    .masonry-item video {
        width: 100%;
        object-fit: cover;
    }
</style>
