@php
    use App\Support\MonevSummaryPresenter;

    $sections = MonevSummaryPresenter::sections($summary);
    $scoreDist = MonevSummaryPresenter::scoreDistribution($summary);
    $perAspek = MonevSummaryPresenter::perAspek($summary);
    $feedbacks = MonevSummaryPresenter::feedbackSamples($summary);
    $totalEntri = MonevSummaryPresenter::totalEntri($summary);
    $maxScore = max(array_values($scoreDist) ?: [1]);
@endphp

@if($showBackLink ?? true)
    <div class="mb-4">
        <a href="{{ $backRoute }}" class="text-sm font-semibold inline-flex items-center gap-1" style="color:#1A6B6B;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke daftar Monev
        </a>
    </div>
@endif

<div x-data="{ tab: 'ringkasan', openSection: @js($sections[0]['key'] ?? 'full') }">
    {{-- Header --}}
    <div class="card overflow-hidden mb-6">
        <div class="px-6 py-6 border-b" style="background:#FAF6F0; border-color: rgba(0,0,0,0.06);">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold" style="color:#2C2C2C;">{{ $anak->name }}</h3>
                    <p class="text-sm mt-1" style="color:#6B6560;">
                        Kelas {{ $anak->kelas?->name ?? '—' }} · {{ $anak->age }} · Periode {{ $summary->periodeLabel() }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($summary->sumber === 'otomatis')
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#D0E8E8; color:#1A6B6B;">Otomatis</span>
                    @else
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#FDE9BC; color:#8A6D00;">Manual</span>
                    @endif
                    <span class="text-xs font-medium px-3 py-1.5 rounded-lg" style="background:#F5F5F5; color:#6B6560;">
                        {{ $summary->generated_at->translatedFormat('d M Y H:i') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px" style="background: rgba(0,0,0,0.06);">
            <div class="px-5 py-4" style="background:white;">
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color:#9E9790;">Total Entri</p>
                <p class="text-2xl font-bold mt-1" style="color:#1A6B6B;">{{ $totalEntri }}</p>
            </div>
            <div class="px-5 py-4" style="background:white;">
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color:#9E9790;">Aspek Dinilai</p>
                <p class="text-2xl font-bold mt-1" style="color:#2C2C2C;">{{ count($perAspek) }}</p>
            </div>
            <div class="px-5 py-4" style="background:white;">
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color:#9E9790;">Indikator</p>
                <p class="text-2xl font-bold mt-1" style="color:#2C2C2C;">{{ count($summary->data_snapshot['indikator_tercatat'] ?? []) }}</p>
            </div>
            <div class="px-5 py-4" style="background:white;">
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color:#9E9790;">Umpan Balik</p>
                <p class="text-2xl font-bold mt-1" style="color:#2C2C2C;">{{ count($feedbacks) }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-4 border-b" style="border-color: rgba(0,0,0,0.08);">
        @foreach(['ringkasan' => 'Ringkasan AI', 'statistik' => 'Statistik', 'umpan' => 'Umpan Balik'] as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'"
                class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-colors"
                :style="tab === '{{ $key }}' ? 'border-color:#1A6B6B; color:#1A6B6B; background:#E8F5F5;' : 'border-color:transparent; color:#6B6560;'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tab: Ringkasan --}}
    <div x-show="tab === 'ringkasan'" class="space-y-3">
        @foreach($sections as $section)
            <div class="card overflow-hidden">
                <button type="button" @click="openSection = openSection === '{{ $section['key'] }}' ? null : '{{ $section['key'] }}'"
                    class="w-full px-6 py-4 flex items-center justify-between text-left border-b transition-colors hover:bg-black/[0.02]"
                    style="border-color: rgba(0,0,0,0.06);">
                    <div class="flex items-center gap-3">
                        @include('monev._section-icon', ['icon' => $section['icon']])
                        <span class="font-semibold text-sm" style="color:#2C2C2C;">{{ $section['title'] }}</span>
                    </div>
                    <svg class="h-4 w-4 transition-transform" :class="openSection === '{{ $section['key'] }}' ? 'rotate-180' : ''" style="color:#9E9790;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openSection === '{{ $section['key'] }}'" x-transition class="px-6 py-5">
                    <p class="text-sm leading-relaxed whitespace-pre-line" style="color:#2C2C2C;">{{ $section['content'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tab: Statistik --}}
    <div x-show="tab === 'statistik'" x-cloak class="space-y-6">
        @if($scoreDist !== [])
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <h4 class="section-title">Distribusi Skor Capaian</h4>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @foreach($scoreDist as $label => $count)
                        <div>
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-medium" style="color:#2C2C2C;">{{ $label }}</span>
                                <span class="font-bold tabular-nums" style="color:#1A6B6B;">{{ $count }}</span>
                            </div>
                            <div class="h-2.5 rounded-full overflow-hidden" style="background:#F0F0F0;">
                                <div class="h-full rounded-full transition-all duration-700" style="background:#1A6B6B; width: {{ $maxScore > 0 ? round(($count / $maxScore) * 100) : 0 }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($perAspek !== [])
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <h4 class="section-title">Capaian per Aspek</h4>
                </div>
                <div class="grid md:grid-cols-2 gap-4 p-6">
                    @foreach($perAspek as $aspek => $data)
                        <div class="rounded-xl p-4 border" style="border-color: rgba(0,0,0,0.06); background:#FAF6F0;">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-bold text-sm" style="color:#2C2C2C;">{{ $aspek }}</h5>
                                <span class="text-xs font-bold px-2 py-1 rounded-lg" style="background:#D0E8E8; color:#1A6B6B;">{{ $data['jumlah'] }} entri</span>
                            </div>
                            @if(!empty($data['skor']))
                                <div class="space-y-2">
                                    @foreach($data['skor'] as $skorLabel => $cnt)
                                        <div class="flex justify-between text-xs">
                                            <span style="color:#6B6560;">{{ $skorLabel }}</span>
                                            <span class="font-semibold" style="color:#2C2C2C;">{{ $cnt }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($scoreDist === [] && $perAspek === [])
            <div class="card px-6 py-12 text-center text-sm" style="color:#9E9790;">Belum ada data statistik pada periode ini.</div>
        @endif
    </div>

    {{-- Tab: Umpan Balik --}}
    <div x-show="tab === 'umpan'" x-cloak>
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                <h4 class="section-title">Cuplikan Umpan Balik Guru</h4>
            </div>
            @if($feedbacks === [])
                <div class="px-6 py-12 text-center text-sm" style="color:#9E9790;">Belum ada umpan balik tercatat.</div>
            @else
                <div class="divide-y" style="border-color: rgba(0,0,0,0.06);">
                    @foreach($feedbacks as $fb)
                        <div class="px-6 py-4 flex gap-3">
                            <div class="shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold" style="background:#E8F5F5; color:#1A6B6B;">"</div>
                            <p class="text-sm leading-relaxed italic" style="color:#2C2C2C;">{{ $fb }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
