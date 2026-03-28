<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Evaluasi Pencapaian Siswa</h2>
        </div>
    </x-slot>

    @php
        // Build a JS-friendly map: kegiatan_id => [{id, indicator, aspek}]
        $kegiatanMatrikulasi = [];
        foreach($kegiatans as $kg) {
            $kegiatanMatrikulasi[$kg->id] = $kg->matrikulasis->map(fn($m) => [
                'id' => $m->id,
                'label' => ($m->aspek ? $m->aspek.': ' : '').$m->indicator,
            ])->values()->toArray();
        }

        $scoreLabels = [
            'BB'  => 'BB — Belum Berkembang',
            'MB'  => 'MB — Mulai Berkembang',
            'BSH' => 'BSH — Berkembang Sesuai Harapan',
            'BSB' => 'BSB — Berkembang Sangat Baik',
        ];
        $scoreColors = ['BB'=>'#FAD7D2','MB'=>'#FDE9BC','BSH'=>'#D0E8E8','BSB'=>'#C5E8C5'];
    @endphp

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{
            showCreateModal: false,
            showEditModal: false,
            showDeleteModal: false,
            editData: {},
            deleteRoute: '',
            kegiatanMap: {{ Js::from($kegiatanMatrikulasi) }},
            selectedKegiatanId: '',
            selectedKegiatanIdEdit: '',
            get matrikulasiOptions() { return this.kegiatanMap[this.selectedKegiatanId] || []; },
            get matrikulasiOptionsEdit() { return this.kegiatanMap[this.selectedKegiatanIdEdit] || []; },
            openEdit(d) { this.editData = d; this.selectedKegiatanIdEdit = String(d.kegiatan_id); this.showEditModal = true; },
            openDelete(r) { this.deleteRoute = r; this.showDeleteModal = true; }
         }">

        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ session('success') }}</div>@endif

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4" style="border-color: rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Filter Tanggal</h3>
                    <p class="section-subtitle">Menampilkan pencapaian yang diinputkan pada tanggal tersebut.</p>
                </div>
                <form method="get" action="{{ route('pengajar.pencapaian.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="input-label">Tanggal Input</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field" onchange="this.form.submit()" required>
                    </div>
                    <button type="submit" class="btn-primary">Tampilkan</button>
                </form>
            </div>
            <div class="px-6 py-3 text-sm" style="background: #FAF6F0; color: #6B6560;">
                Menampilkan data tercatat pada <strong style="color:#2C2C2C;">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}</strong>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Laporan Evaluasi Pencapaian</h3><p class="section-subtitle">Input berdasarkan Kegiatan — matrikulasi terisi otomatis</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Buat Evaluasi</button>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Foto</th><th>Anak</th><th>Kegiatan</th><th>Nilai</th><th>Catatan</th><th>Tanggal</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($pencapaians as $p)
                        <tr>
                            <td>
                                @if($p->photo)
                                    <img src="{{ Storage::url($p->photo) }}" class="h-10 w-10 object-cover rounded shadow-sm cursor-pointer hover:opacity-80 transition" onclick="window.open(this.src)">
                                @else
                                    <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center"><svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                @endif
                            </td>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $p->anak->name ?? '-' }}</span></td>
                            <td>
                                @if($p->kegiatan)
                                    <div class="font-medium text-sm" style="color:#2C2C2C;">{{ $p->kegiatan->title }}</div>
                                    <div class="text-xs" style="color:#9E9790;">{{ \Carbon\Carbon::parse($p->kegiatan->date)->format('d M Y') }}</div>
                                    @if($p->kegiatan->matrikulasis->count() > 0)
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($p->kegiatan->matrikulasis as $m)
                                            <span class="badge badge-teal text-xs">{{ $m->indicator }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                @else -
                                @endif
                            </td>
                            <td><span class="text-xs font-bold px-2 py-1 rounded" style="background:{{ $scoreColors[$p->score] ?? '#eee' }};">{{ $p->score }}</span></td>
                            <td class="max-w-xs truncate italic text-sm" style="color:#9E9790;">{{ $p->feedback }}</td>
                            <td class="whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ json_encode(['id'=>$p->id,'anak_id'=>$p->anak_id,'kegiatan_id'=>$p->kegiatan_id,'score'=>$p->score,'feedback'=>$p->feedback]) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('pengajar.pencapaian.destroy', $p) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-12 text-center" style="color:#9E9790;">Belum ada evaluasi yang dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pencapaians->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $pencapaians->links() }}</div>@endif
        </div>

        {{-- CREATE MODAL --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('pengajar.pencapaian.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Buat Laporan Evaluasi</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        {{-- Anak --}}
                        <div>
                            <label class="input-label">Anak yang Dinilai <span class="text-red-500">*</span></label>
                            <select name="anak_id" required class="input-field">
                                <option value="">-- Pilih Anak --</option>
                                @foreach($anaks as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                            </select>
                        </div>

                        {{-- Kegiatan --}}
                        <div>
                            <label class="input-label">Pilih Kegiatan <span class="text-red-500">*</span></label>
                            <select name="kegiatan_id" required class="input-field" x-model="selectedKegiatanId">
                                <option value="">-- Pilih Kegiatan --</option>
                                @forelse($kegiatans as $k)
                                    <option value="{{ $k->id }}">{{ \Carbon\Carbon::parse($k->date)->format('d M Y') }} — {{ $k->title }}</option>
                                @empty
                                    <option disabled>Belum ada kegiatan. Buat dulu di menu Jurnal Kegiatan.</option>
                                @endforelse
                            </select>
                            @if($kegiatans->count() > 0)
                            <p class="text-xs mt-1" style="color:#9E9790;">Indikator matrikulasi mengikuti kegiatan yang dipilih secara otomatis.</p>
                            @endif
                        </div>

                        {{-- Score & Photo --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Nilai Capaian <span class="text-red-500">*</span></label>
                                <select name="score" required class="input-field">
                                    <option value="BB">BB — Belum Berkembang</option>
                                    <option value="MB">MB — Mulai Berkembang</option>
                                    <option value="BSH">BSH — Berkembang Sesuai Harapan</option>
                                    <option value="BSB">BSB — Berkembang Sangat Baik</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Foto Bukti</label>
                                <input type="file" name="photo" accept="image/*" class="input-field py-1.5 text-xs">
                            </div>
                        </div>

                        <div><label class="input-label">Catatan / Feedback <span class="text-red-500">*</span></label><textarea name="feedback" required rows="3" class="input-field" placeholder="Anak menunjukkan..."></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Evaluasi</button></div>
                </form>
            </div>
        </div>

        {{-- EDIT MODAL --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/pengajar/pencapaian/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Evaluasi</h3></div>
                    <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="input-label">Anak <span class="text-red-500">*</span></label>
                            <select name="anak_id" x-model="editData.anak_id" required class="input-field">
                                @foreach($anaks as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Kegiatan <span class="text-red-500">*</span></label>
                            <select name="kegiatan_id" required class="input-field" x-model="selectedKegiatanIdEdit">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($kegiatans as $k)
                                    <option value="{{ $k->id }}">{{ \Carbon\Carbon::parse($k->date)->format('d M Y') }} — {{ $k->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Nilai <span class="text-red-500">*</span></label>
                                <select name="score" x-model="editData.score" required class="input-field">
                                    <option value="BB">BB</option><option value="MB">MB</option><option value="BSH">BSH</option><option value="BSB">BSB</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Ganti Foto</label>
                                <input type="file" name="photo" accept="image/*" class="input-field py-1.5 text-xs">
                            </div>
                        </div>
                        <div><label class="input-label">Catatan <span class="text-red-500">*</span></label><textarea name="feedback" required x-model="editData.feedback" rows="3" class="input-field"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        {{-- DELETE MODAL --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal=false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
                        <h3 class="section-title">Hapus Evaluasi?</h3>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
