@extends('layouts.app')

@section('title', 'Edit Album')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Edit Album</h1>

            <form action="{{ route('albums.update', $album->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $album->title) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description (Optional)</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">{{ old('description', $album->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="cover_image" class="block text-gray-700 font-medium mb-2">Cover Image</label>

                    @if ($album->cover_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $album->cover_image) }}" alt="Current cover"
                                class="w-32 h-32 object-cover rounded-md">
                            <p class="text-sm text-gray-500 mt-1">Current cover image</p>
                        </div>
                    @endif

                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                        class="w-full border border-gray-300 rounded-md p-2">
                    <p class="text-gray-500 text-sm mt-1">Upload a new image to change the cover (recommended size: 800x800
                        pixels)</p>
                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_private" value="1"
                            {{ old('is_private', $album->is_private) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-gray-700">Make this album private</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1">Private albums are only visible to you</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('albums.show', $album->id) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md">
                        Cancel
                    </a>
                    <button type="submit" class="bg-black hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                        Update Album
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
