@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row">
            <!-- Left side - Media Gallery Component -->
            <x-post.media-gallery :mediaContent="$post->mediaContent" />

            <!-- Right side - Content -->
            <div class="md:w-2/5 px-5 overflow-y-scroll overflow-x-hidden">
                <!-- Post Actions -->
                <x-post.post-actions :post="$post" />

                <!-- Post Header -->
                <x-post.post-header :post="$post" />

                <!-- Post Tags -->
                <x-post.post-tags :tags="$post->tags" />

                <!-- Comments Section -->
                <x-comments :post="$post" />
            </div>
        </div>
    </div>

    <!-- Related Posts Section -->
    <x-post.related-posts :relatedPosts="$relatedPosts ?? null" />

    <!-- Add to Album Modal -->
    @auth
        <x-post.add-to-album-modal :post="$post" />
    @endauth
@endsection

@section('scripts')
    @include('components.ajax')
    <x-post.media-gallery-js />
    <x-post.post-save-js />
    @stack('scripts')
@endsection