<x-guest-layout max-width="max-w-xl">
    <h2 class="text-2xl font-bold mb-1" style="color: #2C2C2C;">Daftar Orang Tua</h2>
    <p class="text-sm mb-6" style="color: #9E9790;">Lengkapi data Anda dan data anak. Akun akan aktif setelah disetujui admin sekolah.</p>

    @if($sekolahs->isEmpty())
        <div class="mb-5 rounded-xl px-4 py-3 text-sm" style="background: #FAD7D2; color: #7a2e2e;">
            Belum ada data sekolah di sistem. Hubungi pengelola PAUD atau jalankan seeder agar pendaftaran bisa diproses.
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nama orang tua / wali" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="sekolah_id" value="Sekolah PAUD" />
            <select id="sekolah_id" name="sekolah_id" class="input-field" required @disabled($sekolahs->isEmpty())>
                <option value="">— Pilih sekolah —</option>
                @foreach($sekolahs as $s)
                    <option value="{{ $s->id }}" @selected(old('sekolah_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('sekolah_id')" class="mt-1" />
        </div>

        <div class="pt-2 border-t" style="border-color: rgba(0,0,0,0.08);">
            <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: #6B6560;">Data anak</p>
            <div class="space-y-4">
                <div>
                    <x-input-label for="anak_name" value="Nama anak" />
                    <x-text-input id="anak_name" type="text" name="anak_name" :value="old('anak_name')" required />
                    <x-input-error :messages="$errors->get('anak_name')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="anak_dob" value="Tanggal lahir anak" />
                    <x-text-input id="anak_dob" type="date" name="anak_dob" :value="old('anak_dob')" required max="{{ now()->subDay()->format('Y-m-d') }}" />
                    <x-input-error :messages="$errors->get('anak_dob')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="catatan_ortu" value="Catatan (opsional)" />
                    <textarea id="catatan_ortu" name="catatan_ortu" rows="2" class="input-field" placeholder="Mis. alergi, nama panggilan…">{{ old('catatan_ortu') }}</textarea>
                    <x-input-error :messages="$errors->get('catatan_ortu')" class="mt-1" />
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Ulangi kata sandi" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
            <a class="text-sm font-medium hover:underline text-center sm:text-left" style="color: #1A6B6B;" href="{{ route('login') }}">
                Sudah punya akun? Masuk
            </a>
            <x-primary-button class="w-full sm:w-auto justify-center py-3" @disabled($sekolahs->isEmpty())>
                Kirim pendaftaran
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
