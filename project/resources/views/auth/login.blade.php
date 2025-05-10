@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex justify-center items-center min-h-[80vh]">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/butterfly-logo.png" alt="Logo" class="mx-auto mb-4" style="height: 5rem; width: auto;" />
            <h1 class="text-2xl font-bold">Welcome to PinClone</h1>
            <p class="text-gray-600">Find new ideas to try</p>
        </div>

        <form method="POST" action="{{ route('auth.login') }}">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('email') border-red-500 @enderror" 
                    required 
                    autofocus
                >
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('password') border-red-500 @enderror" 
                    required
                >
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <button type="submit" class="w-full bg-black hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg">
                    Log in
                </button>
            </div>

            <div class="text-center">
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('auth.register.form') }}" class="text-black hover:underline">Sign up</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection