<?php

namespace App\Models\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filter historis berbasis anak_kelas_riwayats.
 *
 * Dipakai pada model yang berelasi ke Anak lewat `anak_id`
 * (Pencapaian.created_at, Kesehatan.tanggal_pemeriksaan):
 * kelas yang dipakai = kelas siswa PADA TANGGAL data, bukan kelas saat ini.
 *
 * Untuk data bulanan (PembayaranBulanan.periode_bulan/tahun),
 * pakai diKelasSelama() dengan rentang bulan itu.
 */
trait FiltersByKelasRiwayat
{
    public function scopeDiKelasPada(Builder $query, int|array $kelasIds, string $kolomTanggal): Builder
    {
        $ids = is_array($kelasIds) ? $kelasIds : [$kelasIds];
        $table = $query->getModel()->getTable();

        return $query->whereExists(function ($q) use ($ids, $kolomTanggal, $table) {
            $q->selectRaw('1')
                ->from('anak_kelas_riwayats as r')
                ->whereColumn('r.anak_id', $table.'.anak_id')
                ->whereIn('r.kelas_id', $ids)
                ->whereRaw("r.mulai <= DATE({$kolomTanggal})")
                ->where(function ($w) use ($kolomTanggal) {
                    $w->whereNull('r.selesai')
                        ->orWhereRaw("DATE({$kolomTanggal}) < r.selesai");
                });
        });
    }

    public function scopeDiKelasSelama(Builder $query, int|array $kelasIds, Carbon $dari, Carbon $sampai): Builder
    {
        $ids = is_array($kelasIds) ? $kelasIds : [$kelasIds];
        $table = $query->getModel()->getTable();
        $dariStr = $dari->copy()->startOfDay()->toDateString();
        $sampaiStr = $sampai->copy()->startOfDay()->toDateString();

        return $query->whereExists(function ($q) use ($ids, $dariStr, $sampaiStr, $table) {
            $q->selectRaw('1')
                ->from('anak_kelas_riwayats as r')
                ->whereColumn('r.anak_id', $table.'.anak_id')
                ->whereIn('r.kelas_id', $ids)
                ->where('r.mulai', '<=', $sampaiStr)
                ->where(function ($w) use ($dariStr) {
                    $w->whereNull('r.selesai')->orWhere('r.selesai', '>', $dariStr);
                });
        });
    }
}
