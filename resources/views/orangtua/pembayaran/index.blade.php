<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Pembayaran Bulanan</h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if($anaks->count() > 1)
            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                    <h3 class="section-title mb-0">Filter Tagihan</h3>
                </div>
                <form method="GET" action="{{ route('orangtua.pembayaran.index') }}" class="px-6 py-4 flex flex-wrap items-end gap-4">
                    <div class="min-w-[220px]">
                        <label class="input-label">Anak</label>
                        <select name="anak_id" class="input-field" onchange="this.form.submit()">
                            <option value="">Semua Anak</option>
                            @foreach($anaks as $anak)
                                <option value="{{ $anak->id }}" @selected($selectedAnakId === $anak->id)>{{ $anak->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($selectedAnakId !== null)
                        <div>
                            <a href="{{ route('orangtua.pembayaran.index') }}" class="btn-secondary">Reset</a>
                        </div>
                    @endif
                </form>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" data-tour="ortu-pembayaran-summary">
            @foreach($anaks as $anak)
                @php
                    $tagihanAnak = $summaryPembayarans->where('anak_id', $anak->id);
                    $belumBayar = $tagihanAnak->whereIn('status', ['pending', 'rejected'])->sum('total_bayar');
                    $sudahBayar = $tagihanAnak->where('status', 'approved')->sum('total_bayar');
                @endphp
                <div class="card p-5 border-l-4 border-l-[#1A6B6B]">
                    <div class="flex items-center gap-4 mb-4">
                        <x-foto-profil :path="$anak->photo" :name="$anak->name" size="md" />
                        <div>
                            <h4 class="font-bold">{{ $anak->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $anak->kelas->name ?? 'Tanpa Kelas' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Belum Dibayar</p>
                            <p class="text-lg font-bold text-amber-600">Rp {{ number_format($belumBayar, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Sudah Dibayar</p>
                            <p class="text-lg font-bold text-[#1A6B6B]">Rp {{ number_format($sudahBayar, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-4" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title mb-0">Daftar Tagihan</h3>
                <div class="text-xs font-semibold text-gray-400">{{ $pembayarans->total() }} tagihan</div>
            </div>
            <div class="overflow-x-auto" data-tour="ortu-pembayaran-table">
                <table class="w-full text-left">
                    <thead><tr class="bg-gray-50/50">
                        <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400">Periode</th>
                        <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400">Anak</th>
                        <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 text-center">Hadir</th>
                        <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 text-right">Total</th>
                        <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 text-center">Status</th>
                        <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 text-right">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pembayarans as $p)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-6 py-4 font-bold text-sm">{{ $p->getPeriodeLabel() }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->anak->name }}</td>
                                <td class="px-6 py-4 text-center text-sm font-semibold text-[#1A6B6B]">{{ $p->hari_hadir }} hari</td>
                                <td class="px-6 py-4 text-right font-bold">{{ $p->getTotalFormatted() }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="badge badge-{{ $p->status_badge }}">{{ $p->status_label }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a @if($loop->first) data-tour="ortu-pembayaran-action-detail" @endif href="{{ route('orangtua.pembayaran.show', $p) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#1A6B6B;background:#D0E8E8;">Detail</a>
                                        @if($p->isApproved())
                                            <a href="{{ route('orangtua.pembayaran.invoice', $p) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg inline-flex items-center gap-1" style="color:#1A6B6B;background:#E8F5F5;border:1px solid #D0E8E8;" target="_blank" rel="noopener" title="Download Invoice">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Invoice
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$pembayarans" />
                {{ $pembayarans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
