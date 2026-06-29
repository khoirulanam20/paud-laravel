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

        $akunAset = Akun::where('sekolah_id', $sekolahId)->where('is_aktif', true)->where('jenis', 'aset')->orderBy('kode')->get();
        $akunPendapatan = Akun::where('sekolah_id', $sekolahId)->where('is_aktif', true)->where('jenis', 'pendapatan')->orderBy('kode')->get();
        $akunBeban = Akun::where('sekolah_id', $sekolahId)->where('is_aktif', true)->where('jenis', 'beban')->orderBy('kode')->get();

        return view('admin.akuntansi-setting.index', compact('setting', 'akunAset', 'akunPendapatan', 'akunBeban'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'akun_kas_id' => 'required|exists:akuns,id',
            'akun_piutang_id' => 'nullable|exists:akuns,id',
            'akun_pendapatan_id' => 'nullable|exists:akuns,id',
            'akun_untuk_in' => 'required|exists:akuns,id',
            'akun_untuk_out' => 'required|exists:akuns,id',
        ]);

        $sekolahId = auth()->user()->sekolah_id;
        $setting = AkuntansiSetting::forSekolah($sekolahId);

        $setting->update($request->only([
            'akun_kas_id', 'akun_piutang_id',
            'akun_pendapatan_id', 'akun_untuk_in', 'akun_untuk_out',
        ]));

        return redirect()->route('admin.akuntansi-setting.index')->with('success', 'Pengaturan akuntansi berhasil disimpan.');
    }
}
