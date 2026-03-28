<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laporan Pencapaian Anak</h2>
        </div>
    </x-slot>
    @php
        $scoreColors = ['BB'=>'#FAD7D2','MB'=>'#FDE9BC','BSH'=>'#D0E8E8','BSB'=>'#C5E8C5'];
    @endphp
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Rapor per kegiatan &amp; aspek</h3>
                <p class="section-subtitle">Setiap kartu = satu kegiatan; di dalamnya nilai per indikator matrikulasi.</p>
            </div>
            <div class="divide-y" style="border-color:rgba(0,0,0,0.06);">
                @forelse($groupedPencapaian as $bundleKey => $rows)
                    @php $first = $rows->first(); @endphp
                    <div class="px-6 py-5">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                            <div class="shrink-0">
                                @if($first->photo)
                                    <img src="{{ Storage::url($first->photo) }}" class="h-16 w-16 object-cover rounded-xl shadow-sm cursor-pointer" onclick="window.open(this.src)">
                                @else
                                    <div class="h-16 w-16 bg-gray-100 rounded-xl flex items-center justify-center text-gray-300">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-bold" style="color:#2C2C2C;">{{ $first->anak->name ?? '-' }}</span>
                                    <span class="text-xs" style="color:#9E9790;">{{ \Carbon\Carbon::parse($first->created_at)->translatedFormat('d M Y') }}</span>
                                </div>
                                @if($first->kegiatan)
                                    <div class="font-semibold text-sm mb-0.5" style="color:#1A6B6B;">{{ $first->kegiatan->title }}</div>
                                    <div class="text-xs mb-3" style="color:#9E9790;">Kegiatan {{ \Carbon\Carbon::parse($first->kegiatan->date)->format('d M Y') }} · {{ $first->pengajar->name ?? 'Guru' }}</div>
                                @endif
                                <div class="rounded-xl border overflow-hidden" style="border-color:rgba(0,0,0,0.08);">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr style="background:#F5F5F3;">
                                                <th class="text-left px-3 py-2 text-xs font-semibold" style="color:#5A5A5A;">Aspek / indikator</th>
                                                <th class="text-left px-3 py-2 text-xs font-semibold w-24" style="color:#5A5A5A;">Nilai</th>
                                                <th class="text-left px-3 py-2 text-xs font-semibold" style="color:#5A5A5A;">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows->sortBy(fn ($p) => ($p->matrikulasi->aspek ?? '').($p->matrikulasi->indicator ?? '')) as $p)
                                                <tr class="border-t" style="border-color:rgba(0,0,0,0.05);">
                                                    <td class="px-3 py-2 align-top">
                                                        @if($p->matrikulasi)
                                                            <span class="font-semibold text-xs block" style="color:#1A6B6B;">{{ $p->matrikulasi->aspek ?: 'Aspek' }}</span>
                                                            <span class="text-xs mt-0.5 block" style="color:#5A5A5A;">{{ $p->matrikulasi->indicator }}</span>
                                                        @else
                                                            <span class="text-xs italic" style="color:#9E9790;">Catatan lama</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 align-top">
                                                        <span class="text-xs font-bold px-2 py-1 rounded inline-block" style="background:{{ $scoreColors[$p->score] ?? '#eee' }};">{{ $p->score }}</span>
                                                    </td>
                                                    <td class="px-3 py-2 text-xs align-top" style="color:#6B6560;">{{ $p->feedback ?: '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-16 text-center text-sm" style="color:#9E9790;">Belum ada laporan evaluasi.</div>
                @endforelse
            </div>
            @if($groupedPencapaian->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $groupedPencapaian->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
