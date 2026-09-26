<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.laporan-keuangan.index') }}" class="text-sm" style="color:#1A6B6B;">← Laporan</a>
                <h2 class="font-bold text-xl" style="color: #2C2C2C;">Buku Besar</h2>
            </div>
            @if($gl)
                <div class="flex items-center gap-2">
                    <x-export-excel route="admin.laporan-keuangan.buku-besar.export" class="text-sm" />
                    <a href="{{ route('admin.laporan-keuangan.buku-besar.pdf', request()->only(['tipe', 'bulan', 'tahun', 'akun_id'])) }}" class="btn-secondary text-sm">Export PDF</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
        <x-laporan-periode-filter :action="route('admin.laporan-keuangan.buku-besar')" :periode="$periode">
                <div class="min-w-[220px]">
                    <label class="input-label">Akun</label>
                    <select name="akun_id" class="input-field w-full" required>
                        <option value="">— Pilih akun —</option>
                        @foreach($akunOptions as $opt)
                            <option value="{{ $opt['id'] }}" @selected($akunId == $opt['id'])>{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                </div>
        </x-laporan-periode-filter>

        @if(!$gl)
            <div class="card p-8 text-center text-sm" style="color:#9E9790;">Pilih akun dan periode, lalu klik Tampilkan.</div>
        @else
            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 text-center border-b" style="border-color:rgba(0,0,0,0.06);">
                    <p class="font-bold text-lg" style="color:#2C2C2C;">{{ auth()->user()->sekolah->name ?? 'Sekolah' }}</p>
                    <p class="section-title text-base">Buku Besar — {{ $gl['akun']->kode }} {{ $gl['akun']->nama }}</p>
                    <p class="section-subtitle">{{ $periode['label'] }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table text-sm">
                        <thead>
                            <tr>
                                <th style="background:#1A6B6B; color:white;">Tanggal</th>
                                <th style="background:#1A6B6B; color:white;">No. Jurnal</th>
                                <th style="background:#1A6B6B; color:white;">Keterangan</th>
                                <th class="text-right" style="background:#1A6B6B; color:white;">Debit</th>
                                <th class="text-right" style="background:#1A6B6B; color:white;">Kredit</th>
                                <th class="text-right" style="background:#1A6B6B; color:white;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background:#F5F2ED;">
                                <td colspan="3" class="font-semibold">Saldo awal</td>
                                <td></td>
                                <td></td>
                                <td class="text-right font-semibold">Rp {{ number_format($gl['saldoAwal'], 0, ',', '.') }}</td>
                            </tr>
                            @foreach($gl['mutasi'] as $row)
                                @php $j = $row['line']->jurnal; @endphp
                                <tr>
                                    <td>{{ $j?->tanggal?->format('d/m/Y') ?? '—' }}</td>
                                    <td>{{ $j?->no_jurnal ?? '—' }}</td>
                                    <td>{{ Str::limit($j?->deskripsi ?? '—', 60) }}</td>
                                    <td class="text-right">{{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '—' }}</td>
                                    <td class="text-right">{{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '—' }}</td>
                                    <td class="text-right font-medium">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#F5F2ED;">
                                <td colspan="3" class="font-bold">Saldo akhir</td>
                                <td></td>
                                <td></td>
                                <td class="text-right font-bold">Rp {{ number_format($gl['saldoAkhir'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
