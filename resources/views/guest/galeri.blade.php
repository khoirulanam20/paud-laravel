<x-guest-public :cms="$cms">
    <x-slot name="title">Galeri</x-slot>

    <section class="py-20" style="background: linear-gradient(135deg,#FFF0F5,#F0F4FF);">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
            <span class="badge-pill">📸 Galeri</span>
            <h1 style="font-family:'Baloo 2',sans-serif; font-size:clamp(1.8rem,4vw,2.8rem); font-weight:800; color:#1F2937; margin-top:.75rem;">Momen Ceria Bersama Si Kecil</h1>
            <p class="text-gray-500 mt-3">Sekilas pandang keseruan sehari-hari di sekolah kami.</p>

            @php
                $galleries = array_filter([
                    $cms['gallery_1'], $cms['gallery_2'], $cms['gallery_3'],
                    $cms['gallery_4'], $cms['gallery_5'], $cms['gallery_6'],
                ]);
            @endphp

            @if(count($galleries))
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-12">
                @foreach($galleries as $photo)
                <div class="overflow-hidden rounded-3xl shadow-lg aspect-square group cursor-pointer">
                    <img src="{{ Storage::url($photo) }}" alt="Galeri"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                @endforeach
            </div>
            @else
            <div class="mt-16 py-20 rounded-3xl" style="background: linear-gradient(135deg,#FFF3E0,#FFF0F5);">
                <div class="text-8xl mb-6">📸</div>
                <p class="text-xl font-bold" style="color:#1F2937;">Galeri akan segera hadir!</p>
                <p class="text-gray-500 mt-2">Kami sedang mempersiapkan dokumentasi kegiatan terbaik untuk Anda.</p>
            </div>
            @endif
        </div>
    </section>

    <section class="py-16" style="background:#FFF8F0;">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 style="font-family:'Baloo 2',sans-serif; font-size:1.6rem; font-weight:800; color:#1F2937;">Ingin Si Kecil Jadi Bagian dari Foto Berikutnya? 🌟</h2>
            <p class="text-gray-500 mt-2">Daftarkan sekarang dan biarkan buah hati Anda tumbuh dengan penuh keceriaan!</p>
            <a href="{{ route('guest.pendaftaran') }}" class="btn-cta mt-6 inline-flex">🌈 Daftar Sekarang</a>
        </div>
    </section>
</x-guest-public>
