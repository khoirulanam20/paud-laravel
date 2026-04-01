<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ 
        openModal: false, 
        selectedAnak: null, 
        rutinData: {},
        initAnak(id, name, aspek, kegiatan, status) {
            this.selectedAnak = { id, name };
            this.rutinData = { aspek: aspek || '', kegiatan: kegiatan || '', status: status || '' };
            this.openModal = true;
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
                                $r = $rutins->get($anak->id)?->first(); 
                            @endphp
                            <tr class="hover:bg-gray-50/30 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $anak->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $r?->aspek ?: '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $r?->kegiatan ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($r)
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $r->status_pencapaian }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300 italic">Belum Diisi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" 
                                        @click="initAnak('{{ $anak->id }}', '{{ $anak->name }}', '{{ $r?->aspek }}', '{{ $r?->kegiatan }}', '{{ $r?->status_pencapaian }}')"
                                        class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-[#1A6B6B] hover:text-white transition group border border-gray-100 shadow-sm">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                </td>
                            </tr>
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
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform" @click.away="openModal = false">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900" x-text="'Update Kegiatan: ' + (selectedAnak?.name || '')"></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('pengajar.kegiatan-rutin.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                    <input type="hidden" :name="'rutin[' + selectedAnak?.id + '][aspek]'" :value="rutinData.aspek">
                    <input type="hidden" :name="'rutin[' + selectedAnak?.id + '][kegiatan]'" :value="rutinData.kegiatan">
                    <input type="hidden" :name="'rutin[' + selectedAnak?.id + '][status_pencapaian]'" :value="rutinData.status">

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Aspek Perkembangan</label>
                        <input type="text" x-model="rutinData.aspek" class="input-field w-full" placeholder="Contoh: Agama, Kognitif, dll" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Kegiatan</label>
                        <input type="text" x-model="rutinData.kegiatan" class="input-field w-full" placeholder="Contoh: Mengaji, Membaca, dll" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status Pencapaian</label>
                        <select x-model="rutinData.status" class="input-field w-full" required>
                            <option value="">- Pilih Status -</option>
                            <option value="Belum Mulai">Belum Mulai</option>
                            <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                            <option value="Lancar">Lancar</option>
                            <option value="Sangat Lancar">Sangat Lancar</option>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn-primary w-full py-3 rounded-xl font-bold shadow-lg shadow-[#1A6B6B]/20">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
