<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kelola Menu Makanan</h2>
        </div>
    </x-slot>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editData: {},
        deleteRoute: '',
        menuLines: [''],
        editMenuLines: [''],
        parseMenuToLines(text) {
            if (!text || !String(text).trim()) return [''];
            let t = String(text).trim();
            let parts = t.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
            if (parts.length <= 1 && t.includes(' • ')) parts = t.split(' • ').map(s => s.trim()).filter(Boolean);
            if (parts.length <= 1 && t.includes(',')) parts = t.split(',').map(s => s.trim()).filter(Boolean);
            return parts.length ? parts : [t];
        },
        joinMenuLines(lines) {
            return lines.map(s => String(s || '').trim()).filter(Boolean).join('\n');
        },
        openCreateModal() {
            this.menuLines = [''];
            this.showCreateModal = true;
        },
        openEdit(d) {
            this.editData = d;
            this.editMenuLines = this.parseMenuToLines(d.menu);
            this.showEditModal = true;
        },
        addMenuLine() { this.menuLines.push(''); },
        removeMenuLine(i) { if (this.menuLines.length > 1) this.menuLines.splice(i, 1); },
        addEditMenuLine() { this.editMenuLines.push(''); },
        removeEditMenuLine(i) { if (this.editMenuLines.length > 1) this.editMenuLines.splice(i, 1); },
        openDelete(r) { this.deleteRoute = r; this.showDeleteModal = true; }
    }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Jadwal Menu Makanan Harian</h3><p class="section-subtitle">Informasi menu dan gizi yang dikonsumsi siswa di sekolah</p></div>
                <button type="button" @click="openCreateModal()" class="btn-primary"><svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>Input Menu</button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Tanggal</th><th>Daftar Menu</th><th>Informasi Gizi</th><th>Foto</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($menus as $m)
                        <tr>
                            <td>
                                <div class="font-semibold text-sm" style="color:#2C2C2C;">{{ \Carbon\Carbon::parse($m->date)->format('d M Y') }}</div>
                                @if(\Carbon\Carbon::parse($m->date)->isToday())<span class="badge badge-green mt-1">Hari Ini</span>@endif
                            </td>
                            <td class="max-w-xs whitespace-pre-line text-sm">{{ $m->menu }}</td>
                            <td class="max-w-xs" style="color:#9E9790;">{{ $m->nutrition_info ?? '-' }}</td>
                            <td>
                                @if($m->photo)<img src="{{ Storage::url($m->photo) }}" class="h-12 w-16 object-cover rounded-lg">
                                @else<span class="text-xs" style="color:#9E9790;">Tidak ada</span>@endif
                            </td>
                            <td class="text-right"><div class="flex items-center justify-end gap-2">
                                <button type="button" @click='openEdit(@json($m))' class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                                <button type="button" @click="openDelete('{{ route('admin.menu-makanan.destroy', $m) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-12 text-center" style="color:#9E9790;">Belum ada jadwal menu yang diinput.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($menus->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $menus->links() }}</div>@endif
        </div>
        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.menu-makanan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="menu" :value="joinMenuLines(menuLines)">
                    <div class="modal-header"><h3 class="section-title">Input Jadwal Menu Baru</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Tanggal</label><input type="date" name="date" required value="{{ date('Y-m-d') }}" class="input-field"></div>
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="input-label mb-0">Daftar hidangan</label>
                                <button type="button" @click="addMenuLine()" class="text-xs font-semibold px-2.5 py-1 rounded-lg inline-flex items-center gap-1" style="color:#1A6B6B;background:#D0E8E8;">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                    Tambah
                                </button>
                            </div>
                            <p class="text-xs mb-2" style="color:#9E9790;">Satu baris per hidangan. Klik Tambah untuk menambah baris baru.</p>
                            <div class="space-y-2">
                                <template x-for="(line, index) in menuLines" :key="index">
                                    <div class="flex gap-2 items-center">
                                        <input type="text" class="input-field flex-1" x-model="menuLines[index]" placeholder="Contoh: Nasi putih, Ayam goreng…">
                                        <button type="button" @click="removeMenuLine(index)" class="shrink-0 text-xs font-semibold px-2 py-2 rounded-lg disabled:opacity-40" style="color:#C0392B;background:#FAD7D2;" :disabled="menuLines.length <= 1" title="Hapus baris">Hapus</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div><label class="input-label">Informasi Gizi & Catatan Alergi</label><textarea name="nutrition_info" rows="2" class="input-field" placeholder="Tanpa kacang, tinggi protein..."></textarea></div>
                        <div><label class="input-label">Foto Menu (opsional)</label><input type="file" name="photo" accept="image/*" class="input-field py-2"></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" :disabled="!joinMenuLines(menuLines)">Simpan Menu</button></div>
                </form>
            </div>
        </div>
        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showEditModal" x-transition class="modal-box" @click.away="showEditModal=false">
                <form :action="`/admin/menu-makanan/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <input type="hidden" name="menu" :value="joinMenuLines(editMenuLines)">
                    <div class="modal-header"><h3 class="section-title">Edit Menu Makanan</h3></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Tanggal</label><input type="date" name="date" :value="editData.date ? editData.date.split('T')[0] : ''" required class="input-field"></div>
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="input-label mb-0">Daftar hidangan</label>
                                <button type="button" @click="addEditMenuLine()" class="text-xs font-semibold px-2.5 py-1 rounded-lg inline-flex items-center gap-1" style="color:#1A6B6B;background:#D0E8E8;">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                    Tambah
                                </button>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(line, index) in editMenuLines" :key="index">
                                    <div class="flex gap-2 items-center">
                                        <input type="text" class="input-field flex-1" x-model="editMenuLines[index]" placeholder="Nama hidangan">
                                        <button type="button" @click="removeEditMenuLine(index)" class="shrink-0 text-xs font-semibold px-2 py-2 rounded-lg disabled:opacity-40" style="color:#C0392B;background:#FAD7D2;" :disabled="editMenuLines.length <= 1">Hapus</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div><label class="input-label">Informasi Gizi</label><textarea name="nutrition_info" x-model="editData.nutrition_info" rows="2" class="input-field"></textarea></div>
                        <div><label class="input-label">Ganti Foto (opsional)</label><input type="file" name="photo" accept="image/*" class="input-field py-2"></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showEditModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary" :disabled="!joinMenuLines(editMenuLines)">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
        <!-- DELETE MODAL -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDeleteModal" x-transition class="modal-box max-w-sm" @click.away="showDeleteModal=false">
                <form :action="deleteRoute" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-6"><div class="h-14 w-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#FAD7D2;"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#C0392B;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><h3 class="section-title">Hapus Menu?</h3><p class="section-subtitle mt-1">Data jadwal menu ini akan dihapus secara permanen.</p></div>
                    <div class="modal-footer"><button type="button" @click="showDeleteModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
