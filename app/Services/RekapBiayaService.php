<?php

namespace App\Services;

use App\Models\BiayaBulananSiswa;
use App\Models\Diskon;
use App\Models\PembayaranBulanan;
use App\Models\Presensi;
use App\Models\AkuntansiSetting;
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

    public function getBiayaHarian(int $anakId, int $biayaBulananSekolahId): ?float
    {
        $siswaBiaya = BiayaBulananSiswa::where('anak_id', $anakId)
            ->where('biaya_bulanan_sekolah_id', $biayaBulananSekolahId)
            ->first();

        if ($siswaBiaya) {
            return (float) $siswaBiaya->biaya_harian;
        }

        return null;
    }

    /** Siswa yang sudah ditambahkan di menu Biaya Harian (jenis biaya aktif). */
    public function getSiswaDenganBiayaHarian(int $sekolahId): Collection
    {
        return BiayaBulananSiswa::where('sekolah_id', $sekolahId)
            ->whereHas('biayaBulananSekolah', fn ($q) => $q->where('is_aktif', true))
            ->whereHas('anak', fn ($q) => $q->where('status', 'approved'))
            ->with(['anak.kelas', 'biayaBulananSekolah'])
            ->get();
    }

    public function hitungBiaya(PembayaranBulanan $pembayaran): PembayaranBulanan
    {
        $biayaPerHari = $this->getBiayaHarian(
            $pembayaran->anak_id,
            $pembayaran->biaya_bulanan_sekolah_id
        );

        if ($biayaPerHari === null) {
            $biayaPerHari = (float) $pembayaran->biaya_per_hari;
        }

        $subtotal = $biayaPerHari * $pembayaran->hari_hadir;

        $nilaiDiskon = 0;
        if ($pembayaran->diskon_id) {
            $diskon = Diskon::find($pembayaran->diskon_id);
            if ($diskon && $diskon->is_aktif) {
                $nilaiDiskon = $diskon->hitungDiskon($subtotal);
            }
        }

        $total = max(0, $subtotal - $nilaiDiskon);

        $pembayaran->update([
            'biaya_per_hari' => round($biayaPerHari, 2),
            'subtotal' => round($subtotal, 2),
            'nilai_diskon' => round($nilaiDiskon, 2),
            'total_bayar' => round($total, 2),
        ]);

        return $pembayaran->fresh();
    }

    /**
     * @param  array<string, int|null>  $diskonPerTagihan  "{anak_id}_{biaya_id}" => diskon_id
     * @param  list<string>  $selectedKeys  "{anak_id}_{biaya_id}" — kosong = generate semua + cleanup
     */
    public function generateTagihan(
        int $sekolahId,
        int $bulan,
        int $tahun,
        array $diskonPerTagihan = [],
        array $selectedKeys = []
    ): Collection {
        $assignments = $this->getSiswaDenganBiayaHarian($sekolahId);
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

            $key = $anak->id . '_' . $biaya->id;
            $validKeys[] = $key;

            if (! $generateAll && ! in_array($key, $selectedKeys, true)) {
                continue;
            }

            $biayaPerHari = (float) $assignment->biaya_harian;

            $hariHadir = $this->hitungHariHadir($anak->id, $bulan, $tahun);
            $subtotal = $biayaPerHari * $hariHadir;

            $diskonId = $diskonPerTagihan[$key] ?? null;
            $nilaiDiskon = 0;
            if ($diskonId) {
                $diskon = Diskon::find($diskonId);
                if ($diskon && $diskon->is_aktif) {
                    $nilaiDiskon = $diskon->hitungDiskon($subtotal);
                }
            }

            $total = max(0, $subtotal - $nilaiDiskon);

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
                    'biaya_per_hari' => round($biayaPerHari, 2),
                    'subtotal' => round($subtotal, 2),
                    'diskon_id' => $diskonId,
                    'nilai_diskon' => round($nilaiDiskon, 2),
                    'total_bayar' => round($total, 2),
                    'status' => 'pending',
                ]
            );

            // Accrual: buat jurnal saat generate (Piutang / Pendapatan)
            $setting = AkuntansiSetting::forSekolah($sekolahId);
            if ($setting->isAccrual() && $total > 0 && $pembayaran->wasRecentlyCreated) {
                $akuntansiService = app(\App\Services\AkuntansiService::class);
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
                    $key = $pembayaran->anak_id . '_' . $pembayaran->biaya_bulanan_sekolah_id;
                    if (! in_array($key, $validKeys, true)) {
                        $pembayaran->delete();
                    }
                });
        }

        return $pembayarans;
    }

    public function updateHariHadir(PembayaranBulanan $pembayaran, int $hariHadir, ?int $editedBy = null): PembayaranBulanan
    {
        $oldValue = $pembayaran->hari_hadir;
        $pembayaran->update(['hari_hadir' => $hariHadir]);

        $pembayaran->details()->create([
            'field_name' => 'hari_hadir',
            'old_value' => (string) $oldValue,
            'new_value' => (string) $hariHadir,
            'edited_by' => $editedBy,
            'edited_at' => now(),
        ]);

        return $this->hitungBiaya($pembayaran);
    }

    public function updateHariEfektif(PembayaranBulanan $pembayaran, int $hariEfektif, ?int $editedBy = null): PembayaranBulanan
    {
        $oldValue = $pembayaran->hari_efektif;
        $pembayaran->update(['hari_efektif' => $hariEfektif]);

        $pembayaran->details()->create([
            'field_name' => 'hari_efektif',
            'old_value' => (string) $oldValue,
            'new_value' => (string) $hariEfektif,
            'edited_by' => $editedBy,
            'edited_at' => now(),
        ]);

        return $pembayaran->fresh();
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
