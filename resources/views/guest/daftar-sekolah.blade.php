@php
    use App\Support\GuestFeatures;
    $cms = \App\Support\GuestCms::data();
@endphp
<x-guest-public :cms="$cms" title="Daftar Sekolah">
    @include('guest.partials.page-header', [
        'badge' => 'Untuk Sekolah PAUD',
        'title' => 'Daftarkan Sekolah Anda',
        'subtitle' => 'Mulai digitalisasi PAUD dalam hitungan menit — tim kami akan meninjau pendaftaran sebelum akun admin diaktifkan.',
    ])

    <section class="guest-section relative !pt-8 !pb-20 overflow-hidden">
        @include('guest.partials.doodles', ['variant' => 'hero'])

        <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                {{-- Sidebar: value prop + alur --}}
                <aside class="lg:col-span-5 space-y-5" data-guest-stagger>
                    <div class="guest-card p-6 text-center lg:text-left" data-guest-stagger-item>
                        @include('guest.partials.illustration', [
                            'name' => 'placeholder.hero',
                            'alt' => 'SIPP untuk sekolah',
                            'class' => 'guest-illustration guest-illustration-hero mx-auto lg:mx-0 mb-4',
                        ])
                        <h2 class="text-lg guest-heading font-bold text-[var(--guest-text)]">Satu platform untuk seluruh operasional PAUD</h2>
                        <p class="mt-2 text-sm text-[var(--guest-text-muted)] leading-relaxed">
                            Setelah disetujui, Anda login sebagai <strong class="text-[var(--guest-text)]">Admin Sekolah</strong> dan langsung bisa mengelola siswa, guru, serta komunikasi orang tua.
                        </p>
                    </div>

                    <div class="guest-card p-6 space-y-3" data-guest-stagger-item>
                        <h3 class="text-sm font-bold uppercase tracking-wide text-[var(--guest-text-muted)]">Yang Anda dapatkan</h3>
                        @foreach([
                            'Portal orang tua & presensi real-time',
                            'Pembayaran, kegiatan, dan menu makanan',
                            'Monev & laporan dengan bantuan AI',
                        ] as $benefit)
                        <div class="guest-benefit-item">
                            <span class="guest-benefit-check" aria-hidden="true">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span>{{ $benefit }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="guest-card p-6 space-y-4" data-guest-stagger-item>
                        <h3 class="text-sm font-bold uppercase tracking-wide text-[var(--guest-text-muted)]">Alur pendaftaran</h3>
                        @foreach(GuestFeatures::onboardingSteps() as $step)
                        <div class="flex gap-3 items-start">
                            <span class="guest-form-step">{{ $step['step'] }}</span>
                            <div>
                                <p class="text-sm font-bold text-[var(--guest-text)]">{{ $step['title'] }}</p>
                                <p class="text-xs text-[var(--guest-text-muted)] mt-0.5 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <p class="text-center lg:text-left text-xs text-[var(--guest-text-muted)] px-2" data-guest-stagger-item>
                        Butuh bantuan? <a href="{{ route('guest.kontak') }}" class="font-semibold hover:underline" style="color: var(--guest-sage-dark);">Hubungi tim kami</a>
                    </p>
                </aside>

                {{-- Form --}}
                <div class="lg:col-span-7 guest-card p-6 sm:p-8 lg:p-10 shadow-sm" data-guest-animate="fade-up">
                    <div class="mb-6">
                        <h2 class="text-xl guest-heading font-bold text-[var(--guest-text)]">Formulir Pendaftaran</h2>
                        <p class="mt-1 text-sm text-[var(--guest-text-muted)]">Semua field bertanda <span class="text-red-500">*</span> wajib diisi.</p>
                    </div>

                    <div class="guest-notice mb-6">
                        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Pendaftaran akan ditinjau superadmin. Anda akan bisa login setelah sekolah disetujui.</span>
                    </div>

                    @if($errors->any())
                        <div class="guest-notice guest-notice-error mb-6">
                            <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('guest.daftar-sekolah.store') }}" class="space-y-6">
                        @csrf

                        <div class="guest-form-section">
                            <div class="guest-form-section-title">
                                <span class="guest-form-step">1</span>
                                <span>Data Sekolah</span>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="guest-label" for="sekolah_name">Nama sekolah <span class="text-red-500">*</span></label>
                                    <input id="sekolah_name" name="sekolah_name" type="text" class="guest-input" value="{{ old('sekolah_name') }}" placeholder="Contoh: PAUD Ceria Cendekia" required autofocus>
                                    @error('sekolah_name')<p class="text-xs mt-1.5 text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="guest-label" for="sekolah_address">Alamat</label>
                                    <textarea id="sekolah_address" name="sekolah_address" rows="3" class="guest-textarea" placeholder="Alamat lengkap sekolah">{{ old('sekolah_address') }}</textarea>
                                    @error('sekolah_address')<p class="text-xs mt-1.5 text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="guest-label" for="sekolah_phone">Telepon sekolah</label>
                                    <input id="sekolah_phone" name="sekolah_phone" type="tel" class="guest-input" value="{{ old('sekolah_phone') }}" placeholder="08xx atau (021) xxx" inputmode="tel">
                                    @error('sekolah_phone')<p class="text-xs mt-1.5 text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="guest-form-section">
                            <div class="guest-form-section-title">
                                <span class="guest-form-step">2</span>
                                <span>Akun Admin Sekolah</span>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="guest-label" for="admin_name">Nama lengkap <span class="text-red-500">*</span></label>
                                    <input id="admin_name" name="admin_name" type="text" class="guest-input" value="{{ old('admin_name') }}" placeholder="Nama admin / kepala sekolah" required>
                                    @error('admin_name')<p class="text-xs mt-1.5 text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="min-w-0">
                                        <label class="guest-label" for="admin_email">Email login <span class="text-red-500">*</span></label>
                                        <input id="admin_email" name="admin_email" type="email" class="guest-input" value="{{ old('admin_email') }}" placeholder="admin@sekolah.com" required autocomplete="email">
                                        @error('admin_email')<p class="text-xs mt-1.5 text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="min-w-0">
                                        <label class="guest-label" for="admin_phone">Telepon</label>
                                        <input id="admin_phone" name="admin_phone" type="tel" class="guest-input" value="{{ old('admin_phone') }}" placeholder="08xx" inputmode="tel">
                                        @error('admin_phone')<p class="text-xs mt-1.5 text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="min-w-0">
                                        <label class="guest-label" for="password">Password <span class="text-red-500">*</span></label>
                                        <input id="password" name="password" type="password" class="guest-input" required autocomplete="new-password" placeholder="Min. 8 karakter">
                                        @error('password')<p class="text-xs mt-1.5 text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="min-w-0">
                                        <label class="guest-label" for="password_confirmation">Konfirmasi password <span class="text-red-500">*</span></label>
                                        <input id="password_confirmation" name="password_confirmation" type="password" class="guest-input" required autocomplete="new-password" placeholder="Ulangi password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-1">
                            <a href="{{ route('guest.beranda') }}" class="guest-btn guest-btn-secondary w-full sm:w-auto justify-center text-center">Kembali</a>
                            <button type="submit" class="guest-btn guest-btn-primary w-full sm:flex-1 cursor-pointer text-base py-3">
                                Kirim Pendaftaran
                            </button>
                        </div>

                        <p class="text-center text-sm text-[var(--guest-text-muted)] pt-1">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: var(--guest-sage-dark);">Masuk di sini</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-guest-public>
