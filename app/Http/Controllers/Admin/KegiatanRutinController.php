<?php

namespace App\Http\Controllers\Admin;

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
        $sekolahId = $user->sekolah_id;
        $classList = \App\Models\Kelas::where('sekolah_id', $sekolahId)->get();
        $kelasIds = $classList->pluck('id')->toArray();

        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelasId = $request->input('kelas_id');

        if (!$kelasId && !empty($kelasIds)) {
            $kelasId = $kelasIds[0];
        }

        $anaks = $kelasId ? Anak::where('kelas_id', $kelasId)->get() : collect();
        
        $masters = $kelasId ? \App\Models\MasterKegiatanRutin::whereHas('kelas', function ($query) use ($kelasId) {
            $query->where('kelas.id', $kelasId);
        })->get() : collect();

        $rutins = $kelasId ? KegiatanRutin::where('kelas_id', $kelasId)
            ->where('tanggal', $tanggal)
            ->get() : collect();

        return view('pengajar.kegiatan-rutin.index', compact('classList', 'anaks', 'rutins', 'tanggal', 'kelasId', 'masters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'rutin' => 'required|array',
        ]);

        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;
        $pengajar_id = Pengajar::where('sekolah_id', $sekolah_id)->first()->id ?? null;
        if (!$pengajar_id) {
            return back()->withErrors(['error' => 'Tambahkan minimal 1 data pengajar terlebih dahulu.']);
        }

        foreach ($request->rutin as $anakId => $mastersData) {
            foreach ($mastersData as $masterId => $data) {
                if (empty($data['status_pencapaian'])) continue;

                KegiatanRutin::updateOrCreate(
                    [
                        'sekolah_id' => $sekolah_id,
                        'kelas_id' => $request->kelas_id,
                        'anak_id' => $anakId,
                        'master_kegiatan_rutin_id' => $masterId == 'custom' ? null : $masterId,
                        'tanggal' => $request->tanggal,
                    ],
                    [
                        'pengajar_id' => $pengajar_id,
                        'aspek' => $data['aspek'] ?? '',
                        'kegiatan' => $data['kegiatan'] ?? '',
                        'status_pencapaian' => $data['status_pencapaian'],
                    ]
                );
            }
        }

        return back()->with('success', 'Kegiatan rutin berhasil diperbarui.');
    }

    public function detail(Request $request, Anak $anak)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        $mulai = $request->query('mulai', date('Y-m-01'));
        $sampai = $request->query('sampai', date('Y-m-t'));

        $rutins = KegiatanRutin::where('anak_id', $anak->id)
            ->where('sekolah_id', $sekolah_id)
            ->whereBetween('tanggal', [$mulai, $sampai])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'tanggal_formatted' => $q->tanggal->format('d M Y'),
                    'tanggal' => $q->tanggal->format('Y-m-d'),
                    'aspek' => $q->aspek,
                    'kegiatan' => $q->kegiatan,
                    'status_pencapaian' => $q->status_pencapaian,
                    'keterangan' => $q->keterangan,
                ];
            });

        return response()->json($rutins);
    }
}
