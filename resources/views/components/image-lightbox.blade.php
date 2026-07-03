<div x-show="showImageModal"
     class="modal-overlay modal-overlay--elevated modal-overlay--dark"
     style="display: none;"
     x-transition
     @keydown.escape.window="showImageModal = false">
    <div class="relative max-w-4xl w-full" @click.away="showImageModal = false">
        <button class="absolute -top-12 right-0 text-white hover:text-gray-300 transition flex items-center gap-2" @click="showImageModal = false">
            <span class="text-xs font-bold uppercase tracking-widest text-white/50">Klik di mana saja untuk tutup</span>
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <img :src="activeImage" class="w-full h-auto max-h-[85vh] object-contain rounded-2xl shadow-2xl bg-white shadow-black/20">
    </div>
</div>
