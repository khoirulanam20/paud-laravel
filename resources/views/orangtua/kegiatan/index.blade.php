<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Jurnal Kegiatan Anak Saya</h2>
        </div>
    </x-slot>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">📸 Jurnal Kegiatan Harian</h3>
                <p class="section-subtitle">Semua kejadian seru dan pembelajaran anak Anda di sekolah</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse($kegiatans as $k)
                <div class="rounded-2xl overflow-hidden border" style="background:#FAF6F0; border-color:rgba(0,0,0,0.07);">
                    @if($k->photo)
                        <div class="h-40 overflow-hidden"><img src="{{ Storage::url($k->photo) }}" class="w-full h-full object-cover"></div>
                    @else
                        <div class="h-40 flex items-center justify-center" style="background:#EDE8DF;"><svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:#9E9790;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                    @endif
                    <div class="p-4">
                        <p class="text-xs font-semibold mb-1" style="color:#1A6B6B;">{{ \Carbon\Carbon::parse($k->date)->format('d M Y') }}</p>
                        <h4 class="font-bold text-sm mb-1" style="color:#2C2C2C;">{{ $k->title }}</h4>
                        <p class="text-sm line-clamp-2" style="color:#9E9790;">{{ $k->description }}</p>
                    </div>
                </div>
                @empty
                <div class="col-span-3 py-16 text-center">
                    <div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#EDE8DF;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#9E9790;"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <p class="text-sm" style="color:#9E9790;">Belum ada jurnal kegiatan tercatat.</p>
                </div>
                @endforelse
            </div>
            @if($kegiatans->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $kegiatans->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
