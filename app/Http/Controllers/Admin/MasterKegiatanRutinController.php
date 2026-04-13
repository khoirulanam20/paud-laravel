<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterKegiatanRutin;
use App\Models\Pengajar;
use App\Models\Matrikulasi;
use App\Http\Traits\CanUploadImage;

class MasterKegiatanRutinController extends Controller
{
    use CanUploadImage;
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        
        $masters = MasterKegiatanRutin::with(['kelas', 'matrikulasi'])
            ->where('sekolah_id', $sekolah_id)
            ->latest()
            ->get();

        return view('pengajar.master-kegiatan-rutin.index', compact('masters'));
    }

    public function create()
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;
        
        $classList = \App\Models\Kelas::where('sekolah_id', $sekolah_id)->get();
        $matrikulasiList = Matrikulasi::where('sekolah_id', $sekolah_id)->get();

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
        $sekolah_id = $user->sekolah_id;
        $pengajar_id = Pengajar::where('sekolah_id', $sekolah_id)->first()->id ?? null;
        if (!$pengajar_id) {
            return back()->withErrors(['error' => 'Tambahkan minimal 1 data pengajar terlebih dahulu agar bisa membuat Master Kegiatan Rutin.']);
        }

        $master = MasterKegiatanRutin::create([
            'sekolah_id' => $sekolah_id,
            'pengajar_id' => $pengajar_id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'aspek' => $request->aspek,
            'matrikulasi_id' => $request->matrikulasi_id,
        ]);

        $master->kelas()->sync($request->kelas_ids);

        return redirect()->route('admin.master-kegiatan-rutin.index')->with('success', 'Master Kegiatan Rutin berhasil ditambahkan.');
    }

    public function edit(MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        if ($masterKegiatanRutin->sekolah_id !== $sekolah_id) {
            abort(403);
        }

        $classList = \App\Models\Kelas::where('sekolah_id', $sekolah_id)->get();
        $matrikulasiList = Matrikulasi::where('sekolah_id', $sekolah_id)->get();

        return view('pengajar.master-kegiatan-rutin.edit', compact('masterKegiatanRutin', 'classList', 'matrikulasiList'));
    }

    public function update(Request $request, MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        if ($masterKegiatanRutin->sekolah_id !== $sekolah_id) {
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

        return redirect()->route('admin.master-kegiatan-rutin.index')->with('success', 'Master Kegiatan Rutin berhasil diperbarui.');
    }

    public function show(Request $request, MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        if ($masterKegiatanRutin->sekolah_id !== $sekolah_id) {
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
            'keterangan' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        if ($masterKegiatanRutin->sekolah_id !== $sekolah_id) {
            abort(403);
        }
        
        $pengajar_id = $masterKegiatanRutin->pengajar_id ?? (Pengajar::where('sekolah_id', $sekolah_id)->first()->id ?? null);

        $rutin = \App\Models\KegiatanRutin::where([
            'sekolah_id' => $sekolah_id,
            'kelas_id' => $request->kelas_id,
            'anak_id' => $request->anak_id,
            'master_kegiatan_rutin_id' => $masterKegiatanRutin->id,
            'tanggal' => $request->tanggal,
        ])->first();

        $data = [
            'pengajar_id' => $pengajar_id,
            'aspek' => $masterKegiatanRutin->aspek,
            'kegiatan' => $masterKegiatanRutin->nama_kegiatan,
            'status_pencapaian' => $request->status_pencapaian,
            'keterangan' => $request->keterangan,
        ];

        if ($request->hasFile('photo')) {
            if ($rutin && $rutin->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($rutin->photo);
            }
            $data['photo'] = $this->uploadImage($request->file('photo'), 'kegiatan-rutin');
        }

        if ($rutin) {
            $rutin->update($data);
        } else {
            \App\Models\KegiatanRutin::create(array_merge($data, [
                'sekolah_id' => $sekolah_id,
                'kelas_id' => $request->kelas_id,
                'anak_id' => $request->anak_id,
                'master_kegiatan_rutin_id' => $masterKegiatanRutin->id,
                'tanggal' => $request->tanggal,
            ]));
        }

        return back()->with('success', 'Data pencapaian berhasil disimpan.');
    }

    public function detail(Request $request, MasterKegiatanRutin $masterKegiatanRutin, \App\Models\Anak $anak)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        $mulai = $request->query('mulai', date('Y-m-01'));
        $sampai = $request->query('sampai', date('Y-m-t'));

        $rutins = \App\Models\KegiatanRutin::where('anak_id', $anak->id)
            ->where('sekolah_id', $sekolah_id)
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
                    'keterangan' => $q->keterangan,
                    'photo_url' => $q->photo ? \Illuminate\Support\Facades\Storage::url($q->photo) : null,
                ];
            });

        return response()->json($rutins);
    }

    public function destroy(MasterKegiatanRutin $masterKegiatanRutin)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        if ($masterKegiatanRutin->sekolah_id !== $sekolah_id) {
            abort(403);
        }

        foreach($masterKegiatanRutin->kegiatanRutins as $qr) {
            if ($qr->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($qr->photo);
            }
        }

        $masterKegiatanRutin->delete();

        return redirect()->route('admin.master-kegiatan-rutin.index')->with('success', 'Master Kegiatan Rutin berhasil dihapus.');
    }

    public function destroyRutinRecord(\App\Models\KegiatanRutin $kegiatanRutin)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        if ($kegiatanRutin->sekolah_id !== $sekolah_id) {
            abort(403);
        }

        if ($kegiatanRutin->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($kegiatanRutin->photo);
        }

        $kegiatanRutin->delete();

        return back()->with('success', 'Catatan pencapaian berhasil dihapus.');
    }
}
