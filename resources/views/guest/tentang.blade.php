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
                    <div class="prose prose-slate max-w-none text-[var(--guest-text-muted)] leading-relaxed space-y-4">
                        {!! nl2br(e($cms['about_text'] ?? 'SIPP hadir untuk tiga hal penting: menjembatani komunikasi orang tua dan sekolah secara transparan, menyederhanakan operasional harian tim admin dan guru, serta memanfaatkan AI untuk mengurangi pekerjaan dokumentasi yang repetitif.')) !!}
                    </div>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('guest.kontak') }}" class="guest-btn guest-btn-primary cursor-pointer">Hubungi untuk Demo</a>
                        <a href="{{ route('guest.fasilitas') }}" class="guest-btn guest-btn-secondary cursor-pointer">Lihat Fitur</a>
                    </div>
                </div>
                <div data-guest-animate="fade-up">
                    @if(!empty($cms['about_photo']))
                        <img src="{{ Storage::url($cms['about_photo']) }}" alt="Tentang SIPP" class="w-full rounded-2xl border object-cover" style="border-color: var(--guest-border); aspect-ratio: 4/3;">
                    @else
                        <div class="guest-card p-8">
                            <div class="guest-icon-wrap mb-4">
                                @include('guest.partials.icon', ['path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'class' => 'h-6 w-6'])
                            </div>
                            <h3 class="text-xl font-bold">Terpercaya & Aman</h3>
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
                <h2 class="text-2xl sm:text-3xl font-extrabold">Visi & Misi</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" data-guest-stagger>
                <div class="guest-card" data-guest-hover data-guest-stagger-item>
                    <div class="guest-icon-wrap mb-4">
                        @include('guest.partials.icon', ['path' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'])
                    </div>
                    <h3 class="text-xl font-bold">Visi</h3>
                    <p class="mt-3 text-[var(--guest-text-muted)] leading-relaxed">Setiap orang tua PAUD di Indonesia dapat terhubung erat dengan sekolah anaknya — transparan, mudah, dan didukung teknologi cerdas.</p>
                </div>
                <div class="guest-card" data-guest-hover data-guest-stagger-item>
                    <div class="guest-icon-wrap mb-4">
                        @include('guest.partials.icon', ['path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'])
                    </div>
                    <h3 class="text-xl font-bold">Misi</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[var(--guest-text-muted)]">
                        <li class="flex gap-2"><span style="color: var(--guest-teal);">&#10003;</span> Menjembatani komunikasi orang tua dan sekolah secara real-time</li>
                        <li class="flex gap-2"><span style="color: var(--guest-teal);">&#10003;</span> Mempermudah operasional harian admin dan guru</li>
                        <li class="flex gap-2"><span style="color: var(--guest-teal);">&#10003;</span> Mengotomasi dokumentasi rutin dengan AI</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @include('guest.partials.cta-banner')
</x-guest-public>
