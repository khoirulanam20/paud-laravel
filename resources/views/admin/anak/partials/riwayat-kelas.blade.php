<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b bg-white" style="border-color: rgba(0,0,0,0.06);">
        <h3 class="font-bold text-lg text-[#2C2C2C]">Riwayat Kelas</h3>
    </div>
    <div class="p-6">
        @if($anak->riwayatKelas->isEmpty())
            <p class="text-sm text-gray-400 italic">Belum ada riwayat kelas.</p>
        @else
            <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                @foreach($anak->riwayatKelas->sortByDesc('mulai') as $r)
                    <div class="relative flex items-start">
                        <div class="absolute left-0 h-10 w-10 flex items-center justify-center rounded-full bg-white border-2 border-[#1A6B6B] shadow-sm shrink-0 z-10">
                            <svg class="h-4 w-4 text-[#1A6B6B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <div class="ml-14 flex-1">
                            <div class="text-sm font-bold text-[#1A6B6B] bg-[#D0E8E8] px-3 py-1 rounded-full w-fit mb-1">
                                {{ $r->kelas_nama }}
                                @if($r->selesai === null)<span class="ml-1 text-[10px]">· saat ini</span>@endif
                            </div>
                            <p class="text-xs text-gray-500">TA {{ $r->tahun_ajaran }} · Semester {{ $r->semester }} · {{ \Carbon\Carbon::parse($r->mulai)->format('d M Y') }} — {{ $r->selesai ? \Carbon\Carbon::parse($r->selesai)->format('d M Y') : 'sekarang' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Alasan: {{ ucfirst($r->alasan) }}@if($r->alasan_selesai) → {{ ucfirst($r->alasan_selesai) }}@endif</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
