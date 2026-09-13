@props(['post'])

<h1 class="text-2xl font-bold mb-2">{{ $post->title }}</h1>

@if ($post->description)
    <p class="text-gray-700 mb-6">{{ $post->description }}</p>
@endif

<div class="flex items-center mb-6">
    <a href="{{ route('users.show', $post->user_id) }}" class="flex items-center">
        <img src="{{ $post->user->avatar_url ?? 'https://placehold.co/40' }}" alt="User"
            class="w-10 h-10 rounded-full mr-3 object-cover">
        <div>
            <h3 class="font-semibold">{{ $post->user->username }}</h3>
            <p class="text-gray-600 text-sm">
                <span data-follower-count-for="{{ $post->user_id }}">{{ $post->user->followers_count ?? 0 }}</span> followers
            </p>
        </div>
    </a>

    @if (Auth::check() && Auth::id() != $post->user_id)
        @php
            $isFollowingAuthor = Auth::user()->isFollowing($post->user_id);
        @endphp
        <button type="button"
            data-user-id="{{ $post->user_id }}"
            data-following="{{ $isFollowingAuthor ? 'true' : 'false' }}"
            class="btn-follow ml-auto font-medium py-2 px-4 rounded-full transition-colors duration-200 cursor-pointer {{ $isFollowingAuthor ? 'bg-gray-100 hover:bg-gray-200 text-gray-800' : 'bg-red-600 hover:bg-red-700 text-white' }}">
            {{ $isFollowingAuthor ? 'Following' : 'Follow' }}
        </button>
    @endif
</div>
