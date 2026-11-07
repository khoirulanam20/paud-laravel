<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Detail Kenaikan Kelas</h2>
        </div>
    </x-slot>
    <div class="app-page-shell">
        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="card overflow-hidden mb-6">
            <div class="card-pad">
                <h3 class="section-title">Batch #{{ $batch->id }} — {{ ucfirst($batch->mode) }}</h3>
                <p class="section-subtitle">Efektif {{ $batch->tanggal_efektif?->format('d M Y') }} · {{ $batch->tahun_ajaran_asal }} → {{ $batch->tahun_ajaran_tujuan }} · {{ $batch->jumlah_naik }} naik · {{ $batch->jumlah_tinggal }} tinggal · {{ $batch->jumlah_lulus }} lulus</p>
                <p class="section-subtitle mt-1">Status: <span class="badge {{ $batch->status === 'diterapkan' ? 'badge-teal' : 'badge-danger' }}">{{ ucfirst($batch->status) }}</span> · Oleh: {{ $batch->dibuatOleh?->name ?? '-' }}</p>
                <div class="toolbar-actions mt-4">
                    <a href="{{ route('admin.kenaikan-kelas.index') }}" class="btn-secondary">Kembali</a>
                    @if($batch->status === 'diterapkan')
                    <form method="POST" action="{{ route('admin.kenaikan-kelas.destroy', $batch) }}" onsubmit="return confirm('Batalkan batch ini? Kelas siswa dikembalikan seperti sebelum batch.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger">Batalkan Batch Ini</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Siswa</th><th>Kelas Asal</th><th>Aksi</th><th>Kelas Tujuan</th><th>Catatan</th></tr></thead>
                    <tbody>
                        @foreach($batch->items as $item)
                        <tr>
                            <td class="text-sm font-semibold">{{ $item->anak?->name ?? '-' }}</td>
                            <td class="text-sm">{{ $item->kelasAsal?->name ?? '-' }}</td>
                            <td><span class="badge badge-teal">{{ ucfirst($item->aksi) }}</span></td>
                            <td class="text-sm">{{ $item->kelasTujuan?->name ?? '-' }}</td>
                            <td class="text-sm">{{ $item->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
