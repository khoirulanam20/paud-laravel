<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-amber-900 leading-tight">Presensi Guru & Staf</h2>
                <p class="text-sm text-amber-800/60 mt-1">Kelola kehadiran pengajar setiap harinya.</p>
            </div>
            
            <form method="GET" action="{{ route('admin.presensi-guru.index') }}" class="flex items-center gap-3">
                <div class="relative">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" 
                        class="input-field py-2 pl-10 pr-4 text-sm font-bold border-amber-100 bg-white" 
                        onchange="this.form.submit()">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-amber-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center gap-3 animate-fade-in">
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.presensi-guru.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            
            <div class="card overflow-hidden border-none shadow-sm shadow-amber-900/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-amber-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Nama Pengajar</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-amber-900/40 w-48 text-center">Kehadiran</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Keterangan (Optional)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                            @forelse($pengajars as $pengajar)
                                @php $p = $presensis->get($pengajar->id); @endphp
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <x-foto-profil :path="$pengajar->photo" :name="$pengajar->name" size="md" rounded="full" class="border border-amber-200/80" />
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $pengajar->name }}</p>
                                                <p class="text-[10px] font-bold text-amber-600 uppercase tracking-tight">{{ $pengajar->nip ?: 'NIP Belum Diatur' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <label class="relative inline-flex items-center cursor-pointer group">
                                                <input type="radio" name="presensi[{{ $pengajar->id }}][hadir]" value="1" class="sr-only peer" @checked(!($p && $p->hadir == 0))>
                                                <div class="px-3 py-1 rounded-lg text-xs font-bold border border-gray-100 text-gray-400 peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-600 transition uppercase tracking-tighter">Hadir</div>
                                            </label>
                                            <label class="relative inline-flex items-center cursor-pointer group">
                                                <input type="radio" name="presensi[{{ $pengajar->id }}][hadir]" value="0" class="sr-only peer" @checked($p && $p->hadir == 0)>
                                                <div class="px-3 py-1 rounded-lg text-xs font-bold border border-gray-100 text-gray-400 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-700 transition uppercase tracking-tighter">Tidak</div>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="presensi[{{ $pengajar->id }}][status]" class="input-field py-1.5 text-xs border-amber-100 bg-white/50 focus:bg-white w-full max-w-[12rem]">
                                            <option value="">- Pilih Status -</option>
                                            <option value="Sakit" @selected(($p?->status ?? '') === 'Sakit')>Sakit</option>
                                            <option value="Izin" @selected(($p?->status ?? '') === 'Izin')>Izin</option>
                                            <option value="Alpa" @selected(($p?->status ?? '') === 'Alpa')>Alpa</option>
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <p class="text-sm text-amber-800/40 font-medium">Belum ada data pengajar di sekolah ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($pengajars->isNotEmpty())
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary px-8 py-3 rounded-2xl font-bold text-sm shadow-xl shadow-amber-900/20 hover:scale-[1.02] active:scale-95 transition-all">
                        Simpan Presensi Hari Ini
                    </button>
                </div>
            @endif
        </form>

        <div class="mt-12">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-amber-900 leading-tight">Rekap Absensi</h3>
                    <p class="text-xs text-amber-800/60 font-medium">Akumulasi kehadiran pengajar periode {{ Carbon\Carbon::create($rekapYear, $rekapMonth, 1)->translatedFormat('F Y') }}</p>
                </div>
                
                <form action="{{ route('admin.presensi-guru.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <select name="rekap_month" class="input-field py-1.5 text-xs border-amber-100 bg-white min-w-[8rem]" onchange="this.form.submit()">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ $rekapMonth == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create(2024, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="rekap_year" class="input-field py-1.5 text-xs border-amber-100 bg-white min-w-[5rem]" onchange="this.form.submit()">
                        @php $currentYear = date('Y'); @endphp
                        @for($y=$currentYear-2; $y<=$currentYear; $y++)
                            <option value="{{ $y }}" {{ $rekapYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            
            <div class="card overflow-hidden border-none shadow-sm shadow-amber-900/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-amber-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Nama Pengajar</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-green-700 text-center">Hadir</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-amber-600 text-center">Sakit</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-amber-600 text-center">Izin</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-red-600 text-center">Alpa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                            @foreach($pengajars as $pengajar)
                                @php $rekap = $rekapBulanan->get($pengajar->id) ?? ['hadir'=>0,'sakit'=>0,'izin'=>0,'alpa'=>0]; @endphp
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <x-foto-profil :path="$pengajar->photo" :name="$pengajar->name" size="sm" rounded="full" class="border border-amber-100" />
                                            <span class="font-bold text-gray-900 text-sm">{{ $pengajar->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-green-600 text-sm">{{ $rekap['hadir'] }}</td>
                                    <td class="px-6 py-4 text-center font-medium text-amber-700 text-sm">{{ $rekap['sakit'] }}</td>
                                    <td class="px-6 py-4 text-center font-medium text-amber-700 text-sm">{{ $rekap['izin'] }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-red-600 text-sm">{{ $rekap['alpa'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
