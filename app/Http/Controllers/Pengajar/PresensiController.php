<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('Admin Kelas')) {
            return redirect()->route('adminkelas.presensi.index', $request->only('tanggal'));
        }

        abort(403, 'Menu Presensi hanya dapat diakses oleh Wali Kelas.');
    }

    public function store(Request $request)
    {
        abort(403);
    }
}
