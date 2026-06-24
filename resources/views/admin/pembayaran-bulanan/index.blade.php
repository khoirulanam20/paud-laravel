<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Rekap Pembayaran Bulanan</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
             showGenerateModal: false,
             showItemModal: false,
             itemModalRow: null,
             itemModalNama: '',
             itemModalJumlah: 0,
             itemModalEditIdx: null,
             previewData: [],
             selectedKeys: [],
             diskons: @js(\App\Models\Diskon::where('sekolah_id', auth()->user()->sekolah_id)->where('is_aktif', true)->get(['id','nama_diskon','tipe','nilai'])),
             bulan: {{ $bulan }},
             tahun: {{ $tahun }},
             loading: false,
             rowKey(row) {
                 return row.key || (row.anak_id + '_' + row.biaya_id);
             },
             get isAllSelected() {
                 return this.previewData.length > 0 && this.selectedKeys.length === this.previewData.length;
             },
             toggleAll(checked) {
                 this.selectedKeys = checked ? this.previewData.map(r => this.rowKey(r)) : [];
             },
             sumTambahan(row) {
                 return (row.biaya_tambahan || []).reduce((s, i) => s + (parseFloat(i.jumlah) || 0), 0);
             },
             totalBaris(row) {
                 return (parseFloat(row.subtotal) || 0) + this.sumTambahan(row);
             },
             openItemModal(row, idx) {
                 this.itemModalRow = row;
                 if (idx !== undefined && row.biaya_tambahan && row.biaya_tambahan[idx]) {
                     this.itemModalNama = row.biaya_tambahan[idx].nama_item;
                     this.itemModalJumlah = row.biaya_tambahan[idx].jumlah;
                     this.itemModalEditIdx = idx;
                 } else {
                     this.itemModalNama = '';
                     this.itemModalJumlah = 0;
                     this.itemModalEditIdx = null;
                 }
                 this.showItemModal = true;
             },
             saveItemModal() {
                 const nama = this.itemModalNama.trim();
                 const jumlah = parseFloat(this.itemModalJumlah) || 0;
                 if (!nama || jumlah <= 0) return;
                 if (!this.itemModalRow.biaya_tambahan) this.itemModalRow.biaya_tambahan = [];
                 if (this.itemModalEditIdx !== null) {
                     this.itemModalRow.biaya_tambahan[this.itemModalEditIdx] = { nama_item: nama, jumlah: jumlah };
                 } else {
                     this.itemModalRow.biaya_tambahan.push({ nama_item: nama, jumlah: jumlah });
                 }
                 this.showItemModal = false;
                 this.itemModalRow = null;
             },
             removeItem(row, idx) {
                 row.biaya_tambahan.splice(idx, 1);
             },
             async loadPreview() {
                 this.loading = true;
                 try {
                     const res = await fetch(`{{ route('admin.pembayaran-bulanan.generate-preview') }}?bulan=${this.bulan}&tahun=${this.tahun}`);
                     const data = await res.json();
                     this.previewData = (data.preview || []).map(r => ({ ...r, biaya_tambahan: [] }));
                     this.selectedKeys = this.previewData.map(r => this.rowKey(r));
                 } catch(e) { console.error(e); }
                 this.loading = false;
             },
             openGenerate() {
                 this.showGenerateModal = true;
                 this.loadPreview();
             },
             formatRp(n) {
                 return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
             }
         }" @tour-close-modals.window="showGenerateModal=false">

        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6" data-tour="admin-pembayaran-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Total Tagihan</p><p class="text-2xl font-bold">{{ $summary->total_tagihan ?? 0 }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FEF3C7;color:#D97706;">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Menunggu</p><p class="text-2xl font-bold" style="color:#D97706;">{{ $summary->pending ?? 0 }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#D0E8E8;color:#1A6B6B;">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Lunas</p><p class="text-2xl font-bold" style="color:#1A6B6B;">{{ $summary->approved ?? 0 }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#D0E8E8;color:#1A6B6B;">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Total Lunas</p><p class="text-2xl font-bold" style="color:#1A6B6B;">Rp {{ number_format($summary->nominal_approved ?? 0, 0, ',', '.') }}</p></div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Daftar Tagihan</h3><p class="section-subtitle">Total = biaya bulanan + biaya lain - diskon</p></div>
                <div class="flex flex-wrap items-center gap-3">
                    <form action="{{ route('admin.pembayaran-bulanan.index') }}" method="GET" class="flex items-center gap-2" data-tour="admin-pembayaran-filter">
                        <select name="bulan" class="input-field w-auto">
                            @foreach(range(1,12) as $b)<option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}</option>@endforeach
                        </select>
                        <select name="tahun" class="input-field w-auto">
                            @foreach(range(date('Y')-1, date('Y')+1) as $t)<option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>@endforeach
                        </select>
                        <select name="kelas_id" class="input-field w-auto">
                            <option value="">Semua Kelas</option>
                            @foreach($kelas as $k)<option value="{{ $k->id }}" {{ $k->id == $kelasId ? 'selected' : '' }}>{{ $k->name }}</option>@endforeach
                        </select>
                        <select name="status" class="input-field w-auto">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Lunas</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <button type="submit" class="btn-secondary">Filter</button>
                    </form>
                    <button data-tour="admin-pembayaran-generate-btn" data-tour-open-modal="generate" @click="openGenerate()" class="btn-primary">Generate Tagihan</button>
                </div>
            </div>
            <div class="overflow-x-auto" data-tour="admin-pembayaran-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Siswa</th><th>Kelas</th><th>Biaya</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-right">Biaya/Bln</th>
                            <th class="text-right">Biaya Lain</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right">Diskon</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $p)
                            <tr>
                                <td class="font-medium">{{ $p->anak->name ?? '-' }}</td>
                                <td>{{ $p->anak->kelas->name ?? '-' }}</td>
                                <td class="text-xs" style="color:#9E9790;">{{ $p->biayaBulananSekolah->nama_biaya ?? '-' }}</td>
                                <td class="text-center font-semibold" style="color:#1A6B6B;">{{ $p->hari_hadir }}</td>
                                <td class="text-right text-xs">{{ $p->getBiayaPerHariFormatted() }}</td>
                                <td class="text-right text-xs" style="color:#9E9790;">
                                    @php $totalTambahan = $p->getTotalBiayaTambahan(); @endphp
                                    @if($totalTambahan > 0)
                                        {{ $p->getTotalBiayaTambahanFormatted() }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right text-xs">{{ $p->getSubtotalFormatted() }}</td>
                                <td class="text-right text-xs" style="color:#C0392B;">{{ $p->nilai_diskon > 0 ? '-'.$p->getNilaiDiskonFormatted() : '-' }}</td>
                                <td class="text-right font-semibold" style="color:#1A6B6B;">{{ $p->getTotalFormatted() }}</td>
                                <td class="text-center"><span class="badge badge-{{ $p->status_badge }}">{{ $p->status_label }}</span></td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a @if($loop->first) data-tour="admin-pembayaran-action-edit" @endif href="{{ route('admin.pembayaran-bulanan.show', $p) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</a>
                                        @if($p->status === 'pending')
                                            <form action="{{ route('admin.pembayaran-bulanan.destroy', $p) }}" method="POST"
                                                  onsubmit="return confirm('Hapus tagihan ini?')">
                                                @csrf @method('DELETE')
                                                <button @if($loop->first) data-tour="admin-pembayaran-action-delete" data-tour-demo-action="delete" @endif type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FEE2E2;">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="py-12 text-center" style="color:#9E9790;">Belum ada tagihan. Klik Generate Tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pembayarans->hasPages())<div class="px-6 py-4 border-t">{{ $pembayarans->withQueryString()->links() }}</div>@endif
        </div>

        <!-- GENERATE MODAL dengan diskon per siswa + biaya tambahan -->
        <div x-show="showGenerateModal" data-tour="modal-generate" class="modal-overlay" style="display:none;">
            <div x-show="showGenerateModal" x-transition class="modal-box max-w-5xl" @click.away="showGenerateModal=false">
                <form action="{{ route('admin.pembayaran-bulanan.generate') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Generate Tagihan Bulanan</h3></div>
                    <div class="modal-body space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Bulan</label>
                                <select name="bulan" x-model.number="bulan" @change="loadPreview()" class="input-field">
                                    @foreach(range(1,12) as $b)<option value="{{ $b }}">{{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Tahun</label>
                                <select name="tahun" x-model.number="tahun" @change="loadPreview()" class="input-field">
                                    @foreach(range(date('Y')-1, date('Y')+1) as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                                </select>
                            </div>
                        </div>

                        <div x-show="loading" class="text-center py-4 text-sm" style="color:#9E9790;">Memuat data...</div>

                        <div x-show="!loading && previewData.length > 0" class="space-y-2" data-tour="modal-generate-checklist">
                            <div class="flex items-center justify-between px-1 text-xs font-semibold uppercase tracking-wider" style="color:#9E9790;">
                                <label class="flex items-center gap-2 cursor-pointer normal-case">
                                    <input type="checkbox" class="rounded border-gray-300"
                                           :checked="isAllSelected"
                                           @change="toggleAll($event.target.checked)">
                                    Pilih Semua
                                </label>
                                <span x-text="selectedKeys.length + ' dipilih'"></span>
                            </div>
                            <div class="overflow-x-auto max-h-96">
                            <table class="data-table text-sm">
                                <thead>
                                    <tr>
                                        <th class="w-10"></th>
                                        <th>Siswa</th><th>Kelas</th><th>Biaya</th>
                                        <th class="text-center">Hadir</th>
                                        <th class="text-right">Biaya/Bln</th>
                                        <th class="text-right">Subtotal</th>
                                        <th>Diskon</th>
                                        <th>Biaya Lain</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, idx) in previewData" :key="rowKey(row)">
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="tagihan[]" :value="rowKey(row)"
                                                       class="rounded border-gray-300"
                                                       x-model="selectedKeys">
                                            </td>
                                            <td x-text="row.anak_name"></td>
                                            <td x-text="row.kelas_name"></td>
                                            <td x-text="row.biaya_name" class="text-xs" style="color:#9E9790;"></td>
                                            <td class="text-center" x-text="row.hari_hadir"></td>
                                            <td class="text-right" x-text="formatRp(row.biaya_bulanan)"></td>
                                            <td class="text-right" x-text="formatRp(totalBaris(row))"></td>
                                            <td>
                                                <select :name="'diskon[' + rowKey(row) + ']'" class="input-field text-xs py-1"
                                                        :disabled="!selectedKeys.includes(rowKey(row))">
                                                    <option value="">Tanpa Diskon</option>
                                                    <template x-for="d in diskons" :key="d.id">
                                                        <option :value="d.id" x-text="d.nama_diskon + ' (' + (d.tipe === 'persentase' ? d.nilai + '%' : 'Rp ' + new Intl.NumberFormat('id-ID').format(d.nilai)) + ')'"></option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="flex flex-wrap items-center gap-1 min-w-[160px]">
                                                    <template x-for="(item, i) in (row.biaya_tambahan || [])" :key="i">
                                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded"
                                                              style="background:#D0E8E8;color:#1A6B6B;">
                                                            <span x-text="item.nama_item + ' ' + formatRp(item.jumlah)" class="cursor-pointer"
                                                                  @click="openItemModal(row, i)"></span>
                                                            <button type="button" @click="removeItem(row, i)"
                                                                    class="font-bold leading-none hover:opacity-70">&times;</button>
                                                            <input type="hidden" :name="'biaya_tambahan[' + rowKey(row) + '][' + i + '][nama_item]'"
                                                                   :value="item.nama_item">
                                                            <input type="hidden" :name="'biaya_tambahan[' + rowKey(row) + '][' + i + '][jumlah]'"
                                                                   :value="item.jumlah">
                                                        </span>
                                                    </template>
                                                    <button type="button" @click="openItemModal(row)"
                                                            class="text-xs px-2 py-0.5 rounded"
                                                            style="color:#1A6B6B;background:#D0E8E8;"
                                                            :disabled="!selectedKeys.includes(rowKey(row))">
                                                        + Tambah
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <div x-show="!loading && previewData.length === 0" class="text-center py-4 text-sm" style="color:#9E9790;">
                            Belum ada siswa di menu Biaya Bulanan. Tambahkan siswa terlebih dahulu.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showGenerateModal=false" class="btn-secondary">Batal</button>
                        <button type="submit" data-tour="modal-generate-submit" class="btn-primary" :disabled="selectedKeys.length === 0">Generate</button>
                    </div>
                </form>

                <!-- MODAL TAMBAH BIAYA LAIN (nested di dalam generate modal-box) -->
                <div x-show="showItemModal" class="modal-overlay" style="display:none; z-index:60;">
                    <div x-show="showItemModal" x-transition class="modal-box max-w-sm" @click.away="showItemModal=false">
                        <div class="modal-header"><h3 class="section-title" x-text="itemModalEditIdx !== null ? 'Edit Biaya Lain' : 'Tambah Biaya Lain'"></h3></div>
                        <div class="modal-body space-y-3">
                            <div>
                                <label class="input-label">Nama Biaya</label>
                                <input type="text" x-model="itemModalNama" placeholder="Contoh: Popok, Ekstra" class="input-field" @keydown.enter="saveItemModal()">
                            </div>
                            <div>
                                <label class="input-label">Jumlah (Rp)</label>
                                <input type="number" x-model="itemModalJumlah" min="0" placeholder="0" class="input-field" @keydown.enter="saveItemModal()">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click.stop="showItemModal=false" class="btn-secondary">Batal</button>
                            <button type="button" @click.stop="saveItemModal()" class="btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END GENERATE MODAL -->
    </div>
</x-app-layout>
