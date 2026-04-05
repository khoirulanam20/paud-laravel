<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Jadwal Menu Makanan</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" 
         x-data="{ showImageModal: false, activeImage: null }">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">🍱 Jadwal Menu Mingguan</h3>
                <p class="section-subtitle">Menu harian yang disiapkan sekolah untuk putra/putri Anda</p>
            </div>
            <div class="divide-y" style="divide-color:rgba(0,0,0,0.05);">
                @forelse($menus as $m)
                <div class="px-6 py-5 flex gap-5">
                    <div class="flex gap-2 shrink-0">
                        @if($m->photo)
                            <div class="h-20 w-20 rounded-xl overflow-hidden border border-gray-100 cursor-pointer hover:opacity-90 transition" 
                                 @click="activeImage = '{{ Storage::url($m->photo) }}'; showImageModal = true">
                                <img src="{{ Storage::url($m->photo) }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        @if($m->photo_kegiatan)
                            <div class="h-20 w-20 rounded-xl overflow-hidden border border-gray-100 cursor-pointer hover:opacity-90 transition"
                                 @click="activeImage = '{{ Storage::url($m->photo_kegiatan) }}'; showImageModal = true">
                                <img src="{{ Storage::url($m->photo_kegiatan) }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-bold" style="color:#1A6B6B;">{{ \Carbon\Carbon::parse($m->date)->format('l, d M Y') }}</p>
                            @if(\Carbon\Carbon::parse($m->date)->isToday())<span class="badge badge-green">Hari Ini</span>@endif
                        </div>
                        <p class="font-semibold text-sm whitespace-pre-line" style="color:#2C2C2C;">{{ $m->menu }}</p>
                        @if($m->nutrition_info)<p class="text-xs mt-1" style="color:#9E9790;">ℹ️ {{ $m->nutrition_info }}</p>@endif

                        {{-- Final Voting UI --}}
                        <div class="mt-4 flex items-center gap-3">
                            @php $myVote = $m->votes->first()?->vote_type; @endphp
                            <form action="{{ route('orangtua.menu-makanan.vote') }}" method="POST">
                                @csrf
                                <input type="hidden" name="menu_makanan_id" value="{{ $m->id }}">
                                <input type="hidden" name="vote_type" value="like">
                                <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $myVote === 'like' ? 'bg-[#1A6B6B] text-white' : 'bg-teal-50 text-teal-700 hover:bg-teal-100' }}">
                                    <svg class="h-3.5 w-3.5" fill="{{ $myVote === 'like' ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.757c1.246 0 2.228 1.053 2.115 2.285l-1.157 12.63c-.105 1.157-1.077 2.085-2.238 2.085H6.115c-1.161 0-2.133-.928-2.238-2.085L2.72 12.285C2.607 11.053 3.589 10 4.835 10H8.5l.5-5a3 3 0 013 3v2h2z" /></svg>
                                    {{ $m->likes_count }} Suka
                                </button>
                            </form>
                            <form action="{{ route('orangtua.menu-makanan.vote') }}" method="POST">
                                @csrf
                                <input type="hidden" name="menu_makanan_id" value="{{ $m->id }}">
                                <input type="hidden" name="vote_type" value="dislike">
                                <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $myVote === 'dislike' ? 'bg-[#FF8C42] text-white' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' }}">
                                    <svg class="h-3.5 w-3.5" fill="{{ $myVote === 'dislike' ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14H5.243c-1.246 0-2.228-1.053-2.115-2.285l1.157-12.63C4.39 1.157 5.362.23 6.523.23h11.362c1.161 0 2.133.928 2.238 2.085l1.157 12.63c.113 1.232-.869 2.285-2.115 2.285H15.5l-.5 5a3 3 0 01-3-3v-2h-2z" /></svg>
                                    {{ $m->dislikes_count }} Tidak Suka
                                </button>
                            </form>
                        </div>
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

        {{-- Modal Preview Gambar --}}
        <div x-show="showImageModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             style="display: none;"
             x-transition
             @keydown.escape.window="showImageModal = false">
            <div class="relative max-w-4xl w-full" @click.away="showImageModal = false">
                <button class="absolute -top-10 right-0 text-white hover:text-gray-300 transition" @click="showImageModal = false">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <img :src="activeImage" class="w-full h-auto max-h-[85vh] object-contain rounded-2xl shadow-2xl bg-white">
            </div>
        </div>
    </div>
</x-app-layout>
