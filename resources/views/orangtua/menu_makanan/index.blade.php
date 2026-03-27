<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Jadwal Menu Makanan</h2>
        </div>
    </x-slot>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">🍱 Jadwal Menu Mingguan</h3>
                <p class="section-subtitle">Menu harian yang disiapkan sekolah untuk putra/putri Anda</p>
            </div>
            <div class="divide-y" style="divide-color:rgba(0,0,0,0.05);">
                @forelse($menus as $m)
                <div class="px-6 py-5 flex gap-5">
                    @if($m->photo)
                    <div class="h-20 w-20 rounded-xl overflow-hidden shrink-0"><img src="{{ Storage::url($m->photo) }}" class="w-full h-full object-cover"></div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-bold" style="color:#1A6B6B;">{{ \Carbon\Carbon::parse($m->date)->format('l, d M Y') }}</p>
                            @if(\Carbon\Carbon::parse($m->date)->isToday())<span class="badge badge-green">Hari Ini</span>@endif
                        </div>
                        <p class="font-semibold text-sm whitespace-pre-line" style="color:#2C2C2C;">{{ $m->menu }}</p>
                        @if($m->nutrition_info)<p class="text-xs mt-1" style="color:#9E9790;">ℹ️ {{ $m->nutrition_info }}</p>@endif
                    </div>
                </div>
                @empty
                <div class="px-6 py-16 text-center">
                    <p class="text-sm" style="color:#9E9790;">Belum ada jadwal menu yang tersedia.</p>
                </div>
                @endforelse
            </div>
            @if($menus->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $menus->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
