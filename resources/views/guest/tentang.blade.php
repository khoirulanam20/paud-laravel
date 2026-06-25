<x-guest-public :cms="$cms" title="Tentang">
    @include('guest.partials.page-header', [
        'badge' => 'Tentang SIPP',
        'title' => $cms['about_title'] ?? 'Tentang Platform Kami',
        'subtitle' => 'Platform yang menghubungkan orang tua dan sekolah, mempermudah operasional, didukung AI.',
    ])

    <section class="guest-section">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-guest-animate="fade-up">
                    <div class="text-[var(--guest-text-muted)] leading-relaxed space-y-4">
                        {!! nl2br(e($cms['about_text'] ?? 'SIPP hadir untuk tiga hal penting: menjembatani komunikasi orang tua dan sekolah secara transparan, menyederhanakan operasional harian tim admin dan guru, serta memanfaatkan AI untuk mengurangi pekerjaan dokumentasi yang repetitif.')) !!}
                    </div>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('guest.kontak') }}" class="guest-btn guest-btn-primary cursor-pointer">Minta Demo</a>
                        <a href="{{ route('guest.fasilitas') }}" class="guest-btn guest-btn-secondary cursor-pointer">Lihat Fitur</a>
                    </div>
                </div>
                <div data-guest-animate="fade-up">
                    @if(!empty($cms['about_photo']))
                        <img src="{{ Storage::url($cms['about_photo']) }}" alt="Tentang SIPP" class="w-full rounded-3xl object-cover aspect-[4/3]">
                    @else
                        <div class="guest-card p-8 text-center">
                            @include('guest.partials.illustration', [
                                'name' => 'placeholder.about',
                                'alt' => 'Terpercaya & Aman',
                                'class' => 'guest-illustration guest-illustration-hero mx-auto mb-4',
                            ])
                            <h3 class="text-xl guest-heading font-bold">Terpercaya & Aman</h3>
                            <p class="mt-2 text-sm text-[var(--guest-text-muted)] leading-relaxed">Data sekolah terisolasi per cabang dengan kontrol akses berbasis peran. Orang tua hanya melihat data anaknya sendiri.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @include('guest.partials.pillars', ['heading' => false])

    <section class="guest-section guest-section-alt">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10" data-guest-animate="fade-up">
                <h2 class="text-3xl sm:text-4xl guest-heading">Visi & Misi</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" data-guest-stagger>
                <div class="guest-card" data-guest-hover data-guest-stagger-item>
                    <div class="guest-service-blob guest-service-blob-lg mb-4" style="background: var(--guest-blob-yellow); margin-left: 0;">
                        @include('guest.partials.illustration', ['name' => 'placeholder.about', 'alt' => 'Visi', 'class' => 'guest-illustration guest-illustration-card'])
                    </div>
                    <h3 class="text-xl guest-heading font-bold">Visi</h3>
                    <p class="mt-3 text-[var(--guest-text-muted)] leading-relaxed">Setiap orang tua PAUD di Indonesia dapat terhubung erat dengan sekolah anaknya — transparan, mudah, dan didukung teknologi cerdas.</p>
                </div>
                <div class="guest-card" data-guest-hover data-guest-stagger-item>
                    <div class="guest-service-blob guest-service-blob-lg mb-4" style="background: var(--guest-blob-green); margin-left: 0;">
                        @include('guest.partials.illustration', ['name' => 'service.operasional', 'alt' => 'Misi', 'class' => 'guest-illustration guest-illustration-card'])
                    </div>
                    <h3 class="text-xl guest-heading font-bold">Misi</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[var(--guest-text-muted)]">
                        <li class="flex gap-2"><span style="color: var(--guest-sage);">&#10003;</span> Menjembatani komunikasi orang tua dan sekolah secara real-time</li>
                        <li class="flex gap-2"><span style="color: var(--guest-sage);">&#10003;</span> Mempermudah operasional harian admin dan guru</li>
                        <li class="flex gap-2"><span style="color: var(--guest-sage);">&#10003;</span> Mengotomasi dokumentasi rutin dengan AI</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @include('guest.partials.cta-banner')
</x-guest-public>
