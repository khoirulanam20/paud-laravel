<?php

namespace App\Services;

use App\Models\AkuntansiSetting;
use App\Models\BiayaBulananSiswa;
use App\Models\Diskon;
use App\Models\PembayaranBulanan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RekapBiayaService
{
    public function hitungHariEfektif(int $bulan, int $tahun): int
    {
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();
        $hariEfektif = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $hariEfektif++;
            }
            $start->addDay();
        }

        return $hariEfektif;
    }

    public function hitungHariHadir(int $anakId, int $bulan, int $tahun): int
    {
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        return Presensi::where('anak_id', $anakId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->where('hadir', true)
            ->count();
    }

    public function getBiayaBulanan(int $anakId, int $biayaBulananSekolahId): ?float
    {
        $siswaBiaya = BiayaBulananSiswa::where('anak_id', $anakId)
            ->where('biaya_bulanan_sekolah_id', $biayaBulananSekolahId)
            ->first();

        if ($siswaBiaya) {
            return (float) $siswaBiaya->biaya_bulanan;
        }

        return null;
    }

    /** Siswa yang sudah ditambahkan di menu Biaya Bulanan (jenis biaya aktif). */
    public function getSiswaDenganBiayaBulanan(int $sekolahId): Collection
    {
        return BiayaBulananSiswa::where('sekolah_id', $sekolahId)
            ->whereHas('biayaBulananSekolah', fn ($q) => $q->where('is_aktif', true))
            ->whereHas('anak', fn ($q) => $q->where('status', 'approved'))
            ->with(['anak.kelas', 'biayaBulananSekolah'])
            ->get();
    }

    public function hitungBiaya(PembayaranBulanan $pembayaran): PembayaranBulanan
    {
        $biaya = $this->getBiayaBulanan(
            $pembayaran->anak_id,
            $pembayaran->biaya_bulanan_sekolah_id
        );

        if ($biaya === null) {
            $biaya = (float) $pembayaran->biaya_per_hari;
        }

        $subtotal = $biaya;
        $totalBiayaTambahan = (float) $pembayaran->items()->sum('jumlah');

        $nilaiDiskon = 0;
        if ($pembayaran->diskon_id) {
            $diskon = Diskon::find($pembayaran->diskon_id);
            if ($diskon && $diskon->is_aktif) {
                $nilaiDiskon = $diskon->hitungDiskon($subtotal);
            }
        }

        $total = max(0, $subtotal + $totalBiayaTambahan - $nilaiDiskon);

        $pembayaran->update([
            'biaya_per_hari' => round($biaya, 2),
            'subtotal' => round($subtotal, 2),
            'nilai_diskon' => round($nilaiDiskon, 2),
            'total_bayar' => round($total, 2),
        ]);

        return $pembayaran->fresh();
    }

    /**
     * @param  array<string, int|null>  $diskonPerTagihan  "{anak_id}_{biaya_id}" => diskon_id
     * @param  list<string>  $selectedKeys  "{anak_id}_{biaya_id}" — kosong = generate semua + cleanup
     * @param  array<string, list<array{nama_item: string, jumlah: float}>>  $biayaTambahan  "{anak_id}_{biaya_id}" => [{nama_item, jumlah}]
     */
    public function generateTagihan(
        int $sekolahId,
        int $bulan,
        int $tahun,
        array $diskonPerTagihan = [],
        array $selectedKeys = [],
        array $biayaTambahan = []
    ): Collection {
        $assignments = $this->getSiswaDenganBiayaBulanan($sekolahId);
        $hariEfektif = $this->hitungHariEfektif($bulan, $tahun);
        $pembayarans = collect();
        $validKeys = [];
        $generateAll = $selectedKeys === [];

        foreach ($assignments as $assignment) {
            $anak = $assignment->anak;
            $biaya = $assignment->biayaBulananSekolah;
            if (! $anak || ! $biaya) {
                continue;
            }

            $key = $anak->id.'_'.$biaya->id;
            $validKeys[] = $key;

            if (! $generateAll && ! in_array($key, $selectedKeys, true)) {
                continue;
            }

            $biayaBulanan = (float) $assignment->biaya_bulanan;
            $subtotal = $biayaBulanan;
            $hariHadir = $this->hitungHariHadir($anak->id, $bulan, $tahun);

            $diskonId = $diskonPerTagihan[$key] ?? null;
            $nilaiDiskon = 0;
            if ($diskonId) {
                $diskon = Diskon::find($diskonId);
                if ($diskon && $diskon->is_aktif) {
                    $nilaiDiskon = $diskon->hitungDiskon($subtotal);
                }
            }

            $totalTambahan = 0;
            $items = $biayaTambahan[$key] ?? [];
            foreach ($items as $item) {
                $totalTambahan += (float) ($item['jumlah'] ?? 0);
            }

            $total = max(0, $subtotal + $totalTambahan - $nilaiDiskon);

            $pembayaran = PembayaranBulanan::updateOrCreate(
                [
                    'anak_id' => $anak->id,
                    'biaya_bulanan_sekolah_id' => $biaya->id,
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                ],
                [
                    'sekolah_id' => $sekolahId,
                    'hari_efektif' => $hariEfektif,
                    'hari_hadir' => $hariHadir,
                    'biaya_per_hari' => round($biayaBulanan, 2),
                    'subtotal' => round($subtotal, 2),
                    'diskon_id' => $diskonId,
                    'nilai_diskon' => round($nilaiDiskon, 2),
                    'total_bayar' => round($total, 2),
                    'status' => 'pending',
                ]
            );

            // Simpan biaya tambahan
            $pembayaran->items()->delete();
            foreach ($items as $item) {
                $namaItem = trim($item['nama_item'] ?? '');
                $jumlahItem = (float) ($item['jumlah'] ?? 0);
                if ($namaItem !== '' && $jumlahItem > 0) {
                    $pembayaran->items()->create([
                        'nama_item' => $namaItem,
                        'jumlah' => $jumlahItem,
                    ]);
                }
            }

            // Accrual: buat jurnal saat generate (Piutang / Pendapatan)
            $setting = AkuntansiSetting::forSekolah($sekolahId);
            if ($setting->isAccrual() && $total > 0 && $pembayaran->wasRecentlyCreated) {
                $akuntansiService = app(AkuntansiService::class);
                $akuntansiService->buatJurnalSaatGenerate($pembayaran, auth()->id() ?? 1);
            }

            $pembayarans->push($pembayaran);
        }

        if ($generateAll) {
            PembayaranBulanan::where('sekolah_id', $sekolahId)
                ->where('periode_bulan', $bulan)
                ->where('periode_tahun', $tahun)
                ->where('status', 'pending')
                ->get()
                ->each(function (PembayaranBulanan $pembayaran) use ($validKeys) {
                    $key = $pembayaran->anak_id.'_'.$pembayaran->biaya_bulanan_sekolah_id;
                    if (! in_array($key, $validKeys, true)) {
                        $pembayaran->delete();
                    }
                });
        }

        return $pembayarans;
    }

    public function approvePembayaran(PembayaranBulanan $pembayaran, int $approvedBy, ?string $catatan = null): PembayaranBulanan
    {
        $pembayaran->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'catatan_admin' => $catatan,
        ]);

        return $pembayaran->fresh();
    }

    public function rejectPembayaran(PembayaranBulanan $pembayaran, int $approvedBy, ?string $catatan = null): PembayaranBulanan
    {
        $pembayaran->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'catatan_admin' => $catatan,
        ]);

        return $pembayaran->fresh();
    }
}
