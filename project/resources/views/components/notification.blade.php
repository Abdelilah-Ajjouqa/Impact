@props(['type' => 'success', 'message' => ''])

@php
    $isSuccess = $type === 'success';
    $containerClasses = $isSuccess
        ? 'bg-green-100 text-green-700 border border-green-400'
        : 'bg-red-100 text-red-700 border border-red-400';
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)"
    class="fixed top-[5rem] right-6 z-50 min-w-[250px] max-w-xs px-4 py-3 rounded-lg shadow-lg flex items-center space-x-3 transition-all duration-300 {{ $containerClasses }}"
    style="pointer-events: auto;">
    <span>
        @if ($isSuccess)
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        @else
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        @endif
    </span>
    <span class="font-medium">{{ $message }}</span>
</div> 