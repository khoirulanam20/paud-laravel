<?php

namespace App\Observers;

use App\Models\Anak;
use App\Models\Kelas;
use App\Services\KelasRiwayatService;
use Carbon\Carbon;

class AnakObserver
{
    public function created(Anak $anak): void
    {
        if ($anak->kelas_id === null) {
            return;
        }

        $kelas = Kelas::withoutSekolahScope()->find($anak->kelas_id);
        if (! $kelas) {
            return;
        }

        KelasRiwayatService::buka($anak, $kelas, [
            'mulai' => $anak->created_at ?? now(),
            'alasan' => 'masuk',
        ]);
    }

    public function updated(Anak $anak): void
    {
        if (! $anak->wasChanged('kelas_id')) {
            return;
        }

        $ctx = KelasRiwayatService::context();
        $efektif = isset($ctx['efektif']) ? Carbon::parse($ctx['efektif'])->startOfDay() : now()->startOfDay();
        $alasanTutup = $ctx ? ($ctx['alasan'] === 'naik' ? 'naik' : 'pindah') : 'pindah';

        KelasRiwayatService::tutup($anak, $efektif, $alasanTutup);

        if ($anak->kelas_id === null) {
            return;
        }

        $kelas = Kelas::withoutSekolahScope()->find($anak->kelas_id);
        if (! $kelas) {
            return;
        }

        KelasRiwayatService::buka($anak, $kelas, [
            'mulai' => $efektif,
            'alasan' => $ctx['alasan'] ?? 'pindah',
            'batch_id' => $ctx['batch_id'] ?? null,
        ]);
    }
}
