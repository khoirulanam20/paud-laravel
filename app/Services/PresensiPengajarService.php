<?php

namespace App\Services;

use App\Models\Pengajar;
use App\Models\PresensiPengajar;
use App\Models\Sekolah;
use App\Models\SekolahHariLibur;
use App\Models\SekolahJamKerja;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Logika presensi guru: geofence (Haversine), status telat masuk/pulang,
 * dan rekap kehadiran. Method rekap sengaja netral supaya bisa dipakai
 * monev guru tanpa mengubah modul ini.
 */
class PresensiPengajarService
{
    /** Akurasi GPS maksimum yang diterima (meter). */
    public const AKURASI_MAKS_METER = 200;

    /**
     * Ambil koordinat sekolah dari kolom teks "lat, lng".
     *
     * @return array{lat: float, lng: float}|null
     */
    public function koordinatSekolah(Sekolah $sekolah): ?array
    {
        $raw = (string) ($sekolah->location_coordinate ?? '');

        if (preg_match_all('/-?\d+(?:\.\d+)?/', $raw, $matches) < 2) {
            return null;
        }

        $numbers = array_map(
            fn (string $value): float => (float) $value,
            $matches[0],
        );

        [$lat, $lng] = $numbers;

        // Validasi rentang koordinat wajar.
        if (abs($lat) > 90 || abs($lng) > 180) {
            return null;
        }

        return ['lat' => $lat, 'lng' => $lng];
    }

    public function jarakMeter(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function jadwalHari(Sekolah $sekolah, CarbonInterface $tanggal): ?SekolahJamKerja
    {
        return SekolahJamKerja::withoutSekolahScope()
            ->where('sekolah_id', $sekolah->id)
            ->where('hari', $tanggal->dayOfWeekIso)
            ->first();
    }

    public function isLibur(Sekolah $sekolah, CarbonInterface $tanggal): bool
    {
        $jadwal = $this->jadwalHari($sekolah, $tanggal);

        if ($jadwal && ! $jadwal->is_kerja) {
            return true;
        }

        return $sekolah->hariLiburs()
            ->whereDate('tanggal', $tanggal->toDateString())
            ->exists();
    }

    /** Jam pulang efektif hari itu, atau null kalau hari libur / belum diatur. */
    public function jamPulangHari(Sekolah $sekolah, CarbonInterface $tanggal): ?CarbonInterface
    {
        $jadwal = $this->jadwalHari($sekolah, $tanggal);

        if (! $jadwal || ! $jadwal->is_kerja || ! $jadwal->jam_pulang) {
            return null;
        }

        return Carbon::parse($tanggal->toDateString().' '.$jadwal->jam_pulang);
    }

    /** Check-out hanya dibuka mulai jam pulang. */
    public function bolehCheckOut(Sekolah $sekolah, CarbonInterface $tanggal): bool
    {
        $batas = $this->jamPulangHari($sekolah, $tanggal);

        return $batas === null || $tanggal->gte($batas);
    }

    public function presensiHariIni(Pengajar $pengajar, CarbonInterface $tanggal): ?PresensiPengajar
    {
        return PresensiPengajar::withoutSekolahScope()
            ->where('sekolah_id', $pengajar->sekolah_id)
            ->where('pengajar_id', $pengajar->id)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->first();
    }

    /**
     * Ringkasan kondisi presensi guru untuk hari tertentu (dipakai view).
     *
     * @return array{
     *     tanggal: CarbonInterface,
     *     jadwal: SekolahJamKerja|null,
     *     presensi: PresensiPengajar|null,
     *     libur: bool,
     *     koordinat_set: bool,
     *     radius_meter: int,
     *     bisa_check_in: bool,
     *     bisa_check_out: bool
     * }
     */
    public function statusHariIni(Pengajar $pengajar, Sekolah $sekolah, CarbonInterface $tanggal): array
    {
        $presensi = $this->presensiHariIni($pengajar, $tanggal);
        $koordinatSet = $this->koordinatSekolah($sekolah) !== null;
        $libur = $this->isLibur($sekolah, $tanggal);

        return [
            'tanggal' => $tanggal,
            'jadwal' => $this->jadwalHari($sekolah, $tanggal),
            'presensi' => $presensi,
            'libur' => $libur,
            'koordinat_set' => $koordinatSet,
            'radius_meter' => $sekolah->radiusMeter(),
            'bisa_check_in' => ! $libur && $koordinatSet && $presensi?->check_in_at === null,
            'bisa_check_out' => ! $libur && $koordinatSet
                && $presensi?->check_in_at !== null
                && $presensi?->check_out_at === null
                && $this->bolehCheckOut($sekolah, $tanggal),
        ];
    }

    /**
     * @param  array{latitude: float, longitude: float, accuracy?: float|null}  $payload
     */
    public function checkIn(Pengajar $pengajar, Sekolah $sekolah, array $payload): PresensiPengajar
    {
        $now = Carbon::now();
        $this->pastikanLokasiSekolah($sekolah);
        $this->pastikanTidakLibur($sekolah, $now);
        $this->pastikanAkurasiWajar($payload['accuracy'] ?? null);

        $presensi = $this->presensiHariIni($pengajar, $now);

        if ($presensi?->check_in_at !== null) {
            throw ValidationException::withMessages([
                'presensi' => 'Anda sudah melakukan check-in hari ini.',
            ]);
        }

        $koordinat = $this->koordinatSekolah($sekolah);
        $jarak = $this->jarakMeter(
            $payload['latitude'],
            $payload['longitude'],
            $koordinat['lat'],
            $koordinat['lng'],
        );

        if ($jarak > $sekolah->radiusMeter()) {
            throw ValidationException::withMessages([
                'presensi' => sprintf(
                    'Anda berada di luar area sekolah (%.0f m dari titik sekolah, maksimal %d m).',
                    $jarak,
                    $sekolah->radiusMeter(),
                ),
            ]);
        }

        [$terlambat, $menitTerlambat] = $this->hitungTerlambatMasuk($sekolah, $now);

        return PresensiPengajar::withoutSekolahScope()->updateOrCreate(
            [
                'sekolah_id' => $sekolah->id,
                'pengajar_id' => $pengajar->id,
                'tanggal' => $now->toDateString(),
            ],
            [
                'hadir' => true,
                'status' => null,
                'check_in_at' => $now,
                'check_in_latitude' => $payload['latitude'],
                'check_in_longitude' => $payload['longitude'],
                'check_in_accuracy' => $payload['accuracy'] ?? null,
                'jarak_meter' => round($jarak, 2),
                'di_luar_area' => false,
                'sumber' => PresensiPengajar::SUMBER_SELF,
                'terlambat_masuk' => $terlambat,
                'menit_telat_masuk' => $menitTerlambat,
            ],
        );
    }

    /**
     * @param  array{latitude: float, longitude: float, accuracy?: float|null}  $payload
     */
    public function checkOut(Pengajar $pengajar, Sekolah $sekolah, array $payload): PresensiPengajar
    {
        $now = Carbon::now();
        $this->pastikanLokasiSekolah($sekolah);
        $this->pastikanTidakLibur($sekolah, $now);
        $this->pastikanAkurasiWajar($payload['accuracy'] ?? null);

        $presensi = $this->presensiHariIni($pengajar, $now);

        if ($presensi?->check_in_at === null) {
            throw ValidationException::withMessages([
                'presensi' => 'Anda belum melakukan check-in hari ini.',
            ]);
        }

        if ($presensi->check_out_at !== null) {
            throw ValidationException::withMessages([
                'presensi' => 'Anda sudah melakukan check-out hari ini.',
            ]);
        }

        $batasPulang = $this->jamPulangHari($sekolah, $now);

        if ($batasPulang !== null && $now->lt($batasPulang)) {
            throw ValidationException::withMessages([
                'presensi' => 'Check-out baru dibuka pukul '.$batasPulang->format('H:i').'.',
            ]);
        }

        $koordinat = $this->koordinatSekolah($sekolah);
        $jarak = $this->jarakMeter(
            $payload['latitude'],
            $payload['longitude'],
            $koordinat['lat'],
            $koordinat['lng'],
        );

        if ($jarak > $sekolah->radiusMeter()) {
            throw ValidationException::withMessages([
                'presensi' => sprintf(
                    'Anda berada di luar area sekolah (%.0f m dari titik sekolah, maksimal %d m).',
                    $jarak,
                    $sekolah->radiusMeter(),
                ),
            ]);
        }

        [$telatPulang, $menitTelatPulang] = $this->hitungTelatPulang($sekolah, $presensi->check_in_at, $now);

        $presensi->forceFill([
            'check_out_at' => $now,
            'check_out_latitude' => $payload['latitude'],
            'check_out_longitude' => $payload['longitude'],
            'check_out_accuracy' => $payload['accuracy'] ?? null,
            'telat_pulang' => $telatPulang,
            'menit_telat_pulang' => $menitTelatPulang,
        ])->save();

        return $presensi->refresh();
    }

    /**
     * Rekap kehadiran guru pada rentang tanggal. Netral, siap dipakai monev.
     *
     * @return array{
     *     hari_kerja: int, hadir: int, terlambat: int, telat_pulang: int,
     *     tanpa_check_out: int, persentase: float
     * }
     */
    public function rekapKehadiran(Pengajar $pengajar, CarbonInterface $mulai, CarbonInterface $selesai): array
    {
        $mulai = Carbon::parse($mulai)->startOfDay();
        $selesai = Carbon::parse($selesai)->endOfDay();

        $rows = PresensiPengajar::withoutSekolahScope()
            ->where('pengajar_id', $pengajar->id)
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->get();

        $hadir = $rows->whereStrict('hadir', true)->count();
        $terlambat = $rows->where('terlambat_masuk', true)->count();
        $telatPulang = $rows->where('telat_pulang', true)->count();
        $tanpaCheckOut = $rows->filter(fn (PresensiPengajar $row) => $row->check_in_at !== null && $row->check_out_at === null)->count();

        $hariKerja = $this->hitungHariKerja($pengajar->sekolah_id, $mulai, $selesai);

        return [
            'hari_kerja' => $hariKerja,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'telat_pulang' => $telatPulang,
            'tanpa_check_out' => $tanpaCheckOut,
            'persentase' => $hariKerja > 0 ? round($hadir / $hariKerja * 100, 2) : 0.0,
        ];
    }

    public function hitungHariKerja(int $sekolahId, CarbonInterface $mulai, CarbonInterface $selesai): int
    {
        $mulai = Carbon::parse($mulai)->startOfDay();
        $selesai = Carbon::parse($selesai)->endOfDay();

        $jadwalKerja = SekolahJamKerja::withoutSekolahScope()
            ->where('sekolah_id', $sekolahId)
            ->where('is_kerja', true)
            ->pluck('hari')
            ->all();

        $hariLibur = $this->hariLiburSet($sekolahId, $mulai, $selesai);

        $total = 0;

        for ($tanggal = $mulai->copy(); $tanggal->lte($selesai); $tanggal->addDay()) {
            if (! in_array($tanggal->dayOfWeekIso, $jadwalKerja, true)) {
                continue;
            }

            if (isset($hariLibur[$tanggal->toDateString()])) {
                continue;
            }

            $total++;
        }

        return $total;
    }

    /**
     * @return array<string, true>
     */
    private function hariLiburSet(int $sekolahId, CarbonInterface $mulai, CarbonInterface $selesai): array
    {
        return SekolahHariLibur::withoutSekolahScope()
            ->where('sekolah_id', $sekolahId)
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->pluck('tanggal')
            ->mapWithKeys(function ($tanggal): array {
                $value = $tanggal instanceof CarbonInterface ? $tanggal->toDateString() : (string) $tanggal;

                return [$value => true];
            })
            ->all();
    }

    private function pastikanLokasiSekolah(Sekolah $sekolah): void
    {
        if ($this->koordinatSekolah($sekolah) === null) {
            throw ValidationException::withMessages([
                'presensi' => 'Lokasi sekolah belum diatur. Hubungi admin sekolah.',
            ]);
        }
    }

    private function pastikanTidakLibur(Sekolah $sekolah, CarbonInterface $now): void
    {
        if ($this->isLibur($sekolah, $now)) {
            throw ValidationException::withMessages([
                'presensi' => 'Hari ini libur, presensi tidak dibuka.',
            ]);
        }
    }

    private function pastikanAkurasiWajar(?float $accuracy): void
    {
        if ($accuracy !== null && $accuracy > self::AKURASI_MAKS_METER) {
            throw ValidationException::withMessages([
                'presensi' => 'Akurasi GPS terlalu rendah. Coba lagi di area terbuka.',
            ]);
        }
    }

    /**
     * @return array{0: bool, 1: int|null}
     */
    public function hitungTerlambatMasuk(Sekolah $sekolah, CarbonInterface $now): array
    {
        $jadwal = $this->jadwalHari($sekolah, $now);

        if (! $jadwal || ! $jadwal->is_kerja || ! $jadwal->jam_masuk) {
            return [false, null];
        }

        $batas = Carbon::parse($now->toDateString().' '.$jadwal->jam_masuk)
            ->addMinutes($jadwal->toleransi_masuk_menit);

        if ($now->lte($batas)) {
            return [false, 0];
        }

        return [true, (int) $batas->diffInMinutes($now)];
    }

    /**
     * @return array{0: bool, 1: int|null}
     */
    public function hitungTelatPulang(Sekolah $sekolah, CarbonInterface $checkIn, CarbonInterface $checkOut): array
    {
        $jadwal = $this->jadwalHari($sekolah, $checkOut);

        if (! $jadwal || ! $jadwal->is_kerja || ! $jadwal->jam_pulang) {
            return [false, null];
        }

        $batas = Carbon::parse($checkOut->toDateString().' '.$jadwal->jam_pulang)
            ->addMinutes($jadwal->toleransi_pulang_menit);

        if ($checkOut->lte($batas)) {
            return [false, 0];
        }

        return [true, (int) $batas->diffInMinutes($checkOut)];
    }
}
