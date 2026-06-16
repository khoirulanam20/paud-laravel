<x-guest-layout max-width="max-w-lg">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <h2 class="text-2xl font-bold mb-1" style="color: #2C2C2C;">Daftar Orang Tua</h2>
    <p class="text-sm mb-6" style="color: #9E9790;">Lengkapi data Anda dan data anak untuk akun baru. Sudah punya akun? Gunakan opsi tambah anak setelah masuk.</p>

    @include('auth.partials.pendaftaran-form', [
        'action' => route('guest.pendaftaran.store'),
        'sekolahs' => $sekolahs,
    ])
</x-guest-layout>
