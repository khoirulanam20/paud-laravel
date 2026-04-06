<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Siswa Kelasku</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{}, deleteRoute:'', openEdit(d){this.editData=d; this.showEditModal=true}, openDelete(r){this.deleteRoute=r; this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="card overflow-hidden">
            <div class="px-6 py-6 border-b" style="background:#FAF6F0; border-color:rgba(0,0,0,0.06);">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold" style="color:#2C2C2C;">Daftar Siswa</h3>
                        <p class="text-sm font-medium" style="color:#9E9790;">Kelola dan lihat rincian siswa yang terdaftar di kelas Anda</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 w-full md:w-auto">
                        <form method="get" class="flex-1 grid grid-cols-2 sm:flex items-end gap-3">
                            <div class="col-span-2 sm:w-48">
                                <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Filter Kelas</label>
                                <select name="kelas_id" class="input-field w-full text-xs font-bold h-11 sm:h-10 border-black/10 transition focus:border-teal-500" onchange="this.form.submit()" style="background:white;">
                                    <option value="">— Semua Kelas —</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(request('kelas_id'))
                                <a href="{{ route('adminkelas.anak.index') }}" class="inline-flex items-center justify-center px-4 h-11 sm:h-10 rounded-xl bg-white text-gray-400 hover:text-red-500 transition border border-black/10 shadow-sm">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                </a>
                            @endif
                        </form>
                        
                        <button @click="showCreateModal=true" class="btn-primary h-11 sm:h-10 px-6 font-bold flex items-center justify-center gap-2 whitespace-nowrap shadow-md shadow-teal-900/10">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            Registrasi Siswa
                        </button>
                    </div>
                </div>
            </div>
            {{-- Mobile Card View (Hidden on Tablet/Desktop) --}}
            <div class="block md:hidden pb-4">
                <div class="grid grid-cols-1 gap-4 px-4 pt-4">
                    @forelse($anaks as $anak)
                    <div class="relative rounded-2xl bg-white border border-black/5 shadow-sm p-4 hover:shadow-md transition">
                        <div class="flex items-center gap-3 mb-3 pb-3 border-b border-black/[0.03]">
                            <x-foto-profil :path="$anak->photo" :name="$anak->name" size="md" class="shrink-0 ring-2 ring-teal-50" />
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-[#2C2C2C] truncate">{{ $anak->name }}</h4>
                                <p class="text-[10px] font-semibold text-teal-600 uppercase tracking-wider">
                                    {{ $anak->kelas->name ?? 'Belum Ada Kelas' }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[9px] font-bold py-0.5 px-2 rounded-full {{ $anak->jenis_kelamin == 'Pria' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                    {{ $anak->jenis_kelamin }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 mb-4">
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest leading-none mb-1">NIK</p>
                                <p class="text-xs font-semibold text-gray-700 truncate leading-none">{{ $anak->nik ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest leading-none mb-1">Nama Ortu</p>
                                <p class="text-xs font-semibold text-gray-700 truncate leading-none">{{ $anak->parent_name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest leading-none mb-1">Tgl Lahir</p>
                                <p class="text-xs font-semibold text-gray-700 leading-none">{{ $anak->dob ? \Carbon\Carbon::parse($anak->dob)->format('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest leading-none mb-1">Umur</p>
                                <p class="text-xs font-bold text-teal-700 leading-none">{{ $anak->age }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 pt-1">
                            <a href="{{ route('adminkelas.anak.show', $anak) }}" class="flex-1 py-2.5 rounded-xl bg-teal-50 text-teal-700 text-xs font-bold text-center hover:bg-teal-100 transition">Detail</a>
                            <button @click="openEdit({{ json_encode($anak) }})" class="flex-1 py-2.5 rounded-xl bg-gray-50 text-gray-700 text-xs font-bold text-center hover:bg-gray-100 transition border border-black/5">Edit</button>
                            <button @click="openDelete('{{ route('adminkelas.anak.destroy', $anak) }}')" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition border border-rose-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center text-gray-400">Belum ada siswa di kelas ini.</div>
                    @endforelse
                </div>
            </div>

            {{-- Desktop Table View (Hidden on Mobile) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>NIK</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir / Umur</th>
                            <th>Nama Orang Tua</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anaks as $anak)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-foto-profil :path="$anak->photo" :name="$anak->name" size="sm" />
                                    <span class="font-semibold" style="color:#2C2C2C;">{{ $anak->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($anak->kelas)<span class="badge badge-teal font-medium">{{ $anak->kelas->name }}</span>
                                @else<span class="text-xs italic" style="color:#9E9790;">Belum Ditugaskan</span>@endif
                            </td>
                            <td>{{ $anak->nik ?? '-' }}</td>
                            <td>{{ $anak->jenis_kelamin ?? '-' }}</td>
                            <td>
                                {{ $anak->dob ? \Carbon\Carbon::parse($anak->dob)->format('d M Y') : '-' }}
                                @if($anak->dob)
                                    <span class="text-[10px] block font-bold text-[#1A6B6B]">{{ $anak->age }}</span>
                                @endif
                            </td>
                            <td>{{ $anak->parent_name ?? '-' }}</td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                <a href="{{ route('adminkelas.anak.show', $anak) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition" style="color: #1A6B6B; background: #E8F5F5;">Detail</a>
                                <button @click="openEdit({{ json_encode($anak) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('adminkelas.anak.destroy', $anak) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center" style="color:#9E9790;">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    Belum ada siswa di kelas ini.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($anaks->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $anaks->appends(request()->only(['kelas_id']))->links() }}</div>@endif
        </div>

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('adminkelas.anak.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Registrasi Siswa Baru</h3><p class="section-subtitle">Password login ortu awal: <code>password123</code></p></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Nama Lengkap Siswa</label><input type="text" name="name" required class="input-field" placeholder="Nama lengkap siswa"></div>
                            <div><label class="input-label">Email Orang Tua (Login)</label><input type="email" name="email" required class="input-field" placeholder="email@ortu.com"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Penempatan Kelas</label><select name="kelas_id" required class="input-field">@foreach($kelas as $k)<option value="{{ $k->id }}">{{ $k->name }}</option>@endforeach</select></div>
                            <div><label class="input-label">Jenis Kelamin</label><select name="jenis_kelamin" class="input-field"><option value="Pria">Pria</option><option value="Wanita">Wanita</option></select></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Tanggal Lahir</label><input type="date" name="dob" class="input-field"></div>
                            <div><label class="input-label">Foto Siswa</label><input type="file" name="photo" accept="image/*" class="input-field py-1.5"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">NIK Siswa</label><input type="text" name="nik" class="input-field" placeholder="NIK (opsional)"></div>
                            <div><label class="input-label">Nama Orang Tua</label><input type="text" name="parent_name" class="input-field" placeholder="Nama Wali"></div>
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
                <form :action="`/adminkelas/anak/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Data Siswa</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Nama Lengkap Siswa</label><input type="text" name="name" x-model="editData.name" required class="input-field"></div>
                            <div><label class="input-label">Penempatan Kelas</label><select name="kelas_id" x-model="editData.kelas_id" required class="input-field">@foreach($kelas as $k)<option value="{{ $k->id }}">{{ $k->name }}</option>@endforeach</select></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Tanggal Lahir</label><input type="date" name="dob" x-model="editData.dob" class="input-field"></div>
                            <div><label class="input-label">Ganti Foto</label><input type="file" name="photo" accept="image/*" class="input-field py-1.5"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Jenis Kelamin</label><select name="jenis_kelamin" x-model="editData.jenis_kelamin" class="input-field"><option value="Pria">Pria</option><option value="Wanita">Wanita</option></select></div>
                            <div><label class="input-label">Nama Orang Tua</label><input type="text" name="parent_name" x-model="editData.parent_name" class="input-field"></div>
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
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><h3 class="section-title">Hapus Siswa?</h3><p class="section-subtitle mt-1">Akun orang tua juga akan dihapus.</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
