<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laporan Laba Rugi</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <form method="GET" class="flex flex-wrap gap-3 mb-6">
            <select name="bulan" class="input-field w-36">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
            <select name="tahun" class="input-field w-24">
                @foreach(range(now()->year - 2, now()->year) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-xs px-4">Tampilkan</button>
        </form>

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 text-center border-b" style="border-color:rgba(0,0,0,0.06);">
                <p class="font-bold text-lg" style="color:#2C2C2C;">{{ auth()->user()->sekolah->name ?? 'Sekolah' }}</p>
                <p class="section-title text-base">Laporan Laba Rugi</p>
                <p class="section-subtitle">Periode: {{ \Carbon\Carbon::create()->month($bulan)->format('F Y') }}</p>
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
                        <!-- PENDAPATAN -->
                        <tr><td colspan="3" class="font-bold text-xs uppercase py-2" style="background:#D0E8E8; color:#1A6B6B;">Pendapatan</td></tr>
                        @php $no = 1; @endphp
                        @foreach($pendapatan as $item)
                            <tr>
                                <td class="text-sm" style="color:#9E9790;">{{ $no++ }}</td>
                                <td class="text-sm" style="color:#2C2C2C;">{{ $item['akun']->kode }} - {{ $item['akun']->nama }}</td>
                                <td class="text-right font-semibold text-sm" style="color:#1A6B6B;">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td class="font-bold text-sm">Total Pendapatan</td>
                            <td class="text-right font-bold text-sm" style="border-top:2px solid #1A6B6B; color:#1A6B6B;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        </tr>

                        <!-- BEBAN -->
                        <tr><td colspan="3" class="font-bold text-xs uppercase py-2" style="background:#FAD7D2; color:#C0392B;">Beban</td></tr>
                        @php $no = 1; @endphp
                        @foreach($beban as $item)
                            <tr>
                                <td class="text-sm" style="color:#9E9790;">{{ $no++ }}</td>
                                <td class="text-sm" style="color:#2C2C2C;">{{ $item['akun']->kode }} - {{ $item['akun']->nama }}</td>
                                <td class="text-right font-semibold text-sm" style="color:#C0392B;">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td class="font-bold text-sm">Total Beban</td>
                            <td class="text-right font-bold text-sm" style="border-top:2px solid #C0392B; color:#C0392B;">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="font-bold text-sm" style="{{ $surplusDefisit >= 0 ? 'color:#1A6B6B;' : 'color:#C0392B;' }}">
                                {{ $surplusDefisit >= 0 ? 'Surplus' : 'Defisit' }}
                            </td>
                            <td class="text-right font-bold text-base" style="border-top:3px double #2C2C2C; {{ $surplusDefisit >= 0 ? 'color:#1A6B6B;' : 'color:#C0392B;' }}">
                                Rp {{ number_format(abs($surplusDefisit), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
