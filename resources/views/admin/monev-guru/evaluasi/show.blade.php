<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Detail Evaluasi — {{ $evaluasi->pengajar->name ?? 'Guru' }}</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('admin.monev-guru.index') }}" class="btn-secondary text-sm">← Kembali</a>
            @if($evaluasi->isFinal())
                <a href="{{ route('admin.monev-guru.pdf', $evaluasi) }}" class="btn-secondary text-sm">Download PDF</a>
            @else
                <a href="{{ route('admin.monev-guru.edit', $evaluasi) }}" class="btn-primary text-sm">Edit draft</a>
                <form action="{{ route('admin.monev-guru.finalize', $evaluasi) }}" method="POST" class="inline" onsubmit="return confirm('Finalisasi evaluasi ini?')">
                    @csrf
                    <button type="submit" class="btn-primary text-sm">Finalisasi</button>
                </form>
                <form action="{{ route('admin.monev-guru.destroy', $evaluasi) }}" method="POST" class="inline" onsubmit="return confirm('Hapus draft evaluasi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger text-sm">Hapus draft</button>
                </form>
            @endif
        </div>

        <div class="card p-6 mb-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold" style="color:#2C2C2C;">{{ $evaluasi->judul ?: 'Evaluasi Guru' }}</h3>
                    <p class="text-sm mt-1" style="color:#9E9790;">Periode: {{ $evaluasi->periodeLabel() }}</p>
                    <p class="text-sm" style="color:#9E9790;">Evaluator: {{ $evaluasi->evaluator->name ?? '—' }}</p>
                </div>
                <div class="text-right">
                    @if($evaluasi->isFinal())<span class="badge badge-teal">Final</span>@else<span class="badge" style="background:#FDE9BC;color:#8B6914;">Draft</span>@endif
                    @if($evaluasi->skor_keseluruhan !== null)
                        <div class="mt-2 text-3xl font-bold" style="color:#1A6B6B;">{{ $evaluasi->skor_keseluruhan }}</div>
                        <div class="text-xs" style="color:#9E9790;">Skor keseluruhan</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);"><h3 class="section-title">Penilaian per kriteria</h3></div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Kriteria</th><th>Bobot</th><th>Skor</th><th>Catatan</th></tr></thead>
                    <tbody>
                        @foreach($evaluasi->items->filter(fn ($i) => $i->kriteria?->is_active)->sortBy(fn($i) => $i->kriteria?->urutan ?? 999) as $item)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $item->kriteria->nama ?? '—' }}</div>
                                @if($item->kriteria?->deskripsi)<div class="text-xs" style="color:#9E9790;">{{ $item->kriteria->deskripsi }}</div>@endif
                            </td>
                            <td>{{ $item->kriteria->bobot ?? '—' }}%</td>
                            <td class="font-bold" style="color:#1A6B6B;">{{ $item->skor ?? '—' }}</td>
                            <td class="text-sm">{{ $item->catatan ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
