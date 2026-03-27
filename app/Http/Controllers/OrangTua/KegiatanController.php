<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $kegiatans = Kegiatan::with('pengajar')
            ->where('sekolah_id', $sekolah_id)
            ->orderBy('date', 'desc')
            ->paginate(10);
            
        return view('orangtua.kegiatan.index', compact('kegiatans'));
    }
}
