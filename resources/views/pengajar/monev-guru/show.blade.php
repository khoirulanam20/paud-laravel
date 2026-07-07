<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">{{ $evaluasi->judul ?: 'Evaluasi Guru' }}</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="mb-4"><a href="{{ route('pengajar.monev-guru.index') }}" class="btn-secondary text-sm">← Kembali</a></div>

        <div class="card p-6 mb-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm" style="color:#9E9790;">Periode: {{ $evaluasi->periodeLabel() }}</p>
                    <p class="text-sm" style="color:#9E9790;">Evaluator: {{ $evaluasi->evaluator->name ?? '—' }}</p>
                    @if($evaluasi->finalized_at)
                        <p class="text-sm" style="color:#9E9790;">Dipublikasikan: {{ $evaluasi->finalized_at->format('d M Y') }}</p>
                    @endif
                </div>
                @if($evaluasi->skor_keseluruhan !== null)
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color:#1A6B6B;">{{ $evaluasi->skor_keseluruhan }}</div>
                        <div class="text-xs" style="color:#9E9790;">Skor keseluruhan</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);"><h3 class="section-title">Penilaian per kriteria</h3></div>
            <div class="divide-y" style="border-color:rgba(0,0,0,0.06);">
                @foreach($evaluasi->items->filter(fn ($i) => $i->kriteria?->is_active)->sortBy(fn($i) => $i->kriteria?->urutan ?? 999) as $item)
                <div class="px-6 py-4">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                        <div class="font-semibold">{{ $item->kriteria->nama ?? '—' }}</div>
                        <div class="text-lg font-bold" style="color:#1A6B6B;">{{ $item->skor ?? '—' }}</div>
                    </div>
                    @if($item->kriteria?->deskripsi)<p class="text-xs mb-2" style="color:#9E9790;">{{ $item->kriteria->deskripsi }}</p>@endif
                    @if($item->catatan)<p class="text-sm" style="color:#555;">{{ $item->catatan }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>

        @if($evaluasi->catatan_umum || $evaluasi->rekomendasi)
        <div class="card p-6 space-y-4">
            @if($evaluasi->catatan_umum)
                <div><h4 class="font-semibold mb-1">Catatan umum</h4><p class="text-sm whitespace-pre-line" style="color:#555;">{{ $evaluasi->catatan_umum }}</p></div>
            @endif
            @if($evaluasi->rekomendasi)
                <div><h4 class="font-semibold mb-1">Rekomendasi</h4><p class="text-sm whitespace-pre-line" style="color:#555;">{{ $evaluasi->rekomendasi }}</p></div>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>
