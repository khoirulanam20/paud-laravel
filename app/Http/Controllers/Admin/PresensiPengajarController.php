<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use App\Models\PresensiPengajar;
use Illuminate\Http\Request;

class PresensiPengajarController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $sekolahId = $user->sekolah_id;
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $pengajars = Pengajar::where('sekolah_id', $sekolahId)
            ->orderBy('name')
            ->get();

        $presensis = PresensiPengajar::where('sekolah_id', $sekolahId)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('pengajar_id');

        return view('admin.presensi-guru.index', compact('pengajars', 'presensis', 'tanggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.hadir' => 'required|boolean',
            'presensi.*.status' => 'nullable|string',
        ]);

        $sekolahId = auth()->user()->sekolah_id;

        foreach ($request->presensi as $pengajarId => $data) {
            PresensiPengajar::updateOrCreate(
                [
                    'sekolah_id' => $sekolahId,
                    'pengajar_id' => $pengajarId,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'hadir' => $data['hadir'],
                    'status' => $data['status'],
                ]
            );
        }

        return back()->with('success', 'Presensi guru berhasil disimpan.');
    }
}
