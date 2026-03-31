<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kelas;
use App\Models\Kesehatan;
use Illuminate\Http\Request;

class KesehatanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Anak::where('sekolah_id', $user->sekolah_id)->with(['kelas', 'kesehatans' => function($q) {
            $q->latest('tanggal_pemeriksaan')->limit(1);
        }]);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $anaks = $query->orderBy('name')->paginate(20);
        $kelas = Kelas::where('sekolah_id', $user->sekolah_id)->orderBy('name')->get();

        return view('admin.kesehatan.index', compact('anaks', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
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

        Kesehatan::create($request->all());

        return back()->with('success', 'Data kesehatan berhasil disimpan.');
    }
}
