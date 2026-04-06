<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Presensi Harian Kelas</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-danger mb-5">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-6 border-b" style="background:#FAF6F0; border-color: rgba(0,0,0,0.06);">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold" style="color:#2C2C2C;">Filter Kehadiran</h3>
                        <p class="text-sm font-medium" style="color:#9E9790;">Pilih kelas dan tanggal untuk mengelola checklist siswa</p>
                    </div>
                    
                    <form method="get" action="{{ route('adminkelas.presensi.index') }}" class="grid grid-cols-2 gap-4 w-full md:w-auto">
                        <div class="col-span-2 sm:col-span-1 lg:w-48">
                            <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Pilih Kelas</label>
                            <select name="filter_kelas_id" class="input-field w-full text-xs font-bold h-11 border-black/10 transition focus:border-teal-500" onchange="this.form.submit()" style="background:white;">
                                <option value="">Semua Siswa Terdaftar</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" @selected($filterKelasId == $k->id)>{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1 lg:w-44">
                            <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field w-full text-xs font-bold h-11 border-black/10 transition focus:border-teal-500 @error('tanggal') border-red-500 @enderror" required onchange="this.form.submit()" style="background:white;">
                            @error('tanggal')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </form>
                </div>
            </div>
            <div class="px-6 py-3 text-sm flex flex-wrap gap-4" style="background: #FAF6F0; color: #6B6560;">
                <span><strong style="color:#2C2C2C;">{{ $tanggal }}</strong></span>
                <span>Total siswa: <strong style="color:#2C2C2C;">{{ $anaks->count() }}</strong></span>
                <span>Hadir: <strong style="color:#1A6B6B;">{{ $hadirCount }}</strong></span>
                <span>Tidak hadir: <strong style="color:#C0392B;">{{ $anaks->count() - $hadirCount }}</strong></span>
                <span>Rekap: <strong style="color:#6B6560;">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}</strong></span>
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
                <div class="px-6 py-16 text-center text-sm" style="color:#9E9790;">Belum ada data siswa di kelas ini.</div>
            @else
                <form method="post" action="{{ route('adminkelas.presensi.store') }}">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="hidden" name="filter_kelas_id" value="{{ $filterKelasId }}">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="w-14 text-center">
                                        <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" title="Pilih semua"
                                            onclick="const m=this; this.closest('form').querySelectorAll('tbody input[type=checkbox][name=\'hadir[]\']').forEach(function(c){ c.checked = m.checked; });">
                                    </th>
                                    <th>Nama siswa</th>
                                    <th>Rekap Bulan Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anaks as $anak)
                                    @php
                                        $row = $presensiByAnak->get($anak->id);
                                        $checked = $row ? $row->hadir : false;
                                    @endphp
                                    <tr class="hover:bg-teal-50/30 transition-colors">
                                        <td class="text-center py-4">
                                            <input type="checkbox" name="hadir[]" value="{{ $anak->id }}" class="h-6 w-6 rounded-lg border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer"
                                                @checked($checked)>
                                        </td>
                                        <td class="py-4">
                                            <div class="flex items-center gap-3">
                                                <x-foto-profil :path="$anak->photo" :name="$anak->name" size="sm" />
                                                <div class="min-w-0">
                                                    <span class="font-bold block text-sm sm:text-base" style="color:#2C2C2C;">{{ $anak->name }}</span>
                                                    @if($anak->dob)<span class="text-[10px] font-bold text-teal-600 uppercase tracking-tight">Umur: {{ $anak->age }}</span>@endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <div class="flex flex-col">
                                                <span class="font-black text-base sm:text-lg tabular-nums leading-none" style="color:#1A6B6B;">{{ (int)($hadirBulanan[$anak->id] ?? 0) }}</span>
                                                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-widest mt-1">Hari Hadir</span>
                                            </div>
                                        </td>
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
