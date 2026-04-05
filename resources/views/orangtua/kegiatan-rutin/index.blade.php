<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kegiatan Rutin Anak Saya</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ 
            showImageModal: false, 
            activeImage: '',
            showDetailModal: false,
            selectedKeg : null
         }">
        <div class="card overflow-hidden">
            <div class="px-5 py-5 border-b" style="border-color: rgba(0,0,0,0.06); background: #FAF9F6;">
                <form method="GET" class="grid grid-cols-2 lg:flex lg:items-end gap-4">
                    @if($anaks->count() > 1)
                        <div class="col-span-2 lg:col-auto lg:min-w-[160px]">
                            <label class="input-label">Pilih Anak</label>
                            <select name="anak_id" class="input-field w-full" onchange="this.form.submit()">
                                <option value="">Semua Anak</option>
                                @foreach($anaks as $anak)
                                    <option value="{{ $anak->id }}" {{ request('anak_id') == $anak->id ? 'selected' : '' }}>
                                        {{ $anak->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-span-2 lg:col-auto lg:min-w-[180px]">
                        <label class="input-label">Kegiatan</label>
                        <select name="master_id" class="input-field w-full" onchange="this.form.submit()">
                            <option value="">Semua Kegiatan</option>
                            @foreach($masters as $m)
                                <option value="{{ $m->master_kegiatan_rutin_id }}"
                                    @selected(request('master_id') == $m->master_kegiatan_rutin_id)>
                                    {{ $m->kegiatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 lg:col-auto">
                        <label class="input-label">Dari</label>
                        <input type="date" name="mulai" value="{{ $mulai }}" class="input-field w-full h-[42px]">
                    </div>
                    <div class="col-span-1 lg:col-auto">
                        <label class="input-label">Sampai</label>
                        <input type="date" name="sampai" value="{{ $sampai }}" class="input-field w-full h-[42px]">
                    </div>
                    <div class="col-span-2 lg:col-auto lg:flex-1">
                        <button type="submit"
                            class="btn-primary w-full h-[42px] flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kegiatan</th>
                            <th>Aspek</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatans as $keg)
                                                <tr>
                                                    <td class="whitespace-nowrap font-medium text-[#1A6B6B]">
                                                        {{ \Carbon\Carbon::parse($keg->tanggal)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="font-semibold text-[#2C2C2C]">{{ $keg->kegiatan }}</td>
                                                    <td><span
                                                            class="text-xs text-gray-500 font-medium px-2 py-1 bg-gray-100 rounded-md">{{ $keg->aspek }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" @click="selectedKeg = {{ json_encode([
                                'tanggal' => \Carbon\Carbon::parse($keg->tanggal)->format('d M Y'),
                                'kegiatan' => $keg->kegiatan,
                                'aspek' => $keg->aspek,
                                'status' => $keg->status_pencapaian,
                                'keterangan' => $keg->keterangan,
                                'photo' => $keg->photo ? Storage::url($keg->photo) : null
                            ]) }}; showDetailModal = true"
                                                            class="text-[#1A6B6B] hover:text-[#145757] font-bold text-xs underline decoration-dotted underline-offset-4">
                                                            Detail
                                                        </button>
                                                    </td>
                                                </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <p>Belum ada data kegiatan rutin pada periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Detail --}}
        <div x-show="showDetailModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            style="display:none;" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform"
                @click.away="showDetailModal = false">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">Detail Kegiatan Rutin</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <template x-if="selectedKeg?.photo">
                        <div class="rounded-xl overflow-hidden border border-gray-100 shadow-sm cursor-pointer"
                            @click="activeImage = selectedKeg.photo; showImageModal = true">
                            <img :src="selectedKeg.photo" class="w-full h-48 object-cover">
                        </div>
                    </template>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Tanggal</span>
                            <p class="text-sm font-bold text-gray-900" x-text="selectedKeg?.tanggal"></p>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Status</span>
                            <span
                                class="inline-flex px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tighter"
                                :class="selectedKeg?.status ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-500'"
                                x-text="selectedKeg?.status || '-'"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-[#1A6B6B] uppercase tracking-widest block mb-1"
                            x-text="selectedKeg?.aspek"></span>
                        <h4 class="text-lg font-bold text-gray-900 leading-tight" x-text="selectedKeg?.kegiatan"></h4>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Keterangan
                            / Catatan Guru</span>
                        <p class="text-sm text-gray-700 leading-relaxed italic"
                            x-text="selectedKeg?.keterangan || 'Tidak ada keterangan tambahan.'"></p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button @click="showDetailModal = false"
                        class="btn-secondary w-full py-2.5 rounded-xl font-bold">Tutup</button>
                </div>
            </div>
        </div>

        {{-- Modal Preview Gambar --}}
        <div x-show="showImageModal"
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            style="display: none;" x-transition @keydown.escape.window="showImageModal = false">
            <div class="relative max-w-4xl w-full" @click.away="showImageModal = false">
                <button
                    class="absolute -top-12 right-0 text-white hover:text-gray-300 transition flex items-center gap-2"
                    @click="showImageModal = false">
                    <span class="text-xs font-bold uppercase tracking-widest text-white/50">Klik di mana saja untuk
                        tutup</span>
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img :src="activeImage"
                    class="w-full h-auto max-h-[85vh] object-contain rounded-2xl shadow-2xl bg-white shadow-black/20">
            </div>
        </div>
    </div>
</x-app-layout>