<?php

namespace App\Services;

use App\Models\Anak;
use App\Models\AnakKelasRiwayat;
use App\Models\Kelas;
use App\Support\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KelasRiwayatService
{
    /** Konteks batch: observer memakai alasan naik + batch_id alih-alih pindah. */
    protected static ?array $context = null;

    /**
     * @param array{alasan: string, batch_id: int|null, efektif: Carbon|null}|null $ctx
     */
    public static function withContext(?array $ctx, callable $fn): mixed
    {
        $prev = static::$context;
        static::$context = $ctx;

        try {
            return $fn();
        } finally {
            static::$context = $prev;
        }
    }

    public static function context(): ?array
    {
        return static::$context;
    }

    /** Buka baris riwayat baru untuk siswa di kelas. */
    public static function buka(Anak $anak, ?Kelas $kelas, array $opt = []): ?AnakKelasRiwayat
    {
        if (! $kelas) {
            return null;
        }

        $mulai = isset($opt['mulai']) ? Carbon::parse($opt['mulai'])->startOfDay() : now()->startOfDay();
        $ta = TahunAjaran::fromDate($mulai);

        return AnakKelasRiwayat::withoutSekolahScope()->create([
            'sekolah_id' => $anak->sekolah_id,
            'anak_id' => $anak->id,
            'kelas_id' => $kelas->id,
            'kelas_nama' => $kelas->name,
            'tahun_ajaran' => $opt['tahun_ajaran'] ?? $ta['tahun_ajaran'],
            'semester' => $opt['semester'] ?? $ta['semester'],
            'mulai' => $mulai->toDateString(),
            'selesai' => null,
            'alasan' => $opt['alasan'] ?? 'masuk',
            'alasan_selesai' => null,
            'batch_id' => $opt['batch_id'] ?? null,
            'dibuat_oleh' => $opt['dibuat_oleh'] ?? Auth::id(),
            'catatan' => $opt['catatan'] ?? null,
        ]);
    }

    /** Tutup baris terbuka milik siswa. */
    public static function tutup(Anak $anak, Carbon $selesai, ?string $alasanSelesai = null): void
    {
        AnakKelasRiwayat::withoutSekolahScope()
            ->where('anak_id', $anak->id)
            ->whereNull('selesai')
            ->update([
                'selesai' => $selesai->copy()->startOfDay()->toDateString(),
                'alasan_selesai' => $alasanSelesai,
                'updated_at' => now(),
            ]);
    }

    /** Kelas siswa pada tanggal tertentu (null bila tak ada baris cocok). */
    public static function kelasPada(int $anakId, Carbon $tanggal): ?int
    {
        $tanggal = $tanggal->copy()->startOfDay();

        $row = AnakKelasRiwayat::withoutSekolahScope()
            ->where('anak_id', $anakId)
            ->where('mulai', '<=', $tanggal->toDateString())
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('selesai')->orWhere('selesai', '>', $tanggal->toDateString());
            })
            ->orderByDesc('mulai')
            ->first();

        return $row?->kelas_id !== null ? (int) $row->kelas_id : null;
    }

    /**
     * Tanggal mulai di kelas saat ini per siswa yang naik/pindah kelas.
     *
     * @param  array<int, int>  $anakIds
     * @return array<int, string> anak_id => Y-m-d; siswa tanpa riwayat tidak ada di hasil (= tanpa batas).
     */
    public static function mulaiKelasSaatIni(array $anakIds): array
    {
        return AnakKelasRiwayat::withoutSekolahScope()
            ->whereIn('anak_id', $anakIds)
            ->whereNull('selesai')
            ->where('alasan', '!=', 'masuk') // siswa baru/backfill: tanggal 'masuk' bukan batas kegiatan
            ->orderBy('mulai')
            ->pluck('mulai', 'anak_id')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();
    }
}
