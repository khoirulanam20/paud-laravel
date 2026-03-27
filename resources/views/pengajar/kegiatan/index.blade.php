<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Jurnal Kegiatan Anak</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{}, deleteRoute:'', openEdit(d){this.editData=d;this.showEditModal=true}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Log Jurnal Saya</h3><p class="section-subtitle">Dokumentasi kegiatan harian yang Anda catat untuk kelas</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Buat Entri Jurnal</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Tanggal</th><th>Judul Kegiatan</th><th>Deskripsi</th><th>Foto</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($kegiatans as $k)
                        <tr>
                            <td class="whitespace-nowrap font-medium" style="color:#2C2C2C;">{{ \Carbon\Carbon::parse($k->date)->format('d M Y') }}</td>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $k->title }}</span></td>
                            <td class="max-w-xs truncate">{{ $k->description ?? '-' }}</td>
                            <td>@if($k->photo)<img src="{{ Storage::url($k->photo) }}" class="h-12 w-16 object-cover rounded-lg">@else<span class="text-xs" style="color:#9E9790;">-</span>@endif</td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ json_encode($k) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('pengajar.kegiatan.destroy', $k) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-6 md:py-12 text-center" style="color:#9E9790;">Belum ada jurnal kegiatan yang dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($kegiatans->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $kegiatans->links() }}</div>@endif
        </div>
        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('pengajar.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Buat Entri Jurnal Baru</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Tanggal</label><input type="date" name="date" required value="{{ date('Y-m-d') }}" class="input-field"></div>
                        <div><label class="input-label">Judul Kegiatan</label><input type="text" name="title" required class="input-field" placeholder="Contoh: Bermain Sensori"></div>
                        <div><label class="input-label">Deskripsi Kegiatan</label><textarea name="description" rows="3" class="input-field" placeholder="Ceritakan apa yang terjadi..."></textarea></div>
                        <div><label class="input-label">Foto Dokumentasi (opsional)</label><input type="file" name="photo" accept="image/*" class="input-field py-2"></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Jurnal</button></div>
                </form>
            </div>
        </div>
        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/pengajar/kegiatan/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Jurnal</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Tanggal</label><input type="date" name="date" :value="editData.date ? editData.date.split('T')[0] : ''" required class="input-field"></div>
                        <div><label class="input-label">Judul</label><input type="text" name="title" x-model="editData.title" required class="input-field"></div>
                        <div><label class="input-label">Deskripsi</label><textarea name="description" x-model="editData.description" rows="3" class="input-field"></textarea></div>
                        <div><label class="input-label">Ganti Foto</label><input type="file" name="photo" accept="image/*" class="input-field py-2"></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
        <!-- DELETE MODAL -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal=false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><h3 class="section-title">Hapus Jurnal?</h3><p class="section-subtitle mt-1">Data jurnal ini akan terhapus.</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
