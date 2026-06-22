<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Chart of Accounts (COA)</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
            showCreateModal: false,
            showEditModal: false,
            showDeleteModal: false,
            tab: 'all',
            editData: {},
            deleteRoute: '',
            openEdit(d) { this.editData = JSON.parse(JSON.stringify(d)); this.showEditModal = true; },
            openDelete(r) { this.deleteRoute = r; this.showDeleteModal = true; },
         }">

        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-danger mb-5">
                <ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Daftar Akun</h3>
                    <p class="section-subtitle">Kelola chart of accounts sesuai PSAK</p>
                </div>
                <button @click="showCreateModal = true" class="btn-primary">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Akun
                </button>
            </div>

            <!-- Tab Filter -->
            <div class="px-6 py-3 border-b flex gap-2 flex-wrap" style="border-color:rgba(0,0,0,0.06);">
                @php $jenisLabels = ['all' => 'Semua', 'aset' => 'Aset', 'liabilitas' => 'Liabilitas', 'ekuitas' => 'Ekuitas', 'pendapatan' => 'Pendapatan', 'beban' => 'Beban']; @endphp
                @foreach($jenisLabels as $key => $label)
                    <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'btn-primary text-xs' : 'btn-secondary text-xs'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="overflow-x-auto">
                @foreach($akuns as $jenis => $group)
                    <div x-show="tab === 'all' || tab === '{{ $jenis }}'" class="akun-group">
                        <div class="px-6 py-2 bg-[#F5F2ED] border-b font-bold text-xs uppercase tracking-wider" style="color: #1A6B6B;">
                            {{ $jenisLabels[$jenis] ?? ucfirst($jenis) }} ({{ $group->count() }})
                        </div>
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Akun</th>
                                    <th class="text-center">Arus Kas</th>
                                    <th class="text-center">Saldo Normal</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group as $akun)
                                    <tr>
                                        <td class="font-mono text-sm font-semibold" style="color: #1A6B6B;">{{ $akun->kode }}</td>
                                        <td class="font-medium" style="color: #2C2C2C;">{{ $akun->nama }}</td>
                                        <td class="text-center">
                                            @if($akun->kategori_arus_kas)
                                                <span class="badge text-xs" style="background: #D0E8E8; color: #1A6B6B;">
                                                    {{ match($akun->kategori_arus_kas) { 'operasi' => 'Operasi', 'investasi' => 'Investasi', 'pendanaan' => 'Pendanaan' } }}
                                                </span>
                                            @else
                                                <span class="text-xs" style="color: #9E9790;">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge text-xs {{ $akun->saldo_normal === 'debit' ? 'badge-green' : 'badge-rose' }}">
                                                {{ $akun->saldo_normal === 'debit' ? 'Debit' : 'Kredit' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="openEdit({{ json_encode($akun->only(['id','kode','nama','jenis','kategori_arus_kas','saldo_normal','induk_id','deskripsi'])) }})"
                                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                                <button @click="openDelete('{{ route('admin.akun.destroy', $akun) }}')"
                                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="modal-overlay" style="display:none;">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal = false">
                <form action="{{ route('admin.akun.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Tambah Akun Baru</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-4">
                        <div class="col-span-2 text-[13px] font-bold text-[#1A6B6B] mt-1 border-b pb-1">Info Akun</div>
                        <div>
                            <label class="input-label">Kode Akun</label>
                            <input type="text" name="kode" required placeholder="1-1000" class="input-field">
                        </div>
                        <div>
                            <label class="input-label">Jenis Akun</label>
                            <select name="jenis" required class="input-field">
                                <option value="aset">Aset</option>
                                <option value="liabilitas">Liabilitas</option>
                                <option value="ekuitas">Ekuitas</option>
                                <option value="pendapatan">Pendapatan</option>
                                <option value="beban">Beban</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Nama Akun</label>
                            <input type="text" name="nama" required placeholder="Kas" class="input-field">
                        </div>
                        <div>
                            <label class="input-label">Kategori Arus Kas</label>
                            <select name="kategori_arus_kas" class="input-field">
                                <option value="">— Tidak Ada —</option>
                                <option value="operasi">Operasi</option>
                                <option value="investasi">Investasi</option>
                                <option value="pendanaan">Pendanaan</option>
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Saldo Normal</label>
                            <select name="saldo_normal" required class="input-field">
                                <option value="debit">Debit</option>
                                <option value="kredit">Kredit</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Induk Akun</label>
                            <select name="induk_id" class="input-field">
                                <option value="">— Tanpa Induk —</option>
                                @foreach($allAkun as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} - {{ $a->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Deskripsi (opsional)</label>
                            <textarea name="deskripsi" rows="2" class="input-field"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showCreateModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="modal-overlay" style="display:none;">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal = false">
                <form :action="`/admin/akun/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Akun</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-4">
                        <div>
                            <label class="input-label">Kode Akun</label>
                            <input type="text" name="kode" x-model="editData.kode" required class="input-field">
                        </div>
                        <div>
                            <label class="input-label">Jenis Akun</label>
                            <select name="jenis" x-model="editData.jenis" required class="input-field">
                                <option value="aset">Aset</option>
                                <option value="liabilitas">Liabilitas</option>
                                <option value="ekuitas">Ekuitas</option>
                                <option value="pendapatan">Pendapatan</option>
                                <option value="beban">Beban</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Nama Akun</label>
                            <input type="text" name="nama" x-model="editData.nama" required class="input-field">
                        </div>
                        <div>
                            <label class="input-label">Kategori Arus Kas</label>
                            <select name="kategori_arus_kas" x-model="editData.kategori_arus_kas" class="input-field">
                                <option value="">— Tidak Ada —</option>
                                <option value="operasi">Operasi</option>
                                <option value="investasi">Investasi</option>
                                <option value="pendanaan">Pendanaan</option>
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Saldo Normal</label>
                            <select name="saldo_normal" x-model="editData.saldo_normal" required class="input-field">
                                <option value="debit">Debit</option>
                                <option value="kredit">Kredit</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Deskripsi</label>
                            <textarea name="deskripsi" x-model="editData.deskripsi" rows="2" class="input-field"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showEditModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div x-show="showDeleteModal" class="modal-overlay" style="display:none;">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal = false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6">
                        <div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <h3 class="section-title">Hapus Akun?</h3>
                        <p class="section-subtitle mt-1">Akun dengan riwayat transaksi akan dinonaktifkan, bukan dihapus.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showDeleteModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
