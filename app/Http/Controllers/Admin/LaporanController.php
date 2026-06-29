<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Cashflow;
use App\Models\JurnalLine;
use App\Services\AkuntansiService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        private AkuntansiService $akuntansiService
    ) {}

    /**
     * Laporan Arus Kas PSAK 2
     */
    public function arusKas(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $items = Cashflow::where('sekolah_id', $sekolahId)
            ->whereYear('date', $tahun)
            ->whereMonth('date', $bulan)
            ->whereNotNull('akun_id')
            ->with('akun')
            ->get()
            ->groupBy(fn ($c) => $c->akun?->kategori_arus_kas ?? 'tanpa_kategori');

        $labelKategori = [
            'operasi' => 'Arus Kas dari Aktivitas Operasi',
            'investasi' => 'Arus Kas dari Aktivitas Investasi',
            'pendanaan' => 'Arus Kas dari Aktivitas Pendanaan',
            'tanpa_kategori' => 'Transaksi Tanpa Kategori',
        ];

        // Hitung saldo awal (sebelum bulan terpilih)
        $saldoAwal = Cashflow::where('sekolah_id', $sekolahId)
            ->where('date', '<', "$tahun-".str_pad((string) $bulan, 2, '0', STR_PAD_LEFT).'-01')
            ->selectRaw('SUM(CASE WHEN type = "in" THEN amount ELSE 0 END) as total_in,
                         SUM(CASE WHEN type = "out" THEN amount ELSE 0 END) as total_out')
            ->first();
        $saldoAwalVal = ($saldoAwal->total_in ?? 0) - ($saldoAwal->total_out ?? 0);

        return view('admin.laporan.arus-kas', compact('items', 'labelKategori', 'bulan', 'tahun', 'saldoAwalVal'));
    }

    /**
     * Laporan Neraca
     */
    public function neraca(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $sampaiTanggal = $request->input('sampai_tanggal', now()->toDateString());

        $asets = Akun::where('sekolah_id', $sekolahId)
            ->where('jenis', 'aset')
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get()
            ->map(fn ($a) => [
                'akun' => $a,
                'saldo' => $this->akuntansiService->saldoAkun($a->id, $sampaiTanggal),
            ]);

        $liabilitas = Akun::where('sekolah_id', $sekolahId)
            ->where('jenis', 'liabilitas')
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get()
            ->map(fn ($a) => [
                'akun' => $a,
                'saldo' => $this->akuntansiService->saldoAkun($a->id, $sampaiTanggal),
            ]);

        $ekuitas = Akun::where('sekolah_id', $sekolahId)
            ->where('jenis', 'ekuitas')
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get()
            ->map(fn ($a) => [
                'akun' => $a,
                'saldo' => $this->akuntansiService->saldoAkun($a->id, $sampaiTanggal),
            ]);

        $totalAset = $asets->sum('saldo');
        $totalLiabilitas = $liabilitas->sum('saldo');
        $totalEkuitas = $ekuitas->sum('saldo');

        return view('admin.laporan.neraca', compact(
            'asets', 'liabilitas', 'ekuitas',
            'totalAset', 'totalLiabilitas', 'totalEkuitas',
            'sampaiTanggal'
        ));
    }

    /**
     * Laporan Laba Rugi
     */
    public function labaRugi(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $start = "$tahun-".str_pad((string) $bulan, 2, '0', STR_PAD_LEFT).'-01';
        $end = date('Y-m-t', strtotime($start));

        $pendapatan = Akun::where('sekolah_id', $sekolahId)
            ->where('jenis', 'pendapatan')
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get()
            ->map(function ($akun) use ($start, $end) {
                $lines = JurnalLine::where('akun_id', $akun->id)
                    ->whereHas('jurnal', fn ($q) => $q->whereBetween('tanggal', [$start, $end]))
                    ->selectRaw('SUM(kredit) as kredit, SUM(debit) as debit')
                    ->first();
                $saldo = ($lines->kredit ?? 0) - ($lines->debit ?? 0);

                return ['akun' => $akun, 'saldo' => max(0, $saldo)];
            });

        $beban = Akun::where('sekolah_id', $sekolahId)
            ->where('jenis', 'beban')
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get()
            ->map(function ($akun) use ($start, $end) {
                $lines = JurnalLine::where('akun_id', $akun->id)
                    ->whereHas('jurnal', fn ($q) => $q->whereBetween('tanggal', [$start, $end]))
                    ->selectRaw('SUM(debit) as debit, SUM(kredit) as kredit')
                    ->first();
                $saldo = ($lines->debit ?? 0) - ($lines->kredit ?? 0);

                return ['akun' => $akun, 'saldo' => max(0, $saldo)];
            });

        $totalPendapatan = $pendapatan->sum('saldo');
        $totalBeban = $beban->sum('saldo');
        $surplusDefisit = $totalPendapatan - $totalBeban;

        return view('admin.laporan.laba-rugi', compact(
            'pendapatan', 'beban', 'totalPendapatan', 'totalBeban',
            'surplusDefisit', 'bulan', 'tahun'
        ));
    }
}
