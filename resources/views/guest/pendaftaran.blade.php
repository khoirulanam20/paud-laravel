<x-guest-layout max-width="max-w-xl">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Pendaftaran</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">{{ $cms['page_pendaftaran_h2'] }}</h2>
    <p class="text-sm text-muted-foreground mb-6">
        {{ $cms['page_pendaftaran_intro'] }}
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-green-foreground hover:underline">Masuk di sini</a>.
    </p>

    @include('auth.partials.pendaftaran-form', [
        'action' => route('guest.pendaftaran.store'),
        'sekolahs' => $sekolahs,
    ])
</x-guest-layout>
