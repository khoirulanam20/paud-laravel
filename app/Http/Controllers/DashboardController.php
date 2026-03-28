<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Cashflow;
use App\Models\Kegiatan;
use App\Models\KritikSaran;
use App\Models\MenuMakanan;
use App\Models\Pencapaian;
use App\Models\Pengajar;
use App\Models\Sarana;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->hasRole('Lembaga')) {
            $data['totalSekolah'] = Sekolah::where('lembaga_id', $user->lembaga_id)->count();
            $sekolahIds = Sekolah::where('lembaga_id', $user->lembaga_id)->pluck('id');
            $data['totalAdmin'] = User::role('Admin Sekolah')->whereIn('sekolah_id', $sekolahIds)->count();
            $data['totalKritikSaran'] = KritikSaran::whereIn('sekolah_id', $sekolahIds)->count();
            $data['recentFeedback'] = KritikSaran::whereIn('sekolah_id', $sekolahIds)->latest()->limit(5)->get();

        } elseif ($user->hasRole('Admin Sekolah')) {
            $sekolahId = $user->sekolah_id;
            $data['totalAnak'] = Anak::where('sekolah_id', $sekolahId)->count();
            $data['totalPengajar'] = Pengajar::where('sekolah_id', $sekolahId)->count();
            $data['totalSarana'] = Sarana::where('sekolah_id', $sekolahId)->count();

            $uangMasuk = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'in')->sum('amount');
            $uangKeluar = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'out')->sum('amount');
            $data['saldoKas'] = $uangMasuk - $uangKeluar;

            $data['kegiatanHariIni'] = Kegiatan::where('sekolah_id', $sekolahId)->whereDate('date', Carbon::today())->count();
            $data['menuHariIni'] = MenuMakanan::where('sekolah_id', $sekolahId)->whereDate('date', Carbon::today())->first();

        } elseif ($user->hasRole('Pengajar')) {
            $pengajar = Pengajar::where('user_id', $user->id)->first();
            if ($pengajar) {
                $sekolahId = $pengajar->sekolah_id;
                $data['totalAnakSekolah'] = Anak::where('sekolah_id', $sekolahId)->count();
                $data['kegiatanSayaHariIni'] = Kegiatan::where('pengajar_id', $pengajar->id)->whereDate('date', Carbon::today())->count();
                $data['totalKegiatanSaya'] = Kegiatan::where('pengajar_id', $pengajar->id)->count();
                $data['totalEvaluasiSaya'] = Pencapaian::where('pengajar_id', $pengajar->id)->count();
            }

        } elseif ($user->hasRole('Orang Tua')) {
            $sekolahId = $user->sekolah_id;
            $data['anaks'] = Anak::where('user_id', $user->id)->get();
            $data['anakIds'] = $data['anaks']->pluck('id');

            $data['menuHariIni'] = MenuMakanan::where('sekolah_id', $sekolahId)->whereDate('date', Carbon::today())->first();
            $data['kegiatanTerbaru'] = Kegiatan::where('sekolah_id', $sekolahId)->latest('date')->take(3)->get();
            $data['pencapaianTerbaru'] = Pencapaian::whereIn('anak_id', $data['anakIds'])
                ->with(['matrikulasi', 'kegiatan', 'anak'])
                ->latest()
                ->take(3)
                ->get();
        }

        return view('dashboard', $data);
    }
}
