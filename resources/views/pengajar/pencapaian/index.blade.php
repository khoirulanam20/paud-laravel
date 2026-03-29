<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Evaluasi Pencapaian Siswa</h2>
        </div>
    </x-slot>

    @php
        $kegiatanMatrikulasi = [];
        foreach ($kegiatans as $kg) {
            $kegiatanMatrikulasi[$kg->id] = $kg->matrikulasis->map(fn ($m) => [
                'id' => $m->id,
                'aspek' => $m->aspek,
                'indicator' => $m->indicator,
                'label' => ($m->aspek ? $m->aspek.': ' : '').$m->indicator,
            ])->values()->all();
        }
        $flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        $payloadJson = json_encode(['kegiatanMap' => $kegiatanMatrikulasi, 'editBundles' => $editBundles], $flags);
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
            selectedKegiatanId: '',
            selectedKegiatanIdEdit: '',
            editBundleKey: null,
            editNilai: {},
            editCatatan: {},
            createNilai: {},
            createCatatan: {},
            init() {
                const el = document.getElementById('pencapaian-payload-json');
                if (el) {
                    try { this.payload = JSON.parse(el.textContent); } catch (e) { this.payload = { kegiatanMap: {}, editBundles: {} }; }
                }
            },
            get kegiatanMap() { return this.payload.kegiatanMap || {}; },
            get editBundles() { return this.payload.editBundles || {}; },
            get matrikulasiOptions() { return this.kegiatanMap[this.selectedKegiatanId] || []; },
            get matrikulasiOptionsEdit() { return this.kegiatanMap[this.selectedKegiatanIdEdit] || []; },
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
                const opts = this.kegiatanMap[this.selectedKegiatanIdEdit] || [];
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
        @if($errors->any())<div class="alert-error mb-5 text-sm">{{ $errors->first() }}</div>@endif

        <div class="card overflow-hidden mb-6">
            <div class="px-5 sm:px-6 py-5 border-b space-y-5" style="border-color: rgba(0,0,0,0.06);">
                <div class="space-y-1">
                    <h3 class="section-title mb-0">Filter evaluasi</h3>
                    <p class="text-sm leading-relaxed m-0 max-w-3xl" style="color:#9E9790;">Sesuaikan rentang tanggal penyimpanan, anak, dan aspek. Tanggal bersifat inklusif.</p>
                </div>
                <form method="get" action="{{ route('pengajar.pencapaian.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-1 lg:col-span-2 min-w-0">
                        <label class="input-label" for="penc-filter-dari">Dari</label>
                        <input id="penc-filter-dari" type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="input-field w-full min-w-0" required>
                    </div>
                    <div class="sm:col-span-1 lg:col-span-2 min-w-0">
                        <label class="input-label" for="penc-filter-sampai">Sampai</label>
                        <input id="penc-filter-sampai" type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="input-field w-full min-w-0" required>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 min-w-0">
                        <label class="input-label" for="penc-filter-anak">Anak</label>
                        <select id="penc-filter-anak" name="filter_anak_id" class="input-field w-full min-w-0">
                            <option value="">Semua anak</option>
                            @foreach($anaks as $a)
                                <option value="{{ $a->id }}" @selected($filterAnakId === (int) $a->id)>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 min-w-0">
                        <label class="input-label" for="penc-filter-aspek">Aspek</label>
                        <select id="penc-filter-aspek" name="aspek" class="input-field w-full min-w-0">
                            <option value="">Semua aspek</option>
                            <option value="{{ \App\Support\FilterAspekPencapaian::UMUM }}" @selected($filterAspekRaw === \App\Support\FilterAspekPencapaian::UMUM)>Umum / tanpa aspek</option>
                            @foreach($aspekPilihan as $asp)
                                <option value="{{ $asp }}" @selected($filterAspekRaw === $asp)>{{ $asp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2 flex flex-col gap-1.5 min-w-0">
                        <span class="input-label opacity-0 text-[0.65rem] leading-none max-sm:hidden" aria-hidden="true">&nbsp;</span>
                        <button type="submit" class="btn-primary w-full shrink-0">Tampilkan</button>
                    </div>
                </form>
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
                @if($filterAnakId)
                    <span class="text-black/25 hidden sm:inline">·</span>
                    <span>{{ $anaks->firstWhere('id', $filterAnakId)?->name ?? 'Anak terpilih' }}</span>
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

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Foto</th><th>Anak</th><th>Kegiatan</th><th>Aspek &amp; nilai</th><th>Tanggal</th><th class="text-right">Aksi</th></tr></thead>
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
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $first->anak->name ?? '-' }}</span></td>
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
            <div x-show="showCreateModal" x-transition class="modal-box max-w-lg w-full" @click.away="showCreateModal=false">
                <form action="{{ route('pengajar.pencapaian.sync') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}">
                    <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}">
                    @if($filterAnakId)<input type="hidden" name="filter_anak_id" value="{{ $filterAnakId }}">@endif
                    @if($filterAspekRaw !== '')<input type="hidden" name="aspek" value="{{ $filterAspekRaw }}">@endif
                    <div class="modal-header"><h3 class="section-title">Buat evaluasi per aspek</h3></div>
                    <div class="modal-body space-y-4 max-h-[75vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Anak <span class="text-red-500">*</span></label>
                            <select name="anak_id" required class="input-field">
                                <option value="">— Pilih —</option>
                                @foreach($anaks as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Kegiatan <span class="text-red-500">*</span></label>
                            <select name="kegiatan_id" required class="input-field" x-model="selectedKegiatanId" @change="resetCreateMatrices()">
                                <option value="">— Pilih —</option>
                                @foreach($kegiatans as $k)
                                    <option value="{{ $k->id }}">{{ \Carbon\Carbon::parse($k->date)->format('d M Y') }} — {{ $k->title }}</option>
                                @endforeach
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
                            <input type="file" name="photo" accept="image/*" class="input-field py-1.5 text-xs">
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" :disabled="!selectedKegiatanId || matrikulasiOptions.length === 0">Simpan</button></div>
                </form>
            </div>
        </div>

        {{-- EDIT --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box max-w-lg w-full" @click.away="showEditModal=false">
                <form action="{{ route('pengajar.pencapaian.sync') }}" method="POST" enctype="multipart/form-data">
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
                            <input type="file" name="photo" accept="image/*" class="input-field py-1.5 text-xs">
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
