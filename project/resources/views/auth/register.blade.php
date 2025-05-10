@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="flex justify-center items-center min-h-[80vh] py-5">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="text-center">
                <img src="/butterfly-logo.png" alt="Logo" class="mx-auto mb-4" style="height: 7rem; width: auto;" />
                <h1 class="text-2xl font-bold">Create your account</h1>
                <p class="text-gray-600">Find new ideas to try</p>
            </div>

            <form method="POST" action="{{ route('auth.register') }}">
                @csrf

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('first_name') border-red-500 @enderror"
                            required autofocus>
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('last_name') border-red-500 @enderror"
                            required>
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('username') border-red-500 @enderror"
                        required>
                    @error('username')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('email') border-red-500 @enderror"
                        required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('password') border-red-500 @enderror"
                        required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700"
                        required>
                </div>

                <div class="mb-6">
                    <button type="submit"
                        class="w-full bg-black hover:bg-blue-500 text-white font-semibold py-3 px-4 rounded-lg">
                        Sign up
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-gray-600">
                        Already have an account?
                        <a href="{{ route('auth.login.form') }}" class="text-black hover:underline">Log in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
