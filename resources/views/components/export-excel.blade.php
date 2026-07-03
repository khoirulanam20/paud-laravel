@props(['route', 'params' => []])

<a href="{{ route($route, array_merge(request()->except('page', 'per_page'), $params)) }}"
    {{ $attributes->merge(['class' => 'btn-secondary inline-flex items-center gap-2 shrink-0']) }}>
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    Export Excel
</a>
