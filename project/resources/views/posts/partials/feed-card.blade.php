@foreach($posts as $item)
    @php
        $isSaved = isset($savedPostIds) && $savedPostIds->has($item->id);
        $isLiked = Auth::check() && $item->isLikedBy(Auth::user());
        $likesCount = $item->likes_count ?? $item->likes->count();
    @endphp
    <div class="masonry-item" data-post-id="{{ $item->id }}">
        <div class="bg-gray-50 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col group/card">
            
            <!-- Media Container with Double-Click Like Interaction -->
            <div class="relative overflow-hidden double-tap-container select-none cursor-pointer bg-gray-100" data-post-id="{{ $item->id }}">
                <a href="{{ route('posts.show', $item->id) }}" class="block overflow-hidden">
                    @if ($item->mediaContent->isNotEmpty())
                        @php $media = $item->mediaContent->first(); @endphp
                        @if (str_contains($media->type, 'video'))
                            <video class="w-full object-cover transition-transform duration-500 group-hover/card:scale-105" autoplay muted loop playsinline>
                                <source src="{{ $media->url }}" type="{{ $media->type }}">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ $media->url }}"
                                alt="{{ $item->title }}"
                                loading="lazy"
                                decoding="async"
                                class="w-full object-cover transition-transform duration-500 group-hover/card:scale-105">
                        @endif
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                        </div>
                    @endif
                </a>

                <!-- Double-Tap Heart Burst Overlay Animation -->
                <div class="heart-burst absolute inset-0 flex items-center justify-center pointer-events-none opacity-0 z-20">
                    <i class="fas fa-heart text-red-500 text-6xl drop-shadow-[0_10px_20px_rgba(239,68,68,0.5)]"></i>
                </div>

                <!-- Hover Quick-Action Floating Overlay on Image -->
                <div class="absolute top-3 right-3 flex items-center gap-1.5 opacity-0 group-hover/card:opacity-100 transition-opacity duration-200 z-10">
                    @auth
                        <button type="button"
                            class="like-btn p-2 rounded-full bg-white/90 backdrop-blur hover:bg-white text-gray-700 shadow-md hover:scale-110 transition-transform"
                            data-post-id="{{ $item->id }}"
                            title="Like this idea">
                            <i class="like-icon {{ $isLiked ? 'fas fa-heart text-red-500' : 'far fa-heart' }}"></i>
                        </button>
                    @endauth

                    <button type="button"
                        class="share-trigger-btn p-2 rounded-full bg-white/90 backdrop-blur hover:bg-white text-gray-700 shadow-md hover:scale-110 transition-transform"
                        data-url="{{ route('posts.show', $item->id) }}"
                        data-title="{{ $item->title }}"
                        title="Share">
                        <i class="fas fa-share-nodes text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Card Content Body -->
            <div class="p-3.5 flex flex-col justify-between flex-1">
                <div>
                    <a href="{{ route('posts.show', $item->id) }}" class="block">
                        <h3 class="font-semibold text-gray-900 text-base line-clamp-1 hover:text-sky-600 transition">{{ $item->title }}</h3>
                        @if($item->description)
                            <p class="text-gray-500 text-xs line-clamp-2 mt-1 leading-relaxed">{{ $item->description }}</p>
                        @endif
                    </a>
                </div>

                <!-- Footer / Author & Interactivity Bar -->
                <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-gray-100">
                    <a href="{{ route('users.show', $item->user_id) }}"
                        class="flex items-center group/author min-w-0 pr-2">
                        <img src="{{ $item->user->avatar_url ?? 'https://placehold.co/40' }}"
                            alt="{{ $item->user->username ?? 'User' }}"
                            class="w-6 h-6 rounded-full mr-2 object-cover ring-1 ring-gray-200 group-hover/author:ring-sky-400 transition">
                        <span class="text-xs font-medium text-gray-700 truncate group-hover/author:text-sky-600 transition">
                            {{ $item->user->username ?? 'User' }}
                        </span>
                    </a>

                    <div class="flex items-center gap-1 shrink-0">
                        <!-- Like Count Badge & Button -->
                        <button type="button"
                            class="like-btn flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium transition hover:bg-red-50 {{ $isLiked ? 'text-red-500' : 'text-gray-500 hover:text-red-500' }}"
                            data-post-id="{{ $item->id }}">
                            <i class="like-icon {{ $isLiked ? 'fas fa-heart text-red-500' : 'far fa-heart' }}"></i>
                            <span class="like-count font-semibold">{{ $likesCount }}</span>
                        </button>

                        @auth
                            <!-- Bookmark Button -->
                            <button type="button"
                                class="unsave-btn text-gray-400 hover:text-sky-600 p-1.5 rounded-full hover:bg-gray-100 transition"
                                data-post-id="{{ $item->id }}"
                                title="Save bookmark">
                                <i class="{{ $isSaved ? 'fas text-sky-600' : 'far' }} fa-bookmark"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="text-gray-400 hover:text-gray-700 p-1.5 rounded-full hover:bg-gray-100 transition">
                                    <i class="fas fa-ellipsis"></i>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-xl py-1 z-20 border border-gray-100 text-xs">
                                    @if (Auth::id() == $item->user_id || Auth::user()->role == 'admin')
                                        <a href="{{ route('posts.edit', $item->id) }}"
                                            class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 transition">
                                            <i class="fas fa-edit w-4 mr-2 text-gray-400"></i> Edit post
                                        </a>
                                        <form action="{{ route('posts.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 transition">
                                                <i class="fas fa-trash-alt w-4 mr-2"></i> Delete post
                                            </button>
                                        </form>
                                    @endif
                                    @if ($item->mediaContent->isNotEmpty())
                                        <a href="{{ asset('storage/' . $item->mediaContent->first()->path) }}"
                                            download="{{ $item->title }}"
                                            class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 transition">
                                            <i class="fas fa-download w-4 mr-2 text-gray-400"></i> Download
                                        </a>
                                    @endif
                                    <button type="button"
                                        class="share-trigger-btn flex items-center w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 transition"
                                        data-url="{{ route('posts.show', $item->id) }}"
                                        data-title="{{ $item->title }}"
                                        @click="open = false">
                                        <i class="fas fa-share-nodes w-4 mr-2 text-gray-400"></i> Share
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Share button for guest -->
                            <button type="button"
                                class="share-trigger-btn text-gray-400 hover:text-sky-600 p-1.5 rounded-full hover:bg-gray-100 transition"
                                data-url="{{ route('posts.show', $item->id) }}"
                                data-title="{{ $item->title }}"
                                title="Share">
                                <i class="fas fa-share-nodes text-xs"></i>
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

