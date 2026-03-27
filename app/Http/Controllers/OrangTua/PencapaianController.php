<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Pencapaian;
use Illuminate\Http\Request;

class PencapaianController extends Controller
{
    public function index()
    {
        // Get all children belonging to this parent user
        $anakIds = Anak::where('user_id', auth()->id())->pluck('id');

        $pencapaians = Pencapaian::with(['anak', 'matrikulasi', 'pengajar'])
            ->whereIn('anak_id', $anakIds)
            ->latest()
            ->paginate(15);
            
        return view('orangtua.pencapaian.index', compact('pencapaians'));
    }
}
