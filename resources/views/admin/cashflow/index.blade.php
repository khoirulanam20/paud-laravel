<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Arus Kas (Cashflow)</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
            showCreateModal:false, showEditModal:false, showDeleteModal:false, showKwitansiModal:false,
            createType:'in', editData:{}, deleteRoute:'',
            kwitansiData:{}, kwitansiJenis:'pembayaran', kwitansiPdfUrl:'', kwitansiLoading:false,
            openEdit(d){ this.editData=d; this.showEditModal=true },
            openDelete(r){ this.deleteRoute=r; this.showDeleteModal=true },
            async openKwitansi(defaultsUrl, pdfUrl) {
                this.kwitansiLoading = true;
                try {
                    const res = await fetch(defaultsUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('Gagal memuat data kuitansi');
                    const data = await res.json();
                    this.kwitansiJenis = data.jenis || 'pembayaran';
                    this.kwitansiData = data;
                    this.kwitansiPdfUrl = pdfUrl;
                    this.showKwitansiModal = true;
                } catch (e) {
                    alert(e.message || 'Gagal memuat data kuitansi');
                } finally {
                    this.kwitansiLoading = false;
                }
            }
         }"
         @tour-close-modals.window="showCreateModal=false; showEditModal=false; showDeleteModal=false; showKwitansiModal=false">

        @if(session('success'))
            <div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>
        @endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <!-- Summary Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6" data-tour="admin-cashflow-stats">
            <div class="stat-card">
                <div class="stat-icon"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Total Pemasukan</p><p class="text-2xl font-bold" style="color:#1A6B6B;">Rp {{ number_format($totalIn, 0, ',', '.') }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FAD7D2; color:#C0392B;"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Total Pengeluaran</p><p class="text-2xl font-bold" style="color:#C0392B;">Rp {{ number_format($totalOut, 0, ',', '.') }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="{{ $balance >= 0 ? '' : 'background:#FAD7D2; color:#C0392B;' }}"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Saldo Kas</p><p class="text-2xl font-bold" style="color:{{ $balance >= 0 ? '#1A6B6B' : '#C0392B' }};">Rp {{ number_format($balance, 0, ',', '.') }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#D0E8E8; color:#1A6B6B;"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Auto Jurnal</p><p class="text-sm font-semibold" style="color:#1A6B6B;">Setting: Kas &rarr; {{ $setting->akunKas->nama ?? '-' }}</p></div>
            </div>
        </div>

        <!-- Summary per Kategori Arus Kas -->
        @if($summaryArusKas->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @foreach(['operasi' => 'Operasi', 'investasi' => 'Investasi', 'pendanaan' => 'Pendanaan'] as $key => $label)
                    @php $group = $summaryArusKas->get($key, collect()); @endphp
                    <div class="card p-4">
                        <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#9E9790;">Arus Kas {{ $label }}</p>
                        <p class="text-lg font-bold" style="color: #1A6B6B;">
                            Rp {{ number_format($group->where('type','in')->sum('amount') - $group->where('type','out')->sum('amount'), 0, ',', '.') }}
                        </p>
                        <div class="flex gap-3 mt-1 text-xs">
                            <span style="color: #1A6B6B;">+{{ number_format($group->where('type','in')->sum('amount'), 0, ',', '.') }}</span>
                            <span style="color: #C0392B;">-{{ number_format($group->where('type','out')->sum('amount'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Riwayat Transaksi</h3><p class="section-subtitle">Semua catatan pemasukan dan pengeluaran</p></div>
                <div class="flex gap-2 flex-wrap">
                    <form method="GET" class="flex gap-2">
                        <select name="bulan" class="input-field w-36 text-sm">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" class="input-field w-24 text-sm">
                            @foreach(range(now()->year - 2, now()->year) as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-secondary text-xs">Filter</button>
                    </form>
                    <button data-tour="admin-cashflow-add-btn" @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Catat Transaksi</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Tanggal</th>
                            <th class="hidden md:table-cell whitespace-nowrap">Akun</th>
                            <th>Keterangan</th>
                            <th class="text-center whitespace-nowrap">Jenis</th>
                            <th class="text-right whitespace-nowrap">Nominal (Rp)</th>
                            <th class="text-right w-52">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashflows as $trx)
                        <tr>
                            <td class="whitespace-nowrap font-medium align-middle" style="color:#2C2C2C;">{{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}</td>
                            <td class="hidden md:table-cell text-xs align-middle max-w-[9rem] truncate" style="color:#9E9790;" title="{{ optional($trx->akun)->kode }} - {{ optional($trx->akun)->nama }}">{{ optional($trx->akun)->kode }} - {{ optional($trx->akun)->nama }}</td>
                            <td class="align-middle max-w-[14rem] lg:max-w-xs">
                                <p class="text-sm leading-snug" style="color:#2C2C2C;">{{ $trx->description }}</p>
                                @if($trx->jurnal_id)
                                    <a href="{{ route('admin.jurnal.show', $trx->jurnal_id) }}" class="text-xs underline inline-block mt-0.5" style="color:#1A6B6B; opacity:0.7;">#{{ $trx->jurnal->no_jurnal ?? '' }}</a>
                                @endif
                            </td>
                            <td class="text-center align-middle whitespace-nowrap">
                                @if($trx->type === 'in') <span class="badge badge-green">Pemasukan</span>
                                @else <span class="badge badge-rose">Pengeluaran</span> @endif
                            </td>
                            <td class="text-right font-semibold align-middle whitespace-nowrap tabular-nums" style="{{ $trx->type === 'in' ? 'color:#1A6B6B;' : 'color:#C0392B;' }}">
                                {{ $trx->type === 'in' ? '+' : '-' }} {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-right align-middle">
                                <div class="inline-flex items-center justify-end gap-1 whitespace-nowrap">
                                    <button type="button"
                                        @click="openKwitansi('{{ route('admin.cashflow.kwitansi.defaults', $trx) }}', '{{ route('admin.cashflow.kwitansi.pdf', $trx) }}')"
                                        :disabled="kwitansiLoading"
                                        title="Cetak kuitansi BOP-12"
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg whitespace-nowrap transition-opacity disabled:opacity-50"
                                        style="color:#6B5B3A;background:#F5F0E8;border:1px solid #E8E0D4;">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Kuitansi</span>
                                    </button>
                                    <button type="button"
                                        @click="openEdit({{ json_encode($trx->only(['id','type','amount','description','date','akun_id','akun_lawan_id','sumber_dana_id'])) }})"
                                        title="Edit transaksi"
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg whitespace-nowrap"
                                        style="color:#1A6B6B;background:#D0E8E8;">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                    <button type="button"
                                        @click="openDelete('{{ route('admin.cashflow.destroy', $trx) }}')"
                                        title="Hapus transaksi"
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg whitespace-nowrap"
                                        style="color:#C0392B;background:#FAD7D2;">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-6 md:py-12 text-center" style="color:#9E9790;">Belum ada transaksi tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($cashflows->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $cashflows->links() }}</div>@endif
        </div>

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="modal-overlay" style="display:none;">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.cashflow.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Catat Transaksi Kas</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-4">
                        <div><label class="input-label">Tanggal</label><input type="date" name="date" required value="{{ date('Y-m-d') }}" class="input-field"></div>
                        <div><label class="input-label">Jenis</label><select name="type" x-model="createType" required class="input-field"><option value="in">Pemasukan</option><option value="out">Pengeluaran</option></select></div>
                        <div class="col-span-2"><label class="input-label">Nominal (Rp)</label><input type="number" name="amount" min="0" required placeholder="Contoh: 500000" class="input-field"></div>
                        <div class="col-span-2">
                            <label class="input-label">Akun Kas</label>
                            <select name="akun_id" class="input-field">
                                <option value="">— Gunakan Default Setting —</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->id }}" {{ $setting->akun_kas_id == $a->id ? 'selected' : '' }}>{{ $a->kode }} - {{ $a->nama }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs mt-1" style="color:#9E9790;">Akun default: {{ $setting->akunKas->kode ?? '' }} - {{ $setting->akunKas->nama ?? '-' }}. Jurnal dibuat otomatis.</p>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Kode Rekening (Akun Lawan)</label>
                            <select :name="createType === 'in' ? 'akun_lawan_id' : '_skip'" x-show="createType === 'in'" class="input-field">
                                <option value="">— Pilih kode rekening —</option>
                                @foreach($akunPendapatan as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->uraian ?? $a->nama, 50) }}</option>
                                @endforeach
                            </select>
                            <select x-show="createType === 'out'" style="display:none;" :name="createType === 'out' ? 'akun_lawan_id' : '_skip'" class="input-field">
                                <option value="">— Pilih kode rekening —</option>
                                @foreach($akunBeban as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->uraian ?? $a->nama, 50) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2"><label class="input-label">Keterangan</label><textarea name="description" required rows="2" placeholder="Pembayaran SPP Bulan Juli..." class="input-field"></textarea></div>
                        <div class="col-span-2" x-show="createType === 'out'">
                            <label class="input-label">Sumber Dana <span class="text-xs font-normal" style="color:#9E9790;">(untuk RKAS)</span></label>
                            <select name="sumber_dana_id" class="input-field">
                                <option value="">— Belum dialokasikan —</option>
                                @foreach($sumberDanas as $sd)
                                    <option value="{{ $sd->id }}">{{ $sd->kode }} — {{ $sd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Transaksi</button></div>
                </form>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="modal-overlay" style="display:none;">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/admin/cashflow/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Transaksi</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-4">
                        <div><label class="input-label">Tanggal</label><input type="date" name="date" :value="editData.date ? editData.date.split('T')[0] : ''" required class="input-field"></div>
                        <div><label class="input-label">Jenis</label><select name="type" x-model="editData.type" required class="input-field"><option value="in">Pemasukan</option><option value="out">Pengeluaran</option></select></div>
                        <div class="col-span-2"><label class="input-label">Nominal (Rp)</label><input type="number" name="amount" x-model="editData.amount" min="0" required class="input-field"></div>
                        <div class="col-span-2">
                            <label class="input-label">Akun Kas</label>
                            <select name="akun_id" x-model="editData.akun_id" class="input-field">
                                <option value="">— Gunakan Default —</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} - {{ $a->nama }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs mt-1" style="color:#9E9790;">Akun default: {{ $setting->akunKas->kode ?? '' }} - {{ $setting->akunKas->nama ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Kode Rekening (Akun Lawan)</label>
                            <select :name="editData.type === 'in' ? 'akun_lawan_id' : '_skip'" x-show="editData.type === 'in'" x-model="editData.akun_lawan_id" class="input-field">
                                <option value="">— Pilih —</option>
                                @foreach($akunPendapatan as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->uraian ?? $a->nama, 40) }}</option>
                                @endforeach
                            </select>
                            <select :name="editData.type === 'out' ? 'akun_lawan_id' : '_skip'" x-show="editData.type === 'out'" style="display:none;" x-model="editData.akun_lawan_id" class="input-field">
                                <option value="">— Pilih —</option>
                                @foreach($akunBeban as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->uraian ?? $a->nama, 40) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2"><label class="input-label">Keterangan</label><textarea name="description" x-model="editData.description" required rows="2" class="input-field"></textarea></div>
                        <div class="col-span-2" x-show="editData.type === 'out'">
                            <label class="input-label">Sumber Dana</label>
                            <select name="sumber_dana_id" x-model="editData.sumber_dana_id" class="input-field">
                                <option value="">— Belum dialokasikan —</option>
                                @foreach($sumberDanas as $sd)
                                    <option value="{{ $sd->id }}">{{ $sd->kode }} — {{ $sd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
        <x-confirm-modal
            show="showDeleteModal"
            action-binding="deleteRoute"
            method="DELETE"
            title="Hapus Transaksi?"
            message="Ini akan mengubah hitungan saldo kas secara permanen."
        />

        <!-- KWITANSI MODAL -->
        <div x-show="showKwitansiModal" class="modal-overlay" style="display:none;">
            <div x-show="showKwitansiModal" x-transition class="modal-box max-w-3xl max-h-[90vh] overflow-y-auto" @click.away="showKwitansiModal=false">
                <form :action="kwitansiPdfUrl" method="POST" target="_blank">
                    @csrf
                    <div class="modal-header">
                        <h3 class="section-title" x-text="kwitansiJenis === 'penerimaan' ? 'Kuitansi BOP-12 — Bukti Penerimaan' : 'Kuitansi BOP-12 — Bukti Pembayaran'"></h3>
                        <p class="section-subtitle" x-text="kwitansiJenis === 'penerimaan' ? 'Formulir penerimaan dana (pemasukan kas). Periksa dan edit sebelum unduh PDF.' : 'Formulir pengeluaran dana. Periksa dan edit sebelum unduh PDF.'"></p>
                    </div>
                    <div class="modal-body">
                        @include('kwitansi._preview-form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showKwitansiModal=false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">
                            <svg class="h-4 w-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
