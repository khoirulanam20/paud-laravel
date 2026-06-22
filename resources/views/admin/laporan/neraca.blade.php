<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laporan Neraca</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <form method="GET" class="flex flex-wrap gap-3 mb-6">
            <div>
                <label class="input-label">Per Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" class="input-field w-48">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary text-xs px-4">Tampilkan</button>
            </div>
        </form>

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 text-center border-b" style="border-color:rgba(0,0,0,0.06);">
                <p class="font-bold text-lg" style="color:#2C2C2C;">{{ auth()->user()->sekolah->name ?? 'Sekolah' }}</p>
                <p class="section-title text-base">Laporan Neraca</p>
                <p class="section-subtitle">Per {{ \Carbon\Carbon::parse($sampaiTanggal)->format('d M Y') }}</p>
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
                        <!-- ASET -->
                        <tr><td colspan="3" class="font-bold text-xs uppercase py-2" style="background:#D0E8E8; color:#1A6B6B;">Aset</td></tr>
                        @php $no = 1; @endphp
                        @foreach($asets as $item)
                            <tr>
                                <td class="text-sm" style="color:#9E9790;">{{ $no++ }}</td>
                                <td class="text-sm" style="color:#2C2C2C;">{{ $item['akun']->kode }} - {{ $item['akun']->nama }}</td>
                                <td class="text-right font-semibold text-sm" style="color:#1A6B6B;">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td class="font-bold text-sm">Total Aset</td>
                            <td class="text-right font-bold text-sm" style="border-top:2px solid #1A6B6B; color:#1A6B6B;">Rp {{ number_format($totalAset, 0, ',', '.') }}</td>
                        </tr>

                        <!-- LIABILITAS -->
                        <tr><td colspan="3" class="font-bold text-xs uppercase py-2" style="background:#FAD7D2; color:#C0392B;">Liabilitas</td></tr>
                        @php $no = 1; @endphp
                        @foreach($liabilitas as $item)
                            <tr>
                                <td class="text-sm" style="color:#9E9790;">{{ $no++ }}</td>
                                <td class="text-sm" style="color:#2C2C2C;">{{ $item['akun']->kode }} - {{ $item['akun']->nama }}</td>
                                <td class="text-right font-semibold text-sm" style="color:#C0392B;">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td class="font-bold text-sm">Total Liabilitas</td>
                            <td class="text-right font-bold text-sm" style="border-top:2px solid #C0392B; color:#C0392B;">Rp {{ number_format($totalLiabilitas, 0, ',', '.') }}</td>
                        </tr>

                        <!-- EKUITAS -->
                        <tr><td colspan="3" class="font-bold text-xs uppercase py-2" style="background:#E0D6C8; color:#6B5B3A;">Ekuitas</td></tr>
                        @php $no = 1; @endphp
                        @foreach($ekuitas as $item)
                            <tr>
                                <td class="text-sm" style="color:#9E9790;">{{ $no++ }}</td>
                                <td class="text-sm" style="color:#2C2C2C;">{{ $item['akun']->kode }} - {{ $item['akun']->nama }}</td>
                                <td class="text-right font-semibold text-sm" style="color:#6B5B3A;">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td class="font-bold text-sm">Total Ekuitas</td>
                            <td class="text-right font-bold text-sm" style="border-top:2px solid #6B5B3A; color:#6B5B3A;">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        @php $totalPasiva = $totalLiabilitas + $totalEkuitas; @endphp
                        <tr style="background:#F5F2ED;">
                            <td></td>
                            <td class="font-bold text-sm">Total Liabilitas + Ekuitas</td>
                            <td class="text-right font-bold text-sm" style="border-top:3px double #2C2C2C; color:#2C2C2C;">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-xs py-2 text-center" style="color: {{ $totalAset == $totalPasiva ? '#1A6B6B' : '#C0392B' }};">
                                {{ $totalAset == $totalPasiva ? 'Aset = Liabilitas + Ekuitas (Balance)' : 'Selisih: Rp ' . number_format(abs($totalAset - $totalPasiva), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
