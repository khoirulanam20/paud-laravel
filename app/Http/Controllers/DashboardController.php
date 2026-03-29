<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Cashflow;
use App\Models\Kegiatan;
use App\Models\Kelas;
use App\Models\KritikSaran;
use App\Models\MenuMakanan;
use App\Models\Pencapaian;
use App\Models\Pengajar;
use App\Models\Presensi;
use App\Models\Sarana;
use App\Models\Sekolah;
use App\Models\User;
use App\Support\PresensiPeriodeFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $data = [];

        if ($user->hasRole('Lembaga')) {
            $data['totalSekolah'] = Sekolah::where('lembaga_id', $user->lembaga_id)->count();
            $sekolahIds = Sekolah::where('lembaga_id', $user->lembaga_id)->pluck('id');
            $data['totalAdmin'] = User::role('Admin Sekolah')->whereIn('sekolah_id', $sekolahIds)->count();
            $data['totalKritikSaran'] = KritikSaran::whereIn('sekolah_id', $sekolahIds)->count();
            $data['recentFeedback'] = KritikSaran::whereIn('sekolah_id', $sekolahIds)->latest()->limit(5)->get();
        }

        if ($user->hasRole('Admin Sekolah')) {
            $sekolahId = $user->sekolah_id;
            $data['totalAnak'] = Anak::where('sekolah_id', $sekolahId)->count();
            $data['totalPengajar'] = Pengajar::where('sekolah_id', $sekolahId)->count();
            $data['totalSarana'] = Sarana::where('sekolah_id', $sekolahId)->count();

            $uangMasuk = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'in')->sum('amount');
            $uangKeluar = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'out')->sum('amount');
            $data['saldoKas'] = $uangMasuk - $uangKeluar;

            $data['kegiatanHariIni'] = Kegiatan::where('sekolah_id', $sekolahId)->whereDate('date', Carbon::today())->count();
            $data['menuHariIni'] = MenuMakanan::where('sekolah_id', $sekolahId)->whereDate('date', Carbon::today())->first();
        }

        if ($user->hasRole('Admin Kelas') && $user->kelas_id) {
            $data['kelasWali'] = Kelas::where('id', $user->kelas_id)
                ->where('sekolah_id', $user->sekolah_id)
                ->first();
            $data['kelasAnakCount'] = Anak::where('kelas_id', $user->kelas_id)->count();
        }

        if ($user->hasRole('Pengajar')) {
            $pengajar = Pengajar::where('user_id', $user->id)->first();
            if ($pengajar) {
                $sekolahId = $pengajar->sekolah_id;
                if ($user->kelas_id) {
                    $data['totalAnakSekolah'] = Anak::where('kelas_id', $user->kelas_id)->count();
                    $data['dashboardAnakLabel'] = 'Siswa di kelas';
                } else {
                    $data['totalAnakSekolah'] = Anak::where('sekolah_id', $sekolahId)->count();
                    $data['dashboardAnakLabel'] = 'Siswa di sekolah';
                }
                $data['kegiatanSayaHariIni'] = Kegiatan::where('pengajar_id', $pengajar->id)->whereDate('date', Carbon::today())->count();
                $data['totalKegiatanSaya'] = Kegiatan::where('pengajar_id', $pengajar->id)->count();
                $data['totalEvaluasiSaya'] = Pencapaian::where('pengajar_id', $pengajar->id)->count();
            }
        }

        if ($user->hasRole('Orang Tua')) {
            $sekolahId = $user->sekolah_id;
            $data['anaks'] = Anak::where('user_id', $user->id)
                ->where('sekolah_id', $sekolahId)
                ->orderBy('name')
                ->get();
            $data['anakIds'] = $data['anaks']->pluck('id');

            $data['menuHariIni'] = MenuMakanan::where('sekolah_id', $sekolahId)->whereDate('date', Carbon::today())->first();

            // Sama seperti halaman Jurnal Kegiatan: hanya kegiatan yang punya pencapaian untuk anak ortu di sekolah ini.
            if ($data['anakIds']->isEmpty()) {
                $data['kegiatanTerbaru'] = collect();
            } else {
                $data['kegiatanTerbaru'] = Kegiatan::query()
                    ->where('sekolah_id', $sekolahId)
                    ->whereHas('pencapaians', fn ($q) => $q->whereIn('anak_id', $data['anakIds']))
                    ->latest('date')
                    ->latest('id')
                    ->take(3)
                    ->get();
            }

            if ($data['anakIds']->isEmpty()) {
                $data['pencapaianTerbaru'] = collect();
            } else {
                $data['pencapaianTerbaru'] = Pencapaian::whereIn('anak_id', $data['anakIds'])
                    ->with(['matrikulasi', 'kegiatan', 'anak'])
                    ->latest()
                    ->take(3)
                    ->get();
            }

            $data['presensiFilter'] = PresensiPeriodeFilter::resolve($request);
            if ($data['anakIds']->isEmpty()) {
                $data['presensiHadirPerAnak'] = collect();
            } else {
                $data['presensiHadirPerAnak'] = Presensi::query()
                    ->whereIn('anak_id', $data['anakIds'])
                    ->where('hadir', true)
                    ->whereBetween('tanggal', [$data['presensiFilter']['from'], $data['presensiFilter']['to']])
                    ->selectRaw('anak_id, count(*) as total')
                    ->groupBy('anak_id')
                    ->pluck('total', 'anak_id');
            }
        }

        return view('dashboard', $data);
    }
}
