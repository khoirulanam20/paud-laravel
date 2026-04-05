<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\KegiatanRutin;
use Illuminate\Http\Request;

class KegiatanRutinController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $sekolahId = $user->sekolah_id;

        $anaks = Anak::where('user_id', $user->id)
            ->where('sekolah_id', $sekolahId)
            ->get();

        $anakIds = $anaks->pluck('id');

        $query = KegiatanRutin::whereIn('anak_id', $anakIds)
            ->where('sekolah_id', $sekolahId)
            ->with(['anak', 'masterKegiatanRutin', 'pengajar']);

        $mulai = $request->query('mulai', date('Y-m-01'));
        $sampai = $request->query('sampai', date('Y-m-t'));

        if ($request->filled('anak_id')) {
            $query->where('anak_id', $request->query('anak_id'));
        }

        $query->whereBetween('tanggal', [$mulai, $sampai]);

        $kegiatans = $query->latest('tanggal')->get();

        return view('orangtua.kegiatan-rutin.index', compact('kegiatans', 'anaks', 'mulai', 'sampai'));
    }
}
