<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'master-kegiatan-rutin.index') }}" class="h-10 w-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">Tambah Master Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Buat kegiatan rutin baru untuk diterapkan di kelas.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

@php
    $aspekList = $matrikulasiList->pluck('aspek')->filter()->unique()->values();
@endphp

        <div class="card border-none shadow-sm shadow-gray-200" x-data="{
                aspek: '{{ old('aspek') }}',
                allMatrikulasi: {{ $matrikulasiList->toJson() }},
                get filteredMatrikulasi() {
                    return this.aspek 
                        ? this.allMatrikulasi.filter(m => m.aspek === this.aspek)
                        : this.allMatrikulasi;
                }
            }">
            <form action="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'master-kegiatan-rutin.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Kegiatan *</label>
                        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" class="input-field w-full" placeholder="Contoh: Mengaji, Membaca, dll" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Aspek Perkembangan *</label>
                        <select name="aspek" x-model="aspek" class="input-field w-full" required>
                            <option value="">-- Pilih Aspek --</option>
                            @foreach($aspekList as $asp)
                                <option value="{{ $asp }}" @selected(old('aspek') == $asp)>{{ $asp }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Indikator (Sesuai Aspek)</label>
                    <select name="matrikulasi_id" class="input-field w-full">
                        <option value="">- Tidak ada / Kosongkan -</option>
                        <template x-for="m in filteredMatrikulasi" :key="m.id">
                            <option :value="m.id" x-text="m.indicator + ' - ' + m.description.substring(0, 50)" :selected="m.id == '{{ old('matrikulasi_id') }}'"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Peserta (Pilih Kelas) *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($classList as $c)
                            <label class="flex items-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition">
                                <input type="checkbox" name="kelas_ids[]" value="{{ $c->id }}" class="h-4 w-4 text-[#1A6B6B] border-gray-300 rounded focus:ring-[#1A6B6B]" @checked(is_array(old('kelas_ids')) && in_array($c->id, old('kelas_ids')))>
                                <span class="ml-3 text-sm font-bold text-gray-700">{{ $c->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'master-kegiatan-rutin.index') }}" class="btn-secondary py-2 px-5 rounded-xl font-bold">Batal</a>
                    <button type="submit" class="btn-primary py-2 px-6 rounded-xl font-bold shadow-lg shadow-[#1A6B6B]/20">
                        Simpan Kegiatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
