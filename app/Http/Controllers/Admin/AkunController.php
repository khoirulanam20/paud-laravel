<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;

        $akuns = Akun::where('sekolah_id', $sekolahId)
            ->with(['induk', 'anak'])
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get()
            ->groupBy('jenis');

        $allAkun = Akun::where('sekolah_id', $sekolahId)
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get();

        return view('admin.akun.index', compact('akuns', 'allAkun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:200',
            'jenis' => 'required|in:aset,liabilitas,ekuitas,pendapatan,beban',
            'kategori_arus_kas' => 'nullable|in:operasi,investasi,pendanaan',
            'saldo_normal' => 'required|in:debit,kredit',
            'induk_id' => 'nullable|exists:akuns,id',
            'deskripsi' => 'nullable|string',
        ]);

        $sekolahId = auth()->user()->sekolah_id;

        if (Akun::where('sekolah_id', $sekolahId)->where('kode', $request->kode)->exists()) {
            return back()->withErrors(['kode' => 'Kode akun sudah ada.']);
        }

        Akun::create([
            'sekolah_id' => $sekolahId,
            'kode' => $request->kode,
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'kategori_arus_kas' => $request->kategori_arus_kas,
            'saldo_normal' => $request->saldo_normal,
            'induk_id' => $request->induk_id,
            'is_aktif' => true,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, Akun $akun)
    {
        abort_if($akun->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:200',
            'jenis' => 'required|in:aset,liabilitas,ekuitas,pendapatan,beban',
            'kategori_arus_kas' => 'nullable|in:operasi,investasi,pendanaan',
            'saldo_normal' => 'required|in:debit,kredit',
            'induk_id' => 'nullable|exists:akuns,id',
            'deskripsi' => 'nullable|string',
        ]);

        $sekolahId = auth()->user()->sekolah_id;
        $exists = Akun::where('sekolah_id', $sekolahId)
            ->where('kode', $request->kode)
            ->where('id', '!=', $akun->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['kode' => 'Kode akun sudah ada.']);
        }

        $akun->update($request->only([
            'kode', 'nama', 'jenis', 'kategori_arus_kas',
            'saldo_normal', 'induk_id', 'deskripsi',
        ]));

        return redirect()->route('admin.akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Akun $akun)
    {
        abort_if($akun->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($akun->anak()->count() > 0) {
            return back()->withErrors(['akun' => 'Akun memiliki sub-akun. Hapus sub-akun terlebih dahulu.']);
        }

        if ($akun->jurnalLines()->count() > 0) {
            $akun->update(['is_aktif' => false]);
            return redirect()->route('admin.akun.index')->with('success', 'Akun dinonaktifkan karena memiliki riwayat transaksi.');
        }

        $akun->delete();

        return redirect()->route('admin.akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}
