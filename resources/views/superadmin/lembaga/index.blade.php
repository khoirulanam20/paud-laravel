<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" /></svg>
            </div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Kelola Lembaga / Yayasan</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{}, deleteRoute:'', openEdit(d){this.editData=d;this.showEditModal=true}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Daftar Lembaga</h3><p class="section-subtitle">Kelola yayasan yang terdaftar di platform</p></div>
                <button @click="showCreateModal=true" class="btn-primary">Tambah Lembaga</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Alamat</th><th>Sekolah</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($lembagas as $l)
                        <tr>
                            <td class="font-semibold">{{ $l->name }}</td>
                            <td>{{ $l->address ?? '-' }}</td>
                            <td>{{ $l->sekolahs_count }}</td>
                            <td class="text-right">
                                <button @click="openEdit({{ json_encode($l) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('superadmin.lembaga.destroy', $l) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-8 text-center" style="color:#9E9790;">Belum ada lembaga.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t">
                <x-per-page-selector :paginator="$lembagas" />
                {{ $lembagas->links() }}
            </div>
        </div>
        <div x-show="showCreateModal" class="modal-overlay" style="display:none;"><div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
            <form action="{{ route('superadmin.lembaga.store') }}" method="POST">@csrf
                <div class="modal-header"><h3 class="section-title">Tambah Lembaga</h3></div>
                <div class="modal-body space-y-4">
                    <div><label class="input-label">Nama</label><input type="text" name="name" required class="input-field"></div>
                    <div><label class="input-label">Alamat</label><textarea name="address" rows="2" class="input-field"></textarea></div>
                    <div><label class="input-label">Telepon</label><input type="text" name="phone" class="input-field"></div>
                </div>
                <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
            </form>
        </div></div>
        <div x-show="showEditModal" class="modal-overlay" style="display:none;"><div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
            <form :action="`/superadmin/lembaga/${editData.id}`" method="POST">@csrf @method('PUT')
                <div class="modal-header"><h3 class="section-title">Edit Lembaga</h3></div>
                <div class="modal-body space-y-4">
                    <div><label class="input-label">Nama</label><input type="text" name="name" x-model="editData.name" required class="input-field"></div>
                    <div><label class="input-label">Alamat</label><textarea name="address" x-model="editData.address" rows="2" class="input-field"></textarea></div>
                    <div><label class="input-label">Telepon</label><input type="text" name="phone" x-model="editData.phone" class="input-field"></div>
                </div>
                <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
            </form>
        </div></div>
        <div x-show="showDeleteModal" class="modal-overlay" style="display:none;"><div x-show="showDeleteModal" x-transition class="modal-box" @click.away="showDeleteModal=false">
            <form :action="deleteRoute" method="POST">@csrf @method('DELETE')
                <div class="modal-header"><h3 class="section-title">Hapus Lembaga?</h3></div>
                <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" style="background:#C0392B;">Hapus</button></div>
            </form>
        </div></div>
    </div>
</x-app-layout>
