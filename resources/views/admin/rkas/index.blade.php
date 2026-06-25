<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 class="font-bold text-xl" style="color:#2C2C2C;">RKAS</h2>
            </div>
            <a href="{{ route('admin.rkas.laporan', ['tahun_ajaran' => $tahunAjaran]) }}" class="btn-secondary text-sm">Laporan Komparasi</a>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto"
         x-data="{ showCreateModal: false }">

        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 flex flex-wrap gap-3 justify-between items-center border-b" style="border-color:rgba(0,0,0,0.06);">
                <form method="GET" class="flex gap-2 items-center">
                    <label class="text-sm font-semibold" style="color:#9E9790;">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="input-field text-sm" onchange="this.form.submit()">
                        @foreach($tahunOptions as $ta)
                            <option value="{{ $ta }}" @selected($tahunAjaran === $ta)>{{ $ta }}</option>
                        @endforeach
                    </select>
                </form>
                <button @click="showCreateModal=true" class="btn-primary">+ Buat RKAS</button>
            </div>
            <table class="data-table">
                <thead><tr><th>Periode</th><th>Status</th><th class="text-center">Baris</th><th>Sync Terakhir</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($rkasList as $rkas)
                        <tr>
                            <td class="font-medium">{{ \App\Support\TahunAjaran::label($rkas->tahun_ajaran, $rkas->semester) }}</td>
                            <td><span class="badge {{ $rkas->status === 'final' ? 'badge-green' : 'badge-blue' }}">{{ ucfirst($rkas->status) }}</span></td>
                            <td class="text-center">{{ $rkas->lines_count }}</td>
                            <td class="text-xs" style="color:#9E9790;">{{ $rkas->synced_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <a href="{{ route('admin.rkas.edit', $rkas) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Edit</a>
                                    <form action="{{ route('admin.rkas.sync', $rkas) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#145252;background:#B8DEDE;">Sync</button>
                                    </form>
                                    <a href="{{ route('admin.rkas.laporan', ['tahun_ajaran' => $rkas->tahun_ajaran, 'semester' => $rkas->semester]) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#6B5B3E;background:#FFF8E7;">Laporan</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-12 text-center" style="color:#9E9790;">Belum ada RKAS untuk TA {{ $tahunAjaran }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-show="showCreateModal" class="modal-overlay" style="display:none;">
            <div x-show="showCreateModal" x-transition class="modal-box" @click.away="showCreateModal=false">
                <form action="{{ route('admin.rkas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Buat RKAS Baru</h3></div>
                    <div class="modal-body grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="input-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran" class="input-field">
                                @foreach($tahunOptions as $ta)
                                    <option value="{{ $ta }}" @selected($tahunAjaran === $ta)>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Semester</label>
                            <select name="semester" class="input-field">
                                <option value="1">Semester 1 (Jul – Des)</option>
                                <option value="2">Semester 2 (Jan – Jun)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showCreateModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Buat</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
