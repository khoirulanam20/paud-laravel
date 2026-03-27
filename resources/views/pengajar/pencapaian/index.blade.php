<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Evaluasi Pencapaian Siswa</h2>
        </div>
    </x-slot>
    @php
        $scoreLabels = [
            'BB' => 'BB — Belum Berkembang',
            'MB' => 'MB — Mulai Berkembang',
            'BSH' => 'BSH — Berkembang Sesuai Harapan',
            'BSB' => 'BSB — Berkembang Sangat Baik',
        ];
    @endphp
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, editData:{}, deleteRoute:'', openEdit(d){this.editData=d;this.showEditModal=true}, openDelete(r){this.deleteRoute=r;this.showDeleteModal=true} }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Laporan Evaluasi Pencapaian</h3><p class="section-subtitle">Penilaian perkembangan individual setiap siswa berdasarkan indikator matrikulasi</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Buat Evaluasi</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Anak</th><th>Indikator yang Dinilai</th><th>Nilai</th><th>Catatan</th><th>Tanggal</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($pencapaians as $p)
                        <tr>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $p->anak->name ?? '-' }}</span></td>
                            <td>{{ $p->matrikulasi->indicator ?? '-' }}</td>
                            <td><span class="badge badge-teal font-bold text-base px-3 py-1">{{ $scoreLabels[$p->score] ?? $p->score }}</span></td>
                            <td class="max-w-xs truncate italic" style="color:#9E9790;">"{{ $p->feedback }}"</td>
                            <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ json_encode($p) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button @click="openDelete('{{ route('pengajar.pencapaian.destroy', $p) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center" style="color:#9E9790;">Belum ada evaluasi yang dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pencapaians->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $pencapaians->links() }}</div>@endif
        </div>
        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('pengajar.pencapaian.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Buat Laporan Evaluasi</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Anak yang Dinilai</label><select name="anak_id" required class="input-field"><option value="">-- Pilih Anak --</option>@foreach($anaks as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
                        <div><label class="input-label">Indikator Matrikulasi</label><select name="matrikulasi_id" required class="input-field"><option value="">-- Pilih Indikator --</option>@foreach($matrikulasis as $m)<option value="{{ $m->id }}">{{ $m->indicator }}</option>@endforeach</select></div>
                        <div><label class="input-label">Nilai Capaian</label><select name="score" required class="input-field"><option value="BB">BB - Belum Berkembang</option><option value="MB">MB - Mulai Berkembang</option><option value="BSH">BSH - Berkembang Sesuai Harapan</option><option value="BSB">BSB - Berkembang Sangat Baik</option></select></div>
                        <div><label class="input-label">Catatan / Feedback</label><textarea name="feedback" rows="3" class="input-field" placeholder="Anak menunjukkan..."></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Evaluasi</button></div>
                </form>
            </div>
        </div>
        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/pengajar/pencapaian/${editData.id}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Evaluasi</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Anak</label><select name="anak_id" x-model="editData.anak_id" required class="input-field">@foreach($anaks as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
                        <div><label class="input-label">Indikator</label><select name="matrikulasi_id" x-model="editData.matrikulasi_id" required class="input-field">@foreach($matrikulasis as $m)<option value="{{ $m->id }}">{{ $m->indicator }}</option>@endforeach</select></div>
                        <div><label class="input-label">Nilai</label><select name="score" x-model="editData.score" required class="input-field"><option value="BB">BB</option><option value="MB">MB</option><option value="BSH">BSH</option><option value="BSB">BSB</option></select></div>
                        <div><label class="input-label">Catatan</label><textarea name="feedback" x-model="editData.feedback" rows="3" class="input-field"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
        <!-- DELETE MODAL -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal=false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><h3 class="section-title">Hapus Evaluasi?</h3><p class="section-subtitle mt-1">Data evaluasi ini akan terhapus secara permanen.</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
