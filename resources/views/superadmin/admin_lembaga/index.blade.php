<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Kelola Admin Lembaga</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, deleteRoute:'', editRoute:'', editData:{name:'',email:'',lembaga_id:''}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true}, openEdit(a,r){this.editData={name:a.name,email:a.email,lembaga_id:a.lembaga_id};this.editRoute=r;this.showEditModal=true} }">
        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Daftar Admin Lembaga</h3>
                <button @click="showCreateModal=true" class="btn-primary">Tambah Admin</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Email</th><th>Lembaga</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($admins as $a)
                        <tr>
                            <td>{{ $a->name }}</td>
                            <td>{{ $a->email }}</td>
                            <td>{{ $a->lembaga?->name ?? '-' }}</td>
                            <td class="text-right">
                                <button @click="openEdit({{ json_encode($a) }}, '{{ route('superadmin.admin-lembaga.update', $a) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#E8F2F2;">Edit</button>
                                <button @click="openDelete('{{ route('superadmin.admin-lembaga.destroy', $a) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-8 text-center" style="color:#9E9790;">Belum ada admin lembaga.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t">
                <x-per-page-selector :paginator="$admins" />
                {{ $admins->links() }}
            </div>
        </div>
        <div x-show="showCreateModal" class="modal-overlay" style="display:none;"><div class="modal-box" @click.away="showCreateModal=false">
            <form action="{{ route('superadmin.admin-lembaga.store') }}" method="POST">@csrf
                <div class="modal-header"><h3 class="section-title">Tambah Admin Lembaga</h3><p class="section-subtitle">Password awal: password123</p></div>
                <div class="modal-body space-y-4">
                    <div><label class="input-label">Nama</label><input type="text" name="name" required class="input-field"></div>
                    <div><label class="input-label">Email</label><input type="email" name="email" required class="input-field"></div>
                    <div><label class="input-label">Lembaga</label><select name="lembaga_id" required class="input-field"><option value="">-- Pilih --</option>@foreach($lembagas as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
                </div>
                <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
            </form>
        </div></div>
        <div x-show="showEditModal" class="modal-overlay" style="display:none;"><div class="modal-box" @click.away="showEditModal=false">
            <form :action="editRoute" method="POST">@csrf @method('PUT')
                <div class="modal-header"><h3 class="section-title">Edit Admin Lembaga</h3></div>
                <div class="modal-body space-y-4">
                    <div><label class="input-label">Nama</label><input type="text" name="name" x-model="editData.name" required class="input-field"></div>
                    <div><label class="input-label">Email</label><input type="email" name="email" x-model="editData.email" required class="input-field"></div>
                    <div><label class="input-label">Lembaga</label><select name="lembaga_id" x-model="editData.lembaga_id" required class="input-field">@foreach($lembagas as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
                    <div><label class="input-label">Password baru (opsional)</label><input type="password" name="password" class="input-field"></div>
                </div>
                <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
            </form>
        </div></div>
        <div x-show="showDeleteModal" class="modal-overlay" style="display:none;"><div class="modal-box" @click.away="showDeleteModal=false">
            <form :action="deleteRoute" method="POST">@csrf @method('DELETE')
                <div class="modal-header"><h3 class="section-title">Hapus admin ini?</h3></div>
                <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" style="background:#C0392B;">Hapus</button></div>
            </form>
        </div></div>
    </div>
</x-app-layout>
