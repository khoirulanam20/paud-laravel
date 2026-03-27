<x-guest-public :cms="$cms">
    <x-slot name="title">Fasilitas</x-slot>

    <section class="py-20" style="background: linear-gradient(135deg,#FFF3E0,#F0FFF4);">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6 text-center">
            <span class="badge-pill">🏫 Fasilitas</span>
            <h1 style="font-family:'Baloo 2',sans-serif; font-size:clamp(1.8rem,4vw,2.8rem); font-weight:800; color:#1F2937; margin-top:.75rem;">Fasilitas Lengkap untuk Si Kecil</h1>
            <p class="text-gray-500 mt-3">Kami menyediakan lingkungan belajar dan bermain yang aman, bersih, dan menyenangkan.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mt-14">
                @php
                $facilities = [
                    ['title' => $cms['facility_1_title'], 'desc' => $cms['facility_1_desc'], 'icon' => $cms['facility_1_icon'], 'grad' => 'linear-gradient(135deg,#FFF9C4,#FFF3E0)'],
                    ['title' => $cms['facility_2_title'], 'desc' => $cms['facility_2_desc'], 'icon' => $cms['facility_2_icon'], 'grad' => 'linear-gradient(135deg,#E0F7FA,#F0FFF4)'],
                    ['title' => $cms['facility_3_title'], 'desc' => $cms['facility_3_desc'], 'icon' => $cms['facility_3_icon'], 'grad' => 'linear-gradient(135deg,#FCE4EC,#FFF0F5)'],
                    ['title' => $cms['facility_4_title'], 'desc' => $cms['facility_4_desc'], 'icon' => $cms['facility_4_icon'], 'grad' => 'linear-gradient(135deg,#EDE7F6,#E8EAF6)'],
                ];
                @endphp
                @foreach($facilities as $f)
                <div class="card-facility p-8 text-center" style="background: {{ $f['grad'] }};">
                    <div class="text-6xl mb-5">{{ $f['icon'] }}</div>
                    <h3 class="text-lg font-extrabold mb-3" style="color:#1F2937;">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16" style="background:#FFF8F0;">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6">
            <div class="rounded-3xl p-10 text-center" style="background: linear-gradient(135deg,#60A5FA,#A78BFA);">
                <h2 style="font-family:'Baloo 2',sans-serif; color:#fff; font-size:1.8rem; font-weight:800;">Ingin tahu lebih lanjut tentang fasilitas kami?</h2>
                <p class="text-white/80 mt-2">Kunjungi sekolah kami atau hubungi kami untuk jadwal kunjungan.</p>
                <div class="mt-6 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('guest.kontak') }}" class="btn-cta" style="background:#fff; color:#A78BFA;">📞 Hubungi Kami</a>
                    <a href="{{ route('guest.pendaftaran') }}" class="btn-cta-outline" style="border-color:#fff; color:#fff;">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </section>
</x-guest-public>
