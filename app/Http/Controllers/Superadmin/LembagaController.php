<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use Illuminate\Http\Request;

class LembagaController extends Controller
{
    public function index()
    {
        $lembagas = Lembaga::withCount('sekolahs')->latest()->paginate(10);

        return view('superadmin.lembaga.index', compact('lembagas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'pendiri' => 'nullable|string|max:255',
            'organisasi' => 'nullable|string|max:255',
            'no_akta' => 'nullable|string|max:255',
            'no_pengesahan' => 'nullable|string|max:255',
        ]);

        Lembaga::create($validated);

        return redirect()->route('superadmin.lembaga.index')
            ->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function update(Request $request, Lembaga $lembaga)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'pendiri' => 'nullable|string|max:255',
            'organisasi' => 'nullable|string|max:255',
            'no_akta' => 'nullable|string|max:255',
            'no_pengesahan' => 'nullable|string|max:255',
        ]);

        $lembaga->update($validated);

        return redirect()->route('superadmin.lembaga.index')
            ->with('success', 'Data lembaga berhasil diperbarui.');
    }

    public function destroy(Lembaga $lembaga)
    {
        if ($lembaga->sekolahs()->exists()) {
            return back()->withErrors(['lembaga' => 'Lembaga masih memiliki cabang sekolah. Hapus sekolah terlebih dahulu.']);
        }

        $lembaga->delete();

        return redirect()->route('superadmin.lembaga.index')
            ->with('success', 'Lembaga berhasil dihapus.');
    }
}
