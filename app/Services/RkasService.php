<?php

namespace App\Services;

use App\Models\Rkas;
use App\Models\RkasLine;
use App\Models\RkasLineAnggaran;
use App\Models\RkasRealisasi;
use App\Support\TahunAjaran;
use Illuminate\Support\Facades\DB;

class RkasService
{
    public function create(int $sekolahId, string $tahunAjaran, int $semester): Rkas
    {
        [$mulai, $akhir] = TahunAjaran::periode($tahunAjaran, $semester);

        return Rkas::create([
            'sekolah_id' => $sekolahId,
            'tahun_ajaran' => $tahunAjaran,
            'semester' => $semester,
            'tanggal_mulai' => $mulai,
            'tanggal_akhir' => $akhir,
            'status' => 'draft',
        ]);
    }

    /** @param array<int, array{volume?: mixed, satuan?: mixed, harga_satuan?: mixed, keterangan?: mixed, anggaran?: array<int, float>}> $lines */
    public function saveLines(Rkas $rkas, array $lines): void
    {
        if ($rkas->isFinal()) {
            abort(422, 'RKAS sudah final, tidak bisa diubah.');
        }

        DB::transaction(function () use ($rkas, $lines) {
            $keepIds = [];

            foreach ($lines as $akunId => $data) {
                if (empty($data['enabled'])) {
                    continue;
                }

                $line = RkasLine::updateOrCreate(
                    ['rkas_id' => $rkas->id, 'akun_id' => $akunId],
                    [
                        'volume' => $data['volume'] ?? null,
                        'satuan' => $data['satuan'] ?? null,
                        'harga_satuan' => $data['harga_satuan'] ?? null,
                        'keterangan' => $data['keterangan'] ?? null,
                    ],
                );

                $keepIds[] = $line->id;

                foreach ($data['anggaran'] ?? [] as $sumberDanaId => $nominal) {
                    RkasLineAnggaran::updateOrCreate(
                        ['rkas_line_id' => $line->id, 'sumber_dana_id' => $sumberDanaId],
                        ['nominal' => $nominal ?: 0],
                    );

                    RkasRealisasi::firstOrCreate(
                        ['rkas_line_id' => $line->id, 'sumber_dana_id' => $sumberDanaId],
                        ['nominal_otomatis' => 0],
                    );
                }
            }

            RkasLine::where('rkas_id', $rkas->id)->whereNotIn('id', $keepIds)->delete();
        });
    }

    public function finalize(Rkas $rkas): void
    {
        $rkas->update(['status' => 'final']);
    }

    public function reopen(Rkas $rkas): void
    {
        $rkas->update(['status' => 'draft']);
    }
}
