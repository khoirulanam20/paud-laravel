@php
    $adminUx = $adminDashboardUx ?? ['lead' => '', 'summary' => [], 'attention' => []];
    $adminChartCollection = collect($dashboardCharts ?? []);
    $adminChartsWide = $adminChartCollection->where('id', '!=', 'admin-presensi-bulan')->values()->all();
    $adminChartPresensi = $adminChartCollection->firstWhere('id', 'admin-presensi-bulan');
    $acts = $adminUx['activities'] ?? ['items' => [], 'total' => 0, 'href' => route('admin.kegiatan.index')];
@endphp

<div class="dashboard-role dashboard-role--admin-sekolah space-y-5 sm:space-y-6">
    <p class="dashboard-lead m-0">{{ $adminUx['lead'] ?? '' }}</p>

    <section aria-label="Ringkasan KPI" class="dashboard-summary-kpis">
        <div class="dashboard-kpi-grid" data-tour="dashboard-stats">
            @foreach($adminUx['summary'] ?? [] as $stat)
                <x-dashboard.kpi-context
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :context="$stat['context'] ?? null"
                    :href="$stat['href'] ?? null"
                    :icon="$stat['icon'] ?? null"
                    @if($loop->first) data-tour="dashboard-welcome" @endif
                />
            @endforeach
        </div>
    </section>

    <x-dashboard.attention
        :items="$adminUx['attention'] ?? []"
        :overflow-href="$adminUx['attentionOverflowHref'] ?? null"
        data-tour="dashboard-attention"
    >
        @if($adminChartPresensi)
            <x-slot:belowPanel>
                <x-dashboard.chart-card
                    :chart="$adminChartPresensi"
                    spotlight
                    data-tour="dashboard-presensi-spotlight"
                />
            </x-slot:belowPanel>
        @endif
        <x-slot:aside>
            <div class="dashboard-attention-aside-stack" data-tour="dashboard-recent">
                <x-dashboard.activity-list-card
                    title="Kegiatan hari ini"
                    :items="$acts['items'] ?? []"
                    :total="$acts['total'] ?? 0"
                    :href="$acts['href'] ?? route('admin.kegiatan.index')"
                    link-label="Lihat semua"
                />
                <x-dashboard.menu-compact
                    :menu="! empty($menuHariIni) ? $menuHariIni->menu : null"
                    :nutrition="! empty($menuHariIni) ? $menuHariIni->nutrition_info : null"
                    :href="route('admin.menu-makanan.index')"
                />
            </div>
        </x-slot:aside>
    </x-dashboard.attention>

    @if(!empty($adminChartsWide))
        <section aria-labelledby="admin-patterns-heading">
            <div class="dashboard-section-head">
                <h2 id="admin-patterns-heading" class="dashboard-section-title">Pola & distribusi</h2>
                <p class="dashboard-section-desc">Grafik untuk melihat tren, angka utama ada di atas</p>
            </div>
            <x-dashboard-charts :charts="$adminChartsWide" />
        </section>
    @endif
</div>
