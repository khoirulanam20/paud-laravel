<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kegiatan Rutin Anak Saya</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                <form method="GET" class="flex flex-wrap items-end gap-3 flex-1">
                    @if($anaks->count() > 1)
                        <div>
                            <label class="input-label">Pilih Anak</label>
                            <select name="anak_id" class="input-field" onchange="this.form.submit()">
                                <option value="">Semua Anak</option>
                                @foreach($anaks as $anak)
                                    <option value="{{ $anak->id }}" {{ request('anak_id') == $anak->id ? 'selected' : '' }}>
                                        {{ $anak->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="input-label">Mulai Tanggal</label>
                        <input type="date" name="mulai" value="{{ $mulai }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Sampai Tanggal</label>
                        <input type="date" name="sampai" value="{{ $sampai }}" class="input-field">
                    </div>
                    <button type="submit" class="btn-primary">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Anak</th>
                            <th>Kegiatan</th>
                            <th>Aspek</th>
                            <th>Status Pencapaian</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatans as $keg)
                            <tr>
                                <td class="whitespace-nowrap font-medium text-[#1A6B6B]">
                                    {{ \Carbon\Carbon::parse($keg->tanggal)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <x-foto-profil :path="$keg->anak->photo" :name="$keg->anak->name" size="sm" />
                                        <span class="font-semibold text-gray-900 block">{{ $keg->anak->name }}</span>
                                    </div>
                                </td>
                                <td class="font-semibold text-[#2C2C2C]">{{ $keg->kegiatan }}</td>
                                <td><span class="text-xs text-gray-500 font-medium px-2 py-1 bg-gray-100 rounded-md">{{ $keg->aspek }}</span></td>
                                <td>
                                    <span class="text-xs font-bold px-3 py-1.5 rounded-lg {{ $keg->status_pencapaian ? 'bg-[#E8F5E9] text-[#2E7D32]' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $keg->status_pencapaian ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs text-gray-600 @if(!$keg->keterangan) italic @endif">
                                        {{ $keg->keterangan ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                        <p>Belum ada data kegiatan rutin pada periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
