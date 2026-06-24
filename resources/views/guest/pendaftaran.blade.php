<x-guest-layout max-width="max-w-lg">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <h2 class="text-2xl font-bold mb-1" style="color: #2C2C2C;">Pendaftaran Orang Tua</h2>
    <p class="text-sm mb-6" style="color: #9E9790;">Daftarkan akun orang tua dan data anak untuk bergabung dengan sekolah mitra SIPP. Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold underline cursor-pointer" style="color: #1A6B6B;">Masuk di sini</a>.</p>

    @include('auth.partials.pendaftaran-form', [
        'action' => route('guest.pendaftaran.store'),
        'sekolahs' => $sekolahs,
    ])
</x-guest-layout>
