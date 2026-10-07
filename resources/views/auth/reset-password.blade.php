<x-guest-layout>
    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Reset</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">Kata sandi baru</h2>
    <p class="text-sm text-muted-foreground mb-6">Buat kata sandi baru untuk akun Anda.</p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Kata Sandi Baru')" />
            <x-auth-field-wrap icon="fa-solid fa-lock">
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            </x-auth-field-wrap>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Ulangi Kata Sandi')" />
            <x-auth-field-wrap icon="fa-solid fa-lock">
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            </x-auth-field-wrap>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <x-ascent-button>
            <i class="fa-solid fa-check" aria-hidden="true"></i>
            Simpan kata sandi
        </x-ascent-button>
    </form>
</x-guest-layout>
