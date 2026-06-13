@props(['showName' => false])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3 shrink-0']) }}>
    @if($showName)
        <div class="text-right hidden md:block">
            <div class="text-sm font-semibold leading-tight text-[#2C2C2C]">{{ Auth::user()->name }}</div>
            <div class="text-xs text-[#9E9790]">{{ $roleLabel ?? '' }}</div>
        </div>
    @endif
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
