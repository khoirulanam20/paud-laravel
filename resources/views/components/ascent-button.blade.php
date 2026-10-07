@props(['tag' => 'button'])
@php
    $classes = 'bg-primary text-cream-foreground rounded-md btn inline-flex items-center justify-center gap-2.5 w-full max-h-none disabled:opacity-50 disabled:cursor-not-allowed';
@endphp
@if($tag === 'button')
<button type="{{ $attributes->get('type', 'submit') }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
@else
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
@endif
