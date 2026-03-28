<?php

namespace App\Http\Controllers\AdminKelas;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Pengajar;
use App\Support\KegiatanCalendar;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->kelas_id, 403);

        $kelasId = $user->kelas_id;
        $sekolahId = $user->sekolah_id;
        if (! $sekolahId) {
            $sekolahId = Anak::where('kelas_id', $kelasId)->value('sekolah_id')
                ?? $user->kelas?->sekolah_id;
        }
        abort_unless($sekolahId, 403);

        [$year, $month] = KegiatanCalendar::resolveYearMonth($request);
        [$from, $to] = KegiatanCalendar::dateRangeForCalendar($year, $month);

        $anakIdsKelas = Anak::where('kelas_id', $kelasId)->pluck('id')->all();
        $limitAnakIds = $anakIdsKelas !== [] ? $anakIdsKelas : [-1];

        $query = Kegiatan::query()
            ->where('sekolah_id', $sekolahId)
            ->whereHas('pencapaians.anak', fn ($q) => $q->where('kelas_id', $kelasId))
            ->with(['pengajar', 'pencapaians.anak', 'pencapaians.matrikulasi'])
            ->whereBetween('date', [$from, $to]);

        if ($request->filled('pengajar_id')) {
            $pid = $request->integer('pengajar_id');
            if (Pengajar::where('id', $pid)->where('sekolah_id', $sekolahId)->exists()) {
                $query->where('pengajar_id', $pid);
            }
        }

        $kegiatans = $query->orderBy('date')->orderBy('id')->get();

        $calendarEvents = $kegiatans
            ->map(fn (Kegiatan $k) => KegiatanCalendar::toReadonlyEvent($k, null, $limitAnakIds))
            ->values()
            ->all();

        $pengajars = Pengajar::where('sekolah_id', $sekolahId)->orderBy('name')->get();

        return view('adminkelas.kegiatan.index', compact(
            'calendarEvents',
            'year',
            'month',
            'pengajars',
        ));
    }
}
