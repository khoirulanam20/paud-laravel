@props([
    'label',
    'value',
    'href' => null,
    'linkLabel' => 'Detail',
    'icon' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'stat-card ' . $class]) }}>
    @if($icon)
        <div class="stat-icon shrink-0">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
            </svg>
        </div>
    @endif
    <div class="min-w-0 flex-1">
        <p class="dashboard-kpi-label">{{ $label }}</p>
        <p class="dashboard-kpi-value truncate" title="{{ is_string($value) ? $value : '' }}">{{ $value }}</p>
        @if($href)
            <a href="{{ $href }}" class="dashboard-kpi-link">{{ $linkLabel }} &rarr;</a>
        @endif
    </div>
</div>
