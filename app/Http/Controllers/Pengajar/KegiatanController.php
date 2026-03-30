<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Matrikulasi;
use App\Models\Pengajar;
use App\Support\KegiatanCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    private function getPengajar()
    {
        return Pengajar::where('user_id', auth()->id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $pengajar = $this->getPengajar();
        $sekolah_id = $pengajar->sekolah_id;

        [$year, $month] = KegiatanCalendar::resolveYearMonth($request);
        [$from, $to] = KegiatanCalendar::dateRangeForCalendar($year, $month);

        $kelas = $pengajar->kelas;
        $kelasIds = $kelas->pluck('id')->toArray();

        $query = Kegiatan::query()
            ->whereIn('kelas_id', $kelasIds)
            ->with(['matrikulasis', 'pencapaians.anak', 'pencapaians.matrikulasi'])
            ->whereBetween('date', [$from, $to]);

        if ($request->filled('kelas_id')) {
            $kid = $request->integer('kelas_id');
            if (in_array($kid, $kelasIds, true)) {
                $query->where('kelas_id', $kid);
            }
        } else {
            $query->whereIn('kelas_id', $kelasIds);
        }

        if ($request->filled('matrikulasi_id')) {
            $mid = $request->integer('matrikulasi_id');
            if (Matrikulasi::where('id', $mid)->where('sekolah_id', $sekolah_id)->exists()) {
                $query->whereHas('matrikulasis', fn ($q) => $q->where('matrikulasis.id', $mid));
            }
        }

        $kegiatans = $query->orderBy('date')->orderBy('id')->get();
        $calendarEvents = $kegiatans->map(fn (Kegiatan $k) => KegiatanCalendar::toPengajarEvent($k))->values()->all();

        $matrikulasis = Matrikulasi::where('sekolah_id', $sekolah_id)->orderBy('aspek')->get();
        $anaks = Anak::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        return view('pengajar.kegiatan.index', compact('calendarEvents', 'year', 'month', 'matrikulasis', 'anaks', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
            'matrikulasi_ids' => 'nullable|array',
            'matrikulasi_ids.*' => 'exists:matrikulasis,id',
        ]);

        $pengajar = $this->getPengajar();
        abort_if(!$pengajar->kelas()->where('kelas.id', $request->kelas_id)->exists(), 403);

        $data = [
            'sekolah_id' => $pengajar->sekolah_id,
            'pengajar_id' => $pengajar->id,
            'kelas_id' => $request->kelas_id,
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('kegiatan', 'public');
        }

        $kegiatan = Kegiatan::create($data);

        if ($request->matrikulasi_ids) {
            $kegiatan->matrikulasis()->sync($request->matrikulasi_ids);
        }

        return redirect()->route('pengajar.kegiatan.index')->with('success', 'Kegiatan berhasil dicatat.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $pengajar = $this->getPengajar();
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();
        abort_if(!in_array($kegiatan->kelas_id, $kelasIds), 403);

        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
            'matrikulasi_ids' => 'nullable|array',
            'matrikulasi_ids.*' => 'exists:matrikulasis,id',
        ]);

        abort_if(!in_array((int)$request->kelas_id, $kelasIds), 403);

        $data = [
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
            'kelas_id' => $request->kelas_id,
        ];

        if ($request->hasFile('photo')) {
            if ($kegiatan->photo) {
                Storage::disk('public')->delete($kegiatan->photo);
            }
            $data['photo'] = $request->file('photo')->store('kegiatan', 'public');
        }

        $kegiatan->update($data);
        $kegiatan->matrikulasis()->sync($request->matrikulasi_ids ?? []);

        return redirect()->route('pengajar.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $pengajar = $this->getPengajar();
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();
        abort_if(!in_array($kegiatan->kelas_id, $kelasIds), 403);

        if ($kegiatan->photo) {
            Storage::disk('public')->delete($kegiatan->photo);
        }
        $kegiatan->delete();

        return redirect()->route('pengajar.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
