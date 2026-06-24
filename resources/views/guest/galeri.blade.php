<x-guest-public :cms="$cms" title="Galeri">
    @include('guest.partials.page-header', [
        'badge' => 'Galeri Produk',
        'title' => 'Tampilan Antarmuka SIPP',
        'subtitle' => 'Cuplikan dashboard dan modul yang digunakan sehari-hari oleh lembaga, admin, pengajar, dan orang tua.',
    ])

    @php
        $galleries = array_filter([
            $cms['gallery_1'] ?? null,
            $cms['gallery_2'] ?? null,
            $cms['gallery_3'] ?? null,
            $cms['gallery_4'] ?? null,
            $cms['gallery_5'] ?? null,
            $cms['gallery_6'] ?? null,
        ]);
    @endphp

    <section class="guest-section" x-data="{ lightbox: null }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            @if(count($galleries))
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4" data-guest-stagger>
                @foreach($galleries as $photo)
                <button type="button"
                        @click="lightbox = '{{ Storage::url($photo) }}'"
                        class="overflow-hidden rounded-2xl border aspect-square cursor-pointer group focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="border-color: var(--guest-border); --tw-ring-color: var(--guest-teal);"
                        data-guest-stagger-item>
                    <img src="{{ Storage::url($photo) }}" alt="Screenshot SIPP"
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                </button>
                @endforeach
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" data-guest-stagger>
                @foreach([
                    ['title' => 'Dashboard Admin', 'desc' => 'Ringkasan siswa, kegiatan, dan keuangan.', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['title' => 'Portal Orang Tua', 'desc' => 'Pantau anak, bayar tagihan, chat AI.', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['title' => 'Laporan Monev', 'desc' => 'Export PDF perkembangan siswa otomatis.', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ] as $mock)
                <div class="guest-card" data-guest-hover data-guest-stagger-item>
                    <div class="aspect-video rounded-xl mb-4 flex items-center justify-center" style="background: var(--guest-teal-light);">
                        @include('guest.partials.icon', ['path' => $mock['icon'], 'class' => 'h-12 w-12'])
                    </div>
                    <h3 class="font-bold">{{ $mock['title'] }}</h3>
                    <p class="mt-1 text-sm text-[var(--guest-text-muted)]">{{ $mock['desc'] }}</p>
                </div>
                @endforeach
            </div>
            <p class="text-center text-sm text-[var(--guest-text-muted)] mt-8" data-guest-animate="fade-up">Upload screenshot melalui CMS Lembaga untuk menampilkan galeri kustom.</p>
            @endif
        </div>

        <div x-show="lightbox" x-transition x-cloak
             @click="lightbox = null" @keydown.escape.window="lightbox = null"
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 cursor-pointer"
             style="background: rgba(15,23,42,0.85); display: none;">
            <img :src="lightbox" alt="Preview" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl" @click.stop>
        </div>
    </section>

    @include('guest.partials.cta-banner', [
        'title' => 'Ingin melihat demo langsung?',
        'subtitle' => 'Hubungi kami untuk walkthrough platform sesuai peran pengguna Anda.',
    ])
</x-guest-public>
