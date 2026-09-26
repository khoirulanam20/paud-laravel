<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\AkuntansiSetting;
use Illuminate\Http\Request;

class AkuntansiSettingController extends Controller
{
    public function index()
    {
        $sekolahId = auth()->user()->sekolah_id;
        $setting = AkuntansiSetting::forSekolah($sekolahId);

        $jenisAkunAset = $setting->jenisUntukAkunAset();
        $jenisOptions = Akun::where('sekolah_id', $sekolahId)
            ->whereNotNull('jenis')
            ->distinct()
            ->orderBy('jenis')
            ->pluck('jenis')
            ->all();
        foreach ($jenisAkunAset as $jenis) {
            if (! in_array($jenis, $jenisOptions, true)) {
                $jenisOptions[] = $jenis;
            }
        }
        sort($jenisOptions);

        $akunAset = Akun::where('sekolah_id', $sekolahId)->where('is_aktif', true)->whereIn('jenis', $jenisAkunAset)->orderBy('kode')->get();
        $akunPendapatan = Akun::where('sekolah_id', $sekolahId)->where('is_aktif', true)->where('jenis', \App\Support\JenisAkun::PENDAPATAN)->orderBy('kode')->get();
        $akunBeban = Akun::where('sekolah_id', $sekolahId)->where('is_aktif', true)->where('jenis', \App\Support\JenisAkun::BEBAN)->orderBy('kode')->get();
        $akunPiutangOptions = Akun::where('sekolah_id', $sekolahId)->where('is_aktif', true)->where('jenis', \App\Support\JenisAkun::ASSETS)->orderBy('kode')->get();

        return view('admin.akuntansi-setting.index', compact(
            'setting', 'akunAset', 'akunPendapatan', 'akunBeban', 'akunPiutangOptions', 'jenisOptions', 'jenisAkunAset'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'metode_pencatatan' => 'required|in:cash,accrual',
            'akun_kas_id' => 'required|exists:akuns,id',
            'akun_piutang_id' => 'required|exists:akuns,id',
            'akun_pendapatan_id' => 'required|exists:akuns,id',
            'akun_untuk_in' => 'required|exists:akuns,id',
            'akun_untuk_out' => 'required|exists:akuns,id',
            'jenis_akun_aset' => 'required|array|min:1',
            'jenis_akun_aset.*' => 'string|max:50',
        ]);

        $sekolahId = auth()->user()->sekolah_id;
        $setting = AkuntansiSetting::forSekolah($sekolahId);

        $setting->update($request->only([
            'metode_pencatatan', 'akun_kas_id', 'akun_piutang_id',
            'akun_pendapatan_id', 'akun_untuk_in', 'akun_untuk_out',
        ]) + [
            'jenis_akun_aset' => array_values(array_unique(array_map('trim', $request->input('jenis_akun_aset', [])))),
        ]);

        return redirect()->route('admin.akuntansi-setting.index')->with('success', 'Pengaturan akuntansi berhasil disimpan.');
    }
}
