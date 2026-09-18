@php $cms = \App\Support\GuestCms::data(); @endphp
<x-guest-public :cms="$cms" title="Daftar Sekolah">
    @include('guest.partials.page-header', [
        'badge' => 'Untuk Sekolah PAUD',
        'title' => 'Daftarkan Sekolah Anda',
        'subtitle' => 'Isi formulir di bawah. Tim kami akan meninjau pendaftaran sebelum akun admin diaktifkan.',
    ])

    <section class="guest-section !pt-10 !pb-20">
        <div class="max-w-lg mx-auto px-4 sm:px-6">
            <div class="guest-card p-6 sm:p-8" data-guest-animate="fade-up">
                <div class="mb-6 rounded-2xl px-4 py-3 text-sm leading-relaxed" style="background: var(--guest-sage-light); color: var(--guest-sage-dark);">
                    Setelah disetujui, Anda akan menerima akses sebagai <strong>Admin Sekolah</strong> untuk mengelola data siswa, guru, dan operasional PAUD.
                </div>

                @if($errors->any())
                    <div class="mb-6 rounded-2xl px-4 py-3 text-sm" style="background: #FAD7D2; color: #7a2e2e;">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('guest.daftar-sekolah.store') }}" class="space-y-8">
                    @csrf

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide mb-4" style="color: var(--guest-text-muted);">Data Sekolah</p>
                        <div class="space-y-4">
                            <div>
                                <label class="guest-label" for="sekolah_name">Nama sekolah</label>
                                <input id="sekolah_name" name="sekolah_name" type="text" class="guest-input" value="{{ old('sekolah_name') }}" placeholder="Contoh: PAUD Ceria Cendekia" required>
                                @error('sekolah_name')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="guest-label" for="sekolah_address">Alamat</label>
                                <textarea id="sekolah_address" name="sekolah_address" rows="3" class="guest-input !rounded-3xl resize-y min-h-[5rem]" placeholder="Alamat lengkap sekolah">{{ old('sekolah_address') }}</textarea>
                                @error('sekolah_address')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="guest-label" for="sekolah_phone">Telepon sekolah</label>
                                <input id="sekolah_phone" name="sekolah_phone" type="text" class="guest-input" value="{{ old('sekolah_phone') }}" placeholder="08xx atau (021) xxx">
                                @error('sekolah_phone')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t" style="border-color: var(--guest-border);">
                        <p class="text-xs font-bold uppercase tracking-wide mb-4 mt-2" style="color: var(--guest-text-muted);">Admin Sekolah</p>
                        <div class="space-y-4">
                            <div>
                                <label class="guest-label" for="admin_name">Nama lengkap</label>
                                <input id="admin_name" name="admin_name" type="text" class="guest-input" value="{{ old('admin_name') }}" placeholder="Nama admin / kepala sekolah" required>
                                @error('admin_name')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="min-w-0">
                                    <label class="guest-label" for="admin_email">Email login</label>
                                    <input id="admin_email" name="admin_email" type="email" class="guest-input" value="{{ old('admin_email') }}" placeholder="admin@sekolah.com" required>
                                    @error('admin_email')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="min-w-0">
                                    <label class="guest-label" for="admin_phone">Telepon</label>
                                    <input id="admin_phone" name="admin_phone" type="text" class="guest-input" value="{{ old('admin_phone') }}" placeholder="08xx">
                                    @error('admin_phone')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="min-w-0">
                                    <label class="guest-label" for="password">Password</label>
                                    <input id="password" name="password" type="password" class="guest-input" required autocomplete="new-password">
                                    @error('password')<p class="text-xs mt-1 text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="min-w-0">
                                    <label class="guest-label" for="password_confirmation">Konfirmasi password</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="guest-input" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" class="guest-btn guest-btn-primary w-full sm:flex-1 cursor-pointer">Kirim pendaftaran</button>
                        <a href="{{ route('guest.beranda') }}" class="guest-btn guest-btn-secondary w-full sm:w-auto justify-center text-center">Kembali</a>
                    </div>

                    <p class="text-center text-xs" style="color: var(--guest-text-muted);">
                        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: var(--guest-sage-dark);">Masuk di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </section>
</x-guest-public>
