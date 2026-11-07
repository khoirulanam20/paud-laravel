<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kenaikan Kelas</h2>
        </div>
    </x-slot>
    <div class="app-page-shell" x-data="{ showMulaiModal: false }">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="card overflow-hidden mb-6">
            <div class="card-pad">
                <h3 class="section-title">Periode Aktif</h3>
                <p class="section-subtitle">{{ \App\Support\TahunAjaran::label($periode['tahun_ajaran'], $periode['semester']) }}</p>
                <div class="toolbar-actions mt-4">
                    <button type="button" @click="showMulaiModal = true" class="btn-primary">Mulai Kenaikan</button>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="card-pad page-toolbar border-b" style="border-color:rgba(0,0,0,0.06);">
                <div><h3 class="section-title">Riwayat Kenaikan</h3><p class="section-subtitle">Batch kenaikan yang pernah diterapkan</p></div>
                <form method="GET" action="{{ route('admin.kenaikan-kelas.index') }}" class="flex items-end gap-2 flex-wrap">
                    <div>
                        <label class="input-label">Tahun Ajaran Asal</label>
                        <select name="tahun_ajaran" class="input-field">
                            <option value="">Semua</option>
                            @foreach($tahunAjaranOptions as $ta)<option value="{{ $ta }}" @selected(request('tahun_ajaran') === $ta)>{{ $ta }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Mode</label>
                        <select name="mode" class="input-field">
                            <option value="">Semua</option>
                            <option value="semester" @selected(request('mode') === 'semester')>Semester</option>
                            <option value="usia" @selected(request('mode') === 'usia')>Usia</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Status</label>
                        <select name="status" class="input-field">
                            <option value="">Semua</option>
                            <option value="diterapkan" @selected(request('status') === 'diterapkan')>Diterapkan</option>
                            <option value="dibatalkan" @selected(request('status') === 'dibatalkan')>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">Filter</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Tanggal Efektif</th><th>Mode</th><th>TA Asal → Tujuan</th><th>Hasil</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($batches as $b)
                        <tr>
                            <td class="text-sm">{{ $b->tanggal_efektif?->format('d M Y') }}</td>
                            <td><span class="badge badge-teal">{{ ucfirst($b->mode) }}</span></td>
                            <td class="text-sm">{{ $b->tahun_ajaran_asal }} → {{ $b->tahun_ajaran_tujuan }}</td>
                            <td class="text-sm">{{ $b->jumlah_naik }} naik · {{ $b->jumlah_tinggal }} tinggal · {{ $b->jumlah_lulus }} lulus</td>
                            <td><span class="badge {{ $b->status === 'diterapkan' ? 'badge-teal' : 'badge-danger' }}">{{ ucfirst($b->status) }}</span></td>
                            <td class="text-right"><a href="{{ route('admin.kenaikan-kelas.show', $b) }}" class="row-action-icon" style="color:#1A6B6B;background:#E8F5F5;" title="Detail">Detail</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-6 md:py-12 text-center" style="color:#9E9790;">Belum ada kenaikan kelas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$batches" />
                {{ $batches->links() }}
            </div>
        </div>

        <div x-show="showMulaiModal" class="modal-overlay" style="display:none;">
            <div x-show="showMulaiModal" x-transition class="modal-box" @click.away="showMulaiModal = false">
                <form method="GET" action="{{ route('admin.kenaikan-kelas.preview') }}">
                    <div class="modal-header"><h3 class="section-title">Mulai Kenaikan Kelas</h3></div>
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="input-label">Mode Kenaikan</label>
                            <select name="mode" class="input-field" required>
                                <option value="semester">Semester — semua naik satu tingkat</option>
                                <option value="usia">Usia — naik sesuai usia minimum tingkat</option>
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Tanggal Acuan (usia dihitung per tanggal ini)</label>
                            <input type="date" name="tanggal_acuan" value="{{ now()->format('Y-m-d') }}" class="input-field" required>
                        </div>
                        <div>
                            <label class="input-label">Tanggal Efektif (riwayat mulai tanggal ini)</label>
                            <input type="date" name="tanggal_efektif" value="{{ now()->format('Y-m-d') }}" class="input-field" required>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showMulaiModal = false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Lihat Usulan</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
