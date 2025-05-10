@props(['tags'])

@if ($tags->isNotEmpty())
    <div class="mb-6">
        <h3 class="font-semibold mb-2">Tags</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($tags as $tag)
                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">
                    {{ $tag->name }}
                </span>
            @endforeach
        </div>
    </div>
@endif
