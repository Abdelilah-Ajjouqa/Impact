@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>

                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column - Profile Images -->
                        <div>
                            <div class="mb-6">
                                <label class="block text-gray-700 font-medium mb-2">Profile Picture</label>
                                <div class="flex items-center">
                                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 mr-4">
                                        @if ($user->avatar)
                                            <img src="{{ asset($user->avatar) }}" alt="{{ $user->username }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-400 text-3xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" id="avatar" name="avatar"
                                            class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-sky-50 file:text-sky-700
                                        hover:file:bg-sky-100">
                                        <p class="text-xs text-gray-500 mt-1">Recommended: Square image, at least 400x400px
                                        </p>
                                    </div>
                                </div>
                                @error('avatar')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-medium mb-2">Cover Image</label>
                                <div class="mb-2">
                                    <div class="w-full h-32 rounded-lg overflow-hidden bg-gray-200">
                                        @if ($user->cover)
                                            <img src="{{ asset($user->cover) }}" alt="Cover"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-r from-red-500 to-pink-500"></div>
                                        @endif
                                    </div>
                                </div>
                                <input type="file" id="cover" name="cover"
                                    class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-sky-50 file:text-sky-700
                                hover:file:bg-sky-100">
                                <p class="text-xs text-gray-500 mt-1">Recommended: 1500x500px</p>
                                @error('cover')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column - Profile Details -->
                        <div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name</label>
                                    <input type="text" id="first_name" name="first_name" value="{{ $user->first_name }}"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 @error('first_name') border-red-500 @enderror"
                                        required>
                                    @error('first_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" value="{{ $user->last_name }}"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 @error('last_name') border-red-500 @enderror"
                                        required>
                                    @error('last_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                                <input type="text" id="username" name="username" value="{{ $user->username }}"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 @error('username') border-red-500 @enderror"
                                    required>
                                @error('username')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                                <input type="email" id="email" name="email" value="{{ $user->email }}"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 @error('email') border-red-500 @enderror"
                                    required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <a href="{{ route('users.show', $user->id) }}"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-full mr-2">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="bg-sky-500 hover:bg-sky-600 text-white font-medium py-2 px-4 rounded-full">
                                    Save Changes
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
        // Preview uploaded images
        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('avatar');
            const coverInput = document.getElementById('cover');

            avatarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarInput.previousElementSibling.querySelector('img') ?
                            avatarInput.previousElementSibling.querySelector('img').src = e.target
                            .result :
                            avatarInput.previousElementSibling.innerHTML =
                            `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            coverInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const coverPreview = document.querySelector(
                            '.w-full.h-32.rounded-lg.overflow-hidden.bg-gray-200');
                        coverPreview.innerHTML =
                            `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection
