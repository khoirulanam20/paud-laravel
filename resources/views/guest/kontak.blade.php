<x-guest-public :cms="$cms">
    <x-slot name="title">Kontak</x-slot>

    <section class="py-20" style="background: linear-gradient(135deg,#FFF3E0,#F0FFF4);">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <span class="badge-pill">📞 Kontak</span>
                <h1 style="font-family:'Baloo 2',sans-serif; font-size:clamp(1.8rem,4vw,2.8rem); font-weight:800; color:#1F2937; margin-top:.75rem;">Kami Siap Membantu Anda!</h1>
                <p class="text-gray-500 mt-2">Punya pertanyaan? Jangan ragu untuk menghubungi kami.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Info Kontak -->
                <div class="space-y-5">
                    <div class="card-facility p-6 flex gap-4" style="border-left:4px solid var(--guest-orange);">
                        <div class="text-4xl">📍</div>
                        <div><h4 class="font-bold" style="color:#1F2937;">Alamat</h4><p class="text-sm text-gray-600 mt-1">{{ $cms['kontak_alamat'] }}</p></div>
                    </div>
                    <div class="card-facility p-6 flex gap-4" style="border-left:4px solid var(--guest-teal);">
                        <div class="text-4xl">📞</div>
                        <div><h4 class="font-bold" style="color:#1F2937;">Telepon</h4><p class="text-sm text-gray-600 mt-1">{{ $cms['kontak_telepon'] }}</p></div>
                    </div>
                    <div class="card-facility p-6 flex gap-4" style="border-left:4px solid var(--guest-blue);">
                        <div class="text-4xl">✉️</div>
                        <div><h4 class="font-bold" style="color:#1F2937;">Email</h4><p class="text-sm text-gray-600 mt-1">{{ $cms['kontak_email'] }}</p></div>
                    </div>
                    <div class="card-facility p-6 flex gap-4" style="border-left:4px solid var(--guest-purple);">
                        <div class="text-4xl">🕐</div>
                        <div><h4 class="font-bold" style="color:#1F2937;">Jam Operasional</h4><p class="text-sm text-gray-600 mt-1">{{ $cms['kontak_jam'] }}</p></div>
                    </div>
                </div>

                <!-- Form Pesan -->
                <div class="card-facility p-8">
                    @if(session('kontak_success'))
                        <div class="rounded-2xl px-5 py-4 mb-5 flex gap-3" style="background:#D1FAE5; color:#065F46;">
                            <span class="text-xl">✅</span>
                            <p class="font-semibold text-sm">{{ session('kontak_success') }}</p>
                        </div>
                    @endif
                    <h3 class="text-lg font-extrabold mb-5" style="color:#1F2937;">Kirim Pesan</h3>
                    <form method="POST" action="{{ route('guest.kontak.send') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="input-label">Nama Anda *</label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required class="input-field" placeholder="Nama Anda">
                            @error('nama')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="input-field" placeholder="email@anda.com">
                            @error('email')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Pesan *</label>
                            <textarea name="pesan" required rows="5" class="input-field" placeholder="Tuliskan pertanyaan atau pesan Anda...">{{ old('pesan') }}</textarea>
                            @error('pesan')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-cta w-full justify-center">
                            📩 Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-guest-public>
