<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\SekolahHariLibur;
use App\Models\SekolahJamKerja;
use App\Support\ActivityLogger;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class JamKerjaController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::findOrFail(TenantContext::requireSekolahId());

        $this->pastikanJadwalLengkap($sekolah);

        return view('admin.jam-kerja.index', [
            'sekolah' => $sekolah,
            'jadwals' => $sekolah->jamKerjas()->get()->keyBy('hari'),
            'hariLiburs' => $sekolah->hariLiburs()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $sekolah = Sekolah::findOrFail(TenantContext::requireSekolahId());
        $this->pastikanJadwalLengkap($sekolah);

        $validated = $request->validate([
            'radius_m' => ['nullable', 'integer', 'min:10', 'max:5000'],
            'jadwal' => ['required', 'array'],
            'jadwal.*.is_kerja' => ['nullable', 'boolean'],
            'jadwal.*.jam_masuk' => ['nullable', 'date_format:H:i'],
            'jadwal.*.jam_pulang' => ['nullable', 'date_format:H:i'],
            'jadwal.*.toleransi_masuk_menit' => ['nullable', 'integer', 'min:0', 'max:600'],
            'jadwal.*.toleransi_pulang_menit' => ['nullable', 'integer', 'min:0', 'max:600'],
        ]);

        $sekolah->update(['radius_m' => $validated['radius_m'] ?? null]);

        foreach ($validated['jadwal'] as $hari => $row) {
            $isKerja = (bool) ($row['is_kerja'] ?? false);

            SekolahJamKerja::withoutSekolahScope()
                ->where('sekolah_id', $sekolah->id)
                ->where('hari', (int) $hari)
                ->update([
                    'is_kerja' => $isKerja,
                    'jam_masuk' => $isKerja ? ($row['jam_masuk'] ?? null) : null,
                    'jam_pulang' => $isKerja ? ($row['jam_pulang'] ?? null) : null,
                    'toleransi_masuk_menit' => (int) ($row['toleransi_masuk_menit'] ?? 0),
                    'toleransi_pulang_menit' => (int) ($row['toleransi_pulang_menit'] ?? 0),
                ]);
        }

        ActivityLogger::log('Pengaturan presensi guru diperbarui', $sekolah, [
            'radius_m' => $sekolah->radius_m,
        ]);

        return redirect()
            ->route('admin.presensi-guru.pengaturan')
            ->with('success', 'Pengaturan presensi guru berhasil disimpan.');
    }

    public function storeLibur(Request $request)
    {
        $sekolah = Sekolah::findOrFail(TenantContext::requireSekolahId());

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $libur = SekolahHariLibur::withoutSekolahScope()->firstOrCreate(
            [
                'sekolah_id' => $sekolah->id,
                'tanggal' => $validated['tanggal'],
            ],
            [
                'keterangan' => $validated['keterangan'] ?? null,
            ],
        );

        if (! $libur->wasRecentlyCreated) {
            $libur->update(['keterangan' => $validated['keterangan'] ?? null]);
        }

        ActivityLogger::log('Hari libur presensi ditambahkan', $sekolah, [
            'tanggal' => $validated['tanggal'],
        ]);

        return redirect()
            ->route('admin.presensi-guru.pengaturan')
            ->with('success', 'Hari libur berhasil disimpan.');
    }

    public function destroyLibur(SekolahHariLibur $hariLibur)
    {
        $sekolahId = TenantContext::requireSekolahId();

        abort_unless((int) $hariLibur->sekolah_id === (int) $sekolahId, 403);

        $tanggal = $hariLibur->tanggal?->toDateString();
        $hariLibur->delete();

        ActivityLogger::log('Hari libur presensi dihapus', null, [
            'tanggal' => $tanggal,
        ]);

        return redirect()
            ->route('admin.presensi-guru.pengaturan')
            ->with('success', 'Hari libur berhasil dihapus.');
    }

    private function pastikanJadwalLengkap(Sekolah $sekolah): void
    {
        $ada = SekolahJamKerja::withoutSekolahScope()
            ->where('sekolah_id', $sekolah->id)
            ->pluck('hari')
            ->all();

        foreach (range(1, 7) as $hari) {
            if (in_array($hari, $ada, true)) {
                continue;
            }

            $kerja = $hari <= 5;

            SekolahJamKerja::withoutSekolahScope()->create([
                'sekolah_id' => $sekolah->id,
                'hari' => $hari,
                'is_kerja' => $kerja,
                'jam_masuk' => $kerja ? '07:00' : null,
                'jam_pulang' => $kerja ? '13:00' : null,
                'toleransi_masuk_menit' => $kerja ? 10 : 0,
                'toleransi_pulang_menit' => $kerja ? 10 : 0,
            ]);
        }
    }
}
