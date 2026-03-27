<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laporan Pencapaian Anak</h2>
        </div>
    </x-slot>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">🌟 Rapor Evaluasi Perkembangan</h3>
                <p class="section-subtitle">Nilai dan catatan perkembangan yang dibuat oleh pengajar</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Anak</th><th>Indikator</th><th>Nilai Capaian</th><th>Catatan Guru</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse($pencapaians as $p)
                        <tr>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $p->anak->name ?? '-' }}</span></td>
                            <td>{{ $p->matrikulasi->indicator ?? '-' }}</td>
                            <td>
                                @php $score = $p->score; @endphp
                                @if($score === 'BSB') <span class="badge badge-green">{{ $score }} - Sangat Baik</span>
                                @elseif($score === 'BSH') <span class="badge badge-teal">{{ $score }} - Sesuai Harapan</span>
                                @elseif($score === 'MB') <span class="badge badge-amber">{{ $score }} - Mulai Berkembang</span>
                                @else <span class="badge badge-rose">{{ $score }} - Belum Berkembang</span> @endif
                            </td>
                            <td class="max-w-xs italic" style="color:#9E9790;">"{{ $p->feedback }}"</td>
                            <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-12 text-center" style="color:#9E9790;">Belum ada laporan evaluasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pencapaians->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $pencapaians->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
