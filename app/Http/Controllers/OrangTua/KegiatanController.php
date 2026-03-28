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
            ->with(['pengajar', 'pencapaians.anak', 'pencapaians.matrikulasi'])
            ->whereBetween('date', [$from, $to]);

        if (! $semuaSekolah) {
            if ($anakId) {
                $query->whereHas('pencapaians', fn ($q) => $q->where('anak_id', $anakId));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $kegiatans = $query->orderBy('date')->orderBy('id')->get();

        $limitAnakIds = null;
        if ($semuaSekolah) {
            $limitAnakIds = $anakIds !== [] ? $anakIds : [-1];
        }

        $calendarEvents = $kegiatans->map(function (Kegiatan $k) use ($anakId, $limitAnakIds, $semuaSekolah) {
            $subset = null;
            if (! $semuaSekolah && $anakId) {
                $subset = $k->pencapaians->where('anak_id', $anakId)->pluck('id')->values()->all();
            }

            return KegiatanCalendar::toReadonlyEvent(
                $k,
                $subset,
                $semuaSekolah ? $limitAnakIds : null,
            );
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
