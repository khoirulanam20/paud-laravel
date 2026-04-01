<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\KegiatanRutin;
use App\Models\Pengajar;
use Illuminate\Http\Request;

class KegiatanRutinController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();
        $sekolahId = $pengajar->sekolah_id;
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();

        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelasId = $request->input('kelas_id');

        if (!$kelasId && !empty($kelasIds)) {
            $kelasId = $kelasIds[0];
        }

        $anaks = $kelasId ? Anak::where('kelas_id', $kelasId)->get() : collect();
        $rutins = $kelasId ? KegiatanRutin::where('kelas_id', $kelasId)
            ->where('tanggal', $tanggal)
            ->get()
            ->groupBy('anak_id') : collect();

        $classList = $pengajar->kelas;

        return view('pengajar.kegiatan-rutin.index', compact('classList', 'anaks', 'rutins', 'tanggal', 'kelasId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'rutin' => 'required|array',
            'rutin.*.aspek' => 'required|string',
            'rutin.*.kegiatan' => 'required|string',
            'rutin.*.status_pencapaian' => 'required|string',
        ]);

        $pengajar = Pengajar::where('user_id', auth()->id())->firstOrFail();

        foreach ($request->rutin as $anakId => $data) {
            KegiatanRutin::updateOrCreate(
                [
                    'sekolah_id' => $pengajar->sekolah_id,
                    'kelas_id' => $request->kelas_id,
                    'anak_id' => $anakId,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'pengajar_id' => $pengajar->id,
                    'aspek' => $data['aspek'],
                    'kegiatan' => $data['kegiatan'],
                    'status_pencapaian' => $data['status_pencapaian'],
                ]
            );
        }

        return back()->with('success', 'Kegiatan rutin berhasil diperbarui.');
    }
}
