<x-guest-layout>
    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Keamanan</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">Konfirmasi kata sandi</h2>
    <p class="text-sm text-muted-foreground mb-6">
        Area ini membutuhkan konfirmasi. Masukkan kata sandi Anda untuk melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-auth-field-wrap icon="fa-solid fa-lock">
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            </x-auth-field-wrap>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <x-ascent-button>
            <i class="fa-solid fa-check" aria-hidden="true"></i>
            Konfirmasi
        </x-ascent-button>
    </form>
</x-guest-layout>
