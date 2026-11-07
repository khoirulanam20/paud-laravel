<?php

namespace App\Services;

use App\Models\Anak;
use App\Models\AnakKelasRiwayat;
use App\Models\Kelas;
use App\Models\KenaikanKelasBatch;
use App\Models\KenaikanKelasBatchItem;
use App\Models\User;
use App\Support\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class KenaikanKelasService
{
    public const MODE_SEMESTER = 'semester';

    public const MODE_USIA = 'usia';

    /**
     * Bangun usulan kenaikan per siswa.
     *
     * @return array{items: Collection<int, array>, warnings: array<int, string>}
     */
    public function usulan(int $sekolahId, string $mode, Carbon $tanggalAcuan): array
    {
        if (! in_array($mode, [self::MODE_SEMESTER, self::MODE_USIA], true)) {
            throw new InvalidArgumentException('Mode kenaikan tidak dikenal.');
        }

        $kelasList = Kelas::withoutSekolahScope()
            ->where('sekolah_id', $sekolahId)
            ->orderBy('tingkat')
            ->orderBy('name')
            ->get();

        $tanpaTingkat = $kelasList->whereNull('tingkat');
        $bertingkat = $kelasList->whereNotNull('tingkat')->values();

        $warnings = $tanpaTingkat->map(
            fn (Kelas $k) => "Kelas \"{$k->name}\" belum punya tingkat — atur dulu agar siswanya bisa diusulkan."
        )->values()->all();

        $items = collect();
        if ($bertingkat->isEmpty()) {
            return ['items' => $items, 'warnings' => $warnings];
        }

        $anaks = Anak::withoutSekolahScope()
            ->where('sekolah_id', $sekolahId)
            ->where('status', 'approved')
            ->whereIn('kelas_id', $bertingkat->pluck('id'))
            ->with('kelas')
            ->orderBy('name')
            ->get();

        foreach ($anaks as $anak) {
            $asal = $anak->kelas;
            if (! $asal || $asal->tingkat === null) {
                continue;
            }

            $items->push($mode === self::MODE_SEMESTER
                ? $this->usulSemester($anak, $asal, $bertingkat)
                : $this->usulUsia($anak, $asal, $bertingkat, $tanggalAcuan));
        }

        return ['items' => $items, 'warnings' => $warnings];
    }

    /** @return array{anak: Anak, kelas_asal: Kelas, kelas_tujuan: ?Kelas, aksi: string, usia_bulan: ?int, catatan: ?string} */
    protected function usulSemester(Anak $anak, Kelas $asal, Collection $bertingkat): array
    {
        $tujuan = $bertingkat
            ->where('tingkat', '>', $asal->tingkat)
            ->sortBy([['tingkat', 'asc'], ['name', 'asc']])
            ->first();

        if (! $tujuan) {
            return [
                'anak' => $anak, 'kelas_asal' => $asal, 'kelas_tujuan' => null,
                'aksi' => 'lulus', 'usia_bulan' => $this->usiaBulan($anak, now()),
                'catatan' => 'Tingkat tertinggi — lulus.',
            ];
        }

        return [
            'anak' => $anak, 'kelas_asal' => $asal, 'kelas_tujuan' => $tujuan,
            'aksi' => $tujuan->id === $asal->id ? 'tinggal' : 'naik',
            'usia_bulan' => $this->usiaBulan($anak, now()), 'catatan' => null,
        ];
    }

    /** @return array{anak: Anak, kelas_asal: Kelas, kelas_tujuan: ?Kelas, aksi: string, usia_bulan: ?int, catatan: ?string} */
    protected function usulUsia(Anak $anak, Kelas $asal, Collection $bertingkat, Carbon $acuan): array
    {
        $usia = $this->usiaBulan($anak, $acuan);

        if ($usia === null) {
            return [
                'anak' => $anak, 'kelas_asal' => $asal, 'kelas_tujuan' => null,
                'aksi' => 'tinggal', 'usia_bulan' => null,
                'catatan' => 'Tanggal lahir kosong — tentukan manual.',
            ];
        }

        $target = $bertingkat
            ->whereNotNull('usia_min_bulan')
            ->where('usia_min_bulan', '<=', $usia)
            ->sortByDesc('tingkat')
            ->first();

        if (! $target || $target->tingkat <= $asal->tingkat) {
            return [
                'anak' => $anak, 'kelas_asal' => $asal, 'kelas_tujuan' => null,
                'aksi' => 'tinggal', 'usia_bulan' => $usia,
                'catatan' => $target ? null : 'Belum memenuhi usia tingkat berikutnya.',
            ];
        }

        // Target = kelas tingkat itu (nama terkecil bila ganda).
        $tujuan = $bertingkat->where('tingkat', $target->tingkat)->sortBy('name')->first() ?? $target;

        return [
            'anak' => $anak, 'kelas_asal' => $asal, 'kelas_tujuan' => $tujuan,
            'aksi' => 'naik', 'usia_bulan' => $usia, 'catatan' => null,
        ];
    }

    protected function usiaBulan(Anak $anak, Carbon $pada): ?int
    {
        if (! $anak->dob) {
            return null;
        }

        $dob = Carbon::parse($anak->dob)->startOfDay();

        return max(0, $dob->diffInMonths($pada->copy()->startOfDay()));
    }

    /**
     * Terapkan usulan (dengan override per siswa dari UI).
     *
     * @param array<int, array{anak_id: int, aksi: string, kelas_tujuan_id?: ?int}> $items
     */
    public function terapkan(
        int $sekolahId,
        string $mode,
        Carbon $tanggalAcuan,
        Carbon $tanggalEfektif,
        array $items,
        User $oleh
    ): KenaikanKelasBatch {
        if (! in_array($mode, [self::MODE_SEMESTER, self::MODE_USIA], true)) {
            throw new InvalidArgumentException('Mode kenaikan tidak dikenal.');
        }

        $ta = TahunAjaran::fromDate($tanggalEfektif);
        $tahunAjaranAsal = $this->tahunAjaranSebelumnya($ta['tahun_ajaran']);

        if ($mode === self::MODE_SEMESTER && $this->adaSiswaSudahDiproses($sekolahId, $tahunAjaranAsal, array_column($items, 'anak_id'))) {
            throw new InvalidArgumentException("Ada siswa yang sudah diproses pada kenaikan semester TA {$tahunAjaranAsal}.");
        }

        return DB::transaction(function () use ($sekolahId, $mode, $tanggalAcuan, $tanggalEfektif, $items, $oleh, $ta, $tahunAjaranAsal) {
            $batch = KenaikanKelasBatch::withoutSekolahScope()->create([
                'sekolah_id' => $sekolahId,
                'mode' => $mode,
                'tanggal_acuan' => $tanggalAcuan->toDateString(),
                'tanggal_efektif' => $tanggalEfektif->toDateString(),
                'tahun_ajaran_asal' => $tahunAjaranAsal,
                'tahun_ajaran_tujuan' => $ta['tahun_ajaran'],
                'status' => 'diterapkan',
                'dibuat_oleh' => $oleh->id,
            ]);

            $naik = 0;
            $tinggal = 0;
            $lulus = 0;

            foreach ($items as $row) {
                $anak = Anak::withoutSekolahScope()
                    ->where('sekolah_id', $sekolahId)
                    ->where('status', 'approved')
                    ->find($row['anak_id']);

                if (! $anak || $anak->kelas_id === null) {
                    continue;
                }

                $aksi = $row['aksi'];
                if (! in_array($aksi, ['naik', 'tinggal', 'lulus', 'pindah'], true)) {
                    throw new InvalidArgumentException('Aksi tidak dikenal: '.$aksi);
                }

                $kelasAsalId = $anak->kelas_id;
                $kelasTujuanId = null;

                if (in_array($aksi, ['naik', 'pindah'], true)) {
                    if (empty($row['kelas_tujuan_id'])) {
                        throw new InvalidArgumentException('Kelas tujuan wajib diisi untuk aksi naik/pindah.');
                    }
                    $tujuan = Kelas::withoutSekolahScope()
                        ->where('sekolah_id', $sekolahId)
                        ->find($row['kelas_tujuan_id']);
                    if (! $tujuan) {
                        throw new InvalidArgumentException('Kelas tujuan tidak valid.');
                    }
                    $kelasTujuanId = $tujuan->id;

                    KelasRiwayatService::withContext([
                        'alasan' => 'naik',
                        'batch_id' => $batch->id,
                        'efektif' => $tanggalEfektif,
                    ], function () use ($anak, $kelasTujuanId) {
                        $anak->update(['kelas_id' => $kelasTujuanId]);
                    });
                    $naik++;
                } elseif ($aksi === 'lulus') {
                    KelasRiwayatService::withContext([
                        'alasan' => 'naik',
                        'batch_id' => $batch->id,
                        'efektif' => $tanggalEfektif,
                    ], function () use ($anak) {
                        $anak->update(['kelas_id' => null, 'status' => 'lulus']);
                    });
                    $lulus++;
                } else {
                    $tinggal++;
                }

                $batch->items()->create([
                    'anak_id' => $anak->id,
                    'kelas_asal_id' => $kelasAsalId,
                    'kelas_tujuan_id' => $kelasTujuanId,
                    'aksi' => $aksi,
                    'usia_bulan' => $this->usiaBulan($anak, $tanggalAcuan),
                    'catatan' => $row['catatan'] ?? null,
                ]);
            }

            $batch->update(['jumlah_naik' => $naik, 'jumlah_tinggal' => $tinggal, 'jumlah_lulus' => $lulus]);

            return $batch->fresh();
        });
    }

    /** Batalkan batch terbaru: pulihkan kelas/status/riwayat. Hanya batch `diterapkan` termuda per sekolah. */
    public function batalkan(KenaikanKelasBatch $batch, User $oleh): void
    {
        $sekolahId = (int) $batch->sekolah_id;
        if ($batch->status !== 'diterapkan') {
            throw new InvalidArgumentException('Batch sudah dibatalkan.');
        }

        $terbaru = KenaikanKelasBatch::withoutSekolahScope()
            ->where('sekolah_id', $sekolahId)
            ->where('status', 'diterapkan')
            ->orderByDesc('id')
            ->first();

        if (! $terbaru || $terbaru->id !== $batch->id) {
            throw new InvalidArgumentException('Hanya batch kenaikan terbaru yang bisa dibatalkan.');
        }

        DB::transaction(function () use ($batch, $oleh) {
            $items = $batch->items()->with('anak')->get();

            foreach ($items as $item) {
                if (in_array($item->aksi, ['naik', 'pindah'], true)) {
                    // Hapus baris riwayat batch, buka kembali baris asal.
                    AnakKelasRiwayat::withoutSekolahScope()
                        ->where('batch_id', $batch->id)
                        ->where('anak_id', $item->anak_id)
                        ->delete();

                    AnakKelasRiwayat::withoutSekolahScope()
                        ->where('anak_id', $item->anak_id)
                        ->where('kelas_id', $item->kelas_asal_id)
                        ->whereNotNull('selesai')
                        ->orderByDesc('selesai')
                        ->limit(1)
                        ->update(['selesai' => null, 'alasan_selesai' => null]);

                    $item->anak?->updateQuietly(['kelas_id' => $item->kelas_asal_id]);
                } elseif ($item->aksi === 'lulus') {
                    AnakKelasRiwayat::withoutSekolahScope()
                        ->where('batch_id', $batch->id)
                        ->where('anak_id', $item->anak_id)
                        ->delete();

                    AnakKelasRiwayat::withoutSekolahScope()
                        ->where('anak_id', $item->anak_id)
                        ->where('kelas_id', $item->kelas_asal_id)
                        ->whereNotNull('selesai')
                        ->orderByDesc('selesai')
                        ->limit(1)
                        ->update(['selesai' => null, 'alasan_selesai' => null]);

                    $item->anak?->updateQuietly(['kelas_id' => $item->kelas_asal_id, 'status' => 'approved']);
                }
                // tinggal: tak ada perubahan — lewati.
            }

            $batch->update([
                'status' => 'dibatalkan',
                'dibatalkan_oleh' => $oleh->id,
                'dibatalkan_at' => now(),
            ]);
        });
    }

    protected function tahunAjaranSebelumnya(string $ta): string
    {
        [$y1, $y2] = array_map('intval', explode('/', $ta));

        return ($y1 - 1).'/'.($y2 - 1);
    }

    /** @param array<int, int|string> $anakIds */
    protected function adaSiswaSudahDiproses(int $sekolahId, string $tahunAjaranAsal, array $anakIds): bool
    {
        return KenaikanKelasBatchItem::query()
            ->whereIn('anak_id', $anakIds)
            ->whereHas('batch', fn ($q) => $q->withoutSekolahScope()
                ->where('sekolah_id', $sekolahId)
                ->where('mode', self::MODE_SEMESTER)
                ->where('status', 'diterapkan')
                ->where('tahun_ajaran_asal', $tahunAjaranAsal))
            ->exists();
    }
}
