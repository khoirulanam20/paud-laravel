@php
    $routePrefix = auth()->user()->kegiatanRutinRoutePrefix();
    $photoDownloadRoute = $routePrefix.'kegiatan-rutin.photos.download';
@endphp

<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{
        openDetailModal: false,
        selectedAnak: null,
        detailData: [],
        filterMulai: '{{ date('Y-m-01') }}',
        filterSampai: '{{ date('Y-m-t') }}',
        isLoadingDetail: false,
        showImageModal: false,
        activeImage: null,
        activeDownloadUrl: null,
        yesterdayGrid: @js($rutinGridYesterday),
        async loadDetail(id, name) {
            this.selectedAnak = { id, name };
            this.openDetailModal = true;
            this.isLoadingDetail = true;
            try {
                const is_admin = {{ auth()->user()->usesAdminKegiatanRutinRoutes() ? 'true' : 'false' }};
                const prefix = is_admin ? '/admin' : '/pengajar';
                let res = await fetch(`${prefix}/kegiatan-rutin/detail/${id}?mulai=${this.filterMulai}&sampai=${this.filterSampai}`);
                this.detailData = await res.json();
            } catch (e) {
                console.error(e);
                this.detailData = [];
            }
            this.isLoadingDetail = false;
        },
        async reloadDetail() {
            if (this.selectedAnak) {
                this.loadDetail(this.selectedAnak.id, this.selectedAnak.name);
            }
        },
        copyYesterday() {
            this.$root.querySelectorAll('[data-kegiatan-rutin-select]').forEach((select) => {
                if (select.value) {
                    return;
                }
                const anakId = select.dataset.anakId;
                const masterId = select.dataset.masterId;
                const status = this.yesterdayGrid?.[anakId]?.[masterId];
                if (status) {
                    select.value = status;
                }
            });
        },
        setColumnLancar(masterId) {
            this.$root.querySelectorAll(`[data-kegiatan-rutin-select][data-master-id='${masterId}']`).forEach((select) => {
                select.value = 'Lancar';
            });
        },
    }" @tour-close-modals.window="openDetailModal = false">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-tour="page-header">
            <div class="flex items-start gap-4 min-w-0">
                <a href="{{ route($routePrefix.'master-kegiatan-rutin.index') }}" class="h-10 w-10 shrink-0 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition shadow-sm" title="Kembali">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">Update Pencapaian Rutin</h2>
                    <p class="text-sm text-gray-500 mt-1">Isi status pencapaian seluruh siswa dalam satu grid, lalu simpan sekaligus.</p>
                </div>
            </div>

            <form method="GET" action="{{ route($routePrefix.'kegiatan-rutin.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-full sm:w-auto">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 ml-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field py-2 text-sm" onchange="this.form.submit()">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 ml-1">Kelas</label>
                    <select name="kelas_id" class="input-field py-2 text-sm min-w-[120px]" onchange="this.form.submit()">
                        @foreach($classList as $c)
                            <option value="{{ $c->id }}" @selected($kelasId == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            @if(auth()->user()->usesAdminKegiatanRutinRoutes())
                <x-export-excel route="admin.kegiatan-rutin.export" />
            @endif
            <x-download-photos :route="$photoDownloadRoute" :icon-only="true" />
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center gap-3 animate-fade-in shadow-sm">
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="card overflow-hidden border-none shadow-sm shadow-gray-200" data-tour="kegiatan-rutin-list">
            @if($anaks->isEmpty() || $masters->isEmpty())
                <div class="px-6 py-12 text-center text-gray-400 italic text-sm">
                    @if($anaks->isEmpty())
                        Belum ada siswa di kelas ini.
                    @else
                        Belum ada master kegiatan rutin untuk kelas ini.
                    @endif
                </div>
            @else
                <form method="POST" action="{{ route($routePrefix.'kegiatan-rutin.store') }}" data-tour="modal-create-section-form">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}">

                    <div class="px-4 sm:px-6 py-4 border-b flex flex-wrap items-center justify-between gap-3" style="border-color: rgba(0,0,0,0.06); background:#FAF6F0;">
                        <p class="text-sm text-gray-600 m-0">
                            <strong style="color:#2C2C2C;">{{ $anaks->count() }}</strong> siswa ·
                            <strong style="color:#2C2C2C;">{{ $masters->count() }}</strong> kegiatan rutin
                        </p>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="copyYesterday()" class="btn-secondary text-xs py-2 px-3">
                                Salin Kemarin
                            </button>
                            <button type="submit" data-tour="modal-create-submit" class="btn-primary text-sm py-2 px-4">
                                Simpan Semua
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[48rem] border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b" style="border-color: rgba(0,0,0,0.06);">
                                    <th class="sticky left-0 z-20 bg-gray-50 px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-400 align-bottom min-w-[10rem] border-r border-gray-100">
                                        Nama Siswa
                                    </th>
                                    @foreach($masters as $master)
                                        <th class="px-2 py-2 w-[8.75rem] min-w-[8.75rem] max-w-[8.75rem] align-bottom border-l border-gray-100/80">
                                            <div class="flex flex-col gap-1">
                                                <div class="leading-tight">
                                                    <div class="text-[9px] font-bold uppercase tracking-wider text-indigo-500 truncate" title="{{ $master->aspek }}">{{ $master->aspek }}</div>
                                                    <div class="text-[11px] font-bold text-gray-800 line-clamp-2 mt-0.5" title="{{ $master->nama_kegiatan }}">{{ $master->nama_kegiatan }}</div>
                                                </div>
                                                <button type="button" @click="setColumnLancar('{{ $master->id }}')"
                                                    class="w-full text-[9px] font-semibold px-1 py-1 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-[#E8F5F5] hover:text-[#1A6B6B] hover:border-[#D0E8E8] transition leading-none">
                                                    Semua Lancar
                                                </button>
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="px-2 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-400 text-center align-bottom w-14 border-l border-gray-100">
                                        Riwayat
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($anaks as $anak)
                                    <tr class="hover:bg-gray-50/30 transition">
                                        <td class="sticky left-0 z-10 bg-white px-3 py-2 font-bold text-sm text-gray-900 border-r border-gray-50 shadow-[2px_0_6px_-4px_rgba(0,0,0,0.12)]">
                                            {{ $anak->name }}
                                        </td>
                                        @foreach($masters as $master)
                                            @php
                                                $currentStatus = $rutinGrid[$anak->id][$master->id] ?? '';
                                            @endphp
                                            <td class="px-2 py-1.5 w-[8.75rem] min-w-[8.75rem] max-w-[8.75rem] border-l border-gray-50/80 {{ $currentStatus ? 'bg-[#F8FCFC]' : '' }}">
                                                <input type="hidden" name="rutin[{{ $anak->id }}][{{ $master->id }}][aspek]" value="{{ $master->aspek }}">
                                                <input type="hidden" name="rutin[{{ $anak->id }}][{{ $master->id }}][kegiatan]" value="{{ $master->nama_kegiatan }}">
                                                <select
                                                    name="rutin[{{ $anak->id }}][{{ $master->id }}][status_pencapaian]"
                                                    data-kegiatan-rutin-select
                                                    data-anak-id="{{ $anak->id }}"
                                                    data-master-id="{{ $master->id }}"
                                                    class="input-field w-full text-xs py-1.5 {{ $currentStatus ? 'border-[#D0E8E8]' : '' }}">
                                                    <option value="">—</option>
                                                    @foreach($statusOptions as $status)
                                                        <option value="{{ $status }}" @selected($currentStatus === $status)>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endforeach
                                        <td class="px-3 py-3 text-center">
                                            <button type="button"
                                                @click="loadDetail('{{ $anak->id }}', @js($anak->name))"
                                                class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition border border-gray-100 shadow-sm text-indigo-500 mx-auto"
                                                title="Detail Riwayat">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 sm:px-6 py-4 border-t flex justify-end gap-2" style="border-color: rgba(0,0,0,0.06);">
                        <button type="button" @click="copyYesterday()" class="btn-secondary text-sm">Salin Kemarin</button>
                        <button type="submit" class="btn-primary text-sm">Simpan Semua</button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Modal Detail --}}
        <div x-show="openDetailModal" class="modal-overlay modal-overlay--blur" style="display:none;" x-transition>
            <div class="modal-box max-w-4xl w-full" @click.away="openDetailModal = false">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="font-bold text-gray-900" x-text="'Riwayat Kegiatan: ' + (selectedAnak?.name || '')"></h3>
                        <p class="text-xs text-gray-500 mt-1">Lihat capaian kegiatan harian anak</p>
                    </div>
                    <button @click="openDetailModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-4 border-b border-gray-100 bg-white flex flex-wrap gap-4 items-end shrink-0">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Dari Tanggal</label>
                        <input type="date" x-model="filterMulai" @change="reloadDetail" class="input-field py-1.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Sampai Tanggal</label>
                        <input type="date" x-model="filterSampai" @change="reloadDetail" class="input-field py-1.5 text-sm">
                    </div>
                </div>
                <div class="p-6 overflow-y-auto flex-1 bg-gray-50/30">
                    <div x-show="isLoadingDetail" class="flex justify-center items-center py-12">
                        <svg class="animate-spin h-8 w-8 text-[#1A6B6B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>

                    <div x-show="!isLoadingDetail && detailData.length === 0" class="text-center py-12 text-gray-400 italic">
                        Tidak ada catatan kegiatan pada rentang tanggal ini.
                    </div>

                    <div x-show="!isLoadingDetail && detailData.length > 0" class="space-y-4">
                        <template x-for="item in detailData" :key="item.id">
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col gap-3">
                                <div class="flex flex-col sm:flex-row justify-between gap-3">
                                    <div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1" x-text="item.tanggal_formatted"></div>
                                        <div class="font-bold text-gray-900" x-text="item.kegiatan || '-'"></div>
                                        <div class="text-sm text-gray-500 mt-0.5" x-text="item.aspek || '-'"></div>
                                    </div>
                                    <div class="flex items-start sm:items-center shrink-0">
                                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100" x-text="item.status_pencapaian || '-'"></span>
                                    </div>
                                </div>
                                <template x-if="item.keterangan">
                                    <div class="rounded-lg px-3 py-2.5 border border-gray-100 bg-gray-50">
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-1">Keterangan</span>
                                        <p class="text-sm text-gray-700 leading-relaxed" x-text="item.keterangan"></p>
                                    </div>
                                </template>
                                <template x-if="item.photo_url">
                                    <div class="w-24 h-24 shrink-0 rounded-lg overflow-hidden border border-gray-100 shadow-sm cursor-pointer" @click="activeImage = item.photo_url; activeDownloadUrl = item.photo_download_url; showImageModal = true">
                                        <img :src="item.photo_url" class="w-full h-full object-cover" alt="Dokumentasi">
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <x-image-lightbox />
    </div>
</x-app-layout>
