@props(['cms' => [], 'title' => 'SIPP', 'metaDesc' => ''])
<!DOCTYPE html>
<html lang="id" class="guest-site scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="{{ $metaDesc ?: 'SIPP — Sistem Informasi PAUD Terpadu untuk lembaga, admin sekolah, pengajar, dan orang tua.' }}">
    <title>{{ $title }} — SIPP PAUD Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/guest.css', 'resources/js/guest.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="guest-body">
    <nav class="guest-nav" x-data="{ open: false }" aria-label="Navigasi utama">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex justify-between items-center gap-3 min-h-16 py-2">
            <a href="{{ route('guest.beranda') }}" class="flex items-center gap-2.5 min-w-0 cursor-pointer">
                <div class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0" style="background: var(--guest-teal);">
                    @include('guest.partials.icon', ['path' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'class' => 'h-5 w-5 text-white'])
                </div>
                <span class="font-extrabold text-lg truncate" style="color: var(--guest-teal);">SIPP</span>
            </a>

            <div class="hidden md:flex items-center gap-0.5">
                <a href="{{ route('guest.beranda') }}" class="guest-nav-link {{ request()->routeIs('guest.beranda') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('guest.tentang') }}" class="guest-nav-link {{ request()->routeIs('guest.tentang') ? 'active' : '' }}">Tentang</a>
                <a href="{{ route('guest.fasilitas') }}" class="guest-nav-link {{ request()->routeIs('guest.fasilitas') ? 'active' : '' }}">Fitur</a>
                <a href="{{ route('guest.galeri') }}" class="guest-nav-link {{ request()->routeIs('guest.galeri') ? 'active' : '' }}">Galeri</a>
                <a href="{{ route('guest.kontak') }}" class="guest-nav-link {{ request()->routeIs('guest.kontak') ? 'active' : '' }}">Kontak</a>
            </div>

            <div class="hidden md:flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="guest-btn guest-btn-ghost text-sm py-2 px-3">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="guest-btn guest-btn-ghost text-sm py-2 px-3">Masuk</a>
                @endauth
                <a href="{{ route('guest.kontak') }}" class="guest-btn guest-btn-primary text-sm py-2 px-4">Hubungi Kami</a>
            </div>

            <div class="md:hidden flex items-center gap-2 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="guest-btn guest-btn-ghost text-xs py-2 px-3">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="guest-btn guest-btn-ghost text-xs py-2 px-3">Masuk</a>
                @endauth
                <button type="button" @click="open = !open" :aria-expanded="open" class="p-2 rounded-xl cursor-pointer" style="color: var(--guest-teal); background: var(--guest-teal-light);">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="open" x-transition x-cloak class="md:hidden border-t px-4 py-4 space-y-1 bg-white max-h-[min(70vh,calc(100dvh-4rem))] overflow-y-auto" style="border-color: var(--guest-border);">
            <a href="{{ route('guest.beranda') }}" @click="open=false" class="guest-nav-link block">Beranda</a>
            <a href="{{ route('guest.tentang') }}" @click="open=false" class="guest-nav-link block">Tentang</a>
            <a href="{{ route('guest.fasilitas') }}" @click="open=false" class="guest-nav-link block">Fitur</a>
            <a href="{{ route('guest.galeri') }}" @click="open=false" class="guest-nav-link block">Galeri</a>
            <a href="{{ route('guest.kontak') }}" @click="open=false" class="guest-nav-link block">Kontak</a>
            <hr class="my-3" style="border-color: var(--guest-border);">
            <a href="{{ route('guest.kontak') }}" @click="open=false" class="guest-btn guest-btn-primary block text-center w-full">Hubungi Kami</a>
            <a href="{{ route('guest.pendaftaran') }}" @click="open=false" class="guest-btn guest-btn-secondary block text-center w-full mt-2">Pendaftaran Orang Tua</a>
        </div>
    </nav>

    <main>{{ $slot }}</main>

    <footer class="border-t pt-12 pb-8" style="background: var(--guest-text); color: #94A3B8; border-color: rgba(255,255,255,0.08);">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 pb-10 border-b" style="border-color: rgba(255,255,255,0.08);">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-9 w-9 rounded-xl flex items-center justify-center" style="background: var(--guest-teal);">
                            @include('guest.partials.icon', ['path' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'class' => 'h-5 w-5 text-white'])
                        </div>
                        <span class="font-extrabold text-white text-lg">SIPP</span>
                    </div>
                    <p class="text-sm leading-relaxed">{{ $cms['footer_text'] ?? 'Platform manajemen PAUD terpadu untuk lembaga multi-cabang di Indonesia.' }}</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-3 text-sm">Navigasi</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('guest.beranda') }}" class="hover:text-white transition-colors duration-200 cursor-pointer">Beranda</a></li>
                        <li><a href="{{ route('guest.tentang') }}" class="hover:text-white transition-colors duration-200 cursor-pointer">Tentang</a></li>
                        <li><a href="{{ route('guest.fasilitas') }}" class="hover:text-white transition-colors duration-200 cursor-pointer">Fitur</a></li>
                        <li><a href="{{ route('guest.galeri') }}" class="hover:text-white transition-colors duration-200 cursor-pointer">Galeri</a></li>
                        <li><a href="{{ route('guest.kontak') }}" class="hover:text-white transition-colors duration-200 cursor-pointer">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-3 text-sm">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        @if(!empty($cms['kontak_alamat']))<li>{{ $cms['kontak_alamat'] }}</li>@endif
                        @if(!empty($cms['kontak_telepon']))<li>{{ $cms['kontak_telepon'] }}</li>@endif
                        @if(!empty($cms['kontak_email']))<li>{{ $cms['kontak_email'] }}</li>@endif
                        @if(!empty($cms['kontak_jam']))<li>{{ $cms['kontak_jam'] }}</li>@endif
                    </ul>
                </div>
            </div>
            <p class="text-center text-xs mt-6">© {{ date('Y') }} SIPP PAUD Manager</p>
        </div>
    </footer>
</body>
</html>
