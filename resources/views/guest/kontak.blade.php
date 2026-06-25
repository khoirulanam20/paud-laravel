@php use App\Support\GuestWhatsApp; @endphp
<x-guest-public :cms="$cms" title="Kontak">
    @include('guest.partials.page-header', [
        'badge' => 'Hubungi Kami',
        'title' => 'Minta Demo & Penawaran SIPP',
        'subtitle' => 'Isi formulir di bawah — permintaan akan dibuka langsung di WhatsApp kami.',
    ])

    <section class="guest-section">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-4" data-guest-stagger>
                    @foreach([
                        ['label' => 'Alamat', 'value' => $cms['kontak_alamat'] ?? '', 'illustration' => 'kontak.alamat', 'wa' => false],
                        ['label' => 'WhatsApp', 'value' => $cms['kontak_telepon'] ?? GuestWhatsApp::DISPLAY, 'illustration' => 'kontak.telepon', 'wa' => true],
                        ['label' => 'Email', 'value' => $cms['kontak_email'] ?? '', 'illustration' => 'kontak.email', 'wa' => false],
                        ['label' => 'Jam Operasional', 'value' => $cms['kontak_jam'] ?? '', 'illustration' => 'kontak.jam', 'wa' => false],
                    ] as $info)
                    @if($info['value'])
                    <div class="guest-card flex gap-4 items-start" data-guest-stagger-item>
                        <div class="guest-service-blob shrink-0" style="background: var(--guest-sage-light); width: 3rem; height: 3rem; margin: 0;">
                            @include('guest.partials.illustration', [
                                'name' => $info['illustration'],
                                'alt' => $info['label'],
                                'class' => 'guest-illustration h-8 w-8',
                            ])
                        </div>
                        <div>
                            <h4 class="font-bold text-sm guest-heading">{{ $info['label'] }}</h4>
                            @if($info['wa'])
                                <a href="{{ GuestWhatsApp::url(GuestWhatsApp::demoIntro()) }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold mt-0.5 inline-block hover:underline" style="color: var(--guest-sage);">{{ $info['value'] }}</a>
                            @elseif($info['label'] === 'Email' && $info['value'])
                                <a href="mailto:{{ $info['value'] }}" class="text-sm text-[var(--guest-text-muted)] mt-0.5 inline-block hover:underline">{{ $info['value'] }}</a>
                            @else
                                <p class="text-sm text-[var(--guest-text-muted)] mt-0.5">{{ $info['value'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                <div class="guest-card" data-guest-animate="fade-up">
                    <h3 class="text-lg guest-heading font-bold mb-1">Formulir Permintaan Demo</h3>
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
                            <textarea id="kontak-pesan" name="pesan" required rows="5" class="guest-input !rounded-3xl" placeholder="Contoh: Lembaga dengan 3 cabang, butuh modul keuangan PSAK dan portal orang tua...">{{ old('pesan') }}</textarea>
                            @error('pesan')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="guest-btn guest-btn-primary w-full cursor-pointer">Kirim via WhatsApp</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('guest.partials.wave-divider', ['fill' => 'var(--guest-bg)'])
</x-guest-public>
