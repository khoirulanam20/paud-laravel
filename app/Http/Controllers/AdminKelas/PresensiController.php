<?php

namespace App\Http\Controllers\AdminKelas;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $kelas_id = auth()->user()->kelas_id;
        $sekolah_id = auth()->user()->sekolah_id;

        $tanggalInput = $request->query('tanggal', now()->format('Y-m-d'));
        try {
            $tanggal = Carbon::parse($tanggalInput)->format('Y-m-d');
        } catch (\Throwable) {
            $tanggal = now()->format('Y-m-d');
        }

        $anaks = Anak::where('kelas_id', $kelas_id)->with('user')->orderBy('name')->get();

        $presensiByAnak = Presensi::where('kelas_id', $kelas_id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('anak_id');

        $hadirCount = $presensiByAnak->where('hadir', true)->count();

        return view('adminkelas.presensi.index', compact('anaks', 'presensiByAnak', 'tanggal', 'hadirCount'));
    }

    public function store(Request $request)
    {
        $kelas_id = auth()->user()->kelas_id;
        $sekolah_id = auth()->user()->sekolah_id;

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'hadir' => ['nullable', 'array'],
            'hadir.*' => ['integer', 'exists:anaks,id'],
        ]);

        $anakIds = Anak::where('kelas_id', $kelas_id)->pluck('id')->all();
        $hadirIds = array_values(array_unique(array_map('intval', $validated['hadir'] ?? [])));
        $hadirIds = array_values(array_intersect($hadirIds, $anakIds));

        foreach ($anakIds as $anakId) {
            Presensi::updateOrCreate(
                [
                    'kelas_id' => $kelas_id,
                    'anak_id' => $anakId,
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'sekolah_id' => $sekolah_id,
                    'hadir' => in_array((int) $anakId, $hadirIds, true),
                    'status' => in_array((int) $anakId, $hadirIds, true) ? 'hadir' : 'alpha',
                ]
            );
        }

        return redirect()
            ->route('adminkelas.presensi.index', ['tanggal' => $validated['tanggal']])
            ->with('success', 'Presensi tanggal '.Carbon::parse($validated['tanggal'])->translatedFormat('d M Y').' berhasil disimpan.');
    }
}
