<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Dashboard</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

        <!-- ═══ LEMBAGA DASHBOARD ═══ -->
        @hasrole('Lembaga')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9E9790;">Total Cabang</p>
                    <p class="text-3xl font-bold" style="color: #2C2C2C;">{{ $totalSekolah ?? 0 }}</p>
                    <a href="{{ route('lembaga.sekolah.index') }}" class="text-xs font-medium mt-1 inline-block" style="color: #1A6B6B;">Kelola &rarr;</a>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9E9790;">Admin Sekolah</p>
                    <p class="text-3xl font-bold" style="color: #2C2C2C;">{{ $totalAdmin ?? 0 }}</p>
                    <a href="{{ route('lembaga.admin-sekolah.index') }}" class="text-xs font-medium mt-1 inline-block" style="color: #1A6B6B;">Kelola &rarr;</a>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9E9790;">Total Masukan</p>
                    <p class="text-3xl font-bold" style="color: #2C2C2C;">{{ $totalKritikSaran ?? 0 }}</p>
                    <a href="{{ route('lembaga.kritik-saran.index') }}" class="text-xs font-medium mt-1 inline-block" style="color: #1A6B6B;">Lihat &rarr;</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="px-6 py-4 border-b flex justify-between items-center" style="border-color: rgba(0,0,0,0.06);">
                <h3 class="section-title">Masukan Terbaru</h3>
                <a href="{{ route('lembaga.kritik-saran.index') }}" class="text-sm font-medium" style="color: #1A6B6B;">Lihat Semua</a>
            </div>
            <div class="divide-y" style="divide-color: rgba(0,0,0,0.05);">
                @forelse ($recentFeedback ?? [] as $f)
                    <div class="px-6 py-4 flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm leading-relaxed line-clamp-2" style="color: #6B6560;">"{{ $f->message }}"</p>
                            <p class="text-xs mt-1" style="color: #9E9790;">{{ \Carbon\Carbon::parse($f->created_at)->format('d M Y') }}</p>
                        </div>
                        <span class="badge badge-amber shrink-0">{{ $f->status ?? 'Terkirim' }}</span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm" style="color: #9E9790;">Belum ada masukan terbaru.</div>
                @endforelse
            </div>
        </div>
        @endhasrole

        <!-- ═══ ADMIN SEKOLAH DASHBOARD ═══ -->
        @hasrole('Admin Sekolah')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['label' => 'Total Siswa', 'value' => $totalAnak ?? 0, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'href' => route('admin.anak.index')],
                ['label' => 'Total Pengajar', 'value' => $totalPengajar ?? 0, 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'href' => route('admin.pengajar.index')],
                ['label' => 'Total Sarana', 'value' => $totalSarana ?? 0, 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'href' => route('admin.sarana.index')],
                ['label' => 'Saldo Kas', 'value' => 'Rp ' . number_format($saldoKas ?? 0, 0, ',', '.'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'href' => route('admin.cashflow.index')],
            ] as $stat)
            <div class="stat-card">
                <div class="stat-icon shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1 truncate" style="color: #9E9790;">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold truncate" style="color: #2C2C2C;">{{ $stat['value'] }}</p>
                    <a href="{{ $stat['href'] }}" class="text-xs font-medium" style="color: #1A6B6B;">Detail &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="card">
                <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <h3 class="section-title">Kegiatan Hari Ini</h3>
                </div>
                <div class="px-6 py-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs" style="color: #9E9790;">Rekap jurnal hari ini</p>
                        <p class="text-4xl font-bold mt-1" style="color: #1A6B6B;">{{ $kegiatanHariIni ?? 0 }} <span class="text-sm font-normal" style="color: #9E9790;">entri</span></p>
                    </div>
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn-secondary text-sm">Lihat Log</a>
                </div>
            </div>
            <div class="card">
                <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <h3 class="section-title">Menu Makan Hari Ini</h3>
                </div>
                <div class="px-6 py-5">
                    @if(!empty($menuHariIni))
                        <p class="font-bold text-base mb-1 whitespace-pre-line" style="color: #2C2C2C;">{{ $menuHariIni->menu }}</p>
                        <p class="text-sm line-clamp-2" style="color: #9E9790;">{{ $menuHariIni->nutrition_info }}</p>
                    @else
                        <p class="text-sm" style="color: #9E9790;">Belum ada menu yang diinput.</p>
                    @endif
                    <a href="{{ route('admin.menu-makanan.index') }}" class="text-sm font-medium mt-3 inline-block" style="color: #1A6B6B;">Kelola Jadwal &rarr;</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- ═══ PENGAJAR DASHBOARD ═══ -->
        @hasrole('Pengajar')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="stat-card">
                <div class="stat-icon"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9E9790;">Siswa di Sekolah</p><p class="text-3xl font-bold" style="color: #2C2C2C;">{{ $totalAnakSekolah ?? 0 }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9E9790;">Total Jurnal Saya</p><p class="text-3xl font-bold" style="color: #2C2C2C;">{{ $totalKegiatanSaya ?? 0 }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9E9790;">Evaluasi Pencapaian</p><p class="text-3xl font-bold" style="color: #2C2C2C;">{{ $totalEvaluasiSaya ?? 0 }}</p></div>
            </div>
        </div>
        <div class="card p-10 text-center">
            <div class="h-16 w-16 rounded-2xl mx-auto mb-5 flex items-center justify-center" style="background: #1A6B6B; box-shadow: 4px 4px 14px rgba(26,107,107,0.35);">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <h2 class="text-2xl font-bold mb-2" style="color: #2C2C2C;">Selamat Datang, Guru!</h2>
            <p class="text-sm mb-6 max-w-md mx-auto" style="color: #9E9790;">Gunakan menu di atas untuk membuat jurnal harian, mencatat matrikulasi, atau melaporkan evaluasi perkembangan anak.</p>
            <div class="flex gap-3 justify-center flex-wrap">
                <a href="{{ route('pengajar.kegiatan.index') }}" class="btn-primary">Buat Jurnal Kegiatan</a>
                <a href="{{ route('pengajar.pencapaian.index') }}" class="btn-secondary">Nilai Pencapaian</a>
            </div>
        </div>
        @endhasrole

        <!-- ═══ ORANG TUA DASHBOARD ═══ -->
        @hasrole('Orang Tua')
        <div class="flex items-baseline gap-3 mb-2">
            <h1 class="text-2xl font-bold" style="color: #2C2C2C;">Halo, Bunda/Yanda! 👋</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-5">
                <!-- Child Profile Card -->
                <div class="rounded-2xl p-6 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #1A6B6B 0%, #2D8585 100%); box-shadow: 4px 6px 20px rgba(26,107,107,0.4);">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <svg class="h-28 w-28" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-widest mb-4 opacity-80">Profil Anak Terdaftar</p>
                    @forelse($anaks ?? [] as $anak)
                        <div class="flex items-center gap-3 mb-3 last:mb-0">
                            <div class="h-11 w-11 rounded-xl bg-white bg-opacity-20 flex items-center justify-center font-bold text-lg">{{ substr($anak->name, 0, 1) }}</div>
                            <div>
                                <p class="font-bold text-base leading-tight">{{ $anak->name }}</p>
                                <p class="text-xs opacity-70">{{ \Carbon\Carbon::parse($anak->dob)->format('d M Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm opacity-80">Belum ada anak tertaut.</p>
                    @endforelse
                </div>

                <!-- Menu Hari Ini -->
                <div class="card">
                    <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color: rgba(0,0,0,0.06);">
                        <h3 class="font-bold text-sm" style="color: #2C2C2C;">🍱 Menu Hari Ini</h3>
                        <a href="{{ route('orangtua.menu-makanan.index') }}" class="text-xs font-medium" style="color: #1A6B6B;">Lihat Semua</a>
                    </div>
                    <div class="px-5 py-4">
                        @if(!empty($menuHariIni))
                            <p class="font-bold text-base mb-1 whitespace-pre-line" style="color: #2C2C2C;">{{ $menuHariIni->menu }}</p>
                            <p class="text-xs" style="color: #9E9790;">{{ $menuHariIni->nutrition_info }}</p>
                        @else
                            <p class="text-sm" style="color: #9E9790;">Belum ada info menu hari ini.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-5">
                <!-- Jurnal Kegiatan -->
                <div class="card">
                    <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: rgba(0,0,0,0.06);">
                        <h3 class="section-title">📸 Kegiatan Terbaru</h3>
                        <a href="{{ route('orangtua.kegiatan.index') }}" class="text-sm font-medium" style="color: #1A6B6B;">Lihat Jurnal &rarr;</a>
                    </div>
                    <div class="divide-y" style="divide-color: rgba(0,0,0,0.05);">
                        @forelse($kegiatanTerbaru ?? [] as $keg)
                            <div class="px-6 py-4 flex gap-4">
                                @if($keg->photo)
                                    <div class="hidden sm:block w-16 h-16 rounded-xl overflow-hidden shrink-0">
                                        <img src="{{ Storage::url($keg->photo) }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold mb-1" style="color: #1A6B6B;">{{ \Carbon\Carbon::parse($keg->date)->format('d M Y') }}</p>
                                    <h4 class="font-bold text-sm truncate" style="color: #2C2C2C;">{{ $keg->title }}</h4>
                                    <p class="text-sm line-clamp-2 mt-0.5" style="color: #9E9790;">{{ $keg->description }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-sm" style="color: #9E9790;">Belum ada jurnal kegiatan.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pencapaian Terbaru -->
                <div class="card">
                    <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: rgba(0,0,0,0.06);">
                        <h3 class="section-title">🌟 Pencapaian Terbaru</h3>
                        <a href="{{ route('orangtua.pencapaian.index') }}" class="text-sm font-medium" style="color: #1A6B6B;">Lihat Laporan &rarr;</a>
                    </div>
                    <div class="divide-y" style="divide-color: rgba(0,0,0,0.05);">
                        @forelse($pencapaianTerbaru ?? [] as $p)
                            <div class="px-6 py-4 flex items-center justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs" style="color: #9E9790;">{{ $p->anak->name ?? '' }} · {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</p>
                                    <h4 class="font-semibold text-sm mt-0.5" style="color: #2C2C2C;">@if($p->matrikulasi){{ $p->matrikulasi->aspek ? $p->matrikulasi->aspek.': ' : '' }}{{ $p->matrikulasi->indicator }}@else{{ $p->kegiatan?->title ?? 'Evaluasi' }}@endif</h4>
                                    <p class="text-sm italic line-clamp-1 mt-0.5" style="color: #9E9790;">"{{ $p->feedback }}"</p>
                                </div>
                                <span class="badge badge-teal shrink-0 text-center">{{ $p->score }}</span>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-sm" style="color: #9E9790;">Belum ada laporan evaluasi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endhasrole

        @unless(auth()->user()->roles->count() > 0)
        <div class="card p-12 text-center">
            <div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #EDE8DF;">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #9E9790;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <h3 class="text-lg font-bold" style="color: #2C2C2C;">Akun Tanpa Peran</h3>
            <p class="text-sm mt-2 max-w-sm mx-auto" style="color: #9E9790;">Hubungi administrator untuk menetapkan peran pada akun Anda.</p>
        </div>
        @endunless
    </div>
</x-app-layout>
