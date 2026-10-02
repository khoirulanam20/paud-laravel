@props([
    'path',
    'class' => 'h-12 w-12 object-cover rounded-lg shrink-0',
    'videoClass' => null,
])

@php
    use App\Support\DocumentationMedia;
    $url = DocumentationMedia::publicAssetPath($path);
    $isVideo = DocumentationMedia::isVideoPath($path);
    $videoClass = $videoClass ?? $class;
@endphp

<div {{ $attributes->merge(['class' => 'relative shrink-0']) }}>
    @if($isVideo)
        <video src="{{ $url }}" class="{{ $videoClass }} bg-gray-900" muted playsinline preload="metadata"></video>
        <span class="absolute inset-0 flex items-center justify-center text-white text-[10px] sm:text-xs bg-black/25 rounded-[inherit] pointer-events-none" aria-hidden="true">▶</span>
    @else
        <img src="{{ $url }}" alt="" class="{{ $class }}" loading="lazy" decoding="async">
    @endif
</div>
