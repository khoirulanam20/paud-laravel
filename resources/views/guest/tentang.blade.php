<x-guest-public :cms="$cms">
    <x-slot name="title">Tentang Kami</x-slot>

    <section class="py-20" style="background: linear-gradient(135deg,#FFF3E0,#FFFBF0);">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-14 items-center">
                <div>
                    <span class="badge-pill">💛 Tentang Kami</span>
                    <h1 style="font-family:'Baloo 2',sans-serif; font-size:clamp(1.8rem,4vw,2.8rem); font-weight:800; color:#1F2937; margin-top:.75rem;">{{ $cms['about_title'] }}</h1>
                    <div class="mt-5 text-gray-600 leading-relaxed text-base space-y-4">
                        {!! nl2br(e($cms['about_text'])) !!}
                    </div>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('guest.pendaftaran') }}" class="btn-cta">🌟 Daftar Sekarang</a>
                        <a href="{{ route('guest.fasilitas') }}" class="btn-cta-outline">Lihat Fasilitas →</a>
                    </div>
                </div>
                <div>
                    @if($cms['about_photo'])
                        <img src="{{ Storage::url($cms['about_photo']) }}" alt="Tentang" class="w-full rounded-3xl shadow-xl object-cover" style="aspect-ratio:4/3;">
                    @else
                        <div class="w-full rounded-3xl flex items-center justify-center" style="background:linear-gradient(135deg,#FFF3E0,#FFF0F5); aspect-ratio:4/3; font-size:8rem;">🏫</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-16" style="background:#FFF8F0;">
        <div class="max-w-6xl mx-auto px-3 md:px-4 sm:px-6">
            <div class="text-center mb-10">
                <h2 style="font-family:'Baloo 2',sans-serif; font-size:2rem; font-weight:800; color:#1F2937;">Visi & Misi Kami</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="card-facility p-8" style="border-top: 4px solid var(--guest-yellow);">
                    <div class="text-4xl mb-4">🌟</div>
                    <h3 class="text-xl font-bold mb-3" style="color:#1F2937;">Visi</h3>
                    <p class="text-gray-600 leading-relaxed">Menjadi lembaga PAUD terdepan yang mencetak generasi cerdas, berkarakter, dan bahagia di Indonesia.</p>
                </div>
                <div class="card-facility p-8" style="border-top: 4px solid var(--guest-teal);">
                    <div class="text-4xl mb-4">🎯</div>
                    <h3 class="text-xl font-bold mb-3" style="color:#1F2937;">Misi</h3>
                    <ul class="text-gray-600 space-y-2 text-sm leading-relaxed">
                        <li>✅ Memberikan pendidikan berkualitas berbasis bermain</li>
                        <li>✅ Membangun karakter dan nilai moral sejak dini</li>
                        <li>✅ Melibatkan orang tua dalam proses tumbuh kembang</li>
                        <li>✅ Menyediakan lingkungan belajar yang aman dan nyaman</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16" style="background: linear-gradient(135deg,#FF8C42,#FF6B9D);">
        <div class="max-w-3xl mx-auto px-4 text-center text-white">
            <h2 style="font-family:'Baloo 2',sans-serif; font-size:2rem; font-weight:800;">Bergabunglah Bersama Kami! 🎉</h2>
            <a href="{{ route('guest.pendaftaran') }}" class="btn-cta mt-6 inline-flex" style="background:#fff; color:#FF8C42;">🌈 Daftar Sekarang</a>
        </div>
    </section>
</x-guest-public>
