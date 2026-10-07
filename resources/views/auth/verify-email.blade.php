<x-guest-layout>
    <p class="text-secondary-foreground font-bubblegum-sans text-[19px]">Verifikasi</p>
    <h2 class="text-2xl lg:text-[28px] font-bold leading-tight mt-1 mb-1">Konfirmasi email</h2>
    <p class="text-sm text-muted-foreground mb-6">
        Terima kasih sudah mendaftar. Klik tautan verifikasi di email Anda. Jika belum menerima, kami bisa mengirim ulang.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert-success mb-5">
            <i class="fa-solid fa-circle-check shrink-0 mt-0.5" aria-hidden="true"></i>
            <span>Tautan verifikasi baru telah dikirim ke email Anda.</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <x-ascent-button>
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                Kirim ulang email
            </x-ascent-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full sm:w-auto text-sm font-semibold text-muted-foreground hover:text-green-foreground py-3 px-4 rounded-full border border-[#F2F2F2] bg-background transition-colors">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
