@props(['href', 'title', 'description'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'dashboard-quick-tile block']) }}>
    <p class="font-semibold text-xs sm:text-sm mb-0.5 text-[var(--text-primary)]">{{ $title }}</p>
    <p class="text-[11px] sm:text-xs line-clamp-2 text-[var(--text-muted)]">{{ $description }}</p>
</a>
