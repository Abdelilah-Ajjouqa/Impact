@props(['mediaContent'])

<div class="md:w-3/5 bg-black flex flex-col relative">
    <div class="flex-grow flex items-center justify-center">
        @if ($mediaContent->isNotEmpty())
            @foreach ($mediaContent as $index => $media)
                @if (str_contains($media->type, 'video'))
                    <div id="media-{{ $index }}"
                        class="media-item w-full h-full flex items-center justify-center {{ $index > 0 ? 'hidden' : '' }}">
                        <video class="w-full h-auto max-h-[80vh] object-contain" controls loop playsinline
                            preload="metadata">
                            <source src="{{ asset('storage/' . $media->path) }}" type="{{ $media->type }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @else
                    <div id="media-{{ $index }}"
                        class="media-item w-full h-full flex items-center justify-center {{ $index > 0 ? 'hidden' : '' }}">
                        <img src="{{ asset('storage/' . $media->path) }}"
                            alt="{{ $mediaContent->first()->title ?? 'Post image' }} - Image {{ $index + 1 }}"
                            class="w-full h-auto max-h-[80vh] object-contain">
                    </div>
                @endif
            @endforeach
        @else
            <div class="bg-gray-200 w-full h-96 flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-5xl"></i>
            </div>
        @endif
    </div>

    @if ($mediaContent->count() > 1)
        <!-- Pagination Controls -->
        <div class="absolute inset-x-0 top-1/2 transform -translate-y-1/2 flex justify-between px-4 z-10">
            <button id="prev-btn"
                class="bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-md transition-all">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button id="next-btn"
                class="bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-md transition-all">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Pagination Indicators -->
        <div class="absolute bottom-4 inset-x-0 flex justify-center gap-2">
            @foreach ($mediaContent as $index => $media)
                <button
                    class="pagination-indicator h-2 w-2 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"
                    data-index="{{ $index }}"></button>
            @endforeach
        </div>
    @endif
</div>
