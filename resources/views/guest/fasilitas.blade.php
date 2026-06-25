<x-guest-public :cms="$cms" title="Fitur">
    @include('guest.partials.page-header', [
        'badge' => 'Fitur SIPP',
        'title' => 'Tiga Pilar yang Menyatukan Sekolah',
        'subtitle' => 'Penghubung orang tua–sekolah, operasional internal yang efisien, dan kemudahan berkat AI.',
    ])

    @include('guest.partials.services-grid', ['cms' => $cms, 'title' => 'Modul & Layanan', 'subtitle' => 'Semua yang dibutuhkan lembaga PAUD dalam satu platform terpadu.'])

    @include('guest.partials.pillars', ['heading' => false])

    <section class="guest-section">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center" data-guest-animate="fade-up">
            <h2 class="text-2xl sm:text-3xl guest-heading">Ingin lihat langsung di demo?</h2>
            <p class="mt-3 text-[var(--guest-text-muted)]">Tim kami akan menunjukkan bagaimana ketiga pilar ini bekerja di sekolah Anda.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('guest.kontak') }}" class="guest-btn guest-btn-primary cursor-pointer">Jadwalkan Demo</a>
                <a href="{{ route('login') }}" class="guest-btn guest-btn-secondary cursor-pointer">Masuk ke Akun</a>
            </div>
        </div>
    </section>
</x-guest-public>
