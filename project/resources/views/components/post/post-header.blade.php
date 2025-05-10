@props(['post'])

<h1 class="text-2xl font-bold mb-2">{{ $post->title }}</h1>

@if ($post->description)
    <p class="text-gray-700 mb-6">{{ $post->description }}</p>
@endif

<div class="flex items-center mb-6">
    <a href="{{ route('users.show', $post->user_id) }}" class="flex items-center">
        <img src="{{ $post->user->avatar ?? 'https://placehold.co/40' }}" alt="User"
            class="w-10 h-10 rounded-full mr-3">
        <div>
            <h3 class="font-semibold">{{ $post->user->username }}</h3>
            <p class="text-gray-600 text-sm">{{ $post->user->followers_count ?? 0 }} followers</p>
        </div>
    </a>

    @if (Auth::check() && Auth::id() != $post->user_id)
        <button class="ml-auto bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-full">
            Follow
        </button>
    @endif
</div>
