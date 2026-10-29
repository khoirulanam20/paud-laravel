@props([
    'label',
    'value',
    'context' => null,
    'href' => null,
    'linkLabel' => 'Detail',
    'icon' => null,
    'class' => '',
])

@php
    $iconPath = \App\Support\DashboardIcons::path($icon);
    $valueTitle = (string) $value;
    $contextTrimmed = is_string($context) ? trim($context) : '';
@endphp

<div {{ $attributes->merge(['class' => 'stat-card stat-card--context '.$class]) }}>
    @if($iconPath)
        <div class="stat-icon shrink-0">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
            </svg>
        </div>
    @endif
    <div class="min-w-0 flex-1">
        <p class="dashboard-kpi-label">{{ $label }}</p>
        <p class="dashboard-kpi-value truncate" title="{{ $valueTitle }}">{{ $value }}</p>
        @if($contextTrimmed !== '')
            <p class="dashboard-kpi-context">{{ $contextTrimmed }}</p>
        @endif
        @if($href)
            <a href="{{ $href }}" class="dashboard-kpi-link">{{ $linkLabel }} &rarr;</a>
        @endif
    </div>
</div>
