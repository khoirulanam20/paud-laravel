<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\DownloadsExcel;
use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use App\Models\PresensiPengajar;
use App\Models\Sekolah;
use App\Services\PresensiPengajarService;
use App\Support\PaginationPerPage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiPengajarController extends Controller
{
    use DownloadsExcel;

    public function __construct(private readonly PresensiPengajarService $presensi) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $sekolahId = $user->sekolah_id;
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $pengajars = Pengajar::where('sekolah_id', $sekolahId)
            ->orderBy('name')
            ->paginate(PaginationPerPage::resolve($request))
            ->withQueryString();

        $presensis = PresensiPengajar::where('sekolah_id', $sekolahId)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('pengajar_id');

        // Monthly Summary Filter
        $rekapMonth = $request->input('rekap_month', date('n'));
        $rekapYear = $request->input('rekap_year', date('Y'));

        $startOfMonth = Carbon::create($rekapYear, $rekapMonth, 1)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::create($rekapYear, $rekapMonth, 1)->endOfMonth()->toDateString();

        $rekapBulanan = PresensiPengajar::where('sekolah_id', $sekolahId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('pengajar_id')
            ->map(function ($items) {
                return [
                    'hadir' => $items->where('hadir', true)->count(),
                    'sakit' => $items->where('status', 'Sakit')->count(),
                    'izin' => $items->where('status', 'Izin')->count(),
                    'alpa' => $items->where('status', 'Alpa')->count(),
                    'terlambat' => $items->where('terlambat_masuk', true)->count(),
                    'telat_pulang' => $items->where('telat_pulang', true)->count(),
                ];
            });

        $jadwal = $this->presensi->jadwalHari(
            Sekolah::find($sekolahId) ?? new Sekolah,
            Carbon::parse($tanggal),
        );

        return view('admin.presensi-guru.index', compact('pengajars', 'presensis', 'tanggal', 'rekapBulanan', 'rekapMonth', 'rekapYear', 'jadwal'));
    }

    public function export(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $rekapMonth = (int) $request->input('rekap_month', date('n'));
        $rekapYear = (int) $request->input('rekap_year', date('Y'));

        $pengajars = Pengajar::where('sekolah_id', $sekolahId)->orderBy('name')->get();
        $presensis = PresensiPengajar::where('sekolah_id', $sekolahId)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('pengajar_id');

        $startOfMonth = Carbon::create($rekapYear, $rekapMonth, 1)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::create($rekapYear, $rekapMonth, 1)->endOfMonth()->toDateString();
        $rekapBulanan = PresensiPengajar::where('sekolah_id', $sekolahId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('pengajar_id')
            ->map(function ($items) {
                return [
                    'hadir' => $items->where('hadir', true)->count(),
                    'sakit' => $items->where('status', 'Sakit')->count(),
                    'izin' => $items->where('status', 'Izin')->count(),
                    'alpa' => $items->where('status', 'Alpa')->count(),
                    'terlambat' => $items->where('terlambat_masuk', true)->count(),
                    'telat_pulang' => $items->where('telat_pulang', true)->count(),
                ];
            });

        $harianRows = $pengajars->map(function (Pengajar $p) use ($presensis) {
            $presensi = $presensis->get($p->id);

            return [
                $p->name,
                $presensi?->hadir ? 'Hadir' : 'Tidak Hadir',
                $presensi?->status ?? '-',
                $presensi?->check_in_at?->format('H:i') ?? '-',
                $presensi?->check_out_at?->format('H:i') ?? '-',
                $presensi?->terlambat_masuk ? 'Telat masuk' : '-',
                $presensi?->telat_pulang ? 'Telat pulang' : '-',
            ];
        })->all();

        $rekapRows = $pengajars->map(function (Pengajar $p) use ($rekapBulanan) {
            $rekap = $rekapBulanan->get($p->id, ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'terlambat' => 0, 'telat_pulang' => 0]);

            return [
                $p->name,
                $rekap['hadir'],
                $rekap['sakit'],
                $rekap['izin'],
                $rekap['alpa'],
                $rekap['terlambat'],
                $rekap['telat_pulang'],
            ];
        })->all();

        return $this->downloadExcelSheets([
            [
                'title' => 'Presensi Harian',
                'headings' => ['Nama Pengajar', 'Kehadiran', 'Keterangan', 'Jam Masuk', 'Jam Pulang', 'Telat Masuk', 'Telat Pulang'],
                'rows' => $harianRows,
            ],
            [
                'title' => 'Rekap Bulanan',
                'headings' => ['Nama Pengajar', 'Hadir', 'Sakit', 'Izin', 'Alpa', 'Telat Masuk', 'Telat Pulang'],
                'rows' => $rekapRows,
            ],
        ], 'presensi-guru-'.$tanggal.'.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.hadir' => 'required|boolean',
            'presensi.*.status' => 'nullable|string',
            'presensi.*.check_in' => ['nullable', 'date_format:H:i'],
            'presensi.*.check_out' => ['nullable', 'date_format:H:i'],
            'presensi.*.catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $sekolahId = auth()->user()->sekolah_id;
        $sekolah = Sekolah::find($sekolahId);
        $tanggal = Carbon::parse($request->tanggal)->toDateString();

        foreach ($request->presensi as $pengajarId => $data) {
            $presensi = PresensiPengajar::withoutSekolahScope()->firstOrNew([
                'sekolah_id' => $sekolahId,
                'pengajar_id' => $pengajarId,
                'tanggal' => $tanggal,
            ]);

            $presensi->hadir = (bool) $data['hadir'];
            $presensi->status = $data['status'] ?? null;
            $presensi->catatan = $data['catatan'] ?? null;

            if ($sekolah) {
                if (! empty($data['check_in'])) {
                    $checkInAt = Carbon::parse($tanggal.' '.$data['check_in']);
                    [$terlambat, $menitTerlambat] = $this->presensi->hitungTerlambatMasuk($sekolah, $checkInAt);

                    $presensi->check_in_at = $checkInAt;
                    $presensi->terlambat_masuk = $terlambat;
                    $presensi->menit_telat_masuk = $menitTerlambat;
                    $presensi->sumber = PresensiPengajar::SUMBER_MANUAL;
                }

                if (! empty($data['check_out']) && $presensi->check_in_at) {
                    $checkOutAt = Carbon::parse($tanggal.' '.$data['check_out']);
                    [$telatPulang, $menitTelatPulang] = $this->presensi->hitungTelatPulang($sekolah, $presensi->check_in_at, $checkOutAt);

                    $presensi->check_out_at = $checkOutAt;
                    $presensi->telat_pulang = $telatPulang;
                    $presensi->menit_telat_pulang = $menitTelatPulang;
                    $presensi->sumber = PresensiPengajar::SUMBER_MANUAL;
                }
            }

            $presensi->save();
        }

        return back()->with('success', 'Presensi guru berhasil disimpan.');
    }
}
