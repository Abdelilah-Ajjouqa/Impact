@extends('layouts.app')

@section('title', $user->username)

@section('content')
    <div class="max-w-5xl mx-auto">
        <!-- Cover Image -->
        <div class="relative h-64 rounded-lg mb-6">
            @if ($user->cover)
                <img src="{{ asset($user->cover) }}" alt="Cover" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-r from-red-500 to-pink-500"></div>
            @endif

            <!-- Profile Image -->
            <div class="absolute left-1/2 bottom-0 transform -translate-x-1/2 translate-y-1/2">
                <div class="w-32 h-32 rounded-full border-4 border-white overflow-hidden bg-white">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="text-center mt-16 mb-8">
            <h1 class="text-2xl font-bold">{{ $user->first_name }} {{ $user->last_name }}</h1>
            <span class="flex justify-center text-gray-600">
                <p>@</p>
                <p>{{ $user->username }}</p>
            </span>

            <!-- User Actions -->
            <div class="flex justify-center mt-4 space-x-2">
                @auth
                    @if (Auth::id() == $user->id)
                        <a href="{{ route('users.edit', $user->id) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-full">
                            <i class="fas fa-edit mr-1"></i> Edit Profile
                        </a>
                    @else
                        <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-full">
                            Follow
                        </button>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b mb-6">
            <div class="flex justify-center">
                <button id="created-tab" class="px-4 py-2 border-b-2 border-red-600 text-red-600 font-medium">
                    Created
                </button>
                @auth
                    <button id="saved-tab" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                        Saved
                    </button>
                @endauth
                <button id="albums-tab" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    Albums
                </button>
            </div>
        </div>

        <!-- Created posts Grid -->
        <div id="created-posts" class="masonry-grid">
            @forelse($posts as $item)
                <div class="masonry-item">
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <a href="{{ route('posts.show', $item->id) }}" class="block">
                            @if ($item->mediaContent->isNotEmpty())
                                @php
                                    $media = $item->mediaContent->first();
                                    $isVideo = str_contains($media->type, 'video');
                                @endphp

                                @if ($isVideo)
                                    <video class="w-full object-cover" autoplay muted loop playsinline>
                                        <source src="{{ asset('storage/' . $media->path) }}" type="{{ $media->type }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ asset('storage/' . $media->path) }}" alt="{{ $item->title }}"
                                        class="w-full object-cover">
                                @endif
                            @else
                                <div class="bg-gray-200 w-full h-48 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold text-lg truncate">{{ $item->title }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-2 mt-1">{{ $item->description }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-image text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">No posts yet</h3>
                    @if (Auth::check() && Auth::id() == $user->id)
                        <p class="text-gray-600 mb-4">Share your ideas with the world!</p>
                        <a href="{{ route('posts.create') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-full">
                            Create post
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Saved posts -->
        <div id="saved-posts" class="masonry-grid hidden">
            @forelse($savedPosts as $savedPost)
                <div class="masonry-item">
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <a href="{{ route('posts.show', $savedPost->post->id) }}" class="block">
                            @if ($savedPost->post->mediaContent->isNotEmpty())
                                @php
                                    $media = $savedPost->post->mediaContent->first();
                                    $isVideo = str_contains($media->type, 'video');
                                @endphp

                                @if ($isVideo)
                                    <video class="w-full object-cover" autoplay muted loop playsinline>
                                        <source src="{{ asset('storage/' . $media->path) }}" type="{{ $media->type }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ asset('storage/' . $media->path) }}" alt="{{ $savedPost->post->title }}"
                                        class="w-full object-cover">
                                @endif
                            @else
                                <div class="bg-gray-200 w-full h-48 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold text-lg truncate">{{ $savedPost->post->title }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-2 mt-1">{{ $savedPost->post->description }}</p>

                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center">
                                    <img src="{{ $savedPost->post->user->avatar ?? 'https://via.placeholder.com/40' }}"
                                        alt="User" class="w-8 h-8 rounded-full mr-2">
                                    <span class="text-sm font-medium">{{ $savedPost->post->user->username }}</span>
                                </div>

                                <div class="flex space-x-2">
                                    @if (Auth::check() && Auth::id() == $user->id)
                                        <!-- Replace the existing form with this button -->
                                        <button type="button"
                                            class="unsave-btn text-gray-600 hover:text-red-600 p-1 rounded-full hover:bg-gray-100"
                                            data-post-id="{{ $savedPost->post->id }}">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-bookmark text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">No saved posts yet</h3>
                    <p class="text-gray-600 mb-4">Save posts to find them later</p>
                    <a href="{{ route('posts.index') }}"
                        class="bg-black hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-full">
                        Discover ideas
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Albums Grid -->
        <div id="albums-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 hidden">
            @if (Auth::check() && Auth::id() == $user->id)
                <a href="{{ route('albums.create') }}" class="block h-full">
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border-2 border-dashed border-gray-300 flex flex-col items-center justify-center h-full p-6">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-plus-circle text-4xl"></i>
                        </div>
                        <h3 class="font-semibold text-lg text-gray-600">Create New Album</h3>
                    </div>
                </a>
            @endif

            @forelse($user->albums()->withCount('posts')->latest()->get() as $album)
                @if (!$album->is_private || Auth::id() == $user->id)
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <a href="{{ route('albums.show', $album->id) }}" class="block">
                            <div class="relative pb-[100%]">
                                @if ($album->cover_image)
                                    <img src="{{ asset('storage/' . $album->cover_image) }}" alt="{{ $album->title }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-images text-gray-400 text-4xl"></i>
                                    </div>
                                @endif

                                @if ($album->is_private)
                                    <div
                                        class="absolute top-2 right-2 bg-black bg-opacity-70 text-white rounded-full p-1 w-8 h-8 flex items-center justify-center">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold text-lg truncate">{{ $album->title }}</h3>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-gray-600 text-sm">{{ $album->posts_count }}
                                    {{ Str::plural('post', $album->posts_count) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-full text-center py-10">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-folder-open text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">No albums yet</h3>
                    @if (Auth::check() && Auth::id() == $user->id)
                        <p class="text-gray-600 mb-4">Create albums to organize your posts</p>
                        <a href="{{ route('albums.create') }}"
                            class="bg-black hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-full">
                            Create your first album
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
    @include('components.tabs')
    @include('components.save')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createdTab = document.getElementById('created-tab');
            const savedTab = document.getElementById('saved-tab');
            const albumsTab = document.getElementById('albums-tab');

            const createdPosts = document.getElementById('created-posts');
            const savedPosts = document.getElementById('saved-posts');
            const albumsGrid = document.getElementById('albums-grid');

            createdTab.addEventListener('click', function() {
                createdTab.classList.add('border-red-600', 'text-red-600');
                savedTab.classList.remove('border-red-600', 'text-red-600');
                albumsTab.classList.remove('border-red-600', 'text-red-600');

                createdPosts.classList.remove('hidden');
                savedPosts.classList.add('hidden');
                albumsGrid.classList.add('hidden');
            });

            savedTab.addEventListener('click', function() {
                savedTab.classList.add('border-red-600', 'text-red-600');
                createdTab.classList.remove('border-red-600', 'text-red-600');
                albumsTab.classList.remove('border-red-600', 'text-red-600');

                savedPosts.classList.remove('hidden');
                createdPosts.classList.add('hidden');
                albumsGrid.classList.add('hidden');
            });

            albumsTab.addEventListener('click', function() {
                albumsTab.classList.add('border-red-600', 'text-red-600');
                createdTab.classList.remove('border-red-600', 'text-red-600');
                savedTab.classList.remove('border-red-600', 'text-red-600');

                albumsGrid.classList.remove('hidden');
                createdPosts.classList.add('hidden');
                savedPosts.classList.add('hidden');
            });
        });
    </script>
@endsection
