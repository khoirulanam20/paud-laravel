<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SumberDana;
use Illuminate\Http\Request;

class SumberDanaController extends Controller
{
    public function index()
    {
        $sumberDanas = SumberDana::where('sekolah_id', auth()->user()->sekolah_id)
            ->orderBy('urutan')
            ->get();

        return view('admin.sumber-dana.index', compact('sumberDanas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'urutan' => 'nullable|integer|min:0',
        ]);

        SumberDana::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'urutan' => $request->input('urutan', 99),
            'is_aktif' => true,
        ]);

        return redirect()->route('admin.sumber-dana.index')->with('success', 'Sumber dana berhasil ditambahkan.');
    }

    public function update(Request $request, SumberDana $sumberDana)
    {
        abort_if($sumberDana->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'urutan' => 'nullable|integer|min:0',
            'is_aktif' => 'boolean',
        ]);

        $sumberDana->update([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'urutan' => $request->input('urutan', 0),
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return redirect()->route('admin.sumber-dana.index')->with('success', 'Sumber dana berhasil diperbarui.');
    }

    public function destroy(SumberDana $sumberDana)
    {
        abort_if($sumberDana->sekolah_id !== auth()->user()->sekolah_id, 403);
        $sumberDana->delete();

        return redirect()->route('admin.sumber-dana.index')->with('success', 'Sumber dana berhasil dihapus.');
    }
}
