<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kelola Kelas & Ruang Lingkup</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{}, deleteRoute:'', openEdit(d){this.editData=d;this.showEditModal=true}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Daftar Kelas</h3><p class="section-subtitle">Struktur pembagian kelas beserta admin pengelolanya</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Buat Kelas Baru</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama Kelas</th><th>Deskripsi</th><th>Jumlah Siswa</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($kelasList as $k)
                        <tr>
                            <td>
                                <span class="font-semibold" style="color:#2C2C2C;">{{ $k->name }}</span>
                            </td>
                            <td><span class="text-sm" style="color:#5A5A5A;">{{ $k->description ?: '-' }}</span></td>
                            <td><span class="badge badge-teal">{{ $k->anaks_count ?? 0 }} Siswa</span></td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                @php
                                    $kelasEditPayload = [
                                        'id' => $k->id,
                                        'name' => $k->name,
                                        'description' => $k->description,
                                    ];
                                @endphp
                                <button type="button" @click="openEdit(@js($kelasEditPayload))" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('admin.kelas.destroy', $k->id) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 md:py-12 text-center" style="color:#9E9790;">Belum ada kelas yang dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($kelasList->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $kelasList->links() }}</div>@endif
        </div>

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.kelas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Buat Kelas Baru</h3></div>
                    <div class="modal-body max-h-[75vh] overflow-y-auto space-y-4">
                        <div class="text-[13px] font-bold text-[#1A6B6B] mt-1 border-b pb-1">Data Kelas</div>
                        <div>
                            <label class="input-label">Nama Kelas</label>
                            <input type="text" name="name" required class="input-field @error('name') border-red-500 @enderror" placeholder="Contoh: Kelas TK A - Mawar" value="{{ old('name') }}">
                            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Deskripsi (Opsional)</label>
                            <textarea name="description" class="input-field @error('description') border-red-500 @enderror" rows="2">{{ old('description') }}</textarea>
                            @error('description')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        

                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Buat Kelas</button></div>
                </form>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/admin/kelas/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Data Kelas</h3></div>
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="input-label">Nama Kelas</label>
                            <input type="text" name="name" x-model="editData.name" required class="input-field @error('name') border-red-500 @enderror">
                            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Deskripsi</label>
                            <textarea name="description" x-model="editData.description" rows="2" class="input-field @error('description') border-red-500 @enderror"></textarea>
                            @error('description')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

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
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
                    <h3 class="section-title">Hapus Kelas?</h3><p class="section-subtitle mt-1">Kelas ini akan dihapus permanen. Siswa di kelas ini tetap ada namun tanpa kelas (Null).</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
