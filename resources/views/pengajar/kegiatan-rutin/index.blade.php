<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ 
        openModal: false, 
        openDetailModal: false,
        selectedAnak: null, 
        rutinData: {},
        detailData: [],
        filterMulai: '{{ date('Y-m-01') }}',
        filterSampai: '{{ date('Y-m-t') }}',
        isLoadingDetail: false,
        initAnak(id, name, dataRutin) {
            this.selectedAnak = { id, name };
            this.rutinData = JSON.parse(JSON.stringify(dataRutin));
            this.openModal = true;
        },
        async loadDetail(id, name) {
            this.selectedAnak = { id, name };
            this.openDetailModal = true;
            this.isLoadingDetail = true;
            
            // To be implemented: fetch data via API or pass from backend.
            // Since we need to add a quick detail feature, we might need a small endpoint or just pass the data if small.
            // But doing a fetch is cleaner. We will add an endpoint in web.php.
            try {
                let res = await fetch(`/pengajar/kegiatan-rutin/detail/${id}?mulai=${this.filterMulai}&sampai=${this.filterSampai}`);
                let json = await res.json();
                this.detailData = json;
            } catch (e) {
                console.error(e);
                this.detailData = [];
            }
            this.isLoadingDetail = false;
        },
        async reloadDetail() {
            if(this.selectedAnak) {
                this.loadDetail(this.selectedAnak.id, this.selectedAnak.name);
            }
        }
    }">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">Kegiatan Rutin Harian</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau perkembangan aspek harian siswa.</p>
            </div>
            
            <form method="GET" action="{{ route('pengajar.kegiatan-rutin.index') }}" class="flex flex-wrap gap-3 items-end">
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
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center gap-3 animate-fade-in shadow-sm">
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="card overflow-hidden border-none shadow-sm shadow-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Nama Siswa</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Aspek</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Kegiatan</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Status Pencapaian</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($anaks as $anak)
                            @php 
                                $anakRutins = $rutins->where('anak_id', $anak->id);
                                $displayItems = [];
                                $formItems = [];

                                if ($masters->count() > 0) {
                                    foreach ($masters as $master) {
                                        $r = $anakRutins->where('master_kegiatan_rutin_id', $master->id)->first();
                                        $displayItems[] = [
                                            'aspek' => $master->aspek,
                                            'kegiatan' => $master->nama_kegiatan,
                                            'status' => $r ? $r->status_pencapaian : null
                                        ];
                                        $formItems[$master->id] = [
                                            'aspek' => $master->aspek,
                                            'kegiatan' => $master->nama_kegiatan,
                                            'status' => $r ? $r->status_pencapaian : ''
                                        ];
                                    }
                                } else {
                                    // Custom or old routine format
                                    $r = $anakRutins->first();
                                    $displayItems[] = [
                                        'aspek' => $r?->aspek ?: '-',
                                        'kegiatan' => $r?->kegiatan ?: '-',
                                        'status' => $r?->status_pencapaian
                                    ];
                                    $formItems['custom'] = [
                                        'aspek' => $r?->aspek ?: '',
                                        'kegiatan' => $r?->kegiatan ?: '',
                                        'status' => $r?->status_pencapaian ?: ''
                                    ];
                                }
                            @endphp

                            @foreach($displayItems as $index => $item)
                                <tr class="hover:bg-gray-50/30 transition">
                                    @if($index === 0)
                                        <td class="px-6 py-4 font-bold text-gray-900 border-r border-gray-50" rowspan="{{ count($displayItems) }}">{{ $anak->name }}</td>
                                    @endif
                                    <td class="px-6 py-4 text-sm text-gray-600 border-b border-gray-50">{{ $item['aspek'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 border-b border-gray-50">{{ $item['kegiatan'] }}</td>
                                    <td class="px-6 py-4 border-b border-gray-50">
                                        @if($item['status'])
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $item['status'] }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300 italic">Belum Diisi</span>
                                        @endif
                                    </td>
                                    @if($index === 0)
                                        <td class="px-6 py-4 text-center border-l border-gray-50" rowspan="{{ count($displayItems) }}">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <button type="button" 
                                                    @click="initAnak('{{ $anak->id }}', '{{ addslashes($anak->name) }}', {{ json_encode($formItems) }})"
                                                    class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-[#1A6B6B] hover:text-white transition group border border-gray-100 shadow-sm" title="Update Capaian">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </button>
                                                <button type="button" 
                                                    @click="loadDetail('{{ $anak->id }}', '{{ addslashes($anak->name) }}')"
                                                    class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition group border border-gray-100 shadow-sm text-indigo-500" title="Detail Riwayat">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                    Pilih kelas untuk melihat daftar siswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Update --}}
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" style="display:none;" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform flex flex-col max-h-[90vh]" @click.away="openModal = false">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center shrink-0">
                    <h3 class="font-bold text-gray-900" x-text="'Update Kegiatan: ' + (selectedAnak?.name || '')"></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('pengajar.kegiatan-rutin.store') }}" method="POST" class="p-6 space-y-6 overflow-y-auto flex-1">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                    
                    <template x-for="(data, key) in rutinData" :key="key">
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 relative">
                            <!-- Input hidden to store aspek & kegiatan if they are master data -->
                            <input type="hidden" :name="'rutin[' + selectedAnak?.id + '][' + key + '][aspek]'" :value="data.aspek">
                            <input type="hidden" :name="'rutin[' + selectedAnak?.id + '][' + key + '][kegiatan]'" :value="data.kegiatan">
                            
                            <template x-if="key === 'custom'">
                                <div class="space-y-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Aspek Perkembangan</label>
                                        <input type="text" x-model="data.aspek" :name="'rutin[' + selectedAnak?.id + '][' + key + '][aspek]'" class="input-field w-full text-sm" placeholder="Contoh: Agama">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Nama Kegiatan</label>
                                        <input type="text" x-model="data.kegiatan" :name="'rutin[' + selectedAnak?.id + '][' + key + '][kegiatan]'" class="input-field w-full text-sm" placeholder="Contoh: Mengaji">
                                    </div>
                                </div>
                            </template>
                            <template x-if="key !== 'custom'">
                                <div class="mb-3">
                                    <div class="text-xs font-bold text-indigo-500 uppercase tracking-widest mb-1" x-text="data.aspek"></div>
                                    <div class="font-bold text-gray-900" x-text="data.kegiatan"></div>
                                </div>
                            </template>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status Pencapaian</label>
                                <select x-model="data.status" :name="'rutin[' + selectedAnak?.id + '][' + key + '][status_pencapaian]'" class="input-field w-full">
                                    <option value="">- Pilih Status -</option>
                                    <option value="Belum Mulai">Belum Mulai</option>
                                    <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                                    <option value="Lancar">Lancar</option>
                                    <option value="Sangat Lancar">Sangat Lancar</option>
                                </select>
                            </div>
                        </div>
                    </template>
                    
                    <div class="pt-2 sticky bottom-0 bg-white">
                        <button type="submit" class="btn-primary w-full py-3 rounded-xl font-bold shadow-lg shadow-[#1A6B6B]/20">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Detail --}}
        <div x-show="openDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" style="display:none;" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden transform flex flex-col max-h-[90vh]" @click.away="openDetailModal = false">
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
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
