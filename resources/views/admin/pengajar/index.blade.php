<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kelola Data Pengajar</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{ kelas_ids: [] }, deleteRoute:'', openEdit(d){this.editData=d; this.editData.kelas_ids = d.kelas ? d.kelas.map(k => k.id) : []; this.showEditModal=true}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Daftar Guru & Pengajar</h3><p class="section-subtitle">Kelola data SDM dan akun login yang diberikan kepada pengajar</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Registrasi Pengajar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama Pengajar</th><th>Email Login</th><th>Jabatan / Posisi</th><th>Penempatan Kelas</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($pengajars as $p)
                        <tr>
                            <td><div class="flex items-center gap-3">
                                <x-foto-profil :path="$p->photo" :name="$p->name" size="sm" />
                                <span class="font-semibold" style="color:#2C2C2C;">{{ $p->name }}</span></div></td>
                            <td><span class="badge badge-teal">{{ $p->user->email ?? '-' }}</span></td>
                            <td>{{ $p->jabatan ?? '-' }}</td>
                            <td>
                                @if($p->kelas && $p->kelas->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                    @foreach($p->kelas as $k)
                                        <span class="badge badge-teal font-medium">{{ $k->name }}</span>
                                    @endforeach
                                    </div>
                                @else
                                    <span class="text-xs italic" style="color:#9E9790;">Tanpa Kelas</span>
                                @endif
                            </td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ json_encode($p) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('admin.pengajar.destroy', $p) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-6 md:py-12 text-center" style="color:#9E9790;">Belum ada data pengajar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pengajars->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $pengajars->links() }}</div>@endif
        </div>
        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.pengajar.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Registrasi Pengajar Baru</h3><p class="section-subtitle">Password login awal: <code>password123</code></p></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Nama Lengkap</label>
                            <input type="text" name="name" required class="input-field @error('name') border-red-500 @enderror" placeholder="Nama lengkap pengajar" value="{{ old('name') }}">
                            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Alamat Email Valid</label>
                                <input type="email" name="email" required class="input-field @error('email') border-red-500 @enderror" placeholder="email@sekolah.com" value="{{ old('email') }}">
                                @error('email')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="input-label">Penempatan Kelas</label>
                                <div class="mt-1 rounded-xl border p-3 max-h-32 overflow-y-auto bg-gray-50 space-y-2" style="border-color:rgba(0,0,0,0.1);">
                                    @forelse($kelas as $k)
                                    <label class="flex items-start gap-2.5 cursor-pointer">
                                        <input type="checkbox" name="kelas_id[]" value="{{ $k->id }}" class="mt-1 accent-teal-600 rounded">
                                        <span class="text-sm font-medium" style="color:#2C2C2C;">{{ $k->name }}</span>
                                    </label>
                                    @empty
                                    <span class="text-xs italic text-gray-400">Belum ada kelas</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">NIK KTP</label>
                                <input type="text" name="nik" class="input-field @error('nik') border-red-500 @enderror" placeholder="NIK Pengajar" value="{{ old('nik') }}">
                                @error('nik')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="input-label">Kontak / WA</label>
                                <input type="text" name="phone" class="input-field @error('phone') border-red-500 @enderror" placeholder="08..." value="{{ old('phone') }}">
                                @error('phone')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Pendidikan terakhir</label>
                                <x-pendidikan-select name="pendidikan" />
                            </div>
                            <div>
                                <label class="input-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="input-field @error('jenis_kelamin') border-red-500 @enderror">
                                    <option value="">Pilih...</option>
                                    <option value="Pria" @selected(old('jenis_kelamin') == 'Pria')>Pria</option>
                                    <option value="Wanita" @selected(old('jenis_kelamin') == 'Wanita')>Wanita</option>
                                </select>
                                @error('jenis_kelamin')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Jabatan / Posisi</label><input type="text" name="jabatan" class="input-field" placeholder="Contoh: Guru Kelas A"></div>
                            <div><label class="input-label">Foto Pengajar</label><input type="file" name="photo" accept="image/*" class="input-field py-1.5"></div>
                        </div>
                        <div><label class="input-label">Alamat Lengkap</label><textarea name="alamat" rows="2" class="input-field" placeholder="Prov, Kab, Kec, Kel..."></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Registrasikan</button></div>
                </form>
            </div>
        </div>
        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/admin/pengajar/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Data Pengajar</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Nama Lengkap</label>
                            <input type="text" name="name" x-model="editData.name" required class="input-field @error('name') border-red-500 @enderror">
                            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Penempatan Kelas</label>
                            <div class="mt-1 rounded-xl border p-3 max-h-40 overflow-y-auto bg-gray-50 grid grid-cols-1 sm:grid-cols-2 gap-2" style="border-color:rgba(0,0,0,0.1);">
                                @forelse($kelas as $k)
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="kelas_id[]" value="{{ $k->id }}" x-model="editData.kelas_ids" class="mt-1 accent-teal-600 rounded">
                                    <span class="text-sm font-medium" style="color:#2C2C2C;">{{ $k->name }}</span>
                                </label>
                                @empty
                                <span class="text-xs italic text-gray-400">Belum ada data kelas</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">NIK KTP</label><input type="text" name="nik" x-model="editData.nik" class="input-field"></div>
                            <div><label class="input-label">Kontak / WA</label><input type="text" name="phone" x-model="editData.phone" class="input-field"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Pendidikan terakhir</label>
                                <select name="pendidikan" class="input-field" x-model="editData.pendidikan">
                                    <option value="">— Pilih —</option>
                                    @foreach($pendidikanOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div><label class="input-label">Jenis Kelamin</label><select name="jenis_kelamin" x-model="editData.jenis_kelamin" class="input-field"><option value="">Pilih...</option><option value="Pria">Pria</option><option value="Wanita">Wanita</option></select></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Jabatan / Posisi</label><input type="text" name="jabatan" x-model="editData.jabatan" class="input-field"></div>
                            <div><label class="input-label">Ganti Foto</label><input type="file" name="photo" accept="image/*" class="input-field py-1.5"></div>
                        </div>
                        <div><label class="input-label">Alamat Lengkap</label><textarea name="alamat" x-model="editData.alamat" rows="2" class="input-field"></textarea></div>
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
