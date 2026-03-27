<x-guest-public :cms="$cms">
    <x-slot name="title">Beranda — PAUD Kita</x-slot>

    {{-- HERO --}}
    <section class="hero-section relative overflow-hidden py-20 md:py-32">
        <div class="blob h-72 w-72 top-0 left-0" style="background:#FFD93D; transform:translate(-40%,-40%);"></div>
        <div class="blob h-56 w-56 bottom-0 right-0" style="background:#FF6B9D; transform:translate(30%,30%);"></div>
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6 relative">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="badge-pill mb-4">✨ Aman, Nyaman, Menyenangkan</span>
                    <h1 style="font-family:'Baloo 2',sans-serif; font-size:clamp(2.2rem,5vw,3.5rem); font-weight:800; line-height:1.15; color:#1F2937;">
                        {{ $cms['hero_title'] }}
                    </h1>
                    <p class="mt-4 text-lg text-gray-600 leading-relaxed">{{ $cms['hero_subtitle'] }}</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('guest.pendaftaran') }}" class="btn-cta">🌟 Daftar Sekarang</a>
                        <a href="{{ route('guest.tentang') }}" class="btn-cta-outline">Pelajari Lebih →</a>
                    </div>
                    <!-- Stats -->
                    <div class="mt-10 flex gap-8">
                        <div><p style="font-family:'Baloo 2',sans-serif; font-size:1.75rem; font-weight:800; color:#FF8C42;">{{ $sekolahs->count() }}+</p><p class="text-sm text-gray-500 font-semibold">Cabang Sekolah</p></div>
                        <div><p style="font-family:'Baloo 2',sans-serif; font-size:1.75rem; font-weight:800; color:#FF6B9D;">100%</p><p class="text-sm text-gray-500 font-semibold">Tenaga Terlatih</p></div>
                        <div><p style="font-family:'Baloo 2',sans-serif; font-size:1.75rem; font-weight:800; color:#A78BFA;">🏆</p><p class="text-sm text-gray-500 font-semibold">Terakreditasi</p></div>
                    </div>
                </div>
                <div class="flex justify-center">
                    @if($cms['hero_photo'])
                        <img src="{{ Storage::url($cms['hero_photo']) }}" alt="Hero" class="w-full max-w-md rounded-3xl shadow-2xl object-cover" style="aspect-ratio:4/3;">
                    @else
                        <div class="w-full max-w-md rounded-3xl flex items-center justify-center" style="background:linear-gradient(135deg,#FFF3E0,#FFF0F5); aspect-ratio:4/3; font-size:8rem;">🎨</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Wave -->
    <div class="section-wave -mt-1" style="background:#FFF8F0;"><svg viewBox="0 0 1200 60" preserveAspectRatio="none" style="height:60px;width:100%;display:block;"><path d="M0,60 L0,30 Q150,0 300,30 Q450,60 600,30 Q750,0 900,30 Q1050,60 1200,30 L1200,60 Z" fill="#FFF3E0"/></svg></div>

    {{-- BRANCH SHOWCASE --}}
    <section class="py-16" style="background:#FFF8F0;">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6">
            <div class="text-center mb-10">
                <span class="badge-pill">📍 Lokasi Kami</span>
                <h2 style="font-family:'Baloo 2',sans-serif; font-size:2rem; font-weight:800; color:#1F2937; margin-top:.75rem;">Cabang Sekolah Kami</h2>
                <p class="text-gray-500 mt-2">Temukan cabang terdekat dan daftarkan buah hati Anda hari ini!</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($sekolahs as $s)
                <div class="card-facility p-6" style="border-top: 4px solid var(--guest-orange);">
                    <div class="text-4xl mb-3">🏫</div>
                    <h3 class="font-bold text-lg" style="color:#1F2937;">{{ $s->name }}</h3>
                    @if($s->address)<p class="text-sm text-gray-500 mt-1">📍 {{ $s->address }}</p>@endif
                    @if($s->phone)<p class="text-sm text-gray-500">📞 {{ $s->phone }}</p>@endif
                    <a href="{{ route('guest.pendaftaran') }}" class="mt-4 inline-block text-sm font-bold" style="color:var(--guest-orange);">Daftar di cabang ini →</a>
                </div>
                @empty
                <div class="col-span-3 text-center py-12 text-gray-400">Segera hadir! Hubungi kami untuk info lebih lanjut.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- WHY US --}}
    <section class="py-20" style="background: linear-gradient(135deg, #FFF0F5, #F0F4FF);">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6 text-center">
            <span class="badge-pill">💛 Kenapa Kami?</span>
            <h2 style="font-family:'Baloo 2',sans-serif; font-size:2rem; font-weight:800; color:#1F2937; margin-top:.75rem;">Mengapa Orang Tua Mempercayai Kami</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12">
                @foreach([['🎯','Kurikulum Terstruktur','Program belajar berbasis perkembangan anak sesuai usia.'],['👩‍🏫','Guru Berpengalaman','Tenaga didik tersertifikasi dan peduli terhadap perkembangan anak.'],['🛡️','Lingkungan Aman','CCTV, UKS, dan prosedur keamanan yang ketat.'],['📱','Pantau dari Jauh','Laporan harian dan galeri kegiatan bisa diakses orang tua.']] as [$icon, $title, $desc])
                <div class="card-facility p-6 text-center">
                    <div class="text-5xl mb-3">{{ $icon }}</div>
                    <h4 class="font-bold text-sm" style="color:#1F2937;">{{ $title }}</h4>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <section class="py-16" style="background: linear-gradient(135deg,#FF8C42,#FF6B9D);">
        <div class="max-w-3xl mx-auto px-4 text-center text-white">
            <h2 style="font-family:'Baloo 2',sans-serif; font-size:2.2rem; font-weight:800;">Siap Daftarkan Buah Hati Anda? 🌟</h2>
            <p class="mt-3 text-lg opacity-90">Bergabunglah dengan ratusan keluarga bahagia yang telah mempercayai PAUD Kita.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('guest.pendaftaran') }}" class="btn-cta" style="background:#fff; color:#FF8C42; box-shadow: 0 4px 16px rgba(0,0,0,0.15);">🌈 Daftar Sekarang</a>
                <a href="{{ route('guest.kontak') }}" class="btn-cta-outline" style="border-color:#fff; color:#fff;">📞 Hubungi Kami</a>
            </div>
        </div>
    </section>
</x-guest-public>
