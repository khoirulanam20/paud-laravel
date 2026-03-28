<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Matrikulasi;
use App\Models\Pencapaian;
use App\Models\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PencapaianController extends Controller
{
    private function getPengajar()
    {
        return Pengajar::where('user_id', auth()->id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $pengajar = $this->getPengajar();
        $sekolah_id = $pengajar->sekolah_id;

        $tanggalInput = $request->query('tanggal', date('Y-m-d'));
        try {
            $tanggal = \Carbon\Carbon::parse($tanggalInput)->format('Y-m-d');
        } catch (\Throwable) {
            $tanggal = date('Y-m-d');
        }

        $pencapaians = Pencapaian::with(['anak', 'kegiatan.matrikulasis'])
            ->where('pengajar_id', $pengajar->id)
            ->whereDate('created_at', $tanggal)
            ->latest()
            ->paginate(15);

        $anaks = Anak::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        // Load kegiatans with their linked matrikulasis for the cascade select
        $kegiatans = Kegiatan::where('pengajar_id', $pengajar->id)
            ->with('matrikulasis')
            ->orderBy('date', 'desc')
            ->get();

        return view('pengajar.pencapaian.index', compact('pencapaians', 'anaks', 'kegiatans', 'tanggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anak_id'     => 'required|exists:anaks,id',
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'score'       => ['required', 'string', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
            'feedback'    => 'required|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        $pengajar = $this->getPengajar();
        $anak = Anak::findOrFail($request->anak_id);
        abort_if($anak->sekolah_id !== $pengajar->sekolah_id, 403);

        $photoPath = $request->file('photo') ? $request->file('photo')->store('pencapaian', 'public') : null;

        Pencapaian::create([
            'anak_id'     => $request->anak_id,
            'kegiatan_id' => $request->kegiatan_id,
            'pengajar_id' => $pengajar->id,
            'score'       => $request->score,
            'feedback'    => $request->feedback,
            'photo'       => $photoPath,
        ]);

        return redirect()->route('pengajar.pencapaian.index')->with('success', 'Pencapaian anak berhasil dicatat.');
    }

    public function update(Request $request, Pencapaian $pencapaian)
    {
        $pengajar = $this->getPengajar();
        abort_if($pencapaian->pengajar_id !== $pengajar->id, 403);

        $request->validate([
            'anak_id'     => 'required|exists:anaks,id',
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'score'       => ['required', 'string', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
            'feedback'    => 'required|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only('anak_id', 'kegiatan_id', 'score', 'feedback');
        if ($request->hasFile('photo')) {
            if ($pencapaian->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pencapaian->photo);
            }
            $data['photo'] = $request->file('photo')->store('pencapaian', 'public');
        }
        $pencapaian->update($data);

        return redirect()->route('pengajar.pencapaian.index')->with('success', 'Pencapaian berhasil diperbarui.');
    }

    public function destroy(Pencapaian $pencapaian)
    {
        $pengajar = $this->getPengajar();
        abort_if($pencapaian->pengajar_id !== $pengajar->id, 403);
        $pencapaian->delete();
        return redirect()->route('pengajar.pencapaian.index')->with('success', 'Catatan pencapaian berhasil dihapus.');
    }
}
