<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.laporan-keuangan.index') }}" class="text-sm" style="color:#1A6B6B;">← Laporan</a>
                <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laba Rugi</h2>
            </div>
            <div class="flex items-center gap-2">
                <x-export-excel route="admin.laporan-keuangan.laba-rugi.export" class="text-sm" />
                <a href="{{ route('admin.laporan-keuangan.laba-rugi.pdf', request()->only(['tipe', 'bulan', 'tahun'])) }}" class="btn-secondary text-sm">Export PDF</a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <x-laporan-periode-filter :action="route('admin.laporan-keuangan.laba-rugi')" :periode="$periode" />

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 text-center border-b" style="border-color:rgba(0,0,0,0.06);">
                <p class="font-bold text-lg" style="color:#2C2C2C;">{{ auth()->user()->sekolah->name ?? 'Sekolah' }}</p>
                <p class="section-title text-base">Laporan Laba Rugi</p>
                <p class="section-subtitle">Periode: {{ $periode['label'] }}
                    @if($periode['tipe'] === 'bulanan')
                        ({{ \Carbon\Carbon::parse($periode['start'])->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($periode['end'])->format('d/m/Y') }})
                    @else
                        (1 Jan – 31 Des {{ $periode['tahun'] }})
                    @endif
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-8" style="background:#1A6B6B; color:white;">No</th>
                            <th style="background:#1A6B6B; color:white;">Keterangan</th>
                            <th class="text-right w-40" style="background:#1A6B6B; color:white;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="3" class="font-bold text-xs uppercase py-2" style="background:#D0E8E8; color:#1A6B6B;">Pendapatan</td></tr>
                        @php $no = 1; @endphp
                        @include('admin.laporan._grup-akun', ['grup' => $grupPendapatan, 'warna' => '#1A6B6B'])
                        <tr>
                            <td></td>
                            <td class="font-bold text-sm">Total Pendapatan</td>
                            <td class="text-right font-bold text-sm" style="border-top:2px solid #1A6B6B; color:#1A6B6B;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        </tr>

                        <tr><td colspan="3" class="font-bold text-xs uppercase py-2" style="background:#FAD7D2; color:#C0392B;">Beban</td></tr>
                        @php $no = 1; @endphp
                        @include('admin.laporan._grup-akun', ['grup' => $grupBeban, 'warna' => '#C0392B'])
                        <tr>
                            <td></td>
                            <td class="font-bold text-sm">Total Beban</td>
                            <td class="text-right font-bold text-sm" style="border-top:2px solid #C0392B; color:#C0392B;">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:#F5F2ED;">
                            <td></td>
                            <td class="font-bold text-sm">Surplus (Defisit) Periode</td>
                            <td class="text-right font-bold text-sm" style="border-top:3px double #2C2C2C; color:{{ $surplusDefisit >= 0 ? '#1A6B6B' : '#C0392B' }};">
                                Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
