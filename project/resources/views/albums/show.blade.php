@extends('layouts.app')

@section('title', $album->title)

@section('content')
    <div class="max-w-6xl mx-auto" x-data="{ confirmDelete: false }">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('albums.index') }}" class="text-gray-600 hover:text-gray-900 mr-2">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold">{{ $album->title }}</h1>
                    @if ($album->is_private)
                        <span class="ml-2 bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full flex items-center">
                            <i class="fas fa-lock text-xs mr-1"></i> Private
                        </span>
                    @endif
                </div>

                @if (Auth::id() === $album->user_id)
                    <div class="flex space-x-2">
                        <a href="{{ route('albums.edit', $album->id) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-full">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <button @click="confirmDelete = true"
                            class="bg-red-100 hover:bg-red-200 text-red-800 py-2 px-4 rounded-full">
                            <i class="fas fa-trash-alt mr-1"></i> Delete
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Delete Confirmation -->
        <div x-show="confirmDelete" @click.away="confirmDelete = false" class="bg-white border p-4 rounded shadow mb-6">
            <p class="mb-2">Are you sure you want to delete this album?</p>
            <form action="{{ route('albums.destroy', $album->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded">Yes, Delete</button>
                <button type="button" @click="confirmDelete = false" class="ml-2 text-gray-700">Cancel</button>
            </form>
        </div>

        <!-- Album Description -->
        <p class="text-gray-600 mb-4">{{ $album->description }}</p>

        <!-- Media Grid -->
        <div class="masonry-grid">
            @forelse($album->posts as $item)
                <div class="masonry-item">
                    <div class="bg-gray-50 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                        <a href="{{ route('posts.show', $item->id) }}"
                            class="block hover:opacity-85 overflow-hidden rounded-t-lg">
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
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="text-gray-600 hover:text-red-600 p-1 rounded-full hover:bg-gray-100">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false"
                                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                                @if (Auth::id() == $item->user_id || Auth::user()->role == 'admin')
                                                    <a href="{{ route('posts.edit', $item->id) }}"
                                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-edit mr-2"></i> Edit post
                                                    </a>
                                                    <form action="{{ route('posts.destroy', $item->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                                            <i class="fas fa-trash-alt mr-2"></i> Delete post
                                                        </button>
                                                    </form>
                                                @endif
                                                @if (Auth::id() === $album->user_id)
                                                    <form action="{{ route('albums.posts.remove', ['id' => $album->id, 'post_id' => $item->id]) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to remove this post from the album?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                                            <i class="fas fa-minus-circle mr-2"></i> Remove from album
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ asset('storage/' . $media->path) }}"
                                                    download="{{ $item->title }}"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-download mr-2"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center mx-auto py-10">
                    <div class="text-gray-500 mb-4 mx-auto">
                        <i class="fas fa-images text-5xl"></i>
                    </div>
                    {{-- <h3 class="text-xl font-semibold mb-2">No posts in this album yet</h3> --}}
                    {{-- @if (Auth::id() === $album->user_id)
                        <p class="text-gray-600 mb-4">Start adding posts to your album!</p>
                    @endif --}}
                </div>
            @endforelse
        </div>
    </div>
@endsection
