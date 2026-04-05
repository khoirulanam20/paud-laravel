<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Saran & Kritik</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{ 
            showModal: {{ $errors->any() ? 'true' : 'false' }},
            isEdit: false,
            editId: '',
            messageValue: '',
            initCreate() {
                this.isEdit = false;
                this.editId = '';
                this.messageValue = '';
                this.showModal = true;
            },
            initEdit(id, msg) {
                this.isEdit = true;
                this.editId = id;
                this.messageValue = msg;
                this.showModal = true;
            }
         }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert-danger mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm" style="color:#6B6560;">Riwayat masukan dan <strong>tanggapan sekolah</strong> ditampilkan di bawah. Buka detail untuk membaca penuh.</p>
            <button type="button" @click="initCreate()" class="btn-primary text-sm inline-flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Kirim masukan baru
            </button>
        </div>

        <div class="space-y-5">
            @forelse($feedbacks as $fb)
                <article class="card overflow-hidden">
                    <div class="px-5 py-4 border-b flex flex-wrap items-start justify-between gap-3" style="border-color:rgba(0,0,0,0.06);">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                @if($fb->photo)
                                    <div class="h-10 w-10 rounded-lg overflow-hidden border border-gray-100 shadow-sm cursor-pointer" onclick="window.open('{{ Storage::url($fb->photo) }}')">
                                        <img src="{{ Storage::url($fb->photo) }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-semibold" style="color:#9E9790;">{{ $fb->created_at->format('d M Y, H:i') }}</p>
                                <span class="badge badge-teal mt-0.5">{{ $fb->status ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($fb->status === 'Terkirim')
                                <button type="button" @click="initEdit('{{ $fb->id }}', '{{ addslashes($fb->message) }}')" class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 hover:text-[#1A6B6B] hover:bg-[#D0E8E8] transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <form action="{{ route('orangtua.kritik-saran.destroy', $fb) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('orangtua.kritik-saran.show', $fb) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Detail</a>
                        </div>
                    </div>
                    <div class="px-5 py-4">
                        <h3 class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:#6B6560;">Pesan Anda</h3>
                        <p class="text-sm leading-relaxed line-clamp-3" style="color:#2C2C2C;">{{ $fb->message }}</p>
                    </div>
                    @if(filled($fb->umpan_balik))
                        <div class="mx-5 mb-5 rounded-xl px-4 py-4 border" style="border-color:rgba(26,107,107,0.25); background:#F0FAFA;">
                            <h3 class="text-xs font-bold uppercase tracking-wide mb-2 flex items-center gap-1.5" style="color:#1A6B6B;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                Tanggapan sekolah
                            </h3>
                            <p class="text-sm leading-relaxed whitespace-pre-wrap" style="color:#2C2C2C;">{{ $fb->umpan_balik }}</p>
                        </div>
                    @else
                        <div class="px-5 pb-4">
                            <p class="text-xs italic rounded-lg px-3 py-2" style="color:#9E9790;background:#FAF6F0;">Belum ada tanggapan dari sekolah.</p>
                        </div>
                    @endif
                </article>
            @empty
                <div class="card px-6 py-14 text-center text-sm" style="color:#9E9790;">
                    Belum ada masukan. Klik &ldquo;Kirim masukan baru&rdquo; untuk mengirim saran atau kritik pertama Anda.
                </div>
            @endforelse
        </div>

        @if($feedbacks->hasPages())
            <div class="mt-6">{{ $feedbacks->links() }}</div>
        @endif

        <!-- Modal kirim/edit masukan -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none; background: rgba(0,0,0,0.45);">
            <div x-show="showModal" x-transition class="modal-box max-w-lg w-full" @click.away="showModal = false">
                <form method="POST" :action="isEdit ? '/orangtua/kritik-saran/' + editId : '{{ route('orangtua.kritik-saran.store') }}'" enctype="multipart/form-data">
                    @csrf
                    <template x-if="isEdit">
                        @method('PATCH')
                    </template>
                    <div class="modal-header">
                        <h3 class="section-title" x-text="isEdit ? 'Edit saran atau kritik' : 'Kirim saran atau kritik'"></h3>
                        <p class="section-subtitle mt-1">Pesan minimal 10 karakter akan dikirim ke pihak sekolah.</p>
                    </div>
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="input-label">Pesan <span class="text-red-600">*</span></label>
                            <textarea name="message" x-model="messageValue" rows="5" class="input-field" placeholder="Tuliskan pesan Anda di sini..." required minlength="10"></textarea>
                            @error('message')
                                <p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="input-label">Lampiran Foto (Opsional)</label>
                            <input type="file" name="photo" class="input-field text-xs pt-2" accept="image/*">
                            <p class="text-[10px] text-gray-400 mt-1">Maks. 2MB. Format: JPG, PNG.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary" x-text="isEdit ? 'Simpan Perbaruan' : 'Kirim Masukan'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
