<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Pengajar;
use App\Support\KegiatanCalendar;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        [$year, $month] = KegiatanCalendar::resolveYearMonth($request);
        [$from, $to] = KegiatanCalendar::dateRangeForCalendar($year, $month);

        $query = Kegiatan::query()
            ->where('sekolah_id', $sekolah_id)
            ->with(['pengajar', 'kelas'])
            ->whereBetween('date', [$from, $to]);

        if ($request->filled('pengajar_id')) {
            $pid = $request->integer('pengajar_id');
            $query->where('pengajar_id', $pid);
        }

        $kelas = \App\Models\Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        if ($request->filled('kelas_id')) {
            $kid = $request->integer('kelas_id');
            $query->where('kelas_id', $kid);
        }

        $kegiatans = $query->orderBy('date')->orderBy('id')->get();
        $calendarEvents = $kegiatans->map(fn (Kegiatan $k) => KegiatanCalendar::toAdminEvent($k))->values()->all();

        $pengajars = Pengajar::where('sekolah_id', $sekolah_id)->orderBy('name')->get();
        $matrikulasis = \App\Models\Matrikulasi::where('sekolah_id', $sekolah_id)->orderBy('aspek')->get();

        return view('admin.kegiatan.index', compact('calendarEvents', 'year', 'month', 'pengajars', 'kelas', 'matrikulasis'));
    }

    public function store(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
            'pengajar_id' => 'required|exists:pengajars,id',
            'matrikulasi_ids' => 'required|array|min:1',
            'matrikulasi_ids.*' => 'exists:matrikulasis,id',
        ]);

        $data = [
            'sekolah_id' => $sekolah_id,
            'pengajar_id' => $request->pengajar_id,
            'kelas_id' => $request->kelas_id,
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $file) {
                $photos[] = $file->store('kegiatan', 'public');
            }
            $data['photos'] = $photos;
        }

        $kegiatan = Kegiatan::create($data);
        $kegiatan->matrikulasis()->sync($request->matrikulasi_ids);

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dicatat.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        abort_if($kegiatan->sekolah_id !== $sekolah_id, 403);

        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
            'pengajar_id' => 'required|exists:pengajars,id',
            'matrikulasi_ids' => 'required|array|min:1',
            'matrikulasi_ids.*' => 'exists:matrikulasis,id',
        ]);

        $data = [
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
            'kelas_id' => $request->kelas_id,
            'pengajar_id' => $request->pengajar_id,
        ];

        $currentPhotos = $kegiatan->photos ?? [];
        if ($request->filled('delete_photos')) {
            foreach ($request->delete_photos as $p) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($p);
                $currentPhotos = array_values(array_filter($currentPhotos, fn($path) => $path !== $p));
            }
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $currentPhotos[] = $file->store('kegiatan', 'public');
            }
        }
        $data['photos'] = $currentPhotos;

        $kegiatan->update($data);
        $kegiatan->matrikulasis()->sync($request->matrikulasi_ids ?? []);

        return redirect()->route('admin.kegiatan.index', ['year' => explode('-', $request->date)[0], 'month' => explode('-', $request->date)[1]])->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        abort_if($kegiatan->sekolah_id !== $sekolah_id, 403);

        if ($kegiatan->photos) {
            foreach ($kegiatan->photos as $p) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($p);
            }
        }
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
