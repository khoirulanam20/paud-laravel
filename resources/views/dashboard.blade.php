<x-app-layout>
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

        <!-- ═══ ADMIN KELAS / WALI KELAS ═══ -->
        @hasrole('Admin Kelas')
        <div class="card overflow-hidden mb-2">
            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="background: linear-gradient(135deg, #1A6B6B 0%, #2D8585 100%);">
                <div class="text-white">
                    <p class="text-xs font-semibold uppercase tracking-wider opacity-80 mb-1">Wali kelas</p>
                    <h3 class="text-xl font-bold">Terdaftar di {{ $kelasWaliCount ?? 0 }} Kelas</h3>
                    <p class="text-sm opacity-90 mt-1">Total siswa di seluruh kelasmu: <strong>{{ $kelasAnakCount ?? 0 }}</strong></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('adminkelas.anak.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold bg-white/15 hover:bg-white/25 text-white border border-white/30">Siswa Kelasku</a>
                    <a href="{{ route('adminkelas.presensi.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold bg-white text-[#1A6B6B] hover:bg-opacity-95">Presensi Kelasku</a>
                    @hasrole('Pengajar')
                    <a href="{{ route('pengajar.kegiatan.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold bg-white/15 hover:bg-white/25 text-white border border-white/30">Jurnal Kegiatan</a>
                    @endhasrole
                </div>
            </div>
        </div>
        @endhasrole

        <!-- ═══ PENGAJAR DASHBOARD ═══ -->
        @hasrole('Pengajar')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="stat-card">
                <div class="stat-icon"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9E9790;">{{ $dashboardAnakLabel ?? 'Siswa di sekolah' }}</p><p class="text-3xl font-bold" style="color: #2C2C2C;">{{ $totalAnakSekolah ?? 0 }}</p></div>
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
        <div class="space-y-6" x-data="{
            selectedActivity: null,
            showActivityModal: false,
            showImageModal: false,
            activeImage: null,
            openActivity(k) {
                this.selectedActivity = k;
                this.showActivityModal = true;
            },
            selectedRutin: null,
            showRutinModal: false,
            openRutin(k) {
                this.selectedRutin = k;
                this.showRutinModal = true;
            }
        }">
            {{-- ═══ Welcome & Attendance Summary Card ═══ --}}
            <div class="relative rounded-2xl overflow-hidden text-white shadow-lg mb-6"
                 style="background: linear-gradient(135deg, #1A6B6B 0%, #155959 50%, #0f4040 100%);
                        box-shadow: 0 8px 32px rgba(26,107,107,0.30);">

                {{-- Decorative background blobs --}}
                <div class="absolute -right-10 -top-10 pointer-events-none opacity-[0.08]">
                    <svg class="h-52 w-52" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <div class="absolute -left-6 -bottom-6 pointer-events-none opacity-[0.05]">
                    <svg class="h-40 w-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>

                {{-- 1. Welcome Section --}}
                <div class="relative px-5 py-5 sm:px-7 sm:py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-white/60 mb-0.5">Selamat Datang</p>
                            <h1 class="text-xl sm:text-2xl font-bold leading-tight text-white">Assalamu'alaikum, Bunda / Ayah</h1>
                            <p class="text-sm text-white/70 mt-0.5 leading-relaxed">Pantau tumbuh kembang si kecil hari ini.</p>
                        </div>
                    </div>
                </div>

                {{-- 2. Attendance Overview Title & Filter --}}
                <div class="relative px-5 pt-5 pb-4 border-t border-b border-white/10">
                    <div class="flex items-center justify-between mb-0">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0 border border-white/20">
                                <svg class="h-5 w-5 text-white/95" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1 pt-0.5">
                                <p class="text-[11px] font-semibold uppercase tracking-widest text-white/75 leading-tight">Ringkasan Kehadiran</p>
                                <p class="text-sm font-medium text-white/95 mt-0.5">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('orangtua.presensi.index') }}" class="h-8 w-8 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition border border-white/10" title="Filter & Detail">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- 3. Children Attendance Stats List --}}
                <div class="relative px-5 py-5 md:px-6 md:py-6">
                    @forelse($anaks ?? [] as $anak)
                        @php $summary = $presensiSummaryPerAnak[$anak->id] ?? ['hadir' => 0, 'tidak_hadir' => 0, 'efektif' => 0]; @endphp
                        <div class="rounded-xl bg-white/10 border border-white/15 backdrop-blur-sm p-4 mb-3 last:mb-0">
                            <div class="flex items-center gap-3">
                                <x-foto-profil :path="$anak->photo" :name="$anak->name" size="lg" class="border border-white/25 shadow-sm shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-[15px] leading-tight">{{ $anak->name }}</p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <div class="flex flex-col">
                                            <span class="text-[8px] text-white/60 uppercase font-bold tracking-wider">Hadir</span>
                                            <span class="text-sm font-bold">{{ $summary['hadir'] }} hari</span>
                                        </div>
                                        <div class="h-6 w-px bg-white/10"></div>
                                        <div class="flex flex-col">
                                            <span class="text-[8px] text-white/60 uppercase font-bold tracking-wider">Izin/Sakit/Alpa</span>
                                            <span class="text-sm font-bold text-amber-200">{{ $summary['tidak_hadir'] }} hari</span>
                                        </div>
                                        <div class="h-6 w-px bg-white/10"></div>
                                        <div class="flex flex-col">
                                            <span class="text-[8px] text-white/60 uppercase font-bold tracking-wider">Hari Efektif</span>
                                            <span class="text-sm font-bold tabular-nums leading-none text-white">{{ $summary['efektif'] }} hari</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-white/75 py-2">Belum ada anak tertaut pada akun ini.</p>
                    @endforelse              
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="lg:col-span-1 flex flex-col gap-5">
                    

                    <div class="card overflow-hidden">
                        <div class="px-5 py-4 border-b flex items-center justify-between gap-3" style="border-color: rgba(0,0,0,0.06);">
                            <h3 class="section-title mb-0">Menu hari ini</h3>
                            <a href="{{ route('orangtua.menu-makanan.index') }}" class="text-xs font-bold px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 transition" style="color: #1A6B6B;">Jadwal lengkap</a>
                        </div>
                        <div class="px-5 py-5">
                            @if(!empty($menuHariIni))
                                @php
                                    $photos = [];
                                    if($menuHariIni->photo) $photos[] = Storage::url($menuHariIni->photo);
                                    if($menuHariIni->photo_kegiatan) $photos[] = Storage::url($menuHariIni->photo_kegiatan);
                                @endphp

                                @if(count($photos) > 0)
                                    <div class="mb-3 -mx-5 -mt-5 relative overflow-hidden" x-data="{ 
                                        active: 0, 
                                        photos: {{ json_encode($photos) }},
                                        next() { this.active = (this.active + 1) % this.photos.length },
                                        init() { if(this.photos.length > 1) setInterval(() => this.next(), 4000) }
                                    }">
                                        <div class="h-48 w-full relative">
                                            <template x-for="(photo, index) in photos" :key="index">
                                                <img x-show="active === index" 
                                                     x-transition:enter="transition ease-out duration-500"
                                                     x-transition:enter-start="opacity-0 transform scale-105"
                                                     x-transition:enter-end="opacity-100 transform scale-100"
                                                     :src="photo" 
                                                     class="absolute inset-0 w-full h-full object-cover">
                                            </template>
                                        </div>
                                        
                                        @if(count($photos) > 1)
                                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                                                <template x-for="(photo, index) in photos" :key="index">
                                                    <div class="h-1.5 rounded-full transition-all duration-300" 
                                                         :class="active === index ? 'w-4 bg-white' : 'w-1.5 bg-white/40'"></div>
                                                </template>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <p class="font-bold text-base mb-2 whitespace-pre-line leading-snug" style="color: #2C2C2C;">{{ $menuHariIni->menu }}</p>
                                @if($menuHariIni->nutrition_info)
                                    <p class="text-sm leading-relaxed mb-4" style="color: #9E9790;">{{ $menuHariIni->nutrition_info }}</p>
                                @endif
                                
                                <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
                                    <form action="{{ route('orangtua.menu-makanan.vote') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="menu_makanan_id" value="{{ $menuHariIni->id }}">
                                        <button type="submit" name="vote_type" value="like" 
                                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($myVote?->vote_type === 'like') ? 'bg-green-100 text-green-700 ring-1 ring-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                            <svg class="h-3.5 w-3.5" fill="{{ ($myVote?->vote_type === 'like') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.708C19.747 10 21 11.253 21 12.8c0 .964-.46 1.83-1.18 2.373l-.403.303a11.953 11.953 0 011.583 3.992c.114.71-.46 1.332-1.173 1.332H8.3a2 2 0 01-2-2V10h2l3-6h2v6h3z" /></svg>
                                            {{ $menuHariIni->likes_count ?? 0 }} Suka
                                        </button>
                                    </form>
                                    <form action="{{ route('orangtua.menu-makanan.vote') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="menu_makanan_id" value="{{ $menuHariIni->id }}">
                                        <button type="submit" name="vote_type" value="dislike" 
                                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($myVote?->vote_type === 'dislike') ? 'bg-red-100 text-red-700 ring-1 ring-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                            <svg class="h-3.5 w-3.5" fill="{{ ($myVote?->vote_type === 'dislike') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.292C4.253 14 3 12.747 3 11.2c0-.964.46-1.83 1.18-2.373l.403-.303A11.953 11.953 0 013.001 4.532c-.114-.71.46-1.332 1.173-1.332H15.7a2 2 0 012 2V14h-2l-3 6h-2v-6z" /></svg>
                                            {{ $menuHariIni->dislikes_count ?? 0 }} Tidak Suka
                                        </button>
                                    </form>
                                </div>
                            @else
                                <p class="text-sm" style="color: #9E9790;">Belum ada informasi menu untuk hari ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 flex flex-col gap-5">
                    <div class="card overflow-hidden">
                        <div class="px-5 sm:px-6 py-4 border-b flex items-center justify-between gap-3" style="border-color: rgba(0,0,0,0.06);">
                            <h3 class="section-title mb-0 text-amber-900">Agenda & Kegiatan</h3>
                            <div class="flex gap-2">
                                <a href="{{ route('orangtua.kegiatan.index') }}" class="text-xs font-bold px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 transition" style="color: #1A6B6B;">Agenda</a>
                                <a href="{{ route('orangtua.kegiatan-rutin.index') }}" class="text-xs font-bold px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 transition" style="color: #1A6B6B;">Kegiatan</a>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @forelse($dashboardFeed ?? [] as $item)
                                @if($item['type'] === 'kegiatan')
                                    @php
                                        $keg = $item['data'];
                                        $activityData = [
                                            'title' => $keg->title,
                                            'date' => \Carbon\Carbon::parse($keg->date)->translatedFormat('d M Y'),
                                            'description' => $keg->description,
                                            'pengajar' => $keg->pengajar->name ?? '-',
                                            'photos' => collect($keg->photos ?? [])->map(fn($p) => Storage::url($p))->values()->all(),
                                            'pencapaians' => $keg->pencapaians->map(fn($p) => [
                                                'aspek' => $p->matrikulasi->aspek ?? '',
                                                'indicator' => $p->matrikulasi->indicator ?? '',
                                                'score_label' => \App\Support\LabelSkorPencapaian::label($p->score),
                                                'score_color' => \App\Support\LabelSkorPencapaian::color($p->score),
                                                'feedback' => $p->feedback,
                                                'anak_name' => $p->anak->name ?? '-'
                                            ])->values()->all()
                                        ];
                                    @endphp
                                    <div class="px-5 sm:px-6 py-5 flex gap-4 cursor-pointer hover:bg-gray-50 transition border-l-4 border-l-indigo-500" @click="openActivity(@js($activityData))">
                                        <x-foto-profil :path="$keg->pengajar?->photo" :name="$keg->pengajar?->name ?? '?'" size="md" rounded="full" class="shrink-0 ring-2 ring-indigo-100" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-600">Jurnal Kegiatan • {{ \Carbon\Carbon::parse($keg->date)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <div class="flex flex-col gap-3">
                                                <div class="min-w-0">
                                                    <h4 class="font-bold text-[16px] text-gray-900 leading-tight mb-1">{{ $keg->title }}</h4>
                                                    <p class="text-sm text-gray-500 leading-relaxed">{{ $keg->description }}</p>
                                                </div>
                                                @if(!empty($keg->photos))
                                                    <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
                                                        @foreach(collect($keg->photos)->take(4) as $photo)
                                                            <div class="h-32 w-32 rounded-xl border border-gray-100 overflow-hidden shadow-sm shrink-0 cursor-pointer hover:opacity-90 transition"
                                                                 @click.stop="activeImage = '{{ Storage::url($photo) }}'; showImageModal = true">
                                                                <img src="{{ Storage::url($photo) }}" class="h-full w-full object-cover">
                                                            </div>
                                                        @endforeach
                                                        @if(count($keg->photos) > 4)
                                                            <div class="h-32 w-32 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-500 text-sm font-bold shadow-sm shrink-0">
                                                                +{{ count($keg->photos) - 4 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item['type'] === 'kegiatan_rutin')
                                    @php $kr = $item['data'];
                                    $rutinData = [
                                        'kegiatan' => $kr->kegiatan,
                                        'tanggal'  => \Carbon\Carbon::parse($kr->tanggal)->translatedFormat('d M Y'),
                                        'anak'     => $kr->anak->name ?? '-',
                                        'aspek'    => $kr->aspek,
                                        'status'   => $kr->status_pencapaian ?? null,
                                        'keterangan' => $kr->keterangan ?? null,
                                    ];
                                    @endphp
                                    <div class="px-5 sm:px-6 py-5 flex gap-4 border-l-4 border-l-blue-500 cursor-pointer hover:bg-blue-50/40 transition" @click="openRutin(@js($rutinData))">
                                        <x-foto-profil :path="$kr->anak?->photo" :name="$kr->anak?->name ?? '?'" size="md" rounded="full" class="shrink-0 ring-2 ring-blue-100" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-blue-600">Kegiatan Rutin • {{ \Carbon\Carbon::parse($kr->tanggal)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <p class="text-xs font-semibold text-gray-500 mb-1">{{ $kr->anak->name ?? '' }}</p>
                                            <h4 class="font-bold text-[15px] text-gray-900 leading-tight mb-1">{{ $kr->kegiatan }}</h4>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-block px-2 py-1 rounded bg-blue-50 text-blue-700 text-[10px] font-bold" style="border: none;">{{ $kr->aspek }}</span>
                                                @if($kr->status_pencapaian)
                                                    <span class="inline-block px-2 py-1 rounded bg-[#E8F5E9] text-[#2E7D32] text-[10px] font-bold">{{ $kr->status_pencapaian }}</span>
                                                @endif
                                            </div>
                                            @if($kr->keterangan)
                                                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed line-clamp-1">{{ $kr->keterangan }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    @php $p = $item['data']; @endphp
                                    <div class="px-5 sm:px-6 py-5 flex gap-4 border-l-4 border-l-amber-500">
                                        <x-foto-profil :path="$p->anak?->photo" :name="$p->anak?->name ?? '?'" size="md" rounded="full" class="shrink-0 ring-2 ring-amber-100" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-600">Pencapaian • {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <p class="text-xs font-semibold text-gray-500 mb-1">{{ $p->anak->name ?? '' }}</p>
                                            <h4 class="font-bold text-[15px] text-gray-900 leading-tight mb-2">@if($p->matrikulasi){{ $p->matrikulasi->aspek ? $p->matrikulasi->aspek.': ' : '' }}{{ $p->matrikulasi->indicator }}@else{{ $p->kegiatan?->title ?? 'Evaluasi' }}@endif</h4>
                                            <span class="inline-block badge shrink-0 text-center text-[10px] font-bold py-1 px-2.5 rounded-full" style="background: {{ \App\Support\LabelSkorPencapaian::color($p->score) }}; color: white; border: none;">{{ \App\Support\LabelSkorPencapaian::label($p->score) }}</span>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="px-6 py-12 text-center">
                                    <p class="text-sm" style="color: #9E9790;">Belum ada aktivitas terbaru hari ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Activity Modal --}}
            <div x-show="showActivityModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
                <div x-show="showActivityModal" x-transition class="modal-box max-w-2xl w-full" @click.away="showActivityModal=false">
                    <div class="modal-header flex justify-between items-center">
                        <h3 class="section-title mb-0" x-text="selectedActivity?.title || 'Detail Kegiatan'"></h3>
                        <button @click="showActivityModal=false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="modal-body space-y-5 max-h-[75vh] overflow-y-auto">
                        <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs font-semibold uppercase tracking-wider" style="color: #9E9790;">
                            <span class="flex items-center gap-1"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a12 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><span x-text="selectedActivity?.date"></span></span>
                            <span class="flex items-center gap-1"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><span x-text="selectedActivity?.pengajar"></span></span>
                        </div>

                        <div class="text-sm leading-relaxed" style="color: #5A5A5A;" x-text="selectedActivity?.description"></div>

                        <template x-if="selectedActivity?.photos?.length > 0">
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-widest" style="color: #9E9790;">Dokumentasi Foto</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <template x-for="(url, index) in selectedActivity.photos" :key="index">
                                        <div class="aspect-square rounded-xl overflow-hidden ring-1 ring-black/5 cursor-pointer hover:opacity-90 transition">
                                            <img :src="url" class="w-full h-full object-cover" @click="activeImage = url; showImageModal = true">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="space-y-4 pt-4 border-t" style="border-color: rgba(0,0,0,0.06);">
                            <h4 class="text-xs font-bold uppercase tracking-widest" style="color: #975A16;">Laporan Perkembangan Anak</h4>
                            <div class="space-y-3">
                                <template x-for="(p, pi) in selectedActivity?.pencapaians" :key="pi">
                                    <div class="rounded-xl p-4 border" style="background:#FFFBF0; border-color:rgba(151,90,22,0.1);">
                                        <div class="flex items-start justify-between gap-3 mb-2">
                                            <div class="min-w-0">
                                                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-800" x-text="p.aspek || 'Evaluasi'"></p>
                                                <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="p.indicator"></p>
                                            </div>
                                            <span class="badge shrink-0 text-xs py-1 px-2.5" :style="'background:'+p.score_color+';color:white;border:none;'" x-text="p.score_label"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Kegiatan Rutin --}}
            <div x-show="showRutinModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
                <div x-show="showRutinModal" x-transition class="modal-box max-w-lg w-full" @click.away="showRutinModal=false">
                    <div class="modal-header flex justify-between items-center" style="border-bottom: 2px solid #3B82F6;">
                        <div class="flex items-center gap-2">
                            <div class="h-7 w-7 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            </div>
                            <h3 class="section-title mb-0 text-blue-800">Detail Kegiatan Rutin</h3>
                        </div>
                        <button @click="showRutinModal=false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        {{-- Info Anak & Tanggal --}}
                        <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-xs font-semibold" style="color: #9E9790;">
                            <span class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span x-text="selectedRutin?.anak"></span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span x-text="selectedRutin?.tanggal"></span>
                            </span>
                        </div>

                        {{-- Kegiatan --}}
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 mb-1">Kegiatan</p>
                            <p class="text-base font-bold text-gray-900" x-text="selectedRutin?.kegiatan"></p>
                        </div>

                        {{-- Aspek & Status --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <template x-if="selectedRutin?.aspek">
                                <span class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold" x-text="selectedRutin.aspek"></span>
                            </template>
                            <template x-if="selectedRutin?.status">
                                <span class="px-3 py-1.5 rounded-lg bg-[#E8F5E9] text-[#2E7D32] text-xs font-bold" x-text="selectedRutin.status"></span>
                            </template>
                        </div>

                        {{-- Keterangan --}}
                        <template x-if="selectedRutin?.keterangan">
                            <div class="rounded-xl p-4 border" style="background:#EFF6FF; border-color:rgba(59,130,246,0.15);">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 mb-2">Keterangan</p>
                                <p class="text-sm leading-relaxed text-gray-700" x-text="selectedRutin.keterangan"></p>
                            </div>
                        </template>
                        <template x-if="!selectedRutin?.keterangan">
                            <p class="text-xs text-gray-400 italic">Tidak ada keterangan tambahan.</p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Modal Preview Gambar --}}
            <div x-show="showImageModal" 
                 class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 style="display: none;"
                 x-transition
                 @keydown.escape.window="showImageModal = false">
                <div class="relative max-w-4xl w-full" @click.away="showImageModal = false">
                    <button class="absolute -top-12 right-0 text-white hover:text-gray-300 transition flex items-center gap-2" @click="showImageModal = false">
                        <span class="text-xs font-bold uppercase tracking-widest text-white/50">Klik di mana saja untuk tutup</span>
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <img :src="activeImage" class="w-full h-auto max-h-[85vh] object-contain rounded-2xl shadow-2xl bg-white shadow-black/20">
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
