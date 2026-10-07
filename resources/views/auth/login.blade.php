<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Masuk</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">Selamat datang kembali</h2>
    <p class="text-sm text-muted-foreground mb-6">Masuk ke akun Anda untuk melanjutkan</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-auth-field-wrap icon="fa-solid fa-envelope">
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="email@contoh.com" />
            </x-auth-field-wrap>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-auth-field-wrap icon="fa-solid fa-lock">
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </x-auth-field-wrap>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="mb-10 flex items-center justify-between gap-3 flex-wrap">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-[#F2F2F2]" name="remember">
                <span class="text-sm text-muted-foreground">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-green-foreground hover:underline">Lupa kata sandi?</a>
            @endif
        </div>

        <x-ascent-button>
            <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
            Masuk ke Sistem
        </x-ascent-button>
        <div class="text-center pt-1">
            <a href="{{ route('guest.pendaftaran') }}" class="text-sm font-semibold text-green-foreground hover:underline">Belum punya akun? Daftar</a>
        </div>
    </form>
</x-guest-layout>
