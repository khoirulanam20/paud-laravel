<div class="auth-callout auth-callout--warm" role="note">
    <span class="auth-callout__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></span>
    <span>Pendaftaran ditinjau superadmin. Anda bisa login setelah sekolah disetujui.</span>
</div>

@if($errors->any())
<div class="auth-callout auth-callout--danger" role="alert">
    <span class="auth-callout__icon"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i></span>
    <ul class="list-disc pl-4 space-y-1 m-0">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('guest.daftar-sekolah.store') }}" class="space-y-6">
    @csrf

    <div class="guest-form-section pt-0 border-0">
        <div class="flex items-center gap-3 mb-4">
            <span class="inline-flex w-8 h-8 rounded-full bg-primary text-cream-foreground items-center justify-center font-bold text-sm shrink-0">1</span>
            <p class="text-sm font-bold uppercase tracking-wide text-muted-foreground">Data sekolah</p>
        </div>
        <div class="space-y-4">
            <div>
                <x-input-label for="sekolah_name" value="Nama sekolah *" />
                <x-text-input id="sekolah_name" name="sekolah_name" type="text" :value="old('sekolah_name')" required autofocus placeholder="Contoh: PAUD Ceria Cendekia" />
                <x-input-error :messages="$errors->get('sekolah_name')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="sekolah_address" value="Alamat" />
                <textarea id="sekolah_address" name="sekolah_address" rows="3" class="input-field !rounded-[10px] min-h-24" placeholder="Alamat lengkap sekolah">{{ old('sekolah_address') }}</textarea>
                <x-input-error :messages="$errors->get('sekolah_address')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="sekolah_phone" value="Telepon sekolah" />
                <x-text-input id="sekolah_phone" name="sekolah_phone" type="tel" :value="old('sekolah_phone')" inputmode="tel" placeholder="08xx atau (021) xxx" />
                <x-input-error :messages="$errors->get('sekolah_phone')" class="mt-1" />
            </div>
        </div>
    </div>

    <div class="mb-10 pt-2 border-t border-[#F2F2F2]">
        <div class="flex items-center gap-3 mb-4 mt-6">
            <span class="inline-flex w-8 h-8 rounded-full bg-primary text-cream-foreground items-center justify-center font-bold text-sm shrink-0">2</span>
            <p class="text-sm font-bold uppercase tracking-wide text-muted-foreground">Akun admin sekolah</p>
        </div>
        <div class="space-y-4">
            <div>
                <x-input-label for="admin_name" value="Nama lengkap *" />
                <x-text-input id="admin_name" name="admin_name" type="text" :value="old('admin_name')" required placeholder="Nama admin / kepala sekolah" />
                <x-input-error :messages="$errors->get('admin_name')" class="mt-1" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="min-w-0">
                    <x-input-label for="admin_email" value="Email login *" />
                    <x-text-input id="admin_email" name="admin_email" type="email" :value="old('admin_email')" required autocomplete="email" placeholder="admin@sekolah.com" />
                    <x-input-error :messages="$errors->get('admin_email')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <x-input-label for="admin_phone" value="Telepon" />
                    <x-text-input id="admin_phone" name="admin_phone" type="tel" :value="old('admin_phone')" inputmode="tel" placeholder="08xx" />
                    <x-input-error :messages="$errors->get('admin_phone')" class="mt-1" />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="min-w-0">
                    <x-input-label for="password" value="Password *" />
                    <x-text-input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Min. 8 karakter" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <x-input-label for="password_confirmation" value="Ulangi password *" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi password" />
                </div>
            </div>
        </div>
    </div>

    <x-ascent-button>
        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
        Kirim pendaftaran
    </x-ascent-button>
    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-green-foreground hover:underline">Sudah punya akun? Masuk</a>
    </div>
</form>
