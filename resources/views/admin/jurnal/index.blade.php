<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Jurnal Umum</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{ showDeleteModal: false, deleteRoute: '', openDelete(r) { this.deleteRoute = r; this.showDeleteModal = true } }"
         @tour-close-modals.window="showDeleteModal = false">
        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 border-b" style="border-color:rgba(0,0,0,0.06);">
                <div>
                    <h3 class="section-title">Daftar Jurnal</h3>
                    <p class="section-subtitle">Semua jurnal manual dan auto-generated</p>
                </div>
                <a href="{{ route('admin.jurnal.create') }}" class="btn-primary">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Jurnal
                </a>
            </div>

            <!-- Filter Periode -->
            <div class="px-6 py-3 border-b flex flex-wrap gap-3 items-center" style="border-color:rgba(0,0,0,0.06);">
                <form method="GET" class="flex flex-wrap gap-3 items-center">
                    <select name="bulan" class="input-field w-36">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="tahun" class="input-field w-24">
                        @foreach(range(now()->year - 2, now()->year) as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-xs px-4">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No. Jurnal</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Sumber</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jurnals as $jurnal)
                            @php
                                $totalDebit = $jurnal->lines->sum('debit');
                            @endphp
                            <tr>
                                <td class="font-mono text-sm font-semibold" style="color: #1A6B6B;">{{ $jurnal->no_jurnal }}</td>
                                <td class="whitespace-nowrap">{{ $jurnal->tanggal->format('d M Y') }}</td>
                                <td>{{ Str::limit($jurnal->deskripsi, 60) }}</td>
                                <td class="text-right font-semibold">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge text-xs {{ $jurnal->source === 'manual' ? 'badge-green' : 'badge-blue' }}" style="background: {{ $jurnal->source === 'manual' ? '#D0E8E8' : '#E0D6C8' }}; color: {{ $jurnal->source === 'manual' ? '#1A6B6B' : '#6B5B3A' }};">
                                        {{ $jurnal->source === 'manual' ? 'Manual' : 'Auto' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.jurnal.show', $jurnal) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Detail</a>
                                        <button type="button"
                                            @click="openDelete('{{ route('admin.jurnal.destroy', $jurnal) }}')"
                                            class="text-xs font-semibold px-3 py-1.5 rounded-lg"
                                            style="color:#C0392B;background:#FAD7D2;">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center" style="color:#9E9790;">Tidak ada jurnal pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$jurnals" />
                {{ $jurnals->links() }}
            </div>
        </div>

        <x-confirm-modal
            show="showDeleteModal"
            action-binding="deleteRoute"
            method="DELETE"
            title="Hapus Jurnal?"
            message="Jurnal dan entri terkait akan dihapus permanen. Cashflow terkait juga akan terlepas."
        />
    </div>
</x-app-layout>
