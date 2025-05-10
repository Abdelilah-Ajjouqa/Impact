@extends('layouts.app')

@section('title', 'Search Results for "' . $query . '"')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Search results for "{{ $query }}"</h1>
        
        @if($posts->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                <h2 class="text-xl font-semibold text-gray-600">No results found</h2>
                <p class="text-gray-500 mt-2">Try different keywords or check your spelling</p>
            </div>
        @else
            <div class="masonry-grid">
                @foreach($posts as $post)
                    <div class="masonry-item">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <a href="{{ route('posts.show', $post->id) }}" class="block">
                                @if($post->mediaContent->isNotEmpty())
                                    @php $media = $post->mediaContent->first(); @endphp
                                    @if(str_contains($media->type, 'video'))
                                        <div class="relative">
                                            <video class="w-full h-auto" preload="metadata">
                                                <source src="{{ asset('storage/' . $media->path) }}" type="{{ $media->type }}">
                                            </video>
                                            <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                                                <i class="fas fa-play-circle text-white text-4xl"></i>
                                            </div>
                                        </div>
                                    @else
                                        <img src="{{ asset('storage/' . $media->path) }}" alt="{{ $post->title }}" class="w-full h-auto">
                                    @endif
                                @else
                                    <div class="bg-gray-200 w-full" style="aspect-ratio: 3/4;"></div>
                                @endif
                            </a>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg truncate">{{ $post->title }}</h3>
                                @if($post->description)
                                    <p class="text-gray-600 text-sm line-clamp-2 mt-1">{{ $post->description }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center">
                                        <img src="{{ $post->user->avatar ?? 'https://placehold.co/40' }}" alt="{{ $post->user->username }}" class="w-8 h-8 rounded-full mr-2">
                                        <span class="text-sm font-medium">{{ $post->user->username }}</span>
                                    </div>
                                    
                                    @auth
                                    <div class="flex space-x-2">
                                        @php
                                            // Check if the post is saved
                                            $isSaved = \App\Models\SavedPost::where('user_id', Auth::id())
                                                ->where('post_id', $post->id)
                                                ->exists();
                                        @endphp
                                        <button class="text-gray-600 hover:text-red-600 p-1 rounded-full hover:bg-gray-100 save-btn" 
                                                data-post-id="{{ $post->id }}" 
                                                data-saved="{{ $isSaved ? 'true' : 'false' }}">
                                            <i class="bookmark-icon {{ $isSaved ? 'fas' : 'far' }} fa-bookmark"></i>
                                        </button>
                                    </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $posts->appends(['q' => $query])->links() }}
            </div>
        @endif
    </div>
@endsection
