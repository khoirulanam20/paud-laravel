<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Matrikulasi;
use App\Models\Pengajar;
use Illuminate\Http\Request;

class MatrikulasiController extends Controller
{
    private function getSekolahId()
    {
        return Pengajar::where('user_id', auth()->id())->value('sekolah_id');
    }

    public function index()
    {
        $sekolah_id = $this->getSekolahId();
        $matrikulasis = Matrikulasi::where('sekolah_id', $sekolah_id)->latest()->paginate(10);
        
        return view('pengajar.matrikulasi.index', compact('matrikulasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'indicator' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Matrikulasi::create([
            'sekolah_id' => $this->getSekolahId(),
            'indicator' => $request->indicator,
            'description' => $request->description,
        ]);

        return redirect()->route('pengajar.matrikulasi.index')->with('success', 'Indikator Matrikulasi berhasil ditambahkan.');
    }

    public function update(Request $request, Matrikulasi $matrikulasi)
    {
        abort_if($matrikulasi->sekolah_id !== $this->getSekolahId(), 403);

        $request->validate([
            'indicator' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $matrikulasi->update([
            'indicator' => $request->indicator,
            'description' => $request->description,
        ]);

        return redirect()->route('pengajar.matrikulasi.index')->with('success', 'Indikator Matrikulasi berhasil diperbarui.');
    }

    public function destroy(Matrikulasi $matrikulasi)
    {
        abort_if($matrikulasi->sekolah_id !== $this->getSekolahId(), 403);
        $matrikulasi->delete();
        
        return redirect()->route('pengajar.matrikulasi.index')->with('success', 'Indikator Matrikulasi berhasil dihapus.');
    }
}
