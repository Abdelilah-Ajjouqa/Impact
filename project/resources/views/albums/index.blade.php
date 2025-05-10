@extends('layouts.app')

@section('title', 'My Albums')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Albums</h1>
            <a href="{{ route('albums.create') }}"
                class="bg-black hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-full">
                <i class="fas fa-plus mr-1"></i> Create Album
            </a>
        </div>

        @if ($albums->isEmpty())
            <div class="text-center py-10">
                <div class="text-gray-500 mb-4">
                    <i class="fas fa-folder-open text-5xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">No albums yet</h3>
                <p class="text-gray-600 mb-4">Create albums to organize your posts</p>
                <a href="{{ route('albums.create') }}"
                    class="bg-black hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-full">
                    Create your first album
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($albums as $album)
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
                                <div class="flex space-x-2">
                                    <a href="{{ route('albums.edit', $album->id) }}"
                                        class="text-gray-600 hover:text-blue-600">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('albums.destroy', $album->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this album?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-600 hover:text-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
