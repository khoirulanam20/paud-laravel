<?php

namespace App\Http\Controllers\AdminKelas;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kesehatan;
use App\Models\Pengajar;
use Illuminate\Http\Request;

class KesehatanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->firstOrFail();
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();

        $query = Anak::whereIn('kelas_id', $kelasIds)->with(['kelas', 'kesehatans' => function($q) {
            $q->latest('tanggal_pemeriksaan')->limit(1);
        }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $anaks = $query->orderBy('name')->paginate(20);

        return view('adminkelas.kesehatan.index', compact('anaks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'anak_id' => 'required|exists:anaks,id',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'lingkar_kepala' => 'nullable|numeric',
            'gigi' => 'nullable|string',
            'telinga' => 'nullable|string',
            'kuku' => 'nullable|string',
            'alergi' => 'nullable|string',
            'tanggal_pemeriksaan' => 'required|date',
        ]);

        Kesehatan::updateOrCreate(
            ['anak_id' => $data['anak_id'], 'tanggal_pemeriksaan' => $data['tanggal_pemeriksaan']],
            $data
        );

        return back()->with('success', 'Data kesehatan berhasil diperbarui.');
    }
}
