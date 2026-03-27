<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;

        $tanggalInput = $request->query('tanggal', now()->format('Y-m-d'));
        try {
            $tanggal = Carbon::parse($tanggalInput)->format('Y-m-d');
        } catch (\Throwable) {
            $tanggal = now()->format('Y-m-d');
        }

        $anaks = Anak::where('sekolah_id', $sekolah_id)->with('user')->orderBy('name')->get();

        $presensiByAnak = Presensi::where('sekolah_id', $sekolah_id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('anak_id');

        $hadirCount = $presensiByAnak->where('hadir', true)->count();

        return view('admin.presensi.index', compact('anaks', 'presensiByAnak', 'tanggal', 'hadirCount'));
    }

    public function store(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'hadir' => ['nullable', 'array'],
            'hadir.*' => ['integer', 'exists:anaks,id'],
        ]);

        $anakIds = Anak::where('sekolah_id', $sekolah_id)->pluck('id')->all();
        $hadirIds = array_values(array_unique(array_map('intval', $validated['hadir'] ?? [])));
        $hadirIds = array_values(array_intersect($hadirIds, $anakIds));

        foreach ($anakIds as $anakId) {
            Presensi::updateOrCreate(
                [
                    'sekolah_id' => $sekolah_id,
                    'anak_id' => $anakId,
                    'tanggal' => $validated['tanggal'],
                ],
                ['hadir' => in_array((int) $anakId, $hadirIds, true)]
            );
        }

        return redirect()
            ->route('admin.presensi.index', ['tanggal' => $validated['tanggal']])
            ->with('success', 'Presensi tanggal '.Carbon::parse($validated['tanggal'])->translatedFormat('d M Y').' berhasil disimpan.');
    }
}
