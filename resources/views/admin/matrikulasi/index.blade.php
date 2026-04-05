<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Matrikulasi Sekolah</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ 
        showCreateModal:false, 
        showEditModal:false, 
        showDetailModal:false,
        showDeleteModal:false, 
        editData:{}, 
        detailData:{},
        deleteRoute:'', 
        openEdit(d){this.editData=d;this.showEditModal=true}, 
        openDetail(d){this.detailData=d;this.showDetailModal=true},
        openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} 
    }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Indikator matrikulasi</h3>
                    <p class="section-subtitle">Standar penilaian tingkat sekolah; dipakai di jurnal kegiatan dan pencapaian siswa.</p>
                </div>
                <button type="button" @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Tambah indikator</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Aspek / bidang</th><th>Indikator</th><th>Deskripsi</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($matrikulasis as $m)
                        <tr>
                            <td><span class="badge badge-teal">{{ $m->aspek ?? 'Umum' }}</span></td>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $m->indicator }}</span></td>
                            <td class="max-w-xs truncate">{{ $m->description ?? '-' }}</td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                @php
                                    $matPayload = [
                                        'id' => $m->id,
                                        'aspek' => $m->aspek,
                                        'indicator' => $m->indicator,
                                        'description' => $m->description,
                                        'tujuan' => $m->tujuan,
                                        'strategi' => $m->strategi,
                                    ];
                                @endphp
                                <button type="button" @click="openDetail(@js($matPayload))" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#F0F7F7;border:1px solid #D0E8E8;">Detail</button>
                                <button type="button" @click="openEdit(@js($matPayload))" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button type="button" @click="openDelete('{{ route('admin.matrikulasi.destroy', $m) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 md:py-12 text-center" style="color:#9E9790;">Belum ada indikator matrikulasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($matrikulasis->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $matrikulasis->links() }}</div>@endif
        </div>

        {{-- DETAIL MODAL --}}
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/45" style="display:none;">
            <div x-show="showDetailModal" x-transition class="modal-box max-w-2xl" @click.away="showDetailModal=false">
                <div class="modal-header flex items-center justify-between">
                    <h3 class="section-title">Detail Indikator Matrikulasi</h3>
                    <button @click="showDetailModal=false" class="text-gray-400 hover:text-gray-600"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <div class="modal-body space-y-5 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div><p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Aspek / Bidang</p><p class="text-sm font-semibold text-gray-900" x-text="detailData.aspek || 'Umum'"></p></div>
                        <div><p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Indikator</p><p class="text-sm font-semibold text-gray-900" x-text="detailData.indicator"></p></div>
                    </div>
                    <div><p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Tujuan Pembelajaran</p><p class="text-sm text-gray-700 whitespace-pre-line" x-text="detailData.tujuan || '-'"></p></div>
                    <div><p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Strategi / Metode Edukasi</p><p class="text-sm text-gray-700 whitespace-pre-line" x-text="detailData.strategi || '-'"></p></div>
                    <div><p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Deskripsi Lengkap</p><p class="text-sm text-gray-700 whitespace-pre-line" x-text="detailData.description"></p></div>
                </div>
                <div class="modal-footer"><button @click="showDetailModal=false" class="btn-secondary w-full sm:w-auto">Tutup</button></div>
            </div>
        </div>

        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.matrikulasi.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Tambah indikator</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Aspek / faktor</label>
                            <input type="text" name="aspek" class="input-field @error('aspek') border-red-500 @enderror" placeholder="Contoh: Kognitif, Motorik halus…" value="{{ old('aspek') }}">
                            @error('aspek')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Indikator <span class="text-red-600">*</span></label>
                            <input type="text" name="indicator" required class="input-field @error('indicator') border-red-500 @enderror" placeholder="Contoh: Mampu menghitung 1–10" value="{{ old('indicator') }}">
                            @error('indicator')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Tujuan pembelajaran</label>
                            <textarea name="tujuan" rows="2" class="input-field @error('tujuan') border-red-500 @enderror">{{ old('tujuan') }}</textarea>
                            @error('tujuan')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Strategi / metode</label>
                            <textarea name="strategi" rows="2" class="input-field @error('strategi') border-red-500 @enderror">{{ old('strategi') }}</textarea>
                            @error('strategi')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Deskripsi <span class="text-red-600">*</span></label>
                            <textarea name="description" rows="2" required class="input-field @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/admin/matrikulasi/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit indikator</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Aspek / faktor</label>
                            <input type="text" name="aspek" x-model="editData.aspek" class="input-field @error('aspek') border-red-500 @enderror">
                            @error('aspek')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Indikator</label>
                            <input type="text" name="indicator" x-model="editData.indicator" required class="input-field @error('indicator') border-red-500 @enderror">
                            @error('indicator')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Tujuan pembelajaran</label>
                            <textarea name="tujuan" x-model="editData.tujuan" rows="2" class="input-field @error('tujuan') border-red-500 @enderror"></textarea>
                            @error('tujuan')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Strategi / metode</label>
                            <textarea name="strategi" x-model="editData.strategi" rows="2" class="input-field @error('strategi') border-red-500 @enderror"></textarea>
                            @error('strategi')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Deskripsi</label>
                            <textarea name="description" x-model="editData.description" rows="2" required class="input-field @error('description') border-red-500 @enderror"></textarea>
                            @error('description')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal=false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><h3 class="section-title">Hapus indikator?</h3><p class="section-subtitle mt-1">Data penilaian yang memakai indikator ini dapat terpengaruh.</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, hapus</button></div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
