<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Evaluasi Pencapaian Siswa</h2>
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
            $anakMap[$a->id] = ['id' => $a->id, 'kelas_id' => $a->kelas_id];
        }
        $flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        $payloadJson = json_encode(['kegiatanData' => $kegiatanData, 'anakMap' => $anakMap, 'editBundles' => $editBundles], $flags);
        if ($payloadJson === false) {
            $payloadJson = '{}';
        }
    @endphp

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
            showCreateModal: false,
            showEditModal: false,
            showDeleteBundleModal: false,
            deleteBundleAnak: '',
            deleteBundleKeg: '',
            payload: {},
            selectedAnakId: '',
            selectedKegiatanId: '',
            selectedKegiatanIdEdit: '',
            editBundleKey: null,
            editNilai: {},
            editCatatan: {},
            createNilai: {},
            createCatatan: {},
            isCompressing: false,
            compressedFile: null,
            init() {
                const el = document.getElementById('pencapaian-payload-json');
                if (el) {
                    try { this.payload = JSON.parse(el.textContent); } catch (e) { this.payload = { kegiatanData: {}, anakMap: {}, editBundles: {} }; }
                }
            },
            async handleFile(e) {
                const file = e.target.files[0];
                if (!file) return;
                this.isCompressing = true;
                try {
                    this.compressedFile = await window.compressImage(file);
                } finally {
                    this.isCompressing = false;
                }
            },
            submitWithCompression(formRef) {
                if (this.compressedFile) {
                    const dt = new DataTransfer();
                    dt.items.add(this.compressedFile);
                    this.$refs[formRef].querySelector('input[type=file]').files = dt.files;
                }
                this.$refs[formRef].submit();
            },
            get kegiatanData() { return this.payload.kegiatanData || {}; },
            get anakMap() { return this.payload.anakMap || {}; },
            get editBundles() { return this.payload.editBundles || {}; },
            
            // Filtered options for Create Modal
            get filteredKegiatans() {
                if (!this.selectedAnakId) return [];
                const anakKelas = this.anakMap[this.selectedAnakId]?.kelas_id;
                return Object.values(this.kegiatanData).filter(k => k.kelas_id == anakKelas);
            },
            get matrikulasiOptions() { 
                return (this.kegiatanData[this.selectedKegiatanId] || {}).matrikulasis || []; 
            },
            get matrikulasiOptionsEdit() { 
                return (this.kegiatanData[this.selectedKegiatanIdEdit] || {}).matrikulasis || []; 
            },
            resetCreateMatrices() {
                this.createNilai = {};
                this.createCatatan = {};
                (this.matrikulasiOptions || []).forEach(o => {
                    const id = String(o.id);
                    this.createNilai[id] = '';
                    this.createCatatan[id] = '';
                });
            },
            openCreateModal() {
                this.selectedAnakId = '';
                this.selectedKegiatanId = '';
                this.createNilai = {};
                this.createCatatan = {};
                this.showCreateModal = true;
            },
            openEditBundle(key) {
                const b = this.editBundles[key];
                if (!b) return;
                this.editBundleKey = key;
                this.selectedKegiatanIdEdit = String(b.kegiatan_id);
                this.editNilai = {};
                this.editCatatan = {};
                const opts = (this.kegiatanData[this.selectedKegiatanIdEdit] || {}).matrikulasis || [];
                opts.forEach(opt => {
                    const id = String(opt.id);
                    this.editNilai[id] = b.nilai[id] ?? b.nilai[opt.id] ?? '';
                    this.editCatatan[id] = b.catatan[id] ?? b.catatan[opt.id] ?? '';
                });
                this.showEditModal = true;
            },
            openDeleteBundle(key) {
                const b = this.editBundles[key];
                if (!b) return;
                this.deleteBundleAnak = b.anak_id;
                this.deleteBundleKeg = b.kegiatan_id;
                this.showDeleteBundleModal = true;
            }
         }">

        <script type="application/json" id="pencapaian-payload-json">{!! $payloadJson !!}</script>

        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ session('success') }}</div>@endif
        @if($errors->any())
            <div class="alert-danger mb-5">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-6 border-b" style="background:#FAF6F0; border-color: rgba(0,0,0,0.06);">
                <div class="space-y-6">
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold" style="color:#2C2C2C;">Filter Evaluasi</h3>
                        <p class="text-sm font-medium" style="color:#9E9790;">Cari data berdasarkan rentang tanggal, kelas, dan indikator aspek</p>
                    </div>
                    
                    <form method="get" action="{{ route('pengajar.pencapaian.index') }}" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-4 items-end">
                        <div class="col-span-1 lg:col-span-2 min-w-0">
                            <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Dari</label>
                            <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="input-field w-full h-11 text-xs font-bold border-black/10 transition focus:border-teal-500" required style="background:white;">
                        </div>
                        <div class="col-span-1 lg:col-span-2 min-w-0">
                            <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Sampai</label>
                            <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="input-field w-full h-11 text-xs font-bold border-black/10 transition focus:border-teal-500" required style="background:white;">
                        </div>
                        <div class="col-span-2 md:col-span-1 lg:col-span-2 min-w-0">
                            <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Kelas</label>
                            <select name="filter_kelas_id" class="input-field w-full h-11 text-xs font-bold border-black/10 transition focus:border-teal-500" style="background:white;">
                                <option value="">Semua Kelas</option>
                                @foreach($availableKelas as $k)
                                    <option value="{{ $k->id }}" @selected($filterKelasId === (int) $k->id)>{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1 lg:col-span-2 min-w-0">
                            <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Anak</label>
                            <select name="filter_anak_id" class="input-field w-full h-11 text-xs font-bold border-black/10 transition focus:border-teal-500" style="background:white;">
                                <option value="">Semua Anak</option>
                                @foreach($anaks as $a)
                                    <option value="{{ $a->id }}" @selected($filterAnakId === (int) $a->id)>{{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1 lg:col-span-2 min-w-0">
                            <label class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color:#1A6B6B;">Aspek</label>
                            <select name="aspek" class="input-field w-full h-11 text-xs font-bold border-black/10 transition focus:border-teal-500" style="background:white;">
                                <option value="">Semua Aspek</option>
                                <option value="{{ \App\Support\FilterAspekPencapaian::UMUM }}" @selected($filterAspekRaw === \App\Support\FilterAspekPencapaian::UMUM)>Umum / Tanpa Aspek</option>
                                @foreach($aspekPilihan as $asp)
                                    <option value="{{ $asp }}" @selected($filterAspekRaw === $asp)>{{ $asp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 lg:col-span-2">
                            <button type="submit" class="btn-primary w-full h-11 font-bold shadow-lg shadow-teal-900/10 flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <span>Cari Data</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="px-5 sm:px-6 py-3 border-t text-sm flex flex-wrap items-center gap-x-2 gap-y-1" style="background:#FAF6F0; border-color:rgba(0,0,0,0.04); color:#6B6560;">
                <span class="inline-flex items-center gap-1.5 shrink-0">
                    <svg class="h-4 w-4 shrink-0 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Ringkasan:</span>
                </span>
                <span style="color:#2C2C2C;">
                    @if($tanggalDari === $tanggalSampai)
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
                    <span>Anak: {{ $anaks->firstWhere('id', $filterAnakId)?->name ?? 'Anak terpilih' }}</span>
                @endif
                @if($filterAspekRaw !== '')
                    <span class="text-black/25 hidden sm:inline">·</span>
                    <span>{{ $filterAspekRaw === \App\Support\FilterAspekPencapaian::UMUM ? 'Umum / tanpa aspek' : $filterAspekRaw }}</span>
                @endif
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Laporan per Kegiatan & Aspek</h3><p class="section-subtitle">Satu baris = satu anak + satu kegiatan; nilai per indikator matrikulasi.</p></div>
                <button type="button" @click="openCreateModal()" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Buat Evaluasi</button>
            </div>

            {{-- Mobile Card View (Hidden on Tablet/Desktop) --}}
            <div class="block lg:hidden pb-4">
                <div class="grid grid-cols-1 gap-4 px-4 pt-4">
                    @forelse($groupedPencapaian as $bundleKey => $rows)
                        @php $first = $rows->first(); @endphp
                        <div class="relative rounded-2xl bg-white border border-black/5 shadow-sm p-4 hover:shadow-md transition">
                            <div class="flex items-start gap-3 mb-4">
                                @if($first->photo)
                                    <img src="{{ Storage::url($first->photo) }}" class="h-16 w-16 object-cover rounded-xl shadow-sm shrink-0" onclick="window.open(this.src)">
                                @else
                                    <div class="h-16 w-16 bg-gray-50 rounded-xl flex items-center justify-center shrink-0 border border-black/5">
                                        <svg class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <x-foto-profil :path="$first->anak->photo ?? null" :name="$first->anak->name ?? '?'" size="xs" />
                                        <h4 class="font-bold text-[#2C2C2C] truncate text-sm">{{ $first->anak->name ?? '-' }}</h4>
                                    </div>
                                    <p class="text-[11px] font-bold text-teal-600 mb-1 leading-tight">{{ $first->kegiatan->title ?? '-' }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($first->created_at)->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4 bg-gray-50/50 rounded-xl p-3 border border-black/[0.03]">
                                @foreach($rows->filter(fn ($p) => \App\Support\FilterAspekPencapaian::rowMatches($filterAspek, $p))->sortBy(fn ($p) => ($p->matrikulasi->aspek ?? '').($p->matrikulasi->indicator ?? '')) as $p)
                                    <div class="pb-2 border-b border-black/[0.05] last:border-0 last:pb-0">
                                        <div class="flex justify-between items-start gap-2 mb-1">
                                            <span class="text-[10px] font-extrabold text-teal-800 uppercase tracking-tight">{{ $p->matrikulasi->aspek ?: 'Umum' }}</span>
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background:{{ \App\Support\LabelSkorPencapaian::color($p->score) }};">
                                                {{ \App\Support\LabelSkorPencapaian::label($p->score) }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-gray-600 leading-tight mb-1">{{ $p->matrikulasi->indicator ?? 'Data indikator lama' }}</p>
                                        @if($p->feedback)
                                            <p class="text-[10px] text-gray-400 italic bg-white/50 px-2 py-1 rounded border border-black/5 mt-1">{{ $p->feedback }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="openEditBundle('{{ $bundleKey }}')" class="flex-1 py-2.5 rounded-xl bg-teal-50 text-teal-700 text-xs font-bold text-center hover:bg-teal-100 transition">Edit Evaluasi</button>
                                <button type="button" @click="openDeleteBundle('{{ $bundleKey }}')" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition border border-rose-100/50">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-sm">Belum ada evaluasi pada rentang tanggal ini.</div>
                    @endforelse
                </div>
            </div>

            {{-- Desktop Table View (Hidden on Mobile) --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Bukti</th><th>Anak</th><th>Kegiatan</th><th>Aspek &amp; nilai</th><th>Tanggal</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($groupedPencapaian as $bundleKey => $rows)
                            @php $first = $rows->first(); @endphp
                        <tr>
                            <td>
                                @if($first->photo)
                                    <img src="{{ Storage::url($first->photo) }}" class="h-10 w-10 object-cover rounded shadow-sm cursor-pointer hover:opacity-80 transition" onclick="window.open(this.src)">
                                @else
                                    <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center"><svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <x-foto-profil :path="$first->anak->photo ?? null" :name="$first->anak->name ?? '?'" size="sm" />
                                    <div>
                                        <span class="font-semibold block" style="color:#2C2C2C;">{{ $first->anak->name ?? '-' }}</span>
                                        @if($first->anak && $first->anak->dob)
                                            <span class="text-[10px] font-bold text-[#1A6B6B]">{{ $first->anak->age }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($first->kegiatan)
                                    <div class="font-medium text-sm" style="color:#2C2C2C;">{{ $first->kegiatan->title }}</div>
                                    <div class="text-xs" style="color:#9E9790;">{{ \Carbon\Carbon::parse($first->kegiatan->date)->format('d M Y') }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="min-w-[240px]">
                                <div class="space-y-2">
                                    @foreach($rows->filter(fn ($p) => \App\Support\FilterAspekPencapaian::rowMatches($filterAspek, $p))->sortBy(fn ($p) => ($p->matrikulasi->aspek ?? '').($p->matrikulasi->indicator ?? '')) as $p)
                                        <div class="text-xs rounded-lg px-2 py-1.5 border" style="border-color:rgba(0,0,0,0.06);background:#FAF6F0;">
                                            @if($p->matrikulasi)
                                                <div class="font-semibold" style="color:#1A6B6B;">{{ $p->matrikulasi->aspek ?: 'Aspek' }}</div>
                                                <div class="text-[11px] mt-0.5" style="color:#5A5A5A;">{{ $p->matrikulasi->indicator }}</div>
                                            @else
                                                <div class="font-semibold" style="color:#9E9790;">Data lama (tanpa aspek)</div>
                                            @endif
                                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                                <span class="text-xs font-bold px-2 py-0.5 rounded leading-snug max-w-[14rem]" style="background:{{ \App\Support\LabelSkorPencapaian::color($p->score) }};">{{ \App\Support\LabelSkorPencapaian::label($p->score) }}</span>
                                                @if($p->feedback)<span class="text-[11px] italic truncate max-w-[200px]" style="color:#9E9790;">{{ Str::limit($p->feedback, 40) }}</span>@endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($first->created_at)->format('d M Y') }}</td>
                            <td class="text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <button type="button" @click="openEditBundle('{{ $bundleKey }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                    <button type="button" @click="openDeleteBundle('{{ $bundleKey }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center" style="color:#9E9790;">Belum ada evaluasi pada rentang tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($groupedPencapaian->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $groupedPencapaian->links() }}</div>@endif
        </div>

        {{-- CREATE --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box max-w-lg w-full relative overflow-hidden" @click.away="!isCompressing && (showCreateModal=false)">
                {{-- Compressing Overlay --}}
                <div x-show="isCompressing" class="absolute inset-0 z-[60] bg-white/90 backdrop-blur-[4px] flex flex-col items-center justify-center">
                    <div class="h-12 w-12 border-4 border-teal-600/30 border-t-teal-600 rounded-full animate-spin"></div>
                    <p class="mt-4 text-sm font-bold text-teal-800">Mengoptimalkan Foto...</p>
                </div>
                <form action="{{ route('pengajar.pencapaian.sync') }}" method="POST" enctype="multipart/form-data" x-ref="createForm" @submit.prevent="submitWithCompression('createForm')">
                    @csrf
                    <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}">
                    <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}">
                    @if($filterAnakId)<input type="hidden" name="filter_anak_id" value="{{ $filterAnakId }}">@endif
                    @if($filterAspekRaw !== '')<input type="hidden" name="aspek" value="{{ $filterAspekRaw }}">@endif
                    <div class="modal-header"><h3 class="section-title">Buat evaluasi per aspek</h3></div>
                    <div class="modal-body space-y-4 max-h-[75vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Anak <span class="text-red-500">*</span></label>
                            <select name="anak_id" required class="input-field" x-model="selectedAnakId" @change="selectedKegiatanId = ''">
                                <option value="">— Pilih —</option>
                                @foreach($anaks as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                            </select>
                        </div>
                        <div x-show="selectedAnakId">
                            <label class="input-label">Kegiatan <span class="text-red-500">*</span></label>
                            <select name="kegiatan_id" required class="input-field" x-model="selectedKegiatanId" @change="resetCreateMatrices()">
                                <option value="">— Pilih —</option>
                                <template x-for="k in filteredKegiatans" :key="k.id">
                                    <option :value="k.id" x-text="k.date_label + ' — ' + k.title"></option>
                                </template>
                            </select>
                            <p class="text-xs mt-1" style="color:#9E9790;">Nilai wajib untuk setiap indikator yang terhubung ke kegiatan ini.</p>
                        </div>
                        <p class="text-sm" x-show="selectedKegiatanId && matrikulasiOptions.length === 0" style="display:none;color:#C0392B;">Kegiatan ini belum punya matrikulasi. Ubah di Jurnal Kegiatan.</p>
                        <template x-for="opt in matrikulasiOptions" :key="opt.id">
                            <div class="rounded-xl border p-3 space-y-2" style="border-color:rgba(0,0,0,0.1);">
                                <div class="font-semibold text-sm" style="color:#2C2C2C;" x-text="opt.label"></div>
                                <div>
                                    <label class="input-label">Nilai <span class="text-red-500">*</span></label>
                                    <select class="input-field" required
                                        :name="'nilai[' + opt.id + ']'"
                                        x-model="createNilai[String(opt.id)]">
                                        <option value="">— Pilih —</option>
                                        <option value="BB">Belum Berkembang</option>
                                        <option value="MB">Mulai Berkembang</option>
                                        <option value="BSH">Berkembang Sesuai Harapan</option>
                                        <option value="BSB">Berkembang Sangat Baik</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="input-label">Catatan (opsional)</label>
                                    <textarea class="input-field text-sm" rows="2" :name="'catatan[' + opt.id + ']'" x-model="createCatatan[String(opt.id)]" placeholder="Catatan per aspek…"></textarea>
                                </div>
                            </div>
                        </template>
                        <div>
                            <label class="input-label">Foto bukti (opsional, berlaku untuk seluruh aspek)</label>
                            <input type="file" name="photo" accept="image/*" class="input-field py-1.5 text-xs @error('photo') border-red-500 @enderror" @change="handleFile($event)">
                            @error('photo')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" :disabled="!selectedKegiatanId || matrikulasiOptions.length === 0">Simpan</button></div>
                </form>
            </div>
        </div>

        {{-- EDIT --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box max-w-lg w-full relative overflow-hidden" @click.away="!isCompressing && (showEditModal=false)">
                {{-- Compressing Overlay --}}
                <div x-show="isCompressing" class="absolute inset-0 z-[60] bg-white/90 backdrop-blur-[4px] flex flex-col items-center justify-center">
                    <div class="h-12 w-12 border-4 border-teal-600/30 border-t-teal-600 rounded-full animate-spin"></div>
                    <p class="mt-4 text-sm font-bold text-teal-800">Mengoptimalkan Foto...</p>
                </div>
                <form action="{{ route('pengajar.pencapaian.sync') }}" method="POST" enctype="multipart/form-data" x-ref="editForm" @submit.prevent="submitWithCompression('editForm')">
                    @csrf
                    <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}">
                    <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}">
                    @if($filterAnakId)<input type="hidden" name="filter_anak_id" value="{{ $filterAnakId }}">@endif
                    @if($filterAspekRaw !== '')<input type="hidden" name="aspek" value="{{ $filterAspekRaw }}">@endif
                    <input type="hidden" name="anak_id" :value="editBundles[editBundleKey]?.anak_id">
                    <input type="hidden" name="kegiatan_id" :value="editBundles[editBundleKey]?.kegiatan_id">
                    <div class="modal-header"><h3 class="section-title">Edit evaluasi per aspek</h3></div>
                    <div class="modal-body space-y-4 max-h-[75vh] overflow-y-auto">
                        <template x-for="opt in matrikulasiOptionsEdit" :key="opt.id">
                            <div class="rounded-xl border p-3 space-y-2" style="border-color:rgba(0,0,0,0.1);">
                                <div class="font-semibold text-sm" style="color:#2C2C2C;" x-text="opt.label"></div>
                                <div>
                                    <label class="input-label">Nilai <span class="text-red-500">*</span></label>
                                    <select class="input-field" required
                                        :name="'nilai[' + opt.id + ']'"
                                        x-model="editNilai[String(opt.id)]">
                                        <option value="BB">Belum Berkembang</option>
                                        <option value="MB">Mulai Berkembang</option>
                                        <option value="BSH">Berkembang Sesuai Harapan</option>
                                        <option value="BSB">Berkembang Sangat Baik</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="input-label">Catatan (opsional)</label>
                                    <textarea class="input-field text-sm" rows="2" :name="'catatan[' + opt.id + ']'" x-model="editCatatan[String(opt.id)]"></textarea>
                                </div>
                            </div>
                        </template>
                        <div>
                            <label class="input-label">Ganti foto</label>
                            <input type="file" name="photo" accept="image/*" class="input-field py-1.5 text-xs @error('photo') border-red-500 @enderror" @change="handleFile($event)">
                            @error('photo')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        {{-- DELETE BUNDLE --}}
        <div x-show="showDeleteBundleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteBundleModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteBundleModal=false">
                <form method="POST" action="{{ route('pengajar.pencapaian.destroy-bundle') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}">
                    <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}">
                    @if($filterAnakId)<input type="hidden" name="filter_anak_id" value="{{ $filterAnakId }}">@endif
                    @if($filterAspekRaw !== '')<input type="hidden" name="aspek" value="{{ $filterAspekRaw }}">@endif
                    <input type="hidden" name="anak_id" :value="deleteBundleAnak">
                    <input type="hidden" name="kegiatan_id" :value="deleteBundleKeg">
                    <div class="modal-body text-center py-6">
                        <h3 class="section-title">Hapus seluruh evaluasi?</h3>
                        <p class="section-subtitle mt-1">Semua aspek untuk anak &amp; kegiatan ini akan dihapus.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showDeleteBundleModal=false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-danger">Ya, hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
