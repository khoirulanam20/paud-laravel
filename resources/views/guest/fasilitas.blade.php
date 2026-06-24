<x-guest-public :cms="$cms" title="Fitur">
    @include('guest.partials.page-header', [
        'badge' => 'Fitur SIPP',
        'title' => 'Tiga Pilar yang Menyatukan Sekolah',
        'subtitle' => 'Penghubung orang tua–sekolah, operasional internal yang efisien, dan kemudahan berkat AI.',
    ])

    @include('guest.partials.pillars', ['heading' => false])

    <section class="guest-section guest-section-alt">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="guest-card text-center p-8 sm:p-10" data-guest-animate="fade-up">
                <h2 class="text-xl sm:text-2xl font-extrabold">Ingin lihat langsung di demo?</h2>
                <p class="mt-2 text-[var(--guest-text-muted)]">Tim kami akan menunjukkan bagaimana ketiga pilar ini bekerja di sekolah Anda.</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('guest.kontak') }}" class="guest-btn guest-btn-primary cursor-pointer">Jadwalkan Demo</a>
                    <a href="{{ route('login') }}" class="guest-btn guest-btn-secondary cursor-pointer">Masuk ke Akun</a>
                </div>
            </div>
        </div>
    </section>
</x-guest-public>
