<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">Rekap Kehadiran Siswa</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau kehadiran anak Anda secara berkala.</p>
            </div>
            
            <form method="GET" action="{{ route('orangtua.presensi.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-full sm:w-auto">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 ml-1">Rentang</label>
                    <select name="periode" class="input-field py-2 text-sm min-w-[140px]" onchange="this.form.submit()">
                        <option value="bulan" @selected(($filter['periode'] ?? 'bulan') === 'bulan')>Per Bulan</option>
                        <option value="minggu" @selected(($filter['periode'] ?? '') === 'minggu')>Per Minggu</option>
                    </select>
                </div>

                @if(($filter['periode'] ?? 'bulan') === 'bulan')
                    <div class="flex gap-2">
                        <select name="month" class="input-field py-2 text-sm" onchange="this.form.submit()">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" @selected((int) ($filter['bulan'] ?? now()->month) === $m)>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="input-field py-2 text-sm" onchange="this.form.submit()">
                            @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                <option value="{{ $y }}" @selected((int) ($filter['tahun'] ?? now()->year) === $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="week" name="week" value="{{ $filter['minggu'] ?? '' }}" class="input-field py-2 text-sm" onchange="this.form.submit()">
                @endif
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Summary Sidebar --}}
            <div class="lg:col-span-1 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 ml-1">Ringkasan {{ $filter['label'] }}</h3>
                @foreach($anaks as $anak)
                    @php
                        $stats = $presensis->where('anak_id', $anak->id);
                        $hadir = $stats->where('hadir', true)->count();
                        $tidakHadir = $stats->where('hadir', false)->count();
                    @endphp
                    <div class="card p-5 bg-white shadow-sm border-l-4 border-l-[#1A6B6B]">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-10 w-10 rounded-xl bg-[#1A6B6B]/10 flex items-center justify-center font-bold text-[#1A6B6B] border border-[#1A6B6B]/20">
                                {{ substr($anak->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 leading-tight">{{ $anak->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $anak->kelas->name ?? 'Tanpa Kelas' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <p class="text-[10px] font-bold uppercase tracking-tighter text-gray-400 mb-1">Hadir</p>
                                <p class="text-xl font-bold text-[#1A6B6B]">{{ $hadir }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <p class="text-[10px] font-bold uppercase tracking-tighter text-gray-400 mb-1">Izin/Sakit/Alpa</p>
                                <p class="text-xl font-bold text-amber-600">{{ $tidakHadir }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Detail List --}}
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b flex items-center justify-between">
                        <h3 class="section-title mb-0">Riwayat Harian</h3>
                        <span class="text-xs font-semibold text-gray-400">{{ $presensis->count() }} Record ditemukan</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400">Tanggal</th>
                                    <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400">Anak</th>
                                    <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($presensis as $p)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-gray-700">{{ $p->anak->name }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($p->hadir)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-100">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                    Hadir
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $p->status ?: 'Tanpa Keterangan' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $p->keterangan ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-gray-50 mb-3 text-gray-300">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-400">Tidak ada riwayat kehadiran dalam periode ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
