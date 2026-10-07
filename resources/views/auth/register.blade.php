<x-guest-layout max-width="max-w-xl">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Daftar</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">Daftar orang tua</h2>
    <p class="text-sm text-muted-foreground mb-6">Lengkapi data Anda dan data anak. Akun aktif setelah disetujui admin sekolah.</p>

    @include('auth.partials.pendaftaran-form', [
        'action' => route('register'),
        'sekolahs' => $sekolahs,
    ])
</x-guest-layout>
