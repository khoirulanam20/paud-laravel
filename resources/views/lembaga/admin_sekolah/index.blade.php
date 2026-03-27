<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kelola Akun Admin Sekolah</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showDeleteModal:false, deleteRoute:'', openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Daftar Admin Sekolah</h3><p class="section-subtitle">Berikan akses manajemen ke staf terpercaya setiap cabang</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Tambah Admin</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Email Login</th><th>Cabang Sekolah</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($admins as $a)
                        <tr>
                            <td><div class="flex items-center gap-3"><div class="h-8 w-8 rounded-xl flex items-center justify-center font-bold text-sm text-white shrink-0" style="background:#1A6B6B;">{{ substr($a->name, 0, 1) }}</div><span class="font-semibold" style="color:#2C2C2C;">{{ $a->name }}</span></div></td>
                            <td><span class="badge badge-teal">{{ $a->email }}</span></td>
                            <td>{{ $a->sekolah->name ?? '-' }}</td>
                            <td class="text-right">
                                <button @click="openDelete('{{ route('lembaga.admin-sekolah.destroy', $a) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 md:py-12 text-center" style="color:#9E9790;">Belum ada akun admin sekolah.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($admins->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $admins->links() }}</div>@endif
        </div>
        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('lembaga.admin-sekolah.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Registrasi Admin Sekolah Baru</h3><p class="section-subtitle">Password awal: <code>password123</code></p></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Nama Lengkap</label><input type="text" name="name" required class="input-field" placeholder="Nama admin"></div>
                        <div><label class="input-label">Alamat Email Valid</label><input type="email" name="email" required class="input-field" placeholder="admin@sekolah.com"></div>
                        <div><label class="input-label">Cabang Sekolah</label>
                            <select name="sekolah_id" required class="input-field">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($sekolahs as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
        <!-- DELETE MODAL -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal=false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><h3 class="section-title">Hapus Admin?</h3><p class="section-subtitle mt-1">Akses login admin sekolah ini akan dicabut.</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
