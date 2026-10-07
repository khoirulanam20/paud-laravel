<x-guest-layout max-width="max-w-xl">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Daftar Sekolah</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">Pendaftaran Akun Sekolah</h2>

    @include('auth.partials.daftar-sekolah-form')
</x-guest-layout>
