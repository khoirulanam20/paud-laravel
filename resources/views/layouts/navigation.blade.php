@php
    $user = auth()->user();
    $roleNavItems = match (true) {
        $user->hasRole('Lembaga') => [
            ['route' => 'lembaga.sekolah.index', 'label' => 'Sekolah', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'pattern' => 'lembaga.sekolah.*'],
            ['route' => 'lembaga.admin-sekolah.index', 'label' => 'Admin Sekolah', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'pattern' => 'lembaga.admin-sekolah.*'],
            ['route' => 'lembaga.kritik-saran.index', 'label' => 'Kritik & Saran', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'pattern' => 'lembaga.kritik-saran.*'],
        ],
        $user->hasRole('Admin Sekolah') => [
            ['route' => 'admin.anak.index', 'label' => 'Siswa', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'pattern' => 'admin.anak.*'],
            ['route' => 'admin.presensi.index', 'label' => 'Presensi', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'pattern' => 'admin.presensi.*'],
            ['route' => 'admin.pengajar.index', 'label' => 'Pengajar', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'pattern' => 'admin.pengajar.*'],
            ['route' => 'admin.sarana.index', 'label' => 'Sarana', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'pattern' => 'admin.sarana.*'],
            ['route' => 'admin.menu-makanan.index', 'label' => 'Menu Makanan', 'icon' => 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z', 'pattern' => 'admin.menu-makanan.*'],
            ['route' => 'admin.kegiatan.index', 'label' => 'Kegiatan', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'pattern' => 'admin.kegiatan.*'],
            ['route' => 'admin.cashflow.index', 'label' => 'Cashflow', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'admin.cashflow.*'],
        ],
        $user->hasRole('Pengajar') => [
            ['route' => 'pengajar.kegiatan.index', 'label' => 'Jurnal Kegiatan', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'pattern' => 'pengajar.kegiatan.*'],
            ['route' => 'pengajar.matrikulasi.index', 'label' => 'Matrikulasi', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'pattern' => 'pengajar.matrikulasi.*'],
            ['route' => 'pengajar.pencapaian.index', 'label' => 'Pencapaian', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'pengajar.pencapaian.*'],
        ],
        $user->hasRole('Orang Tua') => [
            ['route' => 'orangtua.kegiatan.index', 'label' => 'Kegiatan', 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'orangtua.kegiatan.*'],
            ['route' => 'orangtua.pencapaian.index', 'label' => 'Pencapaian', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'orangtua.pencapaian.*'],
            ['route' => 'orangtua.menu-makanan.index', 'label' => 'Menu Makanan', 'icon' => 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z', 'pattern' => 'orangtua.menu-makanan.*'],
            ['route' => 'orangtua.kritik-saran.index', 'label' => 'Saran & Kritik', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'pattern' => 'orangtua.kritik-saran.*'],
        ],
        default => [],
    };
    $roleLabel = match (true) {
        $user->hasRole('Lembaga') => 'Yayasan',
        $user->hasRole('Admin Sekolah') => 'Admin Sekolah',
        $user->hasRole('Pengajar') => 'Pengajar',
        $user->hasRole('Orang Tua') => 'Orang Tua',
        default => 'Pengguna',
    };
@endphp

<nav x-data="{ open: false }" class="relative z-50" style="background: #FAF6F0; border-bottom: 1px solid rgba(0,0,0,0.07); box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4 sm:gap-6 min-w-0 flex-1">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="h-9 w-9 rounded-xl flex items-center justify-center" style="background: #1A6B6B; box-shadow: 2px 3px 8px rgba(26,107,107,0.35);">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="font-bold text-base hidden sm:block" style="color: #2C2C2C;">PAUD Manager</span>
                </a>

                <!-- Desktop: min-w-0 flex-1 agar overflow-x berfungsi dan area klik tidak “hilang” di flex parent -->
                <div class="hidden sm:flex min-w-0 flex-1 items-center gap-1 overflow-x-auto overscroll-x-contain [scrollbar-gutter:stable]">
                    <a href="{{ route('dashboard') }}"
                       class="nav-item flex shrink-0 items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    @foreach ($roleNavItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="nav-item flex shrink-0 items-center gap-1.5 whitespace-nowrap {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- User Menu -->
            <div class="hidden sm:flex items-center gap-3 shrink-0">
                <div class="text-right hidden md:block">
                    <div class="text-sm font-semibold leading-tight" style="color: #2C2C2C;">{{ Auth::user()->name }}</div>
                    <div class="text-xs" style="color: #9E9790;">{{ $roleLabel }}</div>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="h-9 w-9 rounded-xl flex items-center justify-center font-bold text-white text-sm transition-all hover:opacity-90" style="background: #1A6B6B; box-shadow: 2px 3px 8px rgba(26,107,107,0.30);">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil Saya
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl transition" style="color: #6B6560;">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t" style="border-color: rgba(0,0,0,0.07);">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" @click="open = false"
               class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white' : '' }}"
               style="{{ request()->routeIs('dashboard') ? 'background: #1A6B6B;' : 'color: #2C2C2C;' }}">
                Dashboard
            </a>
            @foreach ($roleNavItems as $item)
            <a href="{{ route($item['route']) }}" @click="open = false"
               class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs($item['pattern']) ? 'text-white' : '' }}"
               style="{{ request()->routeIs($item['pattern']) ? 'background: #1A6B6B;' : 'color: #2C2C2C;' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>
        <div class="px-4 py-3 border-t" style="border-color: rgba(0,0,0,0.07);">
            <div class="font-semibold text-sm" style="color: #2C2C2C;">{{ Auth::user()->name }}</div>
            <div class="text-xs mb-3" style="color: #9E9790;">{{ Auth::user()->email }}</div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-xl text-sm mb-1" style="color: #6B6560;">Profil Saya</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 rounded-xl text-sm" style="color: #6B6560;">Keluar</button>
            </form>
        </div>
    </div>
</nav>
