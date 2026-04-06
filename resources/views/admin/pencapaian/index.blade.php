<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Evaluasi Pencapaian Siswa (Admin)</h2>
        </div>
    </x-slot>

    @php
        $filterAspek = $filterAspek ?? null;
        $kegiatanData = [];
        foreach ($kegiatans as $kg) {
            $kegiatanData[$kg->id] = [
                'id' => $kg->id,
                'kelas_id' => $kg->kelas_id,
                'title' => $kg->title,
                'date_label' => \Carbon\Carbon::parse($kg->date)->format('d M Y'),
                'matrikulasis' => $kg->matrikulasis->map(fn ($m) => [
                    'id' => $m->id,
                    'aspek' => $m->aspek,
                    'indicator' => $m->indicator,
                    'label' => ($m->aspek ? $m->aspek.': ' : '').$m->indicator,
                ])->values()->all()
            ];
        }
        $anakMap = [];
        foreach($anaks as $a) {
            $anakMap[$a->id] = ['id' => $a->id, 'name' => $a->name, 'kelas_id' => $a->kelas_id];
        }
        $flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        $payloadJson = json_encode(['kegiatanData' => $kegiatanData, 'anakMap' => $anakMap, 'editBundles' => $editBundles], $flags);
        if ($payloadJson === false) { $payloadJson = '{}'; }
    @endphp

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
            showCreateModal: false, showEditModal: false, showDeleteBundleModal: false,
            deleteBundleAnak: '', deleteBundleKeg: '', payload: {},
            selectedKelasIdCreate: '', selectedAnakId: '', selectedKegiatanId: '', selectedKegiatanIdEdit: '',
            editBundleKey: null, editNilai: {}, editCatatan: {}, createNilai: {}, createCatatan: {},
            isCompressing: false, compressedFile: null,
            init() {
                const el = document.getElementById('pencapaian-payload-json');
                if (el) { try { this.payload = JSON.parse(el.textContent); } catch (e) { this.payload = { kegiatanData: {}, anakMap: {}, editBundles: {} }; } }
            },
            async handleFile(e) {
                const file = e.target.files[0]; if (!file) return;
                this.isCompressing = true;
                try { this.compressedFile = await window.compressImage(file); } finally { this.isCompressing = false; }
            },
            submitWithCompression(formRef) {
                if (this.compressedFile) {
                    const dt = new DataTransfer(); dt.items.add(this.compressedFile);
                    this.$refs[formRef].querySelector('input[type=file]').files = dt.files;
                }
                this.$refs[formRef].submit();
            },
            get kegiatanData() { return this.payload.kegiatanData || {}; },
            get anakMap() { return this.payload.anakMap || {}; },
            get editBundles() { return this.payload.editBundles || {}; },
            get filteredAnaks() {
                if (!this.selectedKelasIdCreate) return Object.values(this.anakMap);
                return Object.values(this.anakMap).filter(a => a.kelas_id == this.selectedKelasIdCreate);
            },
            get filteredKegiatans() {
                if (!this.selectedAnakId) return [];
                const anakKelas = this.anakMap[this.selectedAnakId]?.kelas_id;
                return Object.values(this.kegiatanData).filter(k => k.kelas_id == anakKelas);
            },
            get matrikulasiOptions() { return (this.kegiatanData[this.selectedKegiatanId] || {}).matrikulasis || []; },
            get matrikulasiOptionsEdit() { return (this.kegiatanData[this.selectedKegiatanIdEdit] || {}).matrikulasis || []; },
            resetCreateMatrices() {
                this.createNilai = {}; this.createCatatan = {};
                (this.matrikulasiOptions || []).forEach(o => { this.createNilai[String(o.id)] = ''; this.createCatatan[String(o.id)] = ''; });
            },
            openCreateModal() { 
                this.selectedKelasIdCreate = ''; 
                this.selectedAnakId = ''; 
                this.selectedKegiatanId = ''; 
                this.createNilai = {}; 
                this.createCatatan = {}; 
                this.showCreateModal = true; 
            },
            openEditBundle(key) {
                const b = this.editBundles[key]; if (!b) return;
                this.editBundleKey = key; this.selectedKegiatanIdEdit = String(b.kegiatan_id);
                this.editNilai = {}; this.editCatatan = {};
                const opts = (this.kegiatanData[this.selectedKegiatanIdEdit] || {}).matrikulasis || [];
                opts.forEach(opt => {
                    const id = String(opt.id);
                    this.editNilai[id] = b.nilai[id] ?? b.nilai[opt.id] ?? '';
                    this.editCatatan[id] = b.catatan[id] ?? b.catatan[opt.id] ?? '';
                });
                this.showEditModal = true;
            },
            openDeleteBundle(key) {
                const b = this.editBundles[key]; if (!b) return;
                this.deleteBundleAnak = b.anak_id; this.deleteBundleKeg = b.kegiatan_id;
                this.showDeleteBundleModal = true;
            }
         }">

        <script type="application/json" id="pencapaian-payload-json">{!! $payloadJson !!}</script>

        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-6 border-b" style="background:#FAF6F0; border-color: rgba(0,0,0,0.06);">
                <div class="space-y-6">
                    <div class="space-y-1"><h3 class="text-xl font-bold" style="color:#2C2C2C;">Filter Evaluasi (Sekolah)</h3></div>
                    <form method="get" action="{{ route('admin.pencapaian.index') }}" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-4 items-end">
                        <div class="col-span-1 lg:col-span-2 min-w-0">
                            <label class="input-label">Dari</label>
                            <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="input-field w-full h-11 text-xs font-bold border-black/10" required>
                        </div>
                        <div class="col-span-1 lg:col-span-2 min-w-0">
                            <label class="input-label">Sampai</label>
                            <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="input-field w-full h-11 text-xs font-bold border-black/10" required>
                        </div>
                        <div class="col-span-2 md:col-span-1 lg:col-span-2 min-w-0">
                            <label class="input-label">Kelas</label>
                            <select name="filter_kelas_id" class="input-field w-full h-11 text-xs font-bold border-black/10">
                                <option value="">Semua Kelas</option>
                                @foreach($availableKelas as $k)<option value="{{ $k->id }}" @selected($filterKelasId === (int) $k->id)>{{ $k->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1 lg:col-span-2 min-w-0">
                            <label class="input-label">Nama Siswa</label>
                            <select name="filter_anak_id" class="input-field w-full h-11 text-xs font-bold border-black/10">
                                <option value="">Semua Siswa</option>
                                @foreach($anaks as $a)<option value="{{ $a->id }}" @selected($filterAnakId === (int) $a->id)>{{ $a->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1 lg:col-span-2 min-w-0">
                            <label class="input-label">Aspek</label>
                            <select name="aspek" class="input-field w-full h-11 text-xs font-bold border-black/10">
                                <option value="">Semua Aspek</option>
                                <option value="{{ \App\Support\FilterAspekPencapaian::UMUM }}" @selected($filterAspekRaw === \App\Support\FilterAspekPencapaian::UMUM)>Umum</option>
                                @foreach($aspekPilihan as $asp)<option value="{{ $asp }}" @selected($filterAspekRaw === $asp)>{{ $asp }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-span-2 lg:col-span-2"><button type="submit" class="btn-primary w-full h-11 font-bold">Cari Data</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="px-5 sm:px-6 py-3 mb-6 bg-[#FAF6F0] border rounded-2xl text-sm flex flex-wrap items-center gap-x-2 gap-y-1" style="border-color:rgba(0,0,0,0.04); color:#6B6560;">
            <span class="inline-flex items-center gap-1.5 shrink-0">
                <svg class="h-4 w-4 shrink-0 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Ringkasan:</span>
            </span>
            <span style="color:#2C2C2C;">
                @if(!$tanggalDari && !$tanggalSampai)
                    Seluruh Periode
                @elseif($tanggalDari === $tanggalSampai)
                    {{ \Carbon\Carbon::parse($tanggalDari)->translatedFormat('d M Y') }}
                @else
                    {{ \Carbon\Carbon::parse($tanggalDari)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d M Y') }}
                @endif
            </span>
            @if($filterKelasId)
                <span class="text-black/25 hidden sm:inline">·</span>
                <span>Kelas: {{ $availableKelas->firstWhere('id', $filterKelasId)?->name ?? 'Terpilih' }}</span>
            @endif
            @if($filterAnakId)
                <span class="text-black/25 hidden sm:inline">·</span>
                <span>Siswa: {{ $anaks->firstWhere('id', $filterAnakId)?->name ?? 'Terpilih' }}</span>
            @endif
            @if($filterAspekRaw !== '')
                <span class="text-black/25 hidden sm:inline">·</span>
                <span>{{ $filterAspekRaw === \App\Support\FilterAspekPencapaian::UMUM ? 'Umum' : $filterAspekRaw }}</span>
            @endif
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Laporan Pencapaian Sekolah</h3>
                <button type="button" @click="openCreateModal()" class="btn-primary">Buat Evaluasi</button>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Bukti</th><th>Siswa</th><th>Kegiatan</th><th>Aspek / Nilai</th><th>Tanggal</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($groupedPencapaian as $bundleKey => $rows)
                            @php $first = $rows->first(); @endphp
                        <tr>
                            <td>
                                @if($first->photo)<img src="{{ Storage::url($first->photo) }}" class="h-10 w-10 object-cover rounded shadow-sm cursor-pointer" onclick="window.open(this.src)">
                                @else<div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center opacity-30"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>@endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <x-foto-profil :path="$first->anak->photo ?? null" :name="$first->anak->name ?? '?'" size="sm" />
                                    <span class="font-semibold" style="color:#2C2C2C;">{{ $first->anak->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td><div class="font-medium" style="color:#2C2C2C;">{{ $first->kegiatan->title ?? '-' }}</div></td>
                            <td class="min-w-[240px]">
                                <div class="space-y-1.5">
                                    @foreach($rows->filter(fn ($p) => \App\Support\FilterAspekPencapaian::rowMatches($filterAspek, $p)) as $p)
                                        <div class="text-[11px] rounded bg-gray-50 p-1.5 border border-black/5">
                                            <span class="font-bold text-teal-700 uppercase" x-text="'{{ $p->matrikulasi->aspek ?: 'Umum' }}'"></span>: {{ $p->matrikulasi->indicator ?? '—' }}
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="font-bold px-1.5 py-0.5 rounded" style="background:{{ \App\Support\LabelSkorPencapaian::color($p->score) }};">{{ \App\Support\LabelSkorPencapaian::label($p->score) }}</span>
                                                @if($p->feedback)<span class="italic text-gray-400 truncate max-w-[150px]">{{ $p->feedback }}</span>@endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-xs">{{ \Carbon\Carbon::parse($first->created_at)->format('d/m/Y') }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="openEditBundle('{{ $bundleKey }}')" class="btn-xs border border-teal-200 text-teal-700 bg-teal-50 px-2 py-1 rounded">Edit</button>
                                    <button type="button" @click="openDeleteBundle('{{ $bundleKey }}')" class="btn-xs border border-red-200 text-red-700 bg-red-50 px-2 py-1 rounded">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center text-gray-400">Belum ada evaluasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($groupedPencapaian->hasPages())<div class="px-6 py-4 border-t">{{ $groupedPencapaian->links() }}</div>@endif
        </div>

        {{-- CREATE MODAL --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box max-w-lg w-full relative" @click.away="!isCompressing && (showCreateModal=false)">
                <div x-show="isCompressing" class="absolute inset-0 z-[60] bg-white/90 flex flex-col items-center justify-center">
                    <div class="h-10 w-10 border-4 border-teal-600/30 border-t-teal-600 rounded-full animate-spin"></div>
                    <p class="mt-3 text-sm font-bold text-teal-800 tracking-wider">Memproses...</p>
                </div>
                <form action="{{ route('admin.pencapaian.sync') }}" method="POST" enctype="multipart/form-data" x-ref="createForm" @submit.prevent="submitWithCompression('createForm')">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Rekam Pencapaian</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Filter Kelas</label>
                            <select class="input-field" x-model="selectedKelasIdCreate" @change="selectedAnakId = ''; selectedKegiatanId = ''">
                                <option value="">— Semua Kelas —</option>
                                @foreach($availableKelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Target Siswa</label>
                            <select name="anak_id" required class="input-field" x-model="selectedAnakId" @change="selectedKegiatanId = ''">
                                <option value="">— Pilih Siswa —</option>
                                <template x-for="a in filteredAnaks" :key="a.id">
                                    <option :value="a.id" x-text="a.name"></option>
                                </template>
                            </select>
                        </div>
                        <div x-show="selectedAnakId">
                            <label class="input-label">Jurnal Kegiatan</label>
                            <select name="kegiatan_id" required class="input-field" x-model="selectedKegiatanId" @change="resetCreateMatrices()">
                                <option value="">— Pilih Kegiatan —</option>
                                <template x-for="k in filteredKegiatans" :key="k.id"><option :value="k.id" x-text="k.date_label + ' : ' + k.title"></option></template>
                            </select>
                        </div>
                        <template x-for="opt in matrikulasiOptions" :key="opt.id">
                            <div class="p-3 bg-gray-50 rounded-xl border border-black/5 space-y-2">
                                <div class="text-xs font-black text-teal-800" x-text="opt.label"></div>
                                <select class="input-field bg-white" required :name="'nilai[' + opt.id + ']'" x-model="createNilai[String(opt.id)]">
                                    <option value="">— Skala Capaian —</option>
                                    <option value="BB">BB (Belum Berkembang)</option>
                                    <option value="MB">MB (Mulai Berkembang)</option>
                                    <option value="BSH">BSH (Berkembang Sesuai Harapan)</option>
                                    <option value="BSB">BSB (Berkembang Sangat Baik)</option>
                                </select>
                                <textarea class="input-field bg-white text-xs" rows="2" :name="'catatan[' + opt.id + ']'" x-model="createCatatan[String(opt.id)]" placeholder="Berikan umpan balik positif…"></textarea>
                            </div>
                        </template>
                        <div>
                            <label class="input-label">Unggah Dokumentasi (Evidence)</label>
                            <input type="file" name="photo" accept="image/*" class="input-field py-1" @change="handleFile($event)">
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" :disabled="!selectedKegiatanId || matrikulasiOptions.length === 0">Simpan Evaluasi</button></div>
                </form>
            </div>
        </div>

        {{-- EDIT MODAL --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box max-w-lg w-full relative" @click.away="!isCompressing && (showEditModal=false)">
                 <div x-show="isCompressing" class="absolute inset-0 z-[60] bg-white/90 flex flex-col items-center justify-center">
                    <div class="h-10 w-10 border-4 border-teal-600/30 border-t-teal-600 rounded-full animate-spin"></div>
                    <p class="mt-3 text-sm font-bold text-teal-800 tracking-wider">Memproses...</p>
                </div>
                <form action="{{ route('admin.pencapaian.sync') }}" method="POST" enctype="multipart/form-data" x-ref="editForm" @submit.prevent="submitWithCompression('editForm')">
                    @csrf
                    <input type="hidden" name="anak_id" :value="editBundles[editBundleKey]?.anak_id">
                    <input type="hidden" name="kegiatan_id" :value="editBundles[editBundleKey]?.kegiatan_id">
                    <div class="modal-header"><h3 class="section-title">Ubah Hasil Evaluasi</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <template x-for="opt in matrikulasiOptionsEdit" :key="opt.id">
                            <div class="p-3 bg-gray-50 rounded-xl border border-black/5 space-y-2">
                                <div class="text-xs font-black text-teal-800" x-text="opt.label"></div>
                                <select class="input-field bg-white" required :name="'nilai[' + opt.id + ']'" x-model="editNilai[String(opt.id)]">
                                    <option value="BB">BB</option><option value="MB">MB</option><option value="BSH">BSH</option><option value="BSB">BSB</option>
                                </select>
                                <textarea class="input-field bg-white text-xs" rows="2" :name="'catatan[' + opt.id + ']'" x-model="editCatatan[String(opt.id)]"></textarea>
                            </div>
                        </template>
                        <div>
                            <label class="input-label">Perbarui Dokumentasi</label>
                            <input type="file" name="photo" accept="image/*" class="input-field" @change="handleFile($event)">
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Update</button></div>
                </form>
            </div>
        </div>

        {{-- DELETE MODAL --}}
        <div x-show="showDeleteBundleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteBundleModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteBundleModal=false">
                <form method="POST" action="{{ route('admin.pencapaian.destroy-bundle') }}">
                    @csrf @method('DELETE')
                    <input type="hidden" name="anak_id" :value="deleteBundleAnak">
                    <input type="hidden" name="kegiatan_id" :value="deleteBundleKeg">
                    <div class="modal-body text-center py-6">
                        <div class="h-14 w-14 bg-red-100 text-red-600 rounded-2xl mx-auto flex items-center justify-center mb-4"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></div>
                        <h3 class="text-lg font-bold">Hapus Evaluasi Ini?</h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showDeleteBundleModal=false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-danger">Hapus Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
