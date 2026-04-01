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

        if ($user->hasRole('Admin Kelas')) {
            $pengajar = Pengajar::where('user_id', $user->id)->first();
            if ($pengajar) {
                $kelasIds = $pengajar->kelas->pluck('id')->toArray();
                $data['kelasWaliCount'] = count($kelasIds);
                $data['kelasAnakCount'] = Anak::whereIn('kelas_id', $kelasIds)->count();
            }
        }

        if ($user->hasRole('Pengajar')) {
            $pengajar = Pengajar::where('user_id', $user->id)->first();
            if ($pengajar) {
                $sekolahId = $pengajar->sekolah_id;
                $kelasIds = $pengajar->kelas->pluck('id')->toArray();
                
                if (!empty($kelasIds)) {
                    $data['totalAnakSekolah'] = Anak::whereIn('kelas_id', $kelasIds)->count();
                    $data['dashboardAnakLabel'] = 'Siswa di kelasku';
                    
                    $data['kegiatanSayaHariIni'] = Kegiatan::whereIn('kelas_id', $kelasIds)->whereDate('date', Carbon::today())->count();
                    $data['totalKegiatanSaya'] = Kegiatan::whereIn('kelas_id', $kelasIds)->count();
                    $data['totalEvaluasiSaya'] = Pencapaian::whereHas('anak', fn($q) => $q->whereIn('kelas_id', $kelasIds))->count();
                } else {
                    $data['totalAnakSekolah'] = Anak::where('sekolah_id', $sekolahId)->count();
                    $data['dashboardAnakLabel'] = 'Siswa di sekolah';
                    $data['kegiatanSayaHariIni'] = 0;
                    $data['totalKegiatanSaya'] = 0;
                    $data['totalEvaluasiSaya'] = 0;
                }
            }
        }

        if ($user->hasRole('Orang Tua')) {
            $sekolahId = $user->sekolah_id;
            $data['anaks'] = Anak::where('user_id', $user->id)
                ->where('sekolah_id', $sekolahId)
                ->orderBy('name')
                ->get();
            $data['anakIds'] = $data['anaks']->pluck('id');

            $data['menuHariIni'] = MenuMakanan::where('sekolah_id', $sekolahId)
                ->whereDate('date', Carbon::today())
                ->withCount(['votes as likes_count' => fn($q) => $q->where('vote_type', 'like')])
                ->withCount(['votes as dislikes_count' => fn($q) => $q->where('vote_type', 'dislike')])
                ->first();

            $data['myVote'] = null;
            if ($data['menuHariIni']) {
                $data['myVote'] = MenuMakananVote::where('menu_makanan_id', $data['menuHariIni']->id)
                    ->where('user_id', $user->id)
                    ->first();
            }

            // Combine Activities and Achievements into a single feed
            $feeds = collect();

            if ($data['anakIds']->isNotEmpty()) {
                $kegiatans = Kegiatan::query()
                    ->where('sekolah_id', $sekolahId)
                    ->whereDate('date', Carbon::today())
                    ->whereHas('pencapaians', fn ($q) => $q->whereIn('anak_id', $data['anakIds']))
                    ->with(['pengajar', 'pencapaians' => fn($q) => $q->whereIn('anak_id', $data['anakIds'])->with('matrikulasi')])
                    ->latest('id')
                    ->get();

                foreach ($kegiatans as $keg) {
                    $feeds->push([
                        'type' => 'kegiatan',
                        'time' => $keg->created_at,
                        'data' => $keg
                    ]);
                }

                $pencapaians = Pencapaian::whereIn('anak_id', $data['anakIds'])
                    ->with(['matrikulasi', 'kegiatan', 'anak'])
                    ->latest()
                    ->take(5)
                    ->get();

                foreach ($pencapaians as $p) {
                    // Avoid duplicating if it's already linked to a kegiatan in today's list
                    if ($p->kegiatan_id && $kegiatans->contains('id', $p->kegiatan_id)) {
                        continue;
                    }
                    $feeds->push([
                        'type' => 'pencapaian',
                        'time' => $p->created_at,
                        'data' => $p
                    ]);
                }
            }

            $data['dashboardFeed'] = $feeds->sortByDesc('time');

            $data['presensiFilter'] = PresensiPeriodeFilter::resolve($request);
            if ($data['anakIds']->isEmpty()) {
                $data['presensiSummaryPerAnak'] = collect();
            } else {
                $from = Carbon::parse($data['presensiFilter']['from']);
                $to = Carbon::parse($data['presensiFilter']['to']);
                
                // Calculate effective days (weekdays between from and to)
                $effectiveDays = 0;
                $tempDate = clone $from;
                while ($tempDate <= $to) {
                    if (!$tempDate->isWeekend()) {
                        $effectiveDays++;
                    }
                    $tempDate->addDay();
                }
                $data['effectiveDaysCount'] = $effectiveDays;

                $data['presensiSummaryPerAnak'] = $data['anaks']->mapWithKeys(function($anak) use ($from, $to, $effectiveDays) {
                    $hadir = Presensi::where('anak_id', $anak->id)
                        ->where('hadir', true)
                        ->whereBetween('tanggal', [$from->toDateString(), $to->toDateString()])
                        ->count();
                    
                    $tidakHadir = Presensi::where('anak_id', $anak->id)
                        ->where('hadir', false)
                        ->whereBetween('tanggal', [$from->toDateString(), $to->toDateString()])
                        ->count();

                    return [$anak->id => [
                        'hadir' => $hadir,
                        'tidak_hadir' => $tidakHadir,
                        'efektif' => $effectiveDays
                    ]];
                });
            }
        }

        return view('dashboard', $data);
    }
}
