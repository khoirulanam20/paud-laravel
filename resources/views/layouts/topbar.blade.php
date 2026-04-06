<header class="bg-[#FAF6F0] border-b border-black/5 shadow-[0_2px_8px_rgba(0,0,0,0.04)] sticky top-0 z-30 h-16 shrink-0 flex items-center justify-between px-3 md:px-4 sm:px-6 lg:px-8">
    <div class="flex items-center">
        <!-- Logo for mobile (so it doesn't look empty on the left) -->
        <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center gap-2.5">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center bg-[#1A6B6B] shadow-sm">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <span class="font-bold text-sm text-[#2C2C2C]">Bintang Kecil</span>
        </a>
    </div>

    <!-- User Menu -->
    <div class="flex items-center gap-3 shrink-0">
        <div class="text-right hidden md:block">
            <div class="text-sm font-semibold leading-tight text-[#2C2C2C]">{{ Auth::user()->name }}</div>
            <div class="text-xs text-[#9E9790]">{{ $roleLabel }}</div>
        </div>
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button type="button" class="rounded-xl overflow-hidden shrink-0 transition-all hover:opacity-90 shadow-[2px_3px_8px_rgba(26,107,107,0.30)] focus:outline-none focus:ring-2 focus:ring-[#1A6B6B] focus:ring-offset-2 ring-offset-[#FAF6F0]" aria-label="Menu profil">
                    <x-foto-profil :path="Auth::user()->hasRole('Orang Tua') ? null : ($topbarProfilePhotoPath ?? null)" :name="Auth::user()->name" size="nav" rounded="xl" class="pointer-events-none" />
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    Profil Saya
                </x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
