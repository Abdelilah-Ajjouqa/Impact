@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <main class="container mx-auto px-4 sm:px-6">
        <section id="content">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Discover Ideas</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Explore curated designs, inspiration, and community creations</p>
                </div>
                @auth
                    <a href="{{ route('posts.create') }}"
                        class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-5 rounded-full flex items-center shadow-sm hover:shadow transition">
                        <i class="fas fa-plus mr-2"></i> Create post
                    </a>
                @endauth
            </div>

            <div id="masonry-grid" class="masonry-grid">
                @if($posts->isNotEmpty())
                    @include('posts.partials.feed-card', ['posts' => $posts, 'savedPostIds' => $savedPostIds])
                @else
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto text-gray-400 mb-4 text-2xl">
                            <i class="fas fa-compass"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1">No posts found</h3>
                        <p class="text-gray-500 text-sm max-w-sm mx-auto">Be the pioneer to kick off inspiration in this community!</p>
                        @auth
                            <a href="{{ route('posts.create') }}"
                                class="mt-5 inline-block bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-5 rounded-full text-sm shadow transition">
                                Create post
                            </a>
                        @else
                            <a href="{{ route('auth.login.form') }}"
                                class="mt-5 inline-block bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-5 rounded-full text-sm shadow transition">
                                Log in to create posts
                            </a>
                        @endauth
                    </div>
                @endif
            </div>

            <!-- Infinite Scroll Sentinel & Live Indicator -->
            <div id="infinite-scroll-sentinel" class="py-10 flex flex-col items-center justify-center {{ $posts->hasMorePages() ? '' : 'hidden' }}"
                data-next-page="{{ $posts->nextPageUrl() }}">
                <div id="infinite-scroll-spinner" class="flex items-center gap-3 text-sky-600">
                    <svg class="animate-spin h-6 w-6 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-500">Loading more ideas...</span>
                </div>
                <button id="load-more-btn" class="hidden mt-3 px-6 py-2 rounded-full border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition shadow-sm">
                    Load More Ideas
                </button>
            </div>

            <!-- End of Feed Indicator -->
            <div id="no-more-posts-indicator" class="py-12 text-center text-xs text-gray-400 font-semibold tracking-wider uppercase {{ $posts->hasMorePages() ? 'hidden' : ($posts->isEmpty() ? 'hidden' : '') }}">
                <div class="inline-flex items-center gap-3">
                    <span class="w-12 h-px bg-gray-200"></span>
                    <span>You've seen all the latest ideas</span>
                    <span class="w-12 h-px bg-gray-200"></span>
                </div>
            </div>

            <!-- Noscript fallback pagination -->
            <noscript>
                @if ($posts->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            </noscript>
        </section>
    </main>

    <!-- One-Click Quick Share Modal -->
    @include('components.post.share-modal')
@endsection

@section('scripts')
    @include('components.save')
@endsection
