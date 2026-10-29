<?php

namespace App\Support;

use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Kelas;
use App\Models\KritikSaran;
use App\Models\MenuMakanan;
use App\Models\PembayaranBulanan;
use App\Models\Presensi;
use Carbon\Carbon;

class AdminSekolahDashboard
{
    /**
     * @return array{
     *     lead: string,
     *     summary: list<array{label: string, value: string, context: string, href: string|null, icon: string}>,
     *     attention: list<array{severity: string, title: string, detail: string, href: string, action: string}>,
     *     activities: array{items: list<array{time: string, title: string, meta: string, status: string}>, total: int, href: string},
     *     attentionOverflowHref: string|null
     * }
     */
    public static function compose(int $sekolahId): array
    {
        $today = Carbon::today()->locale('id');
        $now = Carbon::now()->locale('id');

        $siswaAktif = Anak::where('sekolah_id', $sekolahId)->where('status', 'approved')->count();
        $siswaPending = Anak::where('sekolah_id', $sekolahId)->where('status', 'pending')->count();
        $totalKelas = Kelas::where('sekolah_id', $sekolahId)->count();
        $totalPengajar = \App\Models\Pengajar::where('sekolah_id', $sekolahId)->count();

        $kegiatanHariIni = Kegiatan::where('sekolah_id', $sekolahId)->whereDate('date', $today)->count();
        $kegiatanBulanIni = Kegiatan::where('sekolah_id', $sekolahId)
            ->whereYear('date', $now->year)->whereMonth('date', $now->month)->count();

        $kegiatanTerbaru = Kegiatan::query()
            ->where('sekolah_id', $sekolahId)
            ->whereDate('date', $today)
            ->with('kelas')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $presensiHadirHariIni = (int) Presensi::where('sekolah_id', $sekolahId)
            ->whereDate('tanggal', $today)
            ->where('hadir', true)
            ->distinct()
            ->count('anak_id');

        $isWeekday = ! $today->isWeekend();
        $menuRecord = MenuMakanan::where('sekolah_id', $sekolahId)->whereDate('date', $today)->first();
        $menuKosong = $menuRecord === null || DashboardCompactMenu::isEmpty($menuRecord->menu ?? null);

        $uangMasuk = \App\Models\Cashflow::where('sekolah_id', $sekolahId)->where('type', 'in')->sum('amount');
        $uangKeluar = \App\Models\Cashflow::where('sekolah_id', $sekolahId)->where('type', 'out')->sum('amount');
        $saldoKas = $uangMasuk - $uangKeluar;

        $pembayaranPending = PembayaranBulanan::where('sekolah_id', $sekolahId)
            ->where('status', 'pending')
            ->where('periode_bulan', $now->month)
            ->where('periode_tahun', $now->year)
            ->count();

        $kritikBaru = KritikSaran::where('sekolah_id', $sekolahId)
            ->where(function ($q) {
                $q->whereNull('umpan_balik')->orWhere('umpan_balik', '');
            })
            ->count();

        $hadirLabel = $isWeekday
            ? "{$presensiHadirHariIni} dari {$siswaAktif} siswa sudah hadir"
            : 'Akhir pekan — presensi biasanya diisi Senin';

        $summary = [
            [
                'label' => 'Siswa aktif',
                'value' => (string) $siswaAktif,
                'context' => $siswaPending > 0 ? "{$siswaPending} pendaftaran menunggu" : 'Semua pendaftaran sudah diproses',
                'href' => route('admin.anak.index'),
                'icon' => 'users',
            ],
            [
                'label' => 'Kehadiran hari ini',
                'value' => $isWeekday ? (string) $presensiHadirHariIni : '—',
                'context' => $hadirLabel,
                'href' => route('admin.presensi.index'),
                'icon' => 'check',
            ],
            [
                'label' => 'Jurnal hari ini',
                'value' => (string) $kegiatanHariIni,
                'context' => "Bulan ini {$kegiatanBulanIni} entri · {$totalKelas} kelas",
                'href' => route('admin.kegiatan.index'),
                'icon' => 'calendar',
            ],
            [
                'label' => 'Saldo kas',
                'value' => 'Rp '.number_format($saldoKas, 0, ',', '.'),
                'context' => $totalPengajar.' pengajar aktif',
                'href' => route('admin.cashflow.index'),
                'icon' => 'cash',
            ],
        ];

        $attention = [];

        if ($siswaPending > 0) {
            $attention[] = [
                'severity' => 'warning',
                'title' => "{$siswaPending} pendaftaran perlu disetujui",
                'detail' => 'Orang tua menunggu konfirmasi sebelum anak masuk kelas.',
                'href' => route('admin.pendaftaran.index'),
                'action' => 'Review pendaftaran',
            ];
        }

        if ($isWeekday && $siswaAktif > 0 && $presensiHadirHariIni < (int) ($siswaAktif * 0.5)) {
            $attention[] = [
                'severity' => 'critical',
                'title' => 'Kehadiran hari ini masih rendah',
                'detail' => "Baru {$presensiHadirHariIni} dari {$siswaAktif} siswa tercatat hadir.",
                'href' => route('admin.presensi.index'),
                'action' => 'Cek presensi',
            ];
        }

        if ($isWeekday && $menuKosong) {
            $attention[] = [
                'severity' => 'warning',
                'title' => 'Menu makan hari ini belum diinput',
                'detail' => 'Orang tua belum bisa melihat jadwal makan di aplikasi.',
                'href' => route('admin.menu-makanan.index'),
                'action' => 'Input menu',
            ];
        }

        if ($pembayaranPending > 0) {
            $attention[] = [
                'severity' => 'warning',
                'title' => "{$pembayaranPending} pembayaran menunggu verifikasi",
                'detail' => 'Bulan '.$now->translatedFormat('F Y').'.',
                'href' => route('admin.pembayaran-bulanan.index', ['status' => 'pending']),
                'action' => 'Verifikasi bayar',
            ];
        }

        if ($kritikBaru > 0) {
            $attention[] = [
                'severity' => 'info',
                'title' => "{$kritikBaru} masukan belum dibalas",
                'detail' => 'Balas agar orang tua tahu masukan mereka diterima.',
                'href' => route('admin.kritik-saran.index'),
                'action' => 'Lihat masukan',
            ];
        }

        if ($isWeekday && $totalKelas > 0 && $kegiatanHariIni === 0) {
            $attention[] = [
                'severity' => 'warning',
                'title' => 'Belum ada jurnal kegiatan hari ini',
                'detail' => 'Guru biasanya mengisi jurnal saat jam belajar.',
                'href' => route('admin.kegiatan.index'),
                'action' => 'Lihat agenda',
            ];
        }

        usort($attention, fn (array $a, array $b) => self::severityRank($a['severity']) <=> self::severityRank($b['severity']));

        $activitiesItems = $kegiatanTerbaru->map(function (Kegiatan $k) {
            return [
                'time' => $k->created_at?->format('H:i') ?? '—',
                'title' => $k->title ?: 'Kegiatan belajar',
                'meta' => $k->kelas?->name ?? '',
                'status' => 'Jurnal',
            ];
        })->values()->all();

        $attentionOverflowHref = $siswaPending > 0
            ? route('admin.pendaftaran.index')
            : route('admin.kegiatan.index');

        return [
            'lead' => $today->translatedFormat('l, d F Y').' · Pantau operasional sekolah dari satu tempat',
            'summary' => $summary,
            'attention' => $attention,
            'activities' => [
                'items' => $activitiesItems,
                'total' => $kegiatanHariIni,
                'href' => route('admin.kegiatan.index'),
            ],
            'attentionOverflowHref' => $attentionOverflowHref,
        ];
    }

    /**
     * @param  list<array{id: string, title: string, subtitle?: string, height?: int, option: array<string, mixed>}>  $charts
     * @return list<array{id: string, title: string, subtitle?: string, height?: int, option: array<string, mixed>}>
     */
    public static function enrichCharts(array $charts, int $sekolahId): array
    {
        $today = Carbon::today()->locale('id');
        $now = Carbon::now()->locale('id');
        $siswaAktif = Anak::where('sekolah_id', $sekolahId)->where('status', 'approved')->count();
        $presensiHadirHariIni = (int) Presensi::where('sekolah_id', $sekolahId)
            ->whereDate('tanggal', $today)
            ->where('hadir', true)
            ->distinct()
            ->count('anak_id');

        foreach ($charts as $i => $chart) {
            $id = $chart['id'] ?? '';

            if ($id === 'admin-global-trend') {
                $labels = $chart['option']['xAxis']['data'] ?? [];
                $series = $chart['option']['series'] ?? [];
                $muridData = $series[0]['data'] ?? [];
                $firstLabel = is_array($labels) && count($labels) > 0 ? (string) $labels[0] : '';
                $lastLabel = is_array($labels) && count($labels) > 0 ? (string) end($labels) : '';
                $rangeSubtitle = ($firstLabel !== '' && $lastLabel !== '')
                    ? "Murid terdaftar (kumulatif) vs jumlah entri kegiatan, {$firstLabel}–{$lastLabel}"
                    : 'Murid terdaftar (kumulatif) vs jumlah entri kegiatan, 6 bulan terakhir';

                $charts[$i]['subtitle'] = $rangeSubtitle;
                $charts[$i]['title'] = 'Tren murid dan kegiatan';

                if (is_array($muridData) && count($muridData) >= 2) {
                    $last = (int) ($muridData[count($muridData) - 1] ?? 0);
                    $prev = (int) ($muridData[count($muridData) - 2] ?? 0);
                    if ($prev > 0 && $last !== $prev) {
                        $pct = (int) round(abs($last - $prev) / $prev * 100);
                        $since = count($labels) >= 2 ? (string) $labels[count($labels) - 2] : 'bulan lalu';
                        $charts[$i]['title'] = $last > $prev
                            ? "Murid naik {$pct}% sejak {$since}"
                            : "Murid turun {$pct}% sejak {$since}";
                    } elseif ($prev > 0 && $last === $prev) {
                        $charts[$i]['title'] = "Murid stabil ({$last} terdaftar)";
                    }
                }
            }

            if ($id === 'admin-siswa-kelas') {
                $labels = $chart['option']['xAxis']['data'] ?? [];
                $series = $chart['option']['series'] ?? [];
                $values = $series[0]['data'] ?? [];
                $charts[$i]['subtitle'] = 'Semua kelas aktif';
                $charts[$i]['title'] = 'Murid per kelas';

                if (is_array($values) && is_array($labels) && count($values) > 0) {
                    $max = max(array_map('intval', $values));
                    if ($max > 0) {
                        $idx = array_search($max, array_map('intval', $values), true);
                        $name = $idx !== false && isset($labels[$idx]) ? (string) $labels[$idx] : 'Kelas';
                        $charts[$i]['title'] = "Kelas {$name} paling penuh ({$max} murid)";
                    }
                }
            }

            if ($id === 'admin-presensi-bulan') {
                $charts[$i]['subtitle'] = 'Persentase siswa hadir per hari kerja · '.$now->translatedFormat('F Y');
                $dailyPct = $chart['option']['series'][0]['data'] ?? [];
                $validDays = is_array($dailyPct) ? array_values(array_filter($dailyPct, fn ($v) => $v !== null)) : [];

                if ($siswaAktif > 0 && ! $today->isWeekend()) {
                    $pctHari = (int) round(min(100, 100 * $presensiHadirHariIni / $siswaAktif));
                    $charts[$i]['title'] = "Kehadiran {$pctHari}% hari ini";
                } elseif ($validDays !== []) {
                    $avg = (int) round(array_sum($validDays) / count($validDays));
                    $charts[$i]['title'] = "Rata-rata kehadiran {$avg}% bulan ini";
                } else {
                    $charts[$i]['title'] = 'Belum ada data presensi';
                }
            }
        }

        return $charts;
    }

    private static function severityRank(string $severity): int
    {
        return match ($severity) {
            'critical' => 0,
            'warning' => 1,
            'info' => 2,
            default => 3,
        };
    }
}
