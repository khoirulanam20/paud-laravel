<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\AkuntansiSetting;
use App\Models\SumberDana;
use Illuminate\Http\Request;

class AkuntansiSettingController extends Controller
{
    public function index()
    {
        $sekolahId = auth()->user()->sekolah_id;
        $setting = AkuntansiSetting::forSekolah($sekolahId)->load('akunKas');

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
        $sumberDanas = SumberDana::where('sekolah_id', $sekolahId)->aktif()->orderBy('urutan')->get();

        return view('admin.akuntansi-setting.index', compact(
            'setting', 'akunAset', 'akunPendapatan', 'akunBeban', 'akunPiutangOptions', 'jenisOptions', 'jenisAkunAset', 'sumberDanas'
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
            'akun_beban_gaji_id' => 'required|exists:akuns,id',
            'sumber_dana_gaji_id' => 'nullable|exists:sumber_danas,id',
            'jenis_akun_aset' => 'required|array|min:1',
            'jenis_akun_aset.*' => 'string|max:50',
        ]);

        $sekolahId = auth()->user()->sekolah_id;
        $setting = AkuntansiSetting::forSekolah($sekolahId)->load('akunKas');

        $setting->update($request->only([
            'metode_pencatatan', 'akun_kas_id', 'akun_piutang_id',
            'akun_pendapatan_id', 'akun_untuk_in', 'akun_untuk_out',
            'akun_beban_gaji_id', 'sumber_dana_gaji_id',
        ]) + [
            'jenis_akun_aset' => array_values(array_unique(array_map('trim', $request->input('jenis_akun_aset', [])))),
            'sumber_dana_gaji_id' => $request->filled('sumber_dana_gaji_id') ? $request->input('sumber_dana_gaji_id') : null,
        ]);

        return redirect()->route('admin.akuntansi-setting.index')->with('success', 'Pengaturan akuntansi berhasil disimpan.');
    }
}
