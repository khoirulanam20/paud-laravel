<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kelola Data Pengajar</h2>
        </div>
    </x-slot>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{}, deleteRoute:'', openEdit(d){this.editData=d;this.showEditModal=true}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Daftar Guru & Pengajar</h3><p class="section-subtitle">Kelola data SDM dan akun login yang diberikan kepada pengajar</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Registrasi Pengajar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama Pengajar</th><th>Email Login</th><th>Jabatan</th><th>Riwayat Pendidikan</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($pengajars as $p)
                        <tr>
                            <td><div class="flex items-center gap-3"><div class="h-8 w-8 rounded-xl flex items-center justify-center font-bold text-sm text-white shrink-0" style="background:#1A6B6B;">{{ substr($p->name, 0, 1) }}</div><span class="font-semibold" style="color:#2C2C2C;">{{ $p->name }}</span></div></td>
                            <td><span class="badge badge-teal">{{ $p->user->email ?? '-' }}</span></td>
                            <td>{{ $p->jabatan ?? '-' }}</td>
                            <td class="max-w-xs truncate">{{ $p->education_history ?? '-' }}</td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ json_encode($p) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('admin.pengajar.destroy', $p) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-12 text-center" style="color:#9E9790;">Belum ada data pengajar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pengajars->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $pengajars->links() }}</div>@endif
        </div>
        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.pengajar.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Registrasi Pengajar Baru</h3><p class="section-subtitle">Password login awal: <code>password123</code></p></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Nama Lengkap</label><input type="text" name="name" required class="input-field" placeholder="Nama lengkap pengajar"></div>
                        <div><label class="input-label">Alamat Email Valid</label><input type="email" name="email" required class="input-field" placeholder="email@sekolah.com"></div>
                        <div><label class="input-label">Jabatan / Posisi</label><input type="text" name="jabatan" class="input-field" placeholder="Contoh: Guru Kelas A"></div>
                        <div><label class="input-label">Riwayat Pendidikan</label><textarea name="education_history" rows="3" class="input-field" placeholder="S1 PGPAUD, Universitas..."></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Registrasikan</button></div>
                </form>
            </div>
        </div>
        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/admin/pengajar/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Data Pengajar</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Nama Lengkap</label><input type="text" name="name" x-model="editData.name" required class="input-field"></div>
                        <div><label class="input-label">Jabatan / Posisi</label><input type="text" name="jabatan" x-model="editData.jabatan" class="input-field"></div>
                        <div><label class="input-label">Riwayat Pendidikan</label><textarea name="education_history" x-model="editData.education_history" rows="3" class="input-field"></textarea></div>
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
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><h3 class="section-title">Hapus Pengajar?</h3><p class="section-subtitle mt-1">Akses login pengajar juga akan dihapus bersama data ini.</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
