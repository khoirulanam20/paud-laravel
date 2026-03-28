<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Pengajar;
use App\Support\KegiatanCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        [$year, $month] = KegiatanCalendar::resolveYearMonth($request);
        [$from, $to] = KegiatanCalendar::dateRangeForCalendar($year, $month);

        $query = Kegiatan::query()
            ->where('sekolah_id', $sekolah_id)
            ->with('pengajar')
            ->whereBetween('date', [$from, $to]);

        if ($request->filled('pengajar_id')) {
            $pid = $request->integer('pengajar_id');
            if (Pengajar::where('id', $pid)->where('sekolah_id', $sekolah_id)->exists()) {
                $query->where('pengajar_id', $pid);
            }
        }

        $kegiatans = $query->orderBy('date')->orderBy('id')->get();
        $calendarEvents = $kegiatans->map(fn (Kegiatan $k) => KegiatanCalendar::toAdminEvent($k))->values()->all();

        $pengajars = Pengajar::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        return view('admin.kegiatan.index', compact('calendarEvents', 'year', 'month', 'pengajars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengajar_id' => 'required|exists:pengajars,id',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $pengajar = Pengajar::findOrFail($request->pengajar_id);
        abort_if($pengajar->sekolah_id !== auth()->user()->sekolah_id, 403);

        $data = [
            'sekolah_id' => auth()->user()->sekolah_id,
            'pengajar_id' => $request->pengajar_id,
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('kegiatan', 'public');
            $data['photo'] = $path;
        }

        Kegiatan::create($data);

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        abort_if($kegiatan->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'pengajar_id' => 'required|exists:pengajars,id',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $pengajar = Pengajar::findOrFail($request->pengajar_id);
        abort_if($pengajar->sekolah_id !== auth()->user()->sekolah_id, 403);

        $data = [
            'pengajar_id' => $request->pengajar_id,
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

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        abort_if($kegiatan->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($kegiatan->photo) {
            Storage::disk('public')->delete($kegiatan->photo);
        }
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
