<x-guest-public :cms="$cms" title="Beranda">
    @include('guest.partials.hero', ['cms' => $cms])
    @include('guest.partials.pillars')
    @include('guest.partials.onboarding')

    @if($sekolahs->isNotEmpty())
    <section class="guest-section">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10" data-guest-animate="fade-up">
                <span class="guest-badge mb-3">Jaringan Sekolah</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Cabang yang Menggunakan SIPP</h2>
                <p class="mt-3 text-[var(--guest-text-muted)]">Lembaga mitra yang telah mempercayakan operasional PAUD mereka pada platform kami.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" data-guest-stagger>
                @foreach($sekolahs as $s)
                <div class="guest-card" data-guest-hover data-guest-stagger-item>
                    <div class="guest-icon-wrap mb-4">
                        @include('guest.partials.icon', ['path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'])
                    </div>
                    <h3 class="font-bold text-lg">{{ $s->name }}</h3>
                    @if($s->address)<p class="text-sm text-[var(--guest-text-muted)] mt-1">{{ $s->address }}</p>@endif
                    @if($s->phone)<p class="text-sm text-[var(--guest-text-muted)]">{{ $s->phone }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @include('guest.partials.cta-banner')
</x-guest-public>
