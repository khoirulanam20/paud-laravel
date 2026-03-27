<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Presensi Harian</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4" style="border-color: rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Filter tanggal</h3>
                    <p class="section-subtitle">Pilih hari untuk melihat dan mengisi checklist kehadiran siswa</p>
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
            <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Checklist kehadiran</h3>
                    <p class="section-subtitle">Centang siswa yang hadir pada tanggal di atas, lalu simpan</p>
                </div>
            </div>

            @if($anaks->isEmpty())
                <div class="px-6 py-16 text-center text-sm" style="color:#9E9790;">Belum ada data siswa. Tambahkan siswa di menu Siswa & Ortu.</div>
            @else
                <form method="post" action="{{ route('admin.presensi.store') }}">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="w-14 text-center">Hadir</th>
                                    <th>Nama siswa</th>
                                    <th>Nama orang tua</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anaks as $anak)
                                    @php
                                        $row = $presensiByAnak->get($anak->id);
                                        $checked = $row ? $row->hadir : false;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="hadir[]" value="{{ $anak->id }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                @checked($checked)>
                                        </td>
                                        <td><span class="font-semibold" style="color:#2C2C2C;">{{ $anak->name }}</span></td>
                                        <td style="color:#6B6560;">{{ $anak->user->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t flex justify-end" style="border-color: rgba(0,0,0,0.06);">
                        <button type="submit" class="btn-primary">Simpan presensi</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
