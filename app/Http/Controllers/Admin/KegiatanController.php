<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Pengajar;
use App\Support\KegiatanCalendar;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        [$year, $month] = KegiatanCalendar::resolveYearMonth($request);
        [$from, $to] = KegiatanCalendar::dateRangeForCalendar($year, $month);

        $query = Kegiatan::query()
            ->where('sekolah_id', $sekolah_id)
            ->with(['pengajar', 'kelas'])
            ->whereBetween('date', [$from, $to]);

        if ($request->filled('pengajar_id')) {
            $pid = $request->integer('pengajar_id');
            if (Pengajar::where('id', $pid)->where('sekolah_id', $sekolah_id)->exists()) {
                $query->where('pengajar_id', $pid);
            }
        }

        $kelas = \App\Models\Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        if ($request->filled('kelas_id')) {
            $kid = $request->integer('kelas_id');
            if ($kelas->contains('id', $kid)) {
                $query->where('kelas_id', $kid);
            }
        }

        $kegiatans = $query->orderBy('date')->orderBy('id')->get();
        $calendarEvents = $kegiatans->map(fn (Kegiatan $k) => KegiatanCalendar::toReadonlyEvent($k, null, null))->values()->all();

        $pengajars = Pengajar::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        return view('admin.kegiatan.index', compact('calendarEvents', 'year', 'month', 'pengajars', 'kelas'));
    }
}
