<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Anak;
use App\Models\Presensi;

trait StoresStudentPresensi
{
  /**
   * @param  list<int>  $anakIds
   * @param  array<int|string, array{status?: string, keterangan?: string|null}>  $presensiInput
   */
    protected function persistStudentPresensi(int $sekolahId, string $tanggal, array $anakIds, array $presensiInput): void
    {
        foreach ($anakIds as $anakId) {
            $anakId = (int) $anakId;
            $row = $presensiInput[$anakId] ?? $presensiInput[(string) $anakId] ?? [];
            $status = $row['status'] ?? 'alpha';
            if (! in_array($status, Presensi::statusOptions(), true)) {
                $status = 'alpha';
            }

            $anak = Anak::find($anakId);
            if (! $anak) {
                continue;
            }

            Presensi::updateOrCreate(
                [
                    'sekolah_id' => $sekolahId,
                    'anak_id' => $anakId,
                    'tanggal' => $tanggal,
                ],
                [
                    'kelas_id' => $anak->kelas_id,
                    'hadir' => Presensi::hadirFromStatus($status),
                    'status' => $status,
                    'keterangan' => isset($row['keterangan']) && $row['keterangan'] !== ''
                        ? $row['keterangan']
                        : null,
                ]
            );
        }
    }
}
