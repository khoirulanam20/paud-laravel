<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Biaya Harian Siswa</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
             showCreateModal: false,
             showAddSiswaModal: false,
             filterKelasId: '',
             selectedIds: [],
             semuaAnak: @js($semuaAnak),
             get filteredAnak() {
                 return this.semuaAnak.filter(a =>
                     !a.sudah_assign &&
                     (!this.filterKelasId || String(a.kelas_id) === String(this.filterKelasId))
                 );
             },
             toggleAll(checked) {
                 this.selectedIds = checked ? this.filteredAnak.map(a => a.id) : [];
             },
             openAddSiswa() {
                 this.selectedIds = [];
                 this.filterKelasId = '';
                 this.showAddSiswaModal = true;
             }
         }" @tour-close-modals.window="showCreateModal=false; showAddSiswaModal=false">

        @if(session('success'))
            <div class="alert-success mb-5">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-danger mb-5">
                <ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Jenis Biaya -->
            <div class="card p-6" data-tour="admin-biaya-jenis">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="section-title">Jenis Biaya</h3>
                    <button data-tour="admin-biaya-add-jenis-btn" data-tour-open-modal="create" @click="showCreateModal=true" class="btn-primary text-xs px-3 py-1.5">+ Tambah</button>
                </div>
                <div class="space-y-2">
                    @forelse($semuaBiaya as $biaya)
                        <a href="{{ route('admin.biaya-bulanan.index', ['biaya_id' => $biaya->id, 'kelas_id' => $kelasId]) }}"
                           class="block p-3 rounded-lg border transition {{ $biayaTerpilih && $biayaTerpilih->id === $biaya->id ? 'border-[#1A6B6B] bg-[#D0E8E8]' : 'border-black/5 hover:bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-sm" style="color:#2C2C2C;">{{ $biaya->nama_biaya }}</p>
                                    <p class="text-xs mt-0.5" style="color:#9E9790;">Default: {{ $biaya->getNominalDefaultFormatted() }}/hari</p>
                                </div>
                                @if($biaya->is_aktif)
                                    <span class="badge badge-green text-[10px]">Aktif</span>
                                @else
                                    <span class="badge badge-gray text-[10px]">Nonaktif</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-center py-4" style="color:#9E9790;">Belum ada jenis biaya.</p>
                    @endforelse
                </div>
            </div>

            <!-- Daftar Siswa Ter-assign -->
            <div class="lg:col-span-2 card overflow-hidden" data-tour="admin-biaya-siswa">
                @if($biayaTerpilih)
                    <div class="px-6 py-4 border-b flex flex-col md:flex-row md:items-center justify-between gap-3" style="border-color:rgba(0,0,0,0.06);">
                        <div>
                            <h3 class="section-title">{{ $biayaTerpilih->nama_biaya }}</h3>
                            <p class="section-subtitle">Tambahkan siswa lalu atur biaya harian. Default: {{ $biayaTerpilih->getNominalDefaultFormatted() }}/hari</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <form method="GET" action="{{ route('admin.biaya-bulanan.index') }}" class="flex items-center gap-2">
                                <input type="hidden" name="biaya_id" value="{{ $biayaTerpilih->id }}">
                                <select name="kelas_id" class="input-field w-auto text-sm" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ $k->id == $kelasId ? 'selected' : '' }}>{{ $k->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <button type="button" data-tour="admin-biaya-add-siswa-btn" data-tour-open-modal="addSiswa" @click="openAddSiswa()" class="btn-primary text-sm">+ Tambah Siswa</button>
                        </div>
                    </div>

                    <form id="form-update-biaya" action="{{ route('admin.biaya-bulanan.update-siswa', $biayaTerpilih) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                    </form>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-right">Biaya Harian (Rp)</th>
                                    <th class="text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswaTerassign as $siswaBiaya)
                                    <tr>
                                        <td class="font-medium" style="color:#2C2C2C;">{{ $siswaBiaya->anak->name }}</td>
                                        <td>{{ $siswaBiaya->anak->kelas->name ?? '-' }}</td>
                                        <td class="text-right">
                                            <input type="number" form="form-update-biaya" name="biaya_harian[{{ $siswaBiaya->anak_id }}]"
                                                   value="{{ $siswaBiaya->biaya_harian }}"
                                                   min="0" step="100"
                                                   placeholder="{{ $biayaTerpilih->nominal_default }}"
                                                   class="input-field w-32 text-right text-sm ml-auto">
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.biaya-bulanan.remove-siswa', [$biayaTerpilih, $siswaBiaya->anak]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus siswa dari daftar biaya ini?')">
                                                @csrf @method('DELETE')
                                                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                                                <button type="submit" class="text-xs font-semibold px-2 py-1 rounded" style="color:#C0392B;background:#FEE2E2;">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center" style="color:#9E9790;">
                                            Belum ada siswa. Klik <strong>Tambah Siswa</strong> untuk memilih dari daftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($siswaTerassign->count() > 0)
                        <div class="px-6 py-4 border-t flex justify-end" style="border-color:rgba(0,0,0,0.06);">
                            <button type="submit" form="form-update-biaya" data-tour="admin-biaya-save-btn" class="btn-primary">Simpan Biaya Harian</button>
                        </div>
                    @endif
                @else
                    <div class="px-6 py-12 text-center" style="color:#9E9790;">
                        <p>Tambah jenis biaya terlebih dahulu untuk mulai mengatur biaya harian siswa.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- CREATE JENIS BIAYA MODAL -->
        <div x-show="showCreateModal" class="modal-overlay" style="display:none;">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.biaya-bulanan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Tambah Jenis Biaya</h3></div>
                    <div class="modal-body space-y-4" data-tour="modal-create-section-form">
                        <div>
                            <label class="input-label">Nama Biaya</label>
                            <input type="text" name="nama_biaya" required placeholder="Contoh: SPP Harian" class="input-field">
                        </div>
                        <div>
                            <label class="input-label">Biaya Harian Default (Rp)</label>
                            <input type="number" name="nominal_default" min="0" required placeholder="Contoh: 50000" class="input-field">
                            <p class="text-xs mt-1" style="color:#9E9790;">Digunakan saat siswa baru ditambahkan ke daftar</p>
                        </div>
                        <div>
                            <label class="input-label">Keterangan (Opsional)</label>
                            <textarea name="keterangan" rows="2" class="input-field"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button>
                        <button type="submit" data-tour="modal-create-submit" class="btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAMBAH SISWA MODAL -->
        @if($biayaTerpilih)
        <div x-show="showAddSiswaModal" data-tour="modal-add-siswa" class="modal-overlay" style="display:none;">
            <div x-show="showAddSiswaModal" x-transition class="modal-box max-w-lg" @click.away="showAddSiswaModal=false">
                <form action="{{ route('admin.biaya-bulanan.add-siswa', $biayaTerpilih) }}" method="POST">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                    <div class="modal-header">
                        <h3 class="section-title">Tambah Siswa</h3>
                        <p class="section-subtitle mt-1">Pilih siswa yang akan dikenakan {{ $biayaTerpilih->nama_biaya }}</p>
                    </div>
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="input-label">Filter Kelas</label>
                            <select x-model="filterKelasId" @change="selectedIds = []" class="input-field">
                                <option value="">Semua Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="border rounded-lg overflow-hidden" style="border-color:rgba(0,0,0,0.08);" data-tour="modal-add-siswa-list">
                            <div class="px-4 py-2 flex items-center justify-between text-xs font-semibold uppercase tracking-wider" style="background:#F9FAFB;color:#9E9790;">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                           class="rounded border-gray-300"
                                           :checked="filteredAnak.length > 0 && selectedIds.length === filteredAnak.length"
                                           @change="toggleAll($event.target.checked)">
                                    Pilih Semua
                                </label>
                                <span x-text="selectedIds.length + ' dipilih'"></span>
                            </div>
                            <div class="max-h-72 overflow-y-auto divide-y" style="divide-color:rgba(0,0,0,0.06);">
                                <template x-for="anak in filteredAnak" :key="anak.id">
                                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50">
                                        <input type="checkbox" name="anak_ids[]" :value="anak.id"
                                               class="rounded border-gray-300"
                                               x-model.number="selectedIds">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium truncate" style="color:#2C2C2C;" x-text="anak.name"></p>
                                            <p class="text-xs" style="color:#9E9790;" x-text="anak.kelas_name"></p>
                                        </div>
                                    </label>
                                </template>
                                <div x-show="filteredAnak.length === 0" class="px-4 py-8 text-center text-sm" style="color:#9E9790;">
                                    <span x-show="semuaAnak.every(a => a.sudah_assign)">Semua siswa sudah ada di daftar.</span>
                                    <span x-show="!semuaAnak.every(a => a.sudah_assign)">Tidak ada siswa di kelas ini.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showAddSiswaModal=false" class="btn-secondary">Batal</button>
                        <button type="submit" data-tour="modal-add-siswa-submit" class="btn-primary" :disabled="selectedIds.length === 0">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
