@extends('layouts.app')

@section('title', 'Create post')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6">Create post</h1>

                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column - Media Upload -->
                        <div>
                            <div class="mb-4">
                                <label for="media" class="block text-gray-700 font-medium mb-2">Upload Image or
                                    Video</label>
                                <div class="border-2 border-gray-300 rounded-lg p-6 text-center"
                                    onclick="document.getElementById('media').click()">
                                    <div id="preview" class="hidden mb-4">
                                        <img id="image-preview" class="max-h-80 mx-auto" alt="Preview">
                                    </div>
                                    <div id="upload-prompt">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-gray-500">Click to upload</p>
                                        <p class="text-gray-400 text-sm mt-1">Recommendation: Use high-quality .jpg files
                                            less than 20MB</p>
                                    </div>
                                    <input type="file" id="media" name="media[]" class="hidden"
                                        accept="image/*,video/*" multiple required>
                                </div>
                                @error('media')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column - post Details -->
                        <div>
                            <div class="mb-4">
                                <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 @error('title') border-red-500 @enderror"
                                    required>
                                @error('title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="tags" class="block text-gray-700 font-medium mb-2">Tags (space
                                    separated)</label>
                                <input type="text" id="tags" name="tags" value="{{ old('tags') }}"
                                    placeholder="nature travel photography"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 @error('tags') border-red-500 @enderror">
                                @error('tags')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <a href="{{ route('posts.index') }}"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-full mr-2">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="bg-sky-500 hover:bg-sky-600 text-white font-medium py-2 px-4 rounded-full">
                                    Create post
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mediaInput = document.getElementById('media');
            const preview = document.getElementById('preview');
            const uploadPrompt = document.getElementById('upload-prompt');

            // Handle file selection
            mediaInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    // Clear previous previews
                    preview.innerHTML = '';

                    // Create a container for previews
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'grid grid-cols-2 gap-2';
                    preview.appendChild(previewContainer);

                    // Show preview area and hide upload prompt
                    preview.classList.remove('hidden');
                    uploadPrompt.classList.add('hidden');

                    // Process each file
                    Array.from(this.files).forEach(file => {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'relative';

                            if (file.type.startsWith('video/')) {
                                const video = document.createElement('video');
                                video.src = e.target.result;
                                video.className = 'h-40 w-full object-cover rounded';
                                video.controls = true;
                                video.muted = true;
                                previewItem.appendChild(video);
                            } else {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'h-40 w-full object-cover rounded';
                                img.alt = 'Preview';
                                previewItem.appendChild(img);
                            }

                            previewContainer.appendChild(previewItem);
                        }

                        reader.readAsDataURL(file);
                    });

                    // Add file count indicator
                    const fileCount = document.createElement('p');
                    fileCount.className = 'text-gray-600 text-sm mt-2';
                    fileCount.textContent = `${this.files.length} file(s) selected`;
                    preview.appendChild(fileCount);
                }
            });
        });
    </script>
@endsection
