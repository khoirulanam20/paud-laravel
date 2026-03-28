<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sarana;
use Illuminate\Http\Request;

class SaranaController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $saranas = Sarana::where('sekolah_id', $sekolah_id)->latest()->paginate(10);
        return view('admin.sarana.index', compact('saranas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'condition' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'lokasi' => 'nullable|in:Outdoor,Indoor',
            'jenis' => 'nullable|in:Edukasi,Permainan',
        ]);

        Sarana::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'name' => $request->name,
            'condition' => $request->condition,
            'quantity' => $request->quantity,
            'lokasi' => $request->lokasi,
            'jenis' => $request->jenis,
        ]);

        return redirect()->route('admin.sarana.index')->with('success', 'Data Sarana berhasil ditambahkan.');
    }

    public function update(Request $request, Sarana $sarana)
    {
        abort_if($sarana->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'condition' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'lokasi' => 'nullable|in:Outdoor,Indoor',
            'jenis' => 'nullable|in:Edukasi,Permainan',
        ]);

        $sarana->update([
            'name' => $request->name,
            'condition' => $request->condition,
            'quantity' => $request->quantity,
            'lokasi' => $request->lokasi,
            'jenis' => $request->jenis,
        ]);

        return redirect()->route('admin.sarana.index')->with('success', 'Data Sarana berhasil diperbarui.');
    }

    public function destroy(Sarana $sarana)
    {
        abort_if($sarana->sekolah_id !== auth()->user()->sekolah_id, 403);
        $sarana->delete();
        return redirect()->route('admin.sarana.index')->with('success', 'Data Sarana berhasil dihapus.');
    }
}
