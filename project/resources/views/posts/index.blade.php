@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <main class="container mx-auto">
        <section id="content">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Discover Ideas</h1>
                @auth
                    <a href="{{ route('posts.create') }}"
                        class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-full flex items-center">
                        <i class="fas fa-plus mr-2"></i> Create post
                    </a>
                @endauth
            </div>

            <div class="masonry-grid">
                @forelse($posts as $item)
                    <div class="masonry-item">
                        <div class="bg-gray-50 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <a href="{{ route('posts.show', $item->id) }}"
                                class="block hover:opacity-85 overflow-hidden rounded-t-lg">
                                @if ($item->mediaContent->isNotEmpty())
                                    @php $media = $item->mediaContent->first(); @endphp
                                    @if (str_contains($media->type, 'video'))
                                        <video class="w-full object-cover" autoplay muted loop playsinline>
                                            <source src="{{ asset('storage/' . $media->path) }}" type="{{ $media->type }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <img src="{{ asset('storage/' . $media->path) }}"
                                            alt="{{ $item->title }}" class="w-full object-cover">
                                    @endif
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-3xl"></i>
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
                                        <img src="{{ $item->user->avatar_url ?? 'https://placehold.co/40' }}" alt="User"
                                            class="w-8 h-8 rounded-full mr-2 object-cover">
                                        <span class="text-sm font-medium">{{ $item->user->username ?? 'User' }}</span>
                                    </a>

                                    <div class="flex gap-x-1">
                                        @auth
                                            @php
                                                $isSaved = isset($savedPostIds) && $savedPostIds->has($item->id);
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
                                                    @if ($item->mediaContent->isNotEmpty())
                                                        <a href="{{ asset('storage/' . $item->mediaContent->first()->path) }}"
                                                            download="{{ $item->title }}"
                                                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-download mr-2"></i> Download
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10">
                        <div class="text-gray-500 mb-4">
                            <i class="fas fa-image text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">No posts found</h3>
                        <p class="text-gray-600">Be the first to create a post!</p>
                        @auth
                            <a href="{{ route('posts.create') }}"
                                class="mt-4 inline-block bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-full">
                                Create post
                            </a>
                        @else
                            <a href="{{ route('auth.login.form') }}"
                                class="mt-4 inline-block bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-full">
                                Log in to create posts
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>

            @if ($posts->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>
    </main>
@endsection

@section('scripts')
    @include('components.save')
@endsection
