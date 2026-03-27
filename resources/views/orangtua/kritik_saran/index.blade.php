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
         x-data="{ showModal: {{ $errors->any() ? 'true' : 'false' }} }">
        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm" style="color:#6B6560;">Daftar masukan Anda ke sekolah. Gunakan tombol di kanan untuk mengirim saran atau kritik baru.</p>
            <button type="button" @click="showModal = true" class="btn-primary text-sm inline-flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Kirim masukan baru
            </button>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pesan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $fb)
                            <tr>
                                <td class="whitespace-nowrap text-sm" style="color:#6B6560;">{{ $fb->created_at->format('d M Y, H:i') }}</td>
                                <td class="max-w-md">
                                    <p class="text-sm leading-relaxed" style="color:#2C2C2C;">{{ $fb->message }}</p>
                                </td>
                                <td>
                                    <span class="badge badge-teal">{{ $fb->status ?? '—' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-14 text-center text-sm" style="color:#9E9790;">Belum ada masukan. Klik &ldquo;Kirim masukan baru&rdquo; untuk mengirim saran atau kritik pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($feedbacks->hasPages())
                <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $feedbacks->links() }}</div>
            @endif
        </div>

        <!-- Modal kirim masukan -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none; background: rgba(0,0,0,0.45);">
            <div x-show="showModal" x-transition class="modal-box max-w-lg w-full" @click.away="showModal = false">
                <form method="POST" action="{{ route('orangtua.kritik-saran.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h3 class="section-title">Kirim saran atau kritik</h3>
                        <p class="section-subtitle mt-1">Pesan minimal 10 karakter akan dikirim ke pihak sekolah.</p>
                    </div>
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="input-label">Pesan <span class="text-red-600">*</span></label>
                            <textarea name="message" rows="5" class="input-field" placeholder="Tuliskan pesan Anda di sini..." required minlength="10">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
