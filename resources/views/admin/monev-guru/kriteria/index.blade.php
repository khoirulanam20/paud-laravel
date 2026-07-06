<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kriteria Monev Guru</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{
        showCreateModal:false,
        showEditModal:false,
        showDeleteModal:false,
        editData:{},
        deleteRoute:'',
        openEdit(d){this.editData=d;this.showEditModal=true},
        openDelete(r){this.deleteRoute=r;this.showDeleteModal=true}
    }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('admin.monev-guru.index') }}" class="btn-secondary text-sm">← Daftar Evaluasi</a>
        </div>
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Master kriteria penilaian</h3>
                    <p class="section-subtitle">Rubrik evaluasi kinerja guru per sekolah. Bobot dipakai untuk menghitung skor keseluruhan.</p>
                </div>
                <button type="button" @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Tambah kriteria</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Deskripsi</th><th>Bobot</th><th>Urutan</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($kriterias as $k)
                        <tr>
                            <td class="font-semibold">{{ $k->nama }}</td>
                            <td class="text-sm max-w-xs truncate" title="{{ $k->deskripsi }}">{{ $k->deskripsi ?: '—' }}</td>
                            <td>{{ $k->bobot }}%</td>
                            <td>{{ $k->urutan }}</td>
                            <td>@if($k->is_active)<span class="badge badge-teal">Aktif</span>@else<span class="badge" style="background:#eee;color:#666;">Nonaktif</span>@endif</td>
                            <td class="text-right">
                                @php $payload = ['id'=>$k->id,'nama'=>$k->nama,'deskripsi'=>$k->deskripsi,'bobot'=>$k->bobot,'urutan'=>$k->urutan,'is_active'=>$k->is_active]; @endphp
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="openEdit(@js($payload))" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                    <button type="button" @click="openDelete('{{ route('admin.monev-guru.kriteria.destroy', $k) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center" style="color:#9E9790;">Belum ada kriteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$kriterias" />
                {{ $kriterias->links() }}
            </div>
        </div>

        <div x-show="showCreateModal" class="modal-overlay" style="display:none;">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.monev-guru.kriteria.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Tambah kriteria</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Nama <span class="text-red-600">*</span></label><input type="text" name="nama" required class="input-field" value="{{ old('nama') }}"></div>
                        <div><label class="input-label">Deskripsi</label><textarea name="deskripsi" rows="3" class="input-field">{{ old('deskripsi') }}</textarea></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Bobot (%) <span class="text-red-600">*</span></label><input type="number" name="bobot" min="1" max="100" required class="input-field" value="{{ old('bobot', 10) }}"></div>
                            <div><label class="input-label">Urutan</label><input type="number" name="urutan" min="0" class="input-field" value="{{ old('urutan', 0) }}"></div>
                        </div>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked class="rounded"> Aktif</label>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <div x-show="showEditModal" class="modal-overlay" style="display:none;">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/admin/monev-guru/kriteria/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit kriteria</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Nama <span class="text-red-600">*</span></label><input type="text" name="nama" x-model="editData.nama" required class="input-field"></div>
                        <div><label class="input-label">Deskripsi</label><textarea name="deskripsi" x-model="editData.deskripsi" rows="3" class="input-field"></textarea></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Bobot (%) <span class="text-red-600">*</span></label><input type="number" name="bobot" x-model="editData.bobot" min="1" max="100" required class="input-field"></div>
                            <div><label class="input-label">Urutan</label><input type="number" name="urutan" x-model="editData.urutan" min="0" class="input-field"></div>
                        </div>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" :checked="editData.is_active" class="rounded"> Aktif</label>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <div x-show="showDeleteModal" class="modal-overlay" style="display:none;">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-md" @click.away="showDeleteModal=false">
                <div class="modal-header"><h3 class="section-title">Hapus kriteria?</h3></div>
                <div class="modal-body"><p class="text-sm" style="color:#6B6560;">Kriteria yang sudah dipakai di evaluasi tidak dapat dihapus.</p></div>
                <div class="modal-footer">
                    <button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button>
                    <form :action="deleteRoute" method="POST">@csrf @method('DELETE')<button type="submit" class="btn-danger">Hapus</button></form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
