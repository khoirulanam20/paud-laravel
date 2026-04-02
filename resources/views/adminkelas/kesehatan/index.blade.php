<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kesehatan Kelasku</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
            showInputModal: false,
            selectedAnak: null,
            form: {
                berat_badan: '',
                tinggi_badan: '',
                lingkar_kepala: '',
                gigi: 'Bersih',
                telinga: 'Bersih',
                kuku: 'Bersih',
                alergi: '',
                tanggal_pemeriksaan: '{{ date('Y-m-d') }}'
            },
            openInput(anak) {
                this.selectedAnak = anak;
                let latest = (anak.kesehatans && anak.kesehatans.length > 0) ? anak.kesehatans[0] : null;
                if (latest) {
                    this.form.berat_badan = latest.berat_badan;
                    this.form.tinggi_badan = latest.tinggi_badan;
                    this.form.lingkar_kepala = latest.lingkar_kepala;
                    this.form.gigi = latest.gigi || 'Bersih';
                    this.form.telinga = latest.telinga || 'Bersih';
                    this.form.kuku = latest.kuku || 'Bersih';
                    this.form.alergi = latest.alergi || '';
                } else {
                    this.form.berat_badan = '';
                    this.form.tinggi_badan = '';
                    this.form.lingkar_kepala = '';
                    this.form.gigi = 'Bersih';
                    this.form.telinga = 'Bersih';
                    this.form.kuku = 'Bersih';
                    this.form.alergi = '';
                }
                this.showInputModal = true;
            }
         }">

        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex flex-col gap-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                <form method="get" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[15rem]">
                        <label class="input-label">Cari Nama Siswa</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-10" placeholder="Ketik nama siswa...">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>BB/TB</th>
                            <th>Kebersihan (G/T/K)</th>
                            <th>Alergi</th>
                            <th>Pemeriksaan Terakhir</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anaks as $anak)
                            @php $latest = $anak->kesehatans->first(); @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-xl flex items-center justify-center font-bold text-sm text-white shrink-0" style="background: #1A6B6B;">{{ substr($anak->name, 0, 1) }}</div>
                                        <div>
                                            <span class="font-semibold block text-[#2C2C2C]">{{ $anak->name }}</span>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $anak->jenis_kelamin }}</span>
                                                <span class="text-[10px] font-bold text-[#1A6B6B]">• {{ $anak->age }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($anak->kelas)<span class="badge badge-teal font-medium">{{ $anak->kelas->name }}</span>
                                    @else<span class="text-xs italic text-gray-400">N/A</span>@endif
                                </td>
                                <td>
                                    @if($latest)
                                        <div class="text-sm">
                                            <span class="font-bold text-[#1A6B6B]">{{ $latest->berat_badan ?? '-' }}</span> kg / 
                                            <span class="font-bold text-[#1A6B6B]">{{ $latest->tinggi_badan ?? '-' }}</span> cm
                                        </div>
                                        <div class="text-[10px] text-gray-500">LK: {{ $latest->lingkar_kepala ?? '-' }} cm</div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($latest)
                                        <div class="flex flex-col gap-1 text-[10px] py-1">
                                            @foreach(['gigi' => 'Gigi', 'telinga' => 'Telinga', 'kuku' => 'Kuku'] as $field => $label)
                                                @php 
                                                    $val = $latest->$field;
                                                    $isGood = Str::contains(strtolower($val), 'bersih') || Str::contains(strtolower($val), 'rapi');
                                                    $color = $isGood ? 'text-[#2E7D32]' : 'text-[#C62828]';
                                                    $dot = $isGood ? 'bg-[#4CAF50]' : 'bg-[#F44336]';
                                                @endphp
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $dot }} shrink-0"></span>
                                                    <span class="font-bold text-gray-600 whitespace-nowrap">{{ $label }}:</span>
                                                    <span class="{{ $color }} font-medium">{{ $val }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($latest && $latest->alergi)
                                        <span class="text-xs text-red-600 font-medium">{{ Str::limit($latest->alergi, 20) }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($latest)
                                        <span class="text-xs">{{ \Carbon\Carbon::parse($latest->tanggal_pemeriksaan)->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('adminkelas.anak.show', $anak) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition" style="color: #1A6B6B; background: #E8F5F5;">Riwayat</a>
                                        <button @click="openInput({{ json_encode($anak) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition" style="color: #1A6B6B; background: #D0E8E8;">Input Data</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400">Belum ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($anaks->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: rgba(0,0,0,0.06);">
                    {{ $anaks->links() }}
                </div>
            @endif
        </div>

        <!-- INPUT MODAL -->
        <template x-if="selectedAnak">
            <div x-show="showInputModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
                <div class="modal-box max-w-lg" @click.away="showInputModal = false">
                    <form action="{{ route('adminkelas.kesehatan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="anak_id" :value="selectedAnak.id">
                        <div class="modal-header">
                            <h3 class="section-title">Input Kesehatan & Kebersihan</h3>
                            <p class="section-subtitle">Siswa: <span class="font-bold text-[#1A6B6B]" x-text="selectedAnak.name"></span></p>
                        </div>
                        <div class="modal-body grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="input-label">Tanggal Pemeriksaan</label>
                                <input type="date" name="tanggal_pemeriksaan" required class="input-field" x-model="form.tanggal_pemeriksaan">
                            </div>
                            <div>
                                <label class="input-label">Berat Badan (kg)</label>
                                <input type="number" step="0.01" name="berat_badan" class="input-field" placeholder="0.00" x-model="form.berat_badan">
                            </div>
                            <div>
                                <label class="input-label">Tinggi Badan (cm)</label>
                                <input type="number" step="0.01" name="tinggi_badan" class="input-field" placeholder="0.00" x-model="form.tinggi_badan">
                            </div>
                            <div class="col-span-2">
                                <label class="input-label">Lingkar Kepala (cm)</label>
                                <input type="number" step="0.01" name="lingkar_kepala" class="input-field" placeholder="0.00" x-model="form.lingkar_kepala">
                            </div>
                            
                            <div class="col-span-2 text-xs font-bold text-[#1A6B6B] border-b pb-1 mt-2">Kebersihan</div>
                            <div>
                                <label class="input-label">Gigi</label>
                                <select name="gigi" class="input-field" x-model="form.gigi">
                                    <option value="Bersih">Bersih</option>
                                    <option value="Cukup Bersih">Cukup Bersih</option>
                                    <option value="Kotor">Kotor</option>
                                    <option value="Berlubang">Berlubang</option>
                                    <option value="Perlu Tindakan">Perlu Tindakan</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Telinga</label>
                                <select name="telinga" class="input-field" x-model="form.telinga">
                                    <option value="Bersih">Bersih</option>
                                    <option value="Cukup Bersih">Cukup Bersih</option>
                                    <option value="Kotor">Kotor</option>
                                    <option value="Perlu Tindakan">Perlu Tindakan</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Kuku</label>
                                <select name="kuku" class="input-field" x-model="form.kuku">
                                    <option value="Bersih dan rapi">Bersih dan rapi</option>
                                    <option value="Cukup Bersih">Cukup Bersih</option>
                                    <option value="Kotor dan panjang">Kotor dan panjang</option>
                                    <option value="Perlu Tindakan">Perlu Tindakan</option>
                                </select>
                            </div>
                            <div class="col-span-2 mt-2">
                                <label class="input-label">Alergi (jika ada)</label>
                                <textarea name="alergi" class="input-field" rows="2" placeholder="Sebutkan alergi jika ada..." x-model="form.alergi"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="showInputModal = false" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-primary">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
