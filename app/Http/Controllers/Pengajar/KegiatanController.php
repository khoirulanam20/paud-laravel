<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    private function getPengajar()
    {
        return Pengajar::where('user_id', auth()->id())->firstOrFail();
    }

    public function index()
    {
        $pengajar = $this->getPengajar();
        $kegiatans = Kegiatan::where('pengajar_id', $pengajar->id)->orderBy('date', 'desc')->paginate(10);
        
        return view('pengajar.kegiatan.index', compact('kegiatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $pengajar = $this->getPengajar();

        $data = [
            'sekolah_id' => $pengajar->sekolah_id,
            'pengajar_id' => $pengajar->id,
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('kegiatan', 'public');
            $data['photo'] = $path;
        }

        Kegiatan::create($data);

        return redirect()->route('pengajar.kegiatan.index')->with('success', 'Kegiatan berhasil dicatat.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $pengajar = $this->getPengajar();
        abort_if($kegiatan->pengajar_id !== $pengajar->id, 403);

        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('photo')) {
            if ($kegiatan->photo) {
                Storage::disk('public')->delete($kegiatan->photo);
            }
            $path = $request->file('photo')->store('kegiatan', 'public');
            $data['photo'] = $path;
        }

        $kegiatan->update($data);

        return redirect()->route('pengajar.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $pengajar = $this->getPengajar();
        abort_if($kegiatan->pengajar_id !== $pengajar->id, 403);
        
        if ($kegiatan->photo) {
            Storage::disk('public')->delete($kegiatan->photo);
        }
        $kegiatan->delete();
        
        return redirect()->route('pengajar.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
