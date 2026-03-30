<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Support\KegiatanCalendar;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $anaks = Anak::where('user_id', auth()->id())
            ->where('sekolah_id', $sekolah_id)
            ->orderBy('name')
            ->get();

        $anakIds = $anaks->pluck('id')->all();
        $anakId = $request->input('anak_id');
        if ($anakId !== null && $anakId !== '') {
            $anakId = (int) $anakId;
            if (! in_array($anakId, $anakIds, true)) {
                $anakId = null;
            }
        } elseif (count($anakIds) === 1) {
            $anakId = $anakIds[0];
        }

        $semuaSekolah = $request->boolean('semua_sekolah');

        [$year, $month] = KegiatanCalendar::resolveYearMonth($request);
        [$from, $to] = KegiatanCalendar::dateRangeForCalendar($year, $month);

        $query = Kegiatan::query()
            ->where('sekolah_id', $sekolah_id)
            ->with(['pengajar', 'kelas', 'pencapaians.anak', 'pencapaians.matrikulasi'])
            ->whereBetween('date', [$from, $to]);

        $anakKelasIds = $anaks->pluck('kelas_id')->filter()->unique()->toArray();

        if (! $semuaSekolah) {
            if ($anakId) {
                $specificAnak = $anaks->firstWhere('id', $anakId);
                if ($specificAnak && $specificAnak->kelas_id) {
                    $query->where('kelas_id', $specificAnak->kelas_id);
                } else {
                    $query->whereRaw('1 = 0');
                }
            } elseif ($anakKelasIds !== []) {
                $query->whereIn('kelas_id', $anakKelasIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $kegiatans = $query->orderBy('date')->orderBy('id')->get();

        $limitAnakIds = null;
        if ($semuaSekolah) {
            $limitAnakIds = $anakIds !== [] ? $anakIds : [-1];
        }

        $calendarEvents = $kegiatans->map(function (Kegiatan $k) use ($anakId, $limitAnakIds, $semuaSekolah, $anakIds) {
            $subset = null;
            $limitForEvent = null;

            if ($semuaSekolah) {
                $limitForEvent = $limitAnakIds;
            } elseif ($anakId) {
                $subset = $k->pencapaians->where('anak_id', $anakId)->pluck('id')->values()->all();
            } elseif ($anakIds !== []) {
                $limitForEvent = $anakIds;
            }

            return KegiatanCalendar::toReadonlyEvent($k, $subset, $limitForEvent);
        })->values()->all();

        return view('orangtua.kegiatan.index', compact(
            'calendarEvents',
            'year',
            'month',
            'anaks',
            'anakId',
            'semuaSekolah',
        ));
    }
}
