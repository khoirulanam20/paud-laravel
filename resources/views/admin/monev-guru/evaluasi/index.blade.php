<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Monev Guru</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 border-b" style="border-color:rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Evaluasi kinerja guru</h3>
                    <p class="section-subtitle">Penilaian manual berdasarkan rubrik kriteria per periode.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.monev-guru.kriteria.index') }}" class="btn-secondary text-sm">Kelola Kriteria</a>
                    <x-export-excel route="admin.monev-guru.export" />
                    <a href="{{ route('admin.monev-guru.create') }}" class="btn-primary text-sm">+ Buat Evaluasi</a>
                </div>
            </div>
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FAF6F0;">
                <form method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="input-label">Guru</label>
                        <select name="pengajar_id" class="input-field w-full">
                            <option value="">Semua</option>
                            @foreach($pengajars as $p)
                                <option value="{{ $p->id }}" @selected(request('pengajar_id') == $p->id)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Status</label>
                        <select name="status" class="input-field w-full">
                            <option value="">Semua</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            <option value="final" @selected(request('status') === 'final')>Final</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Periode mulai ≥</label>
                        <input type="date" name="periode_mulai" value="{{ request('periode_mulai') }}" class="input-field w-full">
                    </div>
                    <div>
                        <label class="input-label">Periode selesai ≤</label>
                        <input type="date" name="periode_selesai" value="{{ request('periode_selesai') }}" class="input-field w-full">
                    </div>
                    <div><button type="submit" class="btn-primary w-full">Filter</button></div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Guru</th><th>Judul / Periode</th><th>Skor</th><th>Status</th><th>Evaluator</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($evaluasis as $e)
                        <tr>
                            <td class="font-semibold">{{ $e->pengajar->name ?? '—' }}</td>
                            <td>
                                <div>{{ $e->judul ?: 'Evaluasi Guru' }}</div>
                                <div class="text-xs" style="color:#9E9790;">{{ $e->periodeLabel() }}</div>
                            </td>
                            <td>@if($e->skor_keseluruhan !== null)<span class="font-bold" style="color:#1A6B6B;">{{ $e->skor_keseluruhan }}</span>@else—@endif</td>
                            <td>@if($e->isFinal())<span class="badge badge-teal">Final</span>@else<span class="badge" style="background:#FDE9BC;color:#8B6914;">Draft</span>@endif</td>
                            <td class="text-sm">{{ $e->evaluator->name ?? '—' }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.monev-guru.show', $e) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Detail</a>
                                    @if($e->isDraft())
                                        <a href="{{ route('admin.monev-guru.edit', $e) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#555;background:#eee;">Edit</a>
                                        <form action="{{ route('admin.monev-guru.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('Hapus draft evaluasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.monev-guru.pdf', $e) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#555;background:#eee;">PDF</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center" style="color:#9E9790;">Belum ada evaluasi guru.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$evaluasis" />
                {{ $evaluasis->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
