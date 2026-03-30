<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('Admin Kelas')) {
            return redirect()->route('adminkelas.presensi.index', $request->only('tanggal'));
        }

        $pengajar = \App\Models\Pengajar::where('user_id', auth()->id())->firstOrFail();
        $sekolah_id = $pengajar->sekolah_id;
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();

        $tanggalInput = $request->query('tanggal', now()->format('Y-m-d'));
        try {
            $tanggal = Carbon::parse($tanggalInput)->format('Y-m-d');
        } catch (\Throwable) {
            $tanggal = now()->format('Y-m-d');
        }

        $filterKelasId = $request->query('filter_kelas_id');
        $queryAnak = Anak::query()->where('sekolah_id', $sekolah_id);
        if ($filterKelasId && in_array((int)$filterKelasId, $kelasIds)) {
            $queryAnak->where('kelas_id', $filterKelasId);
        } else {
            $queryAnak->whereIn('kelas_id', $kelasIds);
        }

        $anaks = $queryAnak->with('user')->orderBy('name')->get();

        $presensiByAnak = Presensi::where('sekolah_id', $sekolah_id)
            ->whereIn('anak_id', $anaks->pluck('id'))
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('anak_id');

        $hadirCount = $presensiByAnak->where('hadir', true)->count();
        $kelas = $pengajar->kelas()->orderBy('name')->get();

        return view('pengajar.presensi.index', compact('anaks', 'presensiByAnak', 'tanggal', 'hadirCount', 'kelas', 'filterKelasId'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->hasRole('Admin Kelas')) {
            abort(403);
        }

        $pengajar = \App\Models\Pengajar::where('user_id', auth()->id())->firstOrFail();
        $sekolah_id = $pengajar->sekolah_id;
        $kelasIds = $pengajar->kelas->pluck('id')->toArray();

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'hadir' => ['nullable', 'array'],
            'hadir.*' => ['integer', 'exists:anaks,id'],
            'filter_kelas_id' => ['nullable', 'integer'],
        ]);

        $queryAnak = Anak::where('sekolah_id', $sekolah_id)->whereIn('kelas_id', $kelasIds);
        if ($request->filled('filter_kelas_id')) {
            $queryAnak->where('kelas_id', $request->filter_kelas_id);
        }
        $anakIds = $queryAnak->pluck('id')->all();
        $hadirIds = array_values(array_unique(array_map('intval', $validated['hadir'] ?? [])));
        $hadirIds = array_values(array_intersect($hadirIds, $anakIds));

        foreach ($anakIds as $anakId) {
            Presensi::updateOrCreate(
                [
                    'sekolah_id' => $sekolah_id,
                    'anak_id' => $anakId,
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'hadir' => in_array((int) $anakId, $hadirIds, true),
                    'status' => in_array((int) $anakId, $hadirIds, true) ? 'hadir' : 'alpha',
                ]
            );
        }

        return redirect()
            ->route('pengajar.presensi.index', array_filter(['tanggal' => $validated['tanggal'], 'filter_kelas_id' => $request->filter_kelas_id]))
            ->with('success', 'Presensi tanggal '.Carbon::parse($validated['tanggal'])->translatedFormat('d M Y').' berhasil disimpan.');
    }
}
