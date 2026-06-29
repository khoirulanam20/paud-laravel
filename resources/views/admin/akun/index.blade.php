<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kode Rekening & Akun</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{ showCreateModal: false, showEditModal: false, showDeleteModal: false, editData: {}, deleteRoute: '' }">

        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 border-b" style="border-color:rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Daftar Kode Rekening & Akun</h3>
                    <p class="section-subtitle">Satu master untuk RKAS, cashflow, dan jurnal</p>
                </div>
                <button @click="showCreateModal=true" class="btn-primary">+ Tambah Akun</button>
            </div>

            <div class="px-6 py-3 border-b flex flex-wrap gap-2" data-tour="admin-akun-filter-tabs" style="border-color:rgba(0,0,0,0.06);">
                @foreach(['all' => 'Semua', 'sistem' => 'Sistem', 'belanja' => 'Belanja', 'pendapatan' => 'Pendapatan'] as $key => $label)
                    <a href="{{ route('admin.akun.index', ['filter' => $key, 'q' => request('q')]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $filter === $key ? 'btn-primary' : 'btn-secondary' }}">{{ $label }}</a>
                @endforeach
                <form method="GET" class="ml-auto flex gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari..." class="input-field text-sm w-40">
                    <button type="submit" class="btn-secondary text-xs">Cari</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table" data-tour="admin-akun-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>SNP / Komponen</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($akunList as $akun)
                            <tr>
                                <td class="font-mono font-semibold whitespace-nowrap" style="color:#1A6B6B;">{{ $akun->kode }}</td>
                                <td>
                                    <div class="font-medium">{{ $akun->nama }}</div>
                                    @if($akun->uraian)<div class="text-xs truncate max-w-xs" style="color:#9E9790;" title="{{ $akun->uraian }}">{{ Str::limit($akun->uraian, 60) }}</div>@endif
                                </td>
                                <td><span class="badge {{ $akun->tipe === 'sistem' ? 'badge-blue' : 'badge-green' }}">{{ ucfirst($akun->tipe) }}</span></td>
                                <td class="text-xs" style="color:#9E9790;">{{ $akun->snp ?? '-' }} / {{ $akun->komponen ?? '-' }}</td>
                                <td class="text-center"><span class="badge badge-gray text-xs">{{ ucfirst($akun->jenis) }}</span></td>
                                <td class="text-right">
                                    <button @click="editData={{ json_encode($akun->only(['id','kode','nama','snp','komponen','uraian','tipe','jenis','kategori_arus_kas','saldo_normal','deskripsi'])) }}; showEditModal=true" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                    @if(!$akun->isSistem())
                                        <button @click="deleteRoute='{{ route('admin.akun.destroy', $akun) }}'; showDeleteModal=true" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center" style="color:#9E9790;">Belum ada akun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$akunList" />
                {{ $akunList->links() }}
            </div>
        </div>

        <div x-show="showCreateModal" class="modal-overlay" style="display:none;">
            <div x-show="showCreateModal" x-transition class="modal-box max-w-lg" @click.away="showCreateModal=false">
                <form action="{{ route('admin.akun.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Tambah Kode Rekening</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-3">
                        <div><label class="input-label">Kode</label><input type="text" name="kode" required class="input-field"></div>
                        <div><label class="input-label">Jenis</label><select name="jenis" class="input-field"><option value="beban">Beban</option><option value="pendapatan">Pendapatan</option><option value="aset">Aset</option><option value="liabilitas">Liabilitas</option></select></div>
                        <div class="col-span-2"><label class="input-label">Nama</label><input type="text" name="nama" required class="input-field"></div>
                        <div class="col-span-2"><label class="input-label">Uraian</label><textarea name="uraian" rows="2" class="input-field"></textarea></div>
                        <div><label class="input-label">SNP</label><input type="text" name="snp" class="input-field"></div>
                        <div><label class="input-label">Komponen</label><input type="text" name="komponen" class="input-field"></div>
                        <div><label class="input-label">Saldo Normal</label><select name="saldo_normal" class="input-field"><option value="debit">Debit</option><option value="kredit">Kredit</option></select></div>
                        <input type="hidden" name="tipe" value="rkas">
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <div x-show="showEditModal" class="modal-overlay" style="display:none;">
            <div x-show="showEditModal" x-transition class="modal-box max-w-lg" @click.away="showEditModal=false">
                <form :action="`{{ url('admin/akun') }}/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Akun</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-3">
                        <div><label class="input-label">Kode</label><input type="text" name="kode" x-model="editData.kode" required class="input-field"></div>
                        <div><label class="input-label">Jenis</label><select name="jenis" x-model="editData.jenis" :disabled="editData.tipe==='sistem'" class="input-field"><option value="beban">Beban</option><option value="pendapatan">Pendapatan</option><option value="aset">Aset</option><option value="liabilitas">Liabilitas</option><option value="ekuitas">Ekuitas</option></select></div>
                        <div class="col-span-2"><label class="input-label">Nama</label><input type="text" name="nama" x-model="editData.nama" required class="input-field"></div>
                        <div class="col-span-2"><label class="input-label">Uraian</label><textarea name="uraian" x-model="editData.uraian" rows="2" class="input-field"></textarea></div>
                        <div><label class="input-label">SNP</label><input type="text" name="snp" x-model="editData.snp" class="input-field"></div>
                        <div><label class="input-label">Komponen</label><input type="text" name="komponen" x-model="editData.komponen" class="input-field"></div>
                        <div><label class="input-label">Saldo Normal</label><select name="saldo_normal" x-model="editData.saldo_normal" class="input-field"><option value="debit">Debit</option><option value="kredit">Kredit</option></select></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <x-confirm-modal
            show="showDeleteModal"
            action-binding="deleteRoute"
            method="DELETE"
            title="Hapus Akun?"
            message="Akun yang sudah dipakai di transaksi tidak disarankan dihapus."
        />
    </div>
</x-app-layout>
