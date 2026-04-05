<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ 
        openModal: false, 
        openDetailModal: false,
        selectedAnak: null, 
        statusValue: '',
        keteranganValue: '',
        detailData: [],
        filterMulai: '{{ date('Y-m-01') }}',
        filterSampai: '{{ date('Y-m-t') }}',
        isLoadingDetail: false,
        initAnak(id, name, status, keterangan) {
            this.selectedAnak = { id, name };
            this.statusValue = status || '';
            this.keteranganValue = keterangan || '';
            this.openModal = true;
        },
        async loadDetail(id, name) {
            this.selectedAnak = { id, name };
            this.openDetailModal = true;
            this.isLoadingDetail = true;
            
            try {
                let res = await fetch(`/pengajar/master-kegiatan-rutin/detail/{{ $masterKegiatanRutin->id }}/${id}?mulai=${this.filterMulai}&sampai=${this.filterSampai}`);
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
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('pengajar.master-kegiatan-rutin.index') }}" class="h-10 w-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ $masterKegiatanRutin->nama_kegiatan }}</h2>
                <p class="text-sm text-gray-500 mt-1">Aspek: {{ $masterKegiatanRutin->aspek }}</p>
            </div>
        </div>
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Daftar Siswa</h3>
                <p class="text-xs text-gray-500">Isi status pencapaian untuk tanggal di bawah ini.</p>
            </div>
            <form method="GET" action="{{ route('pengajar.master-kegiatan-rutin.show', $masterKegiatanRutin) }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-full sm:w-auto">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 ml-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field py-2 text-sm" onchange="this.form.submit()">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 ml-1">Kelas</label>
                    <select name="kelas_id" class="input-field py-2 text-sm min-w-[120px]" onchange="this.form.submit()">
                        <option value="">- Pilih Kelas -</option>
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
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Status Pencapaian</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Keterangan</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($anaks as $anak)
                            @php 
                                $r = $rutins->get($anak->id); 
                            @endphp
                            <tr class="hover:bg-gray-50/30 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $anak->name }}</td>
                                <td class="px-6 py-4">
                                    @if($r && $r->status_pencapaian)
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 w-fit">
                                            {{ $r->status_pencapaian }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300 italic">Belum Diisi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-3">
                                        @if($r?->photo)
                                            <div class="h-10 w-10 shrink-0 rounded-lg overflow-hidden border border-gray-100 shadow-sm cursor-pointer" @click="window.open('{{ Storage::url($r->photo) }}')">
                                                <img src="{{ Storage::url($r->photo) }}" class="h-full w-full object-cover">
                                            </div>
                                        @endif
                                        <span class="text-xs text-gray-500 line-clamp-2 max-w-xs">{{ $r?->keterangan ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" 
                                            @click="initAnak('{{ $anak->id }}', '{{ addslashes($anak->name) }}', '{{ $r?->status_pencapaian }}', '{{ addslashes($r?->keterangan ?? '') }}')"
                                            class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-[#1A6B6B] hover:text-white transition group border border-gray-100 shadow-sm text-[#1A6B6B]" title="Isi Capaian">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button type="button" 
                                            @click="loadDetail('{{ $anak->id }}', '{{ addslashes($anak->name) }}')"
                                            class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition group border border-gray-100 shadow-sm text-indigo-500" title="Detail Riwayat">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-sm">
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
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform" @click.away="openModal = false">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900" x-text="'Update Pencapaian: ' + (selectedAnak?.name || '')"></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('pengajar.master-kegiatan-rutin.store-rutin', $masterKegiatanRutin) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                    <input type="hidden" name="anak_id" :value="selectedAnak?.id">

                    <div>
                        <div class="text-xs font-bold text-indigo-500 uppercase tracking-widest mb-1">{{ $masterKegiatanRutin->aspek }}</div>
                        <div class="font-bold text-gray-900 mb-4">{{ $masterKegiatanRutin->nama_kegiatan }}</div>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status Pencapaian</label>
                            <select name="status_pencapaian" x-model="statusValue" class="input-field w-full" required>
                                <option value="">- Pilih Status -</option>
                                <option value="Belum Mulai">Belum Mulai</option>
                                <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                                <option value="Lancar">Lancar</option>
                                <option value="Sangat Lancar">Sangat Lancar</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Keterangan (Opsional)</label>
                            <textarea name="keterangan" x-model="keteranganValue" class="input-field w-full" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Foto Dokumentasi (Opsional)</label>
                            <input type="file" name="photo" class="input-field w-full text-xs" accept="image/*">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn-primary w-full py-3 rounded-xl font-bold shadow-lg shadow-[#1A6B6B]/20">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Detail --}}
        <div x-show="openDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" style="display:none;" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform flex flex-col max-h-[90vh]" @click.away="openDetailModal = false">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="font-bold text-gray-900" x-text="'Riwayat Kegiatan: ' + (selectedAnak?.name || '')"></h3>
                        <p class="text-xs text-gray-500 mt-1">Lihat capaian kegiatan ini</p>
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
                        Tidak ada catatan pencapaian pada rentang tanggal ini.
                    </div>

                    <div x-show="!isLoadingDetail && detailData.length > 0" class="space-y-3">
                        <template x-for="item in detailData" :key="item.id">
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col gap-3">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="font-bold text-gray-900" x-text="item.tanggal_formatted"></div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100" x-text="item.status_pencapaian"></span>
                                        <form :action="'/pengajar/master-kegiatan-rutin/rutin/' + item.id" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 transition p-1">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <template x-if="item.photo_url">
                                        <div class="w-24 h-24 shrink-0 rounded-lg overflow-hidden border border-gray-100 shadow-sm cursor-pointer" @click="window.open(item.photo_url)">
                                            <img :src="item.photo_url" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                    <template x-if="item.keterangan">
                                        <div class="flex-1 rounded-lg px-3 py-2.5 border border-gray-100 bg-gray-50 overflow-hidden">
                                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-1">Keterangan</span>
                                            <p class="text-sm text-gray-700 leading-relaxed break-words" x-text="item.keterangan"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
