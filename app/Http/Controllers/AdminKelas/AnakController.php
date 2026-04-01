<?php

namespace App\Http\Controllers\AdminKelas;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;

class AnakController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pengajar = \App\Models\Pengajar::where('user_id', $user->id)->firstOrFail();
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();

        $anaks = Anak::whereIn('kelas_id', $kelasIds)->with('kelas')->orderBy('name')->paginate(20);
        return view('adminkelas.anak.index', compact('anaks'));
    }

    public function show(Anak $anak)
    {
        $user = auth()->user();
        $pengajar = \App\Models\Pengajar::where('user_id', $user->id)->firstOrFail();
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();

        abort_unless(in_array($anak->kelas_id, $kelasIds), 403);

        $anak->load([
            'user',
            'kelas',
            'kesehatans' => fn($q) => $q->orderBy('tanggal_pemeriksaan', 'desc')
        ]);

        return view('adminkelas.anak.show', compact('anak'));
    }
}
