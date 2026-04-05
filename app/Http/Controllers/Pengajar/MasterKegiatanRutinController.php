<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterKegiatanRutin;
use App\Models\Pengajar;
use App\Models\Matrikulasi;

class MasterKegiatanRutinController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();
        
        $masters = MasterKegiatanRutin::with(['kelas', 'matrikulasi'])
            ->where('sekolah_id', $pengajar->sekolah_id)
            ->where('pengajar_id', $pengajar->id)
            ->latest()
            ->get();

        return view('pengajar.master-kegiatan-rutin.index', compact('masters'));
    }

    public function create()
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();
        
        $classList = $pengajar->kelas;
        $matrikulasiList = Matrikulasi::where('sekolah_id', $pengajar->sekolah_id)->get();

        return view('pengajar.master-kegiatan-rutin.create', compact('classList', 'matrikulasiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'aspek' => 'required|string|max:255',
            'matrikulasi_id' => 'nullable|exists:matrikulasis,id',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);

        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();

        $master = MasterKegiatanRutin::create([
            'sekolah_id' => $pengajar->sekolah_id,
            'pengajar_id' => $pengajar->id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'aspek' => $request->aspek,
            'matrikulasi_id' => $request->matrikulasi_id,
        ]);

        $master->kelas()->sync($request->kelas_ids);

        return redirect()->route('pengajar.master-kegiatan-rutin.index')->with('success', 'Master Kegiatan Rutin berhasil ditambahkan.');
    }

    public function edit(MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();

        if ($masterKegiatanRutin->pengajar_id !== $pengajar->id) {
            abort(403);
        }

        $classList = $pengajar->kelas;
        $matrikulasiList = Matrikulasi::where('sekolah_id', $pengajar->sekolah_id)->get();

        return view('pengajar.master-kegiatan-rutin.edit', compact('masterKegiatanRutin', 'classList', 'matrikulasiList'));
    }

    public function update(Request $request, MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();

        if ($masterKegiatanRutin->pengajar_id !== $pengajar->id) {
            abort(403);
        }

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'aspek' => 'required|string|max:255',
            'matrikulasi_id' => 'nullable|exists:matrikulasis,id',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);

        $masterKegiatanRutin->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'aspek' => $request->aspek,
            'matrikulasi_id' => $request->matrikulasi_id,
        ]);

        $masterKegiatanRutin->kelas()->sync($request->kelas_ids);

        return redirect()->route('pengajar.master-kegiatan-rutin.index')->with('success', 'Master Kegiatan Rutin berhasil diperbarui.');
    }

    public function show(Request $request, MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();

        if ($masterKegiatanRutin->pengajar_id !== $pengajar->id) {
            abort(403);
        }

        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelasId = $request->input('kelas_id');
        $kelasIds = $masterKegiatanRutin->kelas->pluck('id')->toArray();

        if (!$kelasId && !empty($kelasIds)) {
            $kelasId = $kelasIds[0];
        }

        if ($kelasId && !in_array($kelasId, $kelasIds)) {
            abort(403, 'Kelas tidak ditautkan ke kegiatan ini.');
        }

        $anaks = $kelasId ? \App\Models\Anak::where('kelas_id', $kelasId)->get() : collect();
        $rutins = $kelasId ? \App\Models\KegiatanRutin::where('kelas_id', $kelasId)
            ->where('master_kegiatan_rutin_id', $masterKegiatanRutin->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('anak_id') : collect();

        $classList = $masterKegiatanRutin->kelas;

        return view('pengajar.master-kegiatan-rutin.show', compact('masterKegiatanRutin', 'classList', 'anaks', 'rutins', 'tanggal', 'kelasId'));
    }

    public function storeRutin(Request $request, MasterKegiatanRutin $masterKegiatanRutin)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'anak_id' => 'required|exists:anaks,id',
            'status_pencapaian' => 'required|string',
        ]);

        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();

        if ($masterKegiatanRutin->pengajar_id !== $pengajar->id) {
            abort(403);
        }

        \App\Models\KegiatanRutin::updateOrCreate(
            [
                'sekolah_id' => $pengajar->sekolah_id,
                'kelas_id' => $request->kelas_id,
                'anak_id' => $request->anak_id,
                'master_kegiatan_rutin_id' => $masterKegiatanRutin->id,
                'tanggal' => $request->tanggal,
            ],
            [
                'pengajar_id' => $pengajar->id,
                'aspek' => $masterKegiatanRutin->aspek,
                'kegiatan' => $masterKegiatanRutin->nama_kegiatan,
                'status_pencapaian' => $request->status_pencapaian,
            ]
        );

        return back()->with('success', 'Status pencapaian berhasil disimpan.');
    }

    public function detail(Request $request, MasterKegiatanRutin $masterKegiatanRutin, \App\Models\Anak $anak)
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();

        $mulai = $request->query('mulai', date('Y-m-01'));
        $sampai = $request->query('sampai', date('Y-m-t'));

        $rutins = \App\Models\KegiatanRutin::where('anak_id', $anak->id)
            ->where('sekolah_id', $pengajar->sekolah_id)
            ->where('master_kegiatan_rutin_id', $masterKegiatanRutin->id)
            ->whereBetween('tanggal', [$mulai, $sampai])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'tanggal_formatted' => $q->tanggal->format('d M Y'),
                    'tanggal' => $q->tanggal->format('Y-m-d'),
                    'status_pencapaian' => $q->status_pencapaian,
                ];
            });

        return response()->json($rutins);
    }

    public function destroy(MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();

        if ($masterKegiatanRutin->pengajar_id !== $pengajar->id) {
            abort(403);
        }

        $masterKegiatanRutin->delete();

        return redirect()->route('pengajar.master-kegiatan-rutin.index')->with('success', 'Master Kegiatan Rutin berhasil dihapus.');
    }
}
