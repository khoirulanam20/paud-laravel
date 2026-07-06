<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Monev Saya</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Riwayat evaluasi kinerja</h3>
                <p class="section-subtitle">Hasil penilaian dari admin sekolah yang sudah difinalisasi.</p>
            </div>
            <div class="divide-y" style="border-color:rgba(0,0,0,0.06);">
                @forelse($evaluasis as $e)
                <a href="{{ route('pengajar.monev-guru.show', $e) }}" class="block px-6 py-4 hover:bg-[#FAF6F0] transition">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="font-semibold" style="color:#2C2C2C;">{{ $e->judul ?: 'Evaluasi Guru' }}</div>
                            <div class="text-sm mt-1" style="color:#9E9790;">{{ $e->periodeLabel() }} · Evaluator: {{ $e->evaluator->name ?? '—' }}</div>
                        </div>
                        @if($e->skor_keseluruhan !== null)
                            <div class="text-2xl font-bold" style="color:#1A6B6B;">{{ $e->skor_keseluruhan }}</div>
                        @endif
                    </div>
                </a>
                @empty
                <div class="px-6 py-16 text-center text-sm" style="color:#9E9790;">Belum ada evaluasi yang dipublikasikan.</div>
                @endforelse
            </div>
            @if($evaluasis->hasPages())
            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$evaluasis" />
                {{ $evaluasis->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
