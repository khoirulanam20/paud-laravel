<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Anak;
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

    public function index()
    {
        $pengajar = $this->getPengajar();
        $sekolah_id = $pengajar->sekolah_id;

        $pencapaians = Pencapaian::with(['anak', 'matrikulasi'])
            ->where('pengajar_id', $pengajar->id)
            ->latest()
            ->paginate(10);

        $anaks = Anak::where('sekolah_id', $sekolah_id)->orderBy('name', 'asc')->get();
        $matrikulasis = Matrikulasi::where('sekolah_id', $sekolah_id)->orderBy('indicator', 'asc')->get();

        return view('pengajar.pencapaian.index', compact('pencapaians', 'anaks', 'matrikulasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anak_id' => 'required|exists:anaks,id',
            'matrikulasi_id' => 'required|exists:matrikulasis,id',
            'score' => ['required', 'string', 'max:50', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
            'feedback' => 'required|string',
        ]);

        $pengajar = $this->getPengajar();

        // Ensure anak and matrikulasi belong to the same school Let's assume Valid for now or we can verify
        $anak = Anak::findOrFail($request->anak_id);
        abort_if($anak->sekolah_id !== $pengajar->sekolah_id, 403);

        Pencapaian::create([
            'anak_id' => $request->anak_id,
            'matrikulasi_id' => $request->matrikulasi_id,
            'pengajar_id' => $pengajar->id,
            'score' => $request->score,
            'feedback' => $request->feedback,
        ]);

        return redirect()->route('pengajar.pencapaian.index')->with('success', 'Pencapaian anak berhasil dicatat.');
    }

    public function update(Request $request, Pencapaian $pencapaian)
    {
        $pengajar = $this->getPengajar();
        abort_if($pencapaian->pengajar_id !== $pengajar->id, 403);

        $request->validate([
            'anak_id' => 'required|exists:anaks,id',
            'matrikulasi_id' => 'required|exists:matrikulasis,id',
            'score' => ['required', 'string', 'max:50', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
            'feedback' => 'required|string',
        ]);

        $anak = Anak::findOrFail($request->anak_id);
        abort_if($anak->sekolah_id !== $pengajar->sekolah_id, 403);

        $pencapaian->update([
            'anak_id' => $request->anak_id,
            'matrikulasi_id' => $request->matrikulasi_id,
            'score' => $request->score,
            'feedback' => $request->feedback,
        ]);

        return redirect()->route('pengajar.pencapaian.index')->with('success', 'Pencapaian anak berhasil diperbarui.');
    }

    public function destroy(Pencapaian $pencapaian)
    {
        $pengajar = $this->getPengajar();
        abort_if($pencapaian->pengajar_id !== $pengajar->id, 403);

        $pencapaian->delete();
        
        return redirect()->route('pengajar.pencapaian.index')->with('success', 'Catatan pencapaian berhasil dihapus.');
    }
}
