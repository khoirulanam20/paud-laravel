<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Support\PresensiPeriodeFilter;
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

        $filterKelasId = $request->query('filter_kelas_id');
        $anaksQuery = Anak::where('sekolah_id', $sekolah_id)->with(['user', 'kelas'])->orderBy('name');
        if ($filterKelasId) {
            $anaksQuery->where('kelas_id', $filterKelasId);
        }
        $anaks = $anaksQuery->get();

        $presensiByAnak = Presensi::where('sekolah_id', $sekolah_id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('anak_id');

        $hadirCount = $presensiByAnak->where('hadir', true)->count();
        $kelas = Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        // Rekap bulanan: hadir count for the month of the selected date
        $startOfMonth = Carbon::parse($tanggal)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::parse($tanggal)->endOfMonth()->toDateString();
        $hadirBulanan = Presensi::where('sekolah_id', $sekolah_id)
            ->whereIn('anak_id', $anaks->pluck('id'))
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('hadir', true)
            ->selectRaw('anak_id, count(*) as total')
            ->groupBy('anak_id')
            ->pluck('total', 'anak_id');

        return view('admin.presensi.index', compact('anaks', 'presensiByAnak', 'tanggal', 'hadirCount', 'kelas', 'hadirBulanan', 'filterKelasId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'hadir' => ['nullable', 'array'],
            'hadir.*' => ['integer', 'exists:anaks,id'],
            'filter_kelas_id' => ['nullable', 'integer'],
        ]);

        $queryAnak = Anak::where('sekolah_id', $sekolah_id);
        if ($request->filled('filter_kelas_id')) {
            $queryAnak->where('kelas_id', $request->filter_kelas_id);
        }
        $anakIds = $queryAnak->pluck('id')->all();

        $hadirIds = array_values(array_unique(array_map('intval', $validated['hadir'] ?? [])));
        $hadirIds = array_values(array_intersect($hadirIds, $anakIds));

        foreach ($anakIds as $anakId) {
            $anak = Anak::find($anakId);
            Presensi::updateOrCreate(
                [
                    'sekolah_id' => $sekolah_id,
                    'anak_id' => $anakId,
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'kelas_id' => $anak->kelas_id,
                    'hadir' => in_array((int) $anakId, $hadirIds, true),
                    'status' => in_array((int) $anakId, $hadirIds, true) ? 'hadir' : 'alpha',
                ]
            );
        }

        return redirect()
            ->route('admin.presensi.index', array_filter(['tanggal' => $validated['tanggal'], 'filter_kelas_id' => $request->filter_kelas_id]))
            ->with('success', 'Presensi tanggal '.Carbon::parse($validated['tanggal'])->translatedFormat('d M Y').' berhasil disimpan.');
    }

    public function rekap(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;

        $anaksQuery = Anak::where('sekolah_id', $sekolah_id)->with(['user', 'kelas'])->orderBy('name');
        if ($request->filled('kelas_id')) {
            $anaksQuery->where('kelas_id', $request->kelas_id);
        }
        $anaks = $anaksQuery->get();

        $presensiFilter = PresensiPeriodeFilter::resolve($request);
        $hadirPeriode = Presensi::where('sekolah_id', $sekolah_id)
            ->whereBetween('tanggal', [$presensiFilter['from'], $presensiFilter['to']])
            ->where('hadir', true)
            ->selectRaw('anak_id, count(*) as total')
            ->groupBy('anak_id')
            ->pluck('total', 'anak_id');

        $kelas = Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        return view('admin.presensi.rekap', compact('anaks', 'hadirPeriode', 'presensiFilter', 'kelas'));
    }
}
