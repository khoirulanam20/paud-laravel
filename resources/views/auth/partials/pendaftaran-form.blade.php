{{-- Form pendaftaran orang tua + anak; dipakai di /register dan /pendaftaran ($action, $sekolahs) --}}

@if($sekolahs->isEmpty())
    <div class="mb-5 rounded-xl px-4 py-3 text-sm" style="background: #FAD7D2; color: #7a2e2e;">
        Belum ada data sekolah di sistem. Hubungi pengelola PAUD atau jalankan seeder agar pendaftaran bisa diproses.
    </div>
@endif

<div class="mb-5 rounded-xl px-4 py-3 text-sm" style="background: #EDE8DF; color: #6B6560;">
    Pendaftaran akan ditinjau oleh Admin Sekolah. Anda bisa masuk setelah akun disetujui.
</div>

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf

    <div>
        <x-input-label for="name" :value="__('Nama orang tua / wali')" />
        <x-text-input id="name" type="text" name="name" :value="old('name')" required autocomplete="name" placeholder="Nama lengkap wali" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Alamat Email')" />
        <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="email@contoh.com" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="sekolah_id" :value="__('Sekolah PAUD')" />
        <select id="sekolah_id" name="sekolah_id" class="input-field" required{{ $sekolahs->isEmpty() ? ' disabled' : '' }}>
            <option value="">— Pilih sekolah —</option>
            @foreach($sekolahs as $s)
                <option value="{{ $s->id }}" @selected(old('sekolah_id') == $s->id)>
                    {{ $s->name }}{{ $s->address ? ' — '.$s->address : '' }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('sekolah_id')" class="mt-1" />
    </div>

    <div class="pt-2 border-t" style="border-color: rgba(0,0,0,0.08);">
        <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: #6B6560;">Data anak</p>
        <div class="space-y-5">
            <div>
                <x-input-label for="anak_name" :value="__('Nama anak')" />
                <x-text-input id="anak_name" type="text" name="anak_name" :value="old('anak_name')" required placeholder="Nama sesuai akta / panggilan" />
                <x-input-error :messages="$errors->get('anak_name')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="anak_dob" :value="__('Tanggal lahir anak')" />
                <x-text-input id="anak_dob" type="date" name="anak_dob" :value="old('anak_dob')" required max="{{ now()->subDay()->format('Y-m-d') }}" />
                <x-input-error :messages="$errors->get('anak_dob')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="catatan_ortu" :value="__('Catatan (opsional)')" />
                <textarea id="catatan_ortu" name="catatan_ortu" rows="2" class="input-field" placeholder="Mis. alergi, kebutuhan khusus…">{{ old('catatan_ortu') }}</textarea>
                <x-input-error :messages="$errors->get('catatan_ortu')" class="mt-1" />
            </div>
        </div>
    </div>

    <div>
        <x-input-label for="password" :value="__('Kata Sandi')" />
        <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Ulangi kata sandi')" />
        <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
    </div>

    <x-primary-button class="w-full justify-center py-3" :disabled="$sekolahs->isEmpty()">
        Kirim pendaftaran
    </x-primary-button>
    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm font-medium hover:underline" style="color: #1A6B6B;">Sudah punya akun? Masuk</a>
    </div>
</form>
