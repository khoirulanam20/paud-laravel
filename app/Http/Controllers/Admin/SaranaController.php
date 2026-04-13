<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sarana;
use Illuminate\Http\Request;
use App\Http\Traits\CanUploadImage;

class SaranaController extends Controller
{
    use CanUploadImage;
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
            'jenis' => 'nullable|in:Edukasi,Permainan,Sarpras,ATK',
        ]);

        $data = [
            'sekolah_id' => auth()->user()->sekolah_id,
            'name' => $request->name,
            'condition' => $request->condition,
            'quantity' => $request->quantity,
            'lokasi' => $request->lokasi,
            'jenis' => $request->jenis,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadImage($request->file('photo'), 'sarana');
        }

        Sarana::create($data);

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
            'jenis' => 'nullable|in:Edukasi,Permainan,Sarpras,ATK',
        ]);

        $dataArr = [
            'name' => $request->name,
            'condition' => $request->condition,
            'quantity' => $request->quantity,
            'lokasi' => $request->lokasi,
            'jenis' => $request->jenis,
        ];

        if ($request->hasFile('photo')) {
            if ($sarana->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($sarana->photo);
            }
            $dataArr['photo'] = $this->uploadImage($request->file('photo'), 'sarana');
        }

        $sarana->update($dataArr);

        return redirect()->route('admin.sarana.index')->with('success', 'Data Sarana berhasil diperbarui.');
    }

    public function destroy(Sarana $sarana)
    {
        abort_if($sarana->sekolah_id !== auth()->user()->sekolah_id, 403);
        if ($sarana->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($sarana->photo);
        }
        $sarana->delete();
        return redirect()->route('admin.sarana.index')->with('success', 'Data Sarana berhasil dihapus.');
    }
}
