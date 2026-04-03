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

        $anaksQuery = Anak::where('sekolah_id', $sekolah_id)->with(['user', 'kelas'])->orderBy('name');
        if ($request->filled('kelas_id')) {
            $anaksQuery->where('kelas_id', $request->kelas_id);
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

        return view('admin.presensi.index', compact('anaks', 'presensiByAnak', 'tanggal', 'hadirCount', 'kelas', 'hadirBulanan'));
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
