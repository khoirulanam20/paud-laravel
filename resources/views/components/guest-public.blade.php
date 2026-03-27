@props(['cms' => [], 'title' => 'PAUD Kita', 'metaDesc' => ''])
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDesc ?: 'PAUD & Daycare terpercaya untuk tumbuh kembang buah hati Anda.' }}">
    <title>{{ $title }} — PAUD Kita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --guest-yellow:  #FFD93D;
            --guest-orange:  #FF8C42;
            --guest-pink:    #FF6B9D;
            --guest-purple:  #A78BFA;
            --guest-teal:    #34D399;
            --guest-blue:    #60A5FA;
            --guest-bg:      #FFF8F0;
        }
        body { font-family: 'Nunito', sans-serif; background: var(--guest-bg); color: #2C2C2C; }
        .guest-nav-link { font-weight: 700; color: #4B5563; transition: color .2s; padding: .5rem .75rem; border-radius: 12px; text-decoration: none; display: inline-block; }
        .guest-nav-link:hover, .guest-nav-link.active { color: #FF8C42; background: #FFF3E0; }
        .btn-cta { display: inline-flex; align-items: center; gap: .5rem; background: var(--guest-orange); color: #fff !important; font-weight: 800; font-size: 1rem; padding: .85rem 2rem; border-radius: 2rem; box-shadow: 0 4px 16px rgba(255,140,66,.45); transition: transform .2s, box-shadow .2s; border: none; cursor: pointer; text-decoration: none; }
        .btn-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255,140,66,.5); }
        .btn-cta-outline { display: inline-flex; align-items: center; gap: .5rem; background: transparent; color: var(--guest-orange); font-weight: 800; font-size: 1rem; padding: .8rem 1.8rem; border-radius: 2rem; border: 2.5px solid var(--guest-orange); transition: all .2s; text-decoration: none; }
        .btn-cta-outline:hover { background: var(--guest-orange); color: #fff; }
        .card-facility { border-radius: 24px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: transform .25s; }
        .card-facility:hover { transform: translateY(-6px); }
        .badge-pill { display: inline-block; background: var(--guest-yellow); color: #92400E; font-weight: 800; font-size: .7rem; padding: .25rem .75rem; border-radius: 99px; letter-spacing: .05em; text-transform: uppercase; }
        .blob { position: absolute; border-radius: 50%; opacity: .15; pointer-events: none; }
        input, textarea, select { font-family: 'Nunito', sans-serif; }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6 flex justify-between items-center h-16">
            <a href="{{ route('guest.beranda') }}" class="flex items-center gap-2.5">
                <div class="h-10 w-10 rounded-2xl flex items-center justify-center text-xl shrink-0" style="background: linear-gradient(135deg,#FFD93D,#FF8C42);">🌈</div>
                <span style="font-family:'Baloo 2',sans-serif; font-size:1.25rem; font-weight:800; color:#FF8C42;">{{ $cms['hero_title'] ?? 'PAUD Kita' }}</span>
            </a>
            <!-- Desktop Nav -->
            <div class="hidden sm:flex items-center gap-0.5 overflow-x-auto">
                <a href="{{ route('guest.beranda') }}" class="guest-nav-link {{ request()->routeIs('guest.beranda') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('guest.tentang') }}" class="guest-nav-link {{ request()->routeIs('guest.tentang') ? 'active' : '' }}">Tentang</a>
                <a href="{{ route('guest.fasilitas') }}" class="guest-nav-link {{ request()->routeIs('guest.fasilitas') ? 'active' : '' }}">Fasilitas</a>
                <a href="{{ route('guest.galeri') }}" class="guest-nav-link {{ request()->routeIs('guest.galeri') ? 'active' : '' }}">Galeri</a>
                <a href="{{ route('guest.kontak') }}" class="guest-nav-link {{ request()->routeIs('guest.kontak') ? 'active' : '' }}">Kontak</a>
            </div>
            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-cta-outline text-sm py-2 px-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="guest-nav-link">Masuk</a>
                    <a href="{{ route('guest.pendaftaran') }}" class="btn-cta text-sm px-4 py-2">Daftar Sekarang</a>
                @endauth
            </div>
            <!-- Mobile Hamburger -->
            <button @click="open=!open" class="sm:hidden p-2 rounded-xl" style="color:#FF8C42; background:#FFF3E0;">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" x-transition class="sm:hidden border-t px-4 py-4 space-y-1 bg-white" style="display:none; border-color:rgba(0,0,0,0.06);">
            <a href="{{ route('guest.beranda') }}" @click="open=false" class="guest-nav-link block">🏠 Beranda</a>
            <a href="{{ route('guest.tentang') }}" @click="open=false" class="guest-nav-link block">💛 Tentang</a>
            <a href="{{ route('guest.fasilitas') }}" @click="open=false" class="guest-nav-link block">🏫 Fasilitas</a>
            <a href="{{ route('guest.galeri') }}" @click="open=false" class="guest-nav-link block">📸 Galeri</a>
            <a href="{{ route('guest.kontak') }}" @click="open=false" class="guest-nav-link block">📞 Kontak</a>
            <hr class="my-2" style="border-color:rgba(0,0,0,0.06);">
            <a href="{{ route('guest.pendaftaran') }}" @click="open=false" class="btn-cta block text-center">Daftar Sekarang 🌟</a>
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main>{{ $slot }}</main>

    <!-- FOOTER -->
    <footer class="pt-12 pb-8" style="background:#1F2937; color:#D1D5DB;">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 pb-10 border-b" style="border-color:rgba(255,255,255,0.08);">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-9 w-9 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg,#FFD93D,#FF8C42);">🌈</div>
                        <span style="font-family:'Baloo 2',sans-serif; font-size:1.1rem; font-weight:800; color:#FFD93D;">PAUD Kita</span>
                    </div>
                    <p class="text-sm leading-relaxed" style="color:#9CA3AF;">{{ $cms['footer_text'] ?? 'Tumbuh bersama, bahagia bersama. 💛' }}</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-3 text-sm">Navigasi</h4>
                    <ul class="space-y-1.5 text-sm" style="color:#9CA3AF;">
                        <li><a href="{{ route('guest.beranda') }}" class="hover:text-yellow-400 transition-colors">🏠 Beranda</a></li>
                        <li><a href="{{ route('guest.tentang') }}" class="hover:text-yellow-400 transition-colors">💛 Tentang</a></li>
                        <li><a href="{{ route('guest.fasilitas') }}" class="hover:text-yellow-400 transition-colors">🏫 Fasilitas</a></li>
                        <li><a href="{{ route('guest.galeri') }}" class="hover:text-yellow-400 transition-colors">📸 Galeri</a></li>
                        <li><a href="{{ route('guest.kontak') }}" class="hover:text-yellow-400 transition-colors">📞 Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-3 text-sm">Kontak</h4>
                    <ul class="space-y-1.5 text-sm" style="color:#9CA3AF;">
                        <li>📍 {{ $cms['kontak_alamat'] ?? '' }}</li>
                        <li>📞 {{ $cms['kontak_telepon'] ?? '' }}</li>
                        <li>✉️ {{ $cms['kontak_email'] ?? '' }}</li>
                        <li>🕐 {{ $cms['kontak_jam'] ?? '' }}</li>
                    </ul>
                </div>
            </div>
            <p class="text-center text-xs mt-6" style="color:#6B7280;">© {{ date('Y') }} PAUD Manager · Dibuat dengan ❤️ untuk anak-anak Indonesia</p>
        </div>
    </footer>
</body>
</html>
