<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Rekap Presensi</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        {{-- TABS --}}
        <div class="flex gap-1 mb-4 border-b" style="border-color: rgba(0,0,0,0.08);">
            <a href="{{ route('admin.presensi.index') }}"
               class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-colors"
               style="border-color: transparent; color: #6B6560;">
                Kehadiran Harian
            </a>
            <a href="{{ route('admin.presensi.rekap') }}"
               class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-colors"
               style="border-color: #1A6B6B; color: #1A6B6B; background: #E8F5F5;">
                Rekap Periode
            </a>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4" style="border-color: rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Rekap Kehadiran per Periode</h3>
                    <p class="section-subtitle">Jumlah hari hadir setiap siswa dalam periode yang dipilih.</p>
                </div>
                <form method="get" action="{{ route('admin.presensi.rekap') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="input-label">Kelas</label>
                        <select name="kelas_id" class="input-field min-w-[10rem]" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Periode</label>
                        <select name="periode" class="input-field min-w-[9rem]" onchange="this.form.submit()">
                            <option value="bulan" @selected(($presensiFilter['periode'] ?? 'bulan') === 'bulan')>Bulanan</option>
                            <option value="minggu" @selected(($presensiFilter['periode'] ?? '') === 'minggu')>Mingguan</option>
                        </select>
                    </div>
                    @if(($presensiFilter['periode'] ?? 'bulan') === 'bulan')
                        <div>
                            <label class="input-label">Bulan</label>
                            <select name="month" class="input-field" onchange="this.form.submit()">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected((int)($presensiFilter['bulan'] ?? now()->month) === $m)>{{ \Carbon\Carbon::createFromDate((int)($presensiFilter['tahun'] ?? now()->year), $m, 1)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Tahun</label>
                            <select name="year" class="input-field" onchange="this.form.submit()">
                                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                    <option value="{{ $y }}" @selected((int)($presensiFilter['tahun'] ?? now()->year) === $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div>
                            <label class="input-label">Minggu (ISO)</label>
                            <input type="week" name="week" value="{{ $presensiFilter['minggu'] ?? '' }}" class="input-field min-w-[11rem]" onchange="this.form.submit()">
                        </div>
                    @endif
                </form>
            </div>
            <div class="px-6 py-2 text-sm flex flex-wrap gap-4" style="background: #FAF6F0; color: #6B6560;">
                <span>Periode: <strong style="color:#2C2C2C;">{{ $presensiFilter['label'] ?? '' }}</strong></span>
                <span>Total siswa: <strong style="color:#2C2C2C;">{{ $anaks->count() }}</strong></span>
                <span>Total hadir (akumulasi): <strong style="color:#1A6B6B;">{{ $hadirPeriode->sum() }} hari</strong></span>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anaks as $anak)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <x-foto-profil :path="$anak->photo" :name="$anak->name" size="sm" />
                                        <span class="font-semibold" style="color:#2C2C2C;">{{ $anak->name }}</span>
                                        @if($anak->dob)<span class="text-[10px] font-bold text-[#1A6B6B]">({{ $anak->age }})</span>@endif
                                    </div>
                                </td>
                                <td>
                                    @if($anak->kelas)<span class="badge badge-teal">{{ $anak->kelas->name }}</span>
                                    @else<span class="text-xs italic" style="color:#9E9790;">—</span>@endif
                                </td>
                                <td>
                                    <span class="font-bold text-sm tabular-nums" style="color:#1A6B6B;">{{ (int)($hadirPeriode[$anak->id] ?? 0) }}</span>
                                    <span class="text-xs" style="color:#9E9790;"> hari</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-10 text-center text-sm" style="color:#9E9790;">Belum ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
