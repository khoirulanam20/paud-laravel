<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Presensi;
use App\Support\PresensiPeriodeFilter;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $sekolahId = $user->sekolah_id;
        
        $anaks = Anak::where('user_id', $user->id)
            ->where('sekolah_id', $sekolahId)
            ->get();
        $anakIds = $anaks->pluck('id');

        $filter = PresensiPeriodeFilter::resolve($request);
        
        $presensis = Presensi::whereIn('anak_id', $anakIds)
            ->whereBetween('tanggal', [$filter['from'], $filter['to']])
            ->orderBy('tanggal', 'desc')
            ->with('anak')
            ->get();

        return view('orangtua.presensi.index', compact('anaks', 'presensis', 'filter'));
    }
}
