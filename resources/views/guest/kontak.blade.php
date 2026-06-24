<x-guest-public :cms="$cms" title="Kontak">
    @include('guest.partials.page-header', [
        'badge' => 'Hubungi Kami',
        'title' => 'Minta Demo & Penawaran SIPP',
        'subtitle' => 'Isi formulir di bawah atau hubungi langsung — tim kami akan merespons segera.',
    ])

    <section class="guest-section">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-4" data-guest-stagger>
                    @foreach([
                        ['label' => 'Alamat', 'value' => $cms['kontak_alamat'] ?? '', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['label' => 'Telepon', 'value' => $cms['kontak_telepon'] ?? '', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                        ['label' => 'Email', 'value' => $cms['kontak_email'] ?? '', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['label' => 'Jam Operasional', 'value' => $cms['kontak_jam'] ?? '', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ] as $info)
                    @if($info['value'])
                    <div class="guest-card flex gap-4" data-guest-stagger-item>
                        <div class="guest-icon-wrap">
                            @include('guest.partials.icon', ['path' => $info['icon']])
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">{{ $info['label'] }}</h4>
                            <p class="text-sm text-[var(--guest-text-muted)] mt-0.5">{{ $info['value'] }}</p>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                <div class="guest-card" data-guest-animate="fade-up">
                    @if(session('kontak_success'))
                    <div class="rounded-xl px-4 py-3 mb-5 flex gap-3 text-sm font-semibold" style="background: #D1FAE5; color: #065F46;">
                        @include('guest.partials.icon', ['path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'class' => 'h-5 w-5 shrink-0'])
                        {{ session('kontak_success') }}
                    </div>
                    @endif
                    <h3 class="text-lg font-extrabold mb-1">Formulir Permintaan Demo</h3>
                    <p class="text-sm text-[var(--guest-text-muted)] mb-5">Ceritakan kebutuhan lembaga Anda — jumlah cabang, jumlah siswa, dan modul yang diminati.</p>
                    <form method="POST" action="{{ route('guest.kontak.send') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="guest-label" for="kontak-nama">Nama *</label>
                            <input id="kontak-nama" type="text" name="nama" value="{{ old('nama') }}" required class="guest-input" placeholder="Nama lengkap">
                            @error('nama')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="guest-label" for="kontak-email">Email *</label>
                            <input id="kontak-email" type="email" name="email" value="{{ old('email') }}" required class="guest-input" placeholder="email@lembaga.com">
                            @error('email')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="guest-label" for="kontak-pesan">Pesan *</label>
                            <textarea id="kontak-pesan" name="pesan" required rows="5" class="guest-input" placeholder="Contoh: Lembaga dengan 3 cabang, butuh modul keuangan PSAK dan portal orang tua...">{{ old('pesan') }}</textarea>
                            @error('pesan')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="guest-btn guest-btn-primary w-full cursor-pointer">Kirim Permintaan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-guest-public>
