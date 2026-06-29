<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Sumber Dana</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto"
         x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{}, deleteRoute:'' }">

        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex justify-between items-center border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Daftar Sumber Dana</h3><p class="section-subtitle">BOS, Komite, SPP, dll. per sekolah</p></div>
                <button @click="showCreateModal=true" class="btn-primary">+ Tambah</button>
            </div>
            <table class="data-table" data-tour="admin-sumber-dana-table">
                <thead><tr><th>Kode</th><th>Nama</th><th class="text-center">Urutan</th><th class="text-center">Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($sumberDanas as $sd)
                        <tr>
                            <td class="font-mono font-semibold">{{ $sd->kode }}</td>
                            <td>{{ $sd->nama }}</td>
                            <td class="text-center">{{ $sd->urutan }}</td>
                            <td class="text-center"><span class="badge {{ $sd->is_aktif ? 'badge-green' : 'badge-gray' }}">{{ $sd->is_aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="text-right">
                                <button @click="editData={{ json_encode($sd) }}; showEditModal=true" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="deleteRoute='{{ route('admin.sumber-dana.destroy', $sd) }}'; showDeleteModal=true" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-12 text-center" style="color:#9E9790;">Belum ada sumber dana.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-show="showCreateModal" class="modal-overlay" style="display:none;">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.sumber-dana.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Tambah Sumber Dana</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-4">
                        <div><label class="input-label">Kode</label><input type="text" name="kode" required class="input-field" placeholder="BOS"></div>
                        <div><label class="input-label">Urutan</label><input type="number" name="urutan" min="0" class="input-field" value="99"></div>
                        <div class="col-span-2"><label class="input-label">Nama</label><input type="text" name="nama" required class="input-field"></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <div x-show="showEditModal" class="modal-overlay" style="display:none;">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`{{ url('admin/sumber-dana') }}/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Sumber Dana</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-4">
                        <div><label class="input-label">Kode</label><input type="text" name="kode" x-model="editData.kode" required class="input-field"></div>
                        <div><label class="input-label">Urutan</label><input type="number" name="urutan" x-model="editData.urutan" min="0" class="input-field"></div>
                        <div class="col-span-2"><label class="input-label">Nama</label><input type="text" name="nama" x-model="editData.nama" required class="input-field"></div>
                        <div class="col-span-2"><label class="flex items-center gap-2"><input type="checkbox" name="is_aktif" value="1" :checked="editData.is_aktif" class="rounded"><span class="input-label mb-0">Aktif</span></label></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <x-confirm-modal
            show="showDeleteModal"
            action-binding="deleteRoute"
            method="DELETE"
            title="Hapus Sumber Dana?"
            message="Data yang sudah dipakai di RKAS mungkin terpengaruh."
        />
    </div>
</x-app-layout>
