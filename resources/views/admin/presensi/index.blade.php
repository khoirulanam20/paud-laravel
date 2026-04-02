<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Rekap presensi (lihat)</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4" style="border-color: rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Filter tanggal</h3>
                    <p class="section-subtitle">Hanya tampilan rekap; pengisian presensi dilakukan oleh pengajar (atau wali kelas per kelas).</p>
                </div>
                <form method="get" action="{{ route('admin.presensi.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="input-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field" required>
                    </div>
                    <button type="submit" class="btn-primary">Tampilkan</button>
                </form>
            </div>
            <div class="px-6 py-3 text-sm flex flex-wrap gap-4" style="background: #FAF6F0; color: #6B6560;">
                <span><strong style="color:#2C2C2C;">{{ $tanggal }}</strong></span>
                <span>Total siswa: <strong style="color:#2C2C2C;">{{ $anaks->count() }}</strong></span>
                <span>Hadir: <strong style="color:#1A6B6B;">{{ $hadirCount }}</strong></span>
                <span>Tidak hadir: <strong style="color:#C0392B;">{{ $anaks->count() - $hadirCount }}</strong></span>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                <h3 class="section-title">Daftar kehadiran</h3>
                <p class="section-subtitle">Status sesuai data yang diinput pengajar atau wali kelas.</p>
            </div>

            @if($anaks->isEmpty())
                <div class="px-6 py-16 text-center text-sm" style="color:#9E9790;">Belum ada data siswa. Tambahkan siswa di menu Siswa &amp; Ortu.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="w-36">Status</th>
                                <th>Nama siswa</th>
                                <th>Nama orang tua</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anaks as $anak)
                                @php
                                    $row = $presensiByAnak->get($anak->id);
                                    $hadir = $row ? (bool) $row->hadir : false;
                                @endphp
                                <tr>
                                    <td>
                                        @if($hadir)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background:#D0E8E8;color:#1A6B6B;">Hadir</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background:#FAD7D2;color:#C0392B;">Tidak hadir</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <x-foto-profil :path="$anak->photo" :name="$anak->name" size="sm" />
                                            <span class="font-semibold" style="color:#2C2C2C;">{{ $anak->name }}</span>
                                            @if($anak->dob)<span class="text-[10px] font-bold text-[#1A6B6B]">({{ $anak->age }})</span>@endif
                                        </div>
                                    </td>
                                    <td style="color:#6B6560;">{{ $anak->user->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
