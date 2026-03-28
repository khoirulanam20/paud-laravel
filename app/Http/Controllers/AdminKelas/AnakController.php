<?php

namespace App\Http\Controllers\AdminKelas;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;

class AnakController extends Controller
{
    public function index()
    {
        $kelas_id = auth()->user()->kelas_id;
        $anaks = Anak::where('kelas_id', $kelas_id)->with('kelas')->orderBy('name')->paginate(20);
        return view('adminkelas.anak.index', compact('anaks'));
    }
}
