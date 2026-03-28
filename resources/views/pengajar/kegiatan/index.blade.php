<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Jurnal Kegiatan Anak</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{ showCreateModal:false, showEditModal:false, showDeleteModal:false, showDetailModal:false, editData:{}, deleteRoute:'', detailData:{}, openEdit(d){ this.editData=d; this.showEditModal=true }, openDelete(r){ this.deleteRoute=r; this.showDeleteModal=true }, openDetail(d){ this.detailData=d; this.showDetailModal=true } }">

        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ session('success') }}</div>@endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Log Jurnal Saya</h3><p class="section-subtitle">Dokumentasi kegiatan harian yang terhubung ke Matrikulasi</p></div>
                <button @click="showCreateModal=true" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Buat Entri Jurnal</button>
            </div>

            <div class="divide-y" style="divide-color:rgba(0,0,0,0.04);">
                @forelse($kegiatans as $k)
                <div class="px-6 py-5">
                    {{-- Header Row --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            @if($k->photo)
                                <img src="{{ Storage::url($k->photo) }}" class="h-14 w-20 object-cover rounded-xl shrink-0">
                            @else
                                <div class="h-14 w-20 rounded-xl shrink-0 flex items-center justify-center" style="background:#D0E8E8;"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:#1A6B6B;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                            @endif
                            <div>
                                <p class="text-xs font-medium mb-1" style="color:#9E9790;">{{ \Carbon\Carbon::parse($k->date)->format('d M Y') }}</p>
                                <h4 class="font-bold" style="color:#2C2C2C;">{{ $k->title }}</h4>
                                @if($k->description)<p class="text-sm mt-1 leading-relaxed" style="color:#5A5A5A;">{{ Str::limit($k->description, 120) }}</p>@endif

                                {{-- Matrikulasi Badges --}}
                                @if($k->matrikulasis->count() > 0)
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach($k->matrikulasis as $m)
                                        <span class="badge badge-teal text-xs">{{ $m->aspek ? $m->aspek.': ' : '' }}{{ $m->indicator }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($k->pencapaians->count() > 0)
                                <span class="badge badge-teal font-medium">{{ $k->pencapaians->count() }} pencapaian</span>
                            @endif
                            <button @click="openDetail({{ json_encode(['id'=>$k->id,'title'=>$k->title,'date'=>$k->date,'description'=>$k->description,'pencapaians'=>$k->pencapaians->load(['anak'])]) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Detail</button>
                            <button @click="openEdit({{ json_encode(['id'=>$k->id,'date'=>$k->date,'title'=>$k->title,'description'=>$k->description,'matrikulasi_ids'=>$k->matrikulasis->pluck('id')]) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#5A6B1A;background:#E8EFD0;">Edit</button>
                            <button @click="openDelete('{{ route('pengajar.kegiatan.destroy', $k) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center" style="color:#9E9790;">
                    <svg class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Belum ada jurnal kegiatan yang dibuat.
                </div>
                @endforelse
            </div>

            @if($kegiatans->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $kegiatans->links() }}</div>@endif
        </div>

        {{-- DETAIL MODAL --}}
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDetailModal" x-transition class="modal-box max-w-2xl" @click.away="showDetailModal=false">
                <div class="modal-header"><h3 class="section-title">Detail Kegiatan: <span x-text="detailData.title"></span></h3></div>
                <div class="modal-body max-h-[75vh] overflow-y-auto space-y-5">
                    
                    {{-- Activity Info --}}
                    <div class="bg-gray-50 rounded-xl p-4 border" style="border-color:rgba(0,0,0,0.06);">
                        <p class="text-sm" style="color:#5A5A5A;"><strong class="text-gray-800">Tanggal:</strong> <span x-text="detailData.date ? new Date(detailData.date).toLocaleDateString() : '-'"></span></p>
                        <p class="text-sm mt-2" style="color:#5A5A5A;" x-show="detailData.description"><strong class="text-gray-800">Deskripsi:</strong> <br><span x-text="detailData.description"></span></p>
                    </div>

                    {{-- Student List --}}
                    <div>
                        <h4 class="font-bold mb-3 text-sm text-gray-800">Daftar Pencapaian Siswa</h4>
                        <div class="table-responsive border rounded-xl overflow-hidden" style="border-color:rgba(0,0,0,0.06);">
                            <table class="w-full text-sm">
                                <thead style="background:#F5F5F3;">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-xs" style="color:#5A5A5A;">Siswa</th>
                                        <th class="text-left px-4 py-3 font-semibold text-xs" style="color:#5A5A5A;">Nilai</th>
                                        <th class="text-left px-4 py-3 font-semibold text-xs" style="color:#5A5A5A;">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="pc in detailData.pencapaians" :key="pc.id">
                                        <tr class="border-t" style="border-color:rgba(0,0,0,0.04);">
                                            <td class="px-4 py-3 font-medium text-sm" style="color:#2C2C2C;" x-text="pc.anak?.name || '-'"></td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs font-bold px-2 py-1 rounded" 
                                                    x-bind:style="(pc.score=='BB') ? 'background:#FAD7D2' : (pc.score=='MB' ? 'background:#FDE9BC' : (pc.score=='BSH' ? 'background:#D0E8E8' : (pc.score=='BSB' ? 'background:#C5E8C5' : 'background:#eee')))"
                                                    x-text="pc.score"></span>
                                            </td>
                                            <td class="px-4 py-3 text-xs" style="color:#5A5A5A;" x-text="pc.feedback"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="!detailData.pencapaians || detailData.pencapaians.length === 0">
                                        <td colspan="3" class="px-4 py-6 text-center text-xs" style="color:#9E9790;">Belum ada pencapaian siswa pada kegiatan ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer"><button type="button" @click="showDetailModal=false" class="btn-secondary w-full">Tutup</button></div>
            </div>
        </div>

        {{-- CREATE MODAL --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('pengajar.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Buat Entri Jurnal Baru</h3></div>
                    <div class="modal-body max-h-[75vh] overflow-y-auto space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Tanggal <span class="text-red-500">*</span></label><input type="date" name="date" required value="{{ date('Y-m-d') }}" class="input-field"></div>
                            <div><label class="input-label">Foto Dokumentasi</label><input type="file" name="photo" accept="image/*" class="input-field py-2"></div>
                        </div>
                        <div><label class="input-label">Judul Kegiatan <span class="text-red-500">*</span></label><input type="text" name="title" required class="input-field" placeholder="Contoh: Bermain Sensori"></div>
                        <div><label class="input-label">Deskripsi Kegiatan</label><textarea name="description" rows="3" class="input-field" placeholder="Ceritakan apa yang terjadi..."></textarea></div>

                        {{-- Multi Matrikulasi --}}
                        <div>
                            <label class="input-label">Kaitkan dengan Indikator Matrikulasi</label>
                            <div class="mt-2 rounded-xl border overflow-hidden max-h-48 overflow-y-auto" style="border-color:rgba(0,0,0,0.1);">
                                @forelse($matrikulasis as $m)
                                <label class="flex items-start gap-3 px-4 py-2.5 hover:bg-teal-50 cursor-pointer border-b last:border-b-0" style="border-color:rgba(0,0,0,0.04);">
                                    <input type="checkbox" name="matrikulasi_ids[]" value="{{ $m->id }}" class="mt-0.5 accent-teal-600">
                                    <div>
                                        @if($m->aspek)<span class="text-xs font-bold" style="color:#1A6B6B;">{{ $m->aspek }}</span><br>@endif
                                        <span class="text-sm" style="color:#2C2C2C;">{{ $m->indicator }}</span>
                                    </div>
                                </label>
                                @empty
                                <p class="px-4 py-3 text-xs text-center" style="color:#9E9790;">Belum ada indikator matrikulasi. Buat dulu di menu Matrikulasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Jurnal</button></div>
                </form>
            </div>
        </div>

        {{-- EDIT MODAL --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/pengajar/kegiatan/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="section-title">Edit Jurnal Kegiatan</h3></div>
                    <div class="modal-body max-h-[75vh] overflow-y-auto space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="input-label">Tanggal <span class="text-red-500">*</span></label><input type="date" name="date" :value="editData.date ? editData.date.split('T')[0] : editData.date" required class="input-field"></div>
                            <div><label class="input-label">Ganti Foto</label><input type="file" name="photo" accept="image/*" class="input-field py-2"></div>
                        </div>
                        <div><label class="input-label">Judul <span class="text-red-500">*</span></label><input type="text" name="title" x-model="editData.title" required class="input-field"></div>
                        <div><label class="input-label">Deskripsi</label><textarea name="description" x-model="editData.description" rows="3" class="input-field"></textarea></div>
                        <div>
                            <label class="input-label">Indikator Matrikulasi</label>
                            <div class="mt-2 rounded-xl border overflow-hidden max-h-48 overflow-y-auto" style="border-color:rgba(0,0,0,0.1);">
                                @forelse($matrikulasis as $m)
                                <label class="flex items-start gap-3 px-4 py-2.5 hover:bg-teal-50 cursor-pointer border-b last:border-b-0" style="border-color:rgba(0,0,0,0.04);">
                                    <input type="checkbox" name="matrikulasi_ids[]" value="{{ $m->id }}"
                                        x-bind:checked="editData.matrikulasi_ids && editData.matrikulasi_ids.includes({{ $m->id }})"
                                        class="mt-0.5 accent-teal-600">
                                    <div>
                                        @if($m->aspek)<span class="text-xs font-bold" style="color:#1A6B6B;">{{ $m->aspek }}</span><br>@endif
                                        <span class="text-sm" style="color:#2C2C2C;">{{ $m->indicator }}</span>
                                    </div>
                                </label>
                                @empty
                                <p class="px-4 py-3 text-xs text-center" style="color:#9E9790;">Belum ada indikator matrikulasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>

        {{-- DELETE MODAL --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal=false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
                        <h3 class="section-title">Hapus Jurnal?</h3><p class="section-subtitle mt-1">Semua pencapaian yang terkait juga akan terhapus.</p>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
