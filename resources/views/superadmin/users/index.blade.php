<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl" style="color:#2C2C2C;">Kelola Superadmin</h2></x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, deleteRoute:'', editRoute:'', editData:{name:'',email:''}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true}, openEdit(u,r){this.editData={name:u.name,email:u.email};this.editRoute=r;this.showEditModal=true} }">
        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex justify-between border-b"><h3 class="section-title">Daftar Superadmin</h3><button @click="showCreateModal=true" class="btn-primary">Tambah</button></div>
            <table class="data-table">
                <thead><tr><th>Nama</th><th>Email</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td class="text-right">
                            <button @click="openEdit({{ json_encode($u) }}, '{{ route('superadmin.users.update', $u) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#E8F2F2;">Edit</button>
                            @if($u->id !== auth()->id())
                            <button @click="openDelete('{{ route('superadmin.users.destroy', $u) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-8 text-center" style="color:#9E9790;">Belum ada superadmin.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($users->hasPages())<div class="px-6 py-4 border-t">{{ $users->links() }}</div>@endif
        </div>
        <div x-show="showCreateModal" class="modal-overlay" style="display:none;"><div class="modal-box" @click.away="showCreateModal=false">
            <form action="{{ route('superadmin.users.store') }}" method="POST">@csrf
                <div class="modal-header"><h3 class="section-title">Tambah Superadmin</h3><p class="section-subtitle">Password awal: password123</p></div>
                <div class="modal-body space-y-4">
                    <div><label class="input-label">Nama</label><input type="text" name="name" required class="input-field"></div>
                    <div><label class="input-label">Email</label><input type="email" name="email" required class="input-field"></div>
                </div>
                <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
            </form>
        </div></div>
        <div x-show="showEditModal" class="modal-overlay" style="display:none;"><div class="modal-box" @click.away="showEditModal=false">
            <form :action="editRoute" method="POST">@csrf @method('PUT')
                <div class="modal-header"><h3 class="section-title">Edit Superadmin</h3></div>
                <div class="modal-body space-y-4">
                    <div><label class="input-label">Nama</label><input type="text" name="name" x-model="editData.name" required class="input-field"></div>
                    <div><label class="input-label">Email</label><input type="email" name="email" x-model="editData.email" required class="input-field"></div>
                    <div><label class="input-label">Password baru (opsional)</label><input type="password" name="password" class="input-field"></div>
                </div>
                <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
            </form>
        </div></div>
        <div x-show="showDeleteModal" class="modal-overlay" style="display:none;"><div class="modal-box" @click.away="showDeleteModal=false">
            <form :action="deleteRoute" method="POST">@csrf @method('DELETE')
                <div class="modal-header"><h3 class="section-title">Hapus superadmin?</h3></div>
                <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" style="background:#C0392B;">Hapus</button></div>
            </form>
        </div></div>
    </div>
</x-app-layout>
