@props([
    'items' => [],
    'title' => 'Perlu perhatian',
    'overflowHref' => null,
    'maxVisible' => 5,
])

@php
    $maxVisible = (int) $maxVisible;
    $total = count($items);
    $visible = array_slice($items, 0, $maxVisible);
    $overflowHref = $overflowHref ?: ($visible[0]['href'] ?? null);
    $hasAside = isset($aside);
    $hasBelowPanel = isset($belowPanel);
    $hasSplit = $hasAside || $hasBelowPanel;
@endphp

<section {{ $attributes->merge(['class' => 'dashboard-attention'.($hasSplit ? ' dashboard-attention--split' : '')]) }} aria-labelledby="dashboard-attention-heading">
    <div class="dashboard-section-head">
        <h2 id="dashboard-attention-heading" class="dashboard-section-title">{{ $title }}</h2>
        <p class="dashboard-section-desc">Hal yang perlu tindakan hari ini</p>
    </div>

    <div @class(['dashboard-attention-split' => $hasSplit])>
        <div class="dashboard-attention-main-col">
            <div class="dashboard-attention-panel">
            @if($total === 0)
                <div class="dashboard-all-clear" role="status">
                    <span class="dashboard-all-clear-icon" aria-hidden="true">✓</span>
                    <div>
                        <p class="dashboard-all-clear-title">Semua beres</p>
                        <p class="dashboard-all-clear-desc">Tidak ada antrian atau isu yang perlu ditindak sekarang.</p>
                    </div>
                </div>
            @else
                <ul class="dashboard-attention-list">
                    @foreach($visible as $item)
                        @php
                            $href = ! empty($item['href']) ? $item['href'] : '#';
                            if (empty($item['href'])) {
                                \Illuminate\Support\Facades\Log::warning('Dashboard attention item missing href', ['title' => $item['title'] ?? '']);
                            }
                            $severity = $item['severity'] ?? 'warning';
                        @endphp
                        <li>
                            <a href="{{ $href }}" class="dashboard-attention-item dashboard-attention-item--{{ $severity }}">
                                <div class="min-w-0 flex-1">
                                    <p class="dashboard-attention-title">{{ $item['title'] }}</p>
                                    <p class="dashboard-attention-detail">{{ $item['detail'] }}</p>
                                </div>
                                <span class="dashboard-attention-action dashboard-attention-action--{{ $severity }}">{{ $item['action'] ?? 'Buka' }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if($total > $maxVisible && $overflowHref)
                    <p class="m-0 mt-2">
                        <a href="{{ $overflowHref }}" class="dashboard-compact-link">Lihat semua ({{ $total }}) &rarr;</a>
                    </p>
                @endif
            @endif
            </div>

            @if($hasBelowPanel)
                <div class="dashboard-attention-below-panel">
                    {{ $belowPanel }}
                </div>
            @endif
        </div>

        @if($hasAside)
            <div class="dashboard-attention-aside">
                {{ $aside }}
            </div>
        @endif
    </div>
</section>
