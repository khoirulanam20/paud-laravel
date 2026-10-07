<x-guest-layout>
    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Reset</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">Lupa kata sandi?</h2>
    <p class="text-sm text-muted-foreground mb-6">
        Masukkan email terdaftar. Kami akan mengirim tautan reset kata sandi ke email Anda.
    </p>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-auth-field-wrap icon="fa-solid fa-envelope">
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="email@contoh.com" />
            </x-auth-field-wrap>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <x-ascent-button>
            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
            Kirim tautan reset
        </x-ascent-button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-green-foreground hover:underline">Kembali ke masuk</a>
        </div>
    </form>
</x-guest-layout>
