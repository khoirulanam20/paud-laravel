@props(['cms'])
<section class="guest-section overflow-hidden relative">
    <div class="absolute inset-0 pointer-events-none opacity-40" style="background: radial-gradient(ellipse 80% 60% at 70% 20%, var(--guest-teal-light), transparent);"></div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="text-center lg:text-left">
                <span class="guest-badge mb-5" data-guest-animate="hero">Ortu · Sekolah · AI</span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-[var(--guest-text)]" data-guest-animate="hero">
                    {{ $cms['hero_title'] ?? 'Sistem Informasi PAUD Terpadu (SIPP)' }}
                </h1>
                <p class="mt-5 text-base sm:text-lg leading-relaxed text-[var(--guest-text-muted)] max-w-xl mx-auto lg:mx-0" data-guest-animate="hero">
                    {{ $cms['hero_subtitle'] ?? 'Menghubungkan orang tua dan sekolah, mempermudah operasional internal, dan mengotomasi pekerjaan rutin dengan AI — dalam satu platform PAUD.' }}
                </p>
                <div class="mt-8 flex flex-col sm:flex-row flex-wrap gap-3 justify-center lg:justify-start" data-guest-animate="hero">
                    <a href="{{ route('guest.kontak') }}" class="guest-btn guest-btn-primary w-full sm:w-auto">Hubungi untuk Demo</a>
                    <a href="{{ route('guest.fasilitas') }}" class="guest-btn guest-btn-secondary w-full sm:w-auto">Lihat Fitur</a>
                </div>
                @guest
                <p class="mt-4 text-sm text-[var(--guest-text-muted)]" data-guest-animate="hero">
                    Sudah menggunakan SIPP?
                    <a href="{{ route('login') }}" class="font-bold underline underline-offset-2 cursor-pointer" style="color: var(--guest-teal);">Masuk ke dashboard</a>
                </p>
                @endguest
            </div>
            <div class="flex justify-center lg:justify-end" data-guest-animate="hero">
                @if(!empty($cms['hero_photo']))
                    <img src="{{ Storage::url($cms['hero_photo']) }}" alt="SIPP Dashboard" class="w-full max-w-lg rounded-2xl border object-cover shadow-lg" style="border-color: var(--guest-border); aspect-ratio: 4/3;">
                @else
                    <div class="w-full max-w-lg rounded-2xl border p-8 bg-white" style="border-color: var(--guest-border);">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="guest-icon-wrap">
                                @include('guest.partials.icon', ['path' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'])
                            </div>
                            <div>
                                <p class="font-bold text-lg">SIPP Dashboard</p>
                                <p class="text-sm text-[var(--guest-text-muted)]">Admin · Pengajar · Orang Tua</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach(['Ortu & Sekolah', 'Operasional', 'AI Cerdas'] as $label)
                            <div class="rounded-xl p-3 text-sm font-semibold text-center" style="background: var(--guest-teal-light); color: var(--guest-teal-dark);">{{ $label }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@include('guest.partials.stats')
