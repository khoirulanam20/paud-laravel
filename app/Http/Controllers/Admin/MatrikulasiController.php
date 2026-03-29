<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matrikulasi;
use Illuminate\Http\Request;

class MatrikulasiController extends Controller
{
    private function sekolahId(): ?int
    {
        $id = auth()->user()->sekolah_id;

        return $id !== null ? (int) $id : null;
    }

    public function index()
    {
        $sekolah_id = $this->sekolahId();
        abort_if($sekolah_id === null, 403, 'Akun tidak terikat sekolah.');

        $matrikulasis = Matrikulasi::query()
            ->where('sekolah_id', $sekolah_id)
            ->latest()
            ->paginate(10);

        return view('admin.matrikulasi.index', compact('matrikulasis'));
    }

    public function store(Request $request)
    {
        $sekolah_id = $this->sekolahId();
        abort_if($sekolah_id === null, 403, 'Akun tidak terikat sekolah.');

        $request->validate([
            'aspek' => 'nullable|string|max:255',
            'indicator' => 'required|string|max:255',
            'description' => 'required|string',
            'tujuan' => 'nullable|string',
            'strategi' => 'nullable|string',
        ]);

        Matrikulasi::create([
            'sekolah_id' => $sekolah_id,
            'aspek' => $request->aspek,
            'indicator' => $request->indicator,
            'description' => $request->description,
            'tujuan' => $request->tujuan,
            'strategi' => $request->strategi,
        ]);

        return redirect()->route('admin.matrikulasi.index')->with('success', 'Indikator matrikulasi berhasil ditambahkan.');
    }

    public function update(Request $request, Matrikulasi $matrikulasi)
    {
        $sekolah_id = $this->sekolahId();
        abort_if($sekolah_id === null, 403);
        abort_if((int) $matrikulasi->sekolah_id !== $sekolah_id, 403);

        $request->validate([
            'aspek' => 'nullable|string|max:255',
            'indicator' => 'required|string|max:255',
            'description' => 'required|string',
            'tujuan' => 'nullable|string',
            'strategi' => 'nullable|string',
        ]);

        $matrikulasi->update([
            'aspek' => $request->aspek,
            'indicator' => $request->indicator,
            'description' => $request->description,
            'tujuan' => $request->tujuan,
            'strategi' => $request->strategi,
        ]);

        return redirect()->route('admin.matrikulasi.index')->with('success', 'Indikator matrikulasi berhasil diperbarui.');
    }

    public function destroy(Matrikulasi $matrikulasi)
    {
        $sekolah_id = $this->sekolahId();
        abort_if($sekolah_id === null, 403);
        abort_if((int) $matrikulasi->sekolah_id !== $sekolah_id, 403);
        $matrikulasi->delete();

        return redirect()->route('admin.matrikulasi.index')->with('success', 'Indikator matrikulasi berhasil dihapus.');
    }
}
