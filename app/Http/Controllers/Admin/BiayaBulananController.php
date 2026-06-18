<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\BiayaBulananSekolah;
use App\Models\BiayaBulananSiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class BiayaBulananController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;

        $biayaAktif = BiayaBulananSekolah::where('sekolah_id', $sekolahId)
            ->where('is_aktif', true)
            ->orderBy('nama_biaya')
            ->get();

        $biayaTerpilih = $biayaAktif->first();
        if ($request->filled('biaya_id')) {
            $biayaTerpilih = $biayaAktif->firstWhere('id', (int) $request->biaya_id) ?? $biayaTerpilih;
        }

        $kelasId = $request->input('kelas_id');

        $siswaTerassign = collect();
        $anakSudahAssignIds = collect();

        if ($biayaTerpilih) {
            $siswaTerassign = BiayaBulananSiswa::where('biaya_bulanan_sekolah_id', $biayaTerpilih->id)
                ->with(['anak.kelas'])
                ->get()
                ->sortBy(fn (BiayaBulananSiswa $bs) => $bs->anak->name ?? '')
                ->values();

            if ($kelasId) {
                $siswaTerassign = $siswaTerassign->filter(
                    fn (BiayaBulananSiswa $bs) => $bs->anak && (int) $bs->anak->kelas_id === (int) $kelasId
                )->values();
            }

            $anakSudahAssignIds = BiayaBulananSiswa::where('biaya_bulanan_sekolah_id', $biayaTerpilih->id)
                ->pluck('anak_id');
        }

        $semuaAnak = Anak::where('sekolah_id', $sekolahId)
            ->where('status', 'approved')
            ->with('kelas')
            ->orderBy('name')
            ->get()
            ->map(fn (Anak $anak) => [
                'id' => $anak->id,
                'name' => $anak->name,
                'kelas_id' => $anak->kelas_id,
                'kelas_name' => $anak->kelas->name ?? '-',
                'sudah_assign' => $anakSudahAssignIds->contains($anak->id),
            ]);

        $kelas = Kelas::where('sekolah_id', $sekolahId)->orderBy('name')->get();
        $semuaBiaya = BiayaBulananSekolah::where('sekolah_id', $sekolahId)
            ->orderBy('nama_biaya')
            ->get();

        return view('admin.biaya-bulanan.index', compact(
            'siswaTerassign', 'biayaTerpilih', 'biayaAktif', 'kelas', 'kelasId', 'semuaBiaya', 'semuaAnak'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'nominal_default' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        BiayaBulananSekolah::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'nama_biaya' => $request->nama_biaya,
            'nominal_default' => $request->nominal_default,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.biaya-bulanan.index')
            ->with('success', 'Jenis biaya berhasil ditambahkan.');
    }

    public function update(Request $request, BiayaBulananSekolah $biayaBulanan)
    {
        abort_if($biayaBulanan->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'nominal_default' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'is_aktif' => 'boolean',
        ]);

        $biayaBulanan->update([
            'nama_biaya' => $request->nama_biaya,
            'nominal_default' => $request->nominal_default,
            'keterangan' => $request->keterangan,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return redirect()->route('admin.biaya-bulanan.index', ['biaya_id' => $biayaBulanan->id])
            ->with('success', 'Jenis biaya berhasil diperbarui.');
    }

    public function destroy(BiayaBulananSekolah $biayaBulanan)
    {
        abort_if($biayaBulanan->sekolah_id !== auth()->user()->sekolah_id, 403);
        $biayaBulanan->delete();

        return redirect()->route('admin.biaya-bulanan.index')
            ->with('success', 'Jenis biaya berhasil dihapus.');
    }

    public function addSiswa(Request $request, BiayaBulananSekolah $biayaBulanan)
    {
        abort_if($biayaBulanan->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'anak_ids' => 'required|array|min:1',
            'anak_ids.*' => 'integer|exists:anaks,id',
        ]);

        $sekolahId = auth()->user()->sekolah_id;
        $validAnakIds = Anak::where('sekolah_id', $sekolahId)
            ->where('status', 'approved')
            ->whereIn('id', $request->anak_ids)
            ->pluck('id');

        $added = 0;
        foreach ($validAnakIds as $anakId) {
            $created = BiayaBulananSiswa::firstOrCreate(
                [
                    'anak_id' => $anakId,
                    'biaya_bulanan_sekolah_id' => $biayaBulanan->id,
                ],
                [
                    'sekolah_id' => $sekolahId,
                    'biaya_harian' => $biayaBulanan->nominal_default,
                ]
            );

            if ($created->wasRecentlyCreated) {
                $added++;
            }
        }

        $message = $added > 0
            ? "{$added} siswa berhasil ditambahkan."
            : 'Siswa yang dipilih sudah ada di daftar.';

        return redirect()->route('admin.biaya-bulanan.index', [
            'biaya_id' => $biayaBulanan->id,
            'kelas_id' => $request->kelas_id,
        ])->with('success', $message);
    }

    public function removeSiswa(BiayaBulananSekolah $biayaBulanan, Anak $anak)
    {
        abort_if($biayaBulanan->sekolah_id !== auth()->user()->sekolah_id, 403);
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        BiayaBulananSiswa::where('anak_id', $anak->id)
            ->where('biaya_bulanan_sekolah_id', $biayaBulanan->id)
            ->delete();

        return redirect()->route('admin.biaya-bulanan.index', [
            'biaya_id' => $biayaBulanan->id,
            'kelas_id' => request('kelas_id'),
        ])->with('success', 'Siswa berhasil dihapus dari daftar.');
    }

    public function updateSiswaBiayaHarian(Request $request, BiayaBulananSekolah $biayaBulanan)
    {
        abort_if($biayaBulanan->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'biaya_harian' => 'required|array',
            'biaya_harian.*' => 'nullable|numeric|min:0',
        ]);

        $sekolahId = auth()->user()->sekolah_id;
        $anakIds = Anak::where('sekolah_id', $sekolahId)
            ->where('status', 'approved')
            ->pluck('id')
            ->all();

        foreach ($request->biaya_harian as $anakId => $biayaHarian) {
            $anakId = (int) $anakId;
            if (! in_array($anakId, $anakIds, true)) {
                continue;
            }

            $siswaBiaya = BiayaBulananSiswa::where('anak_id', $anakId)
                ->where('biaya_bulanan_sekolah_id', $biayaBulanan->id)
                ->first();

            if (! $siswaBiaya) {
                continue;
            }

            if ($biayaHarian === null || $biayaHarian === '') {
                $siswaBiaya->update(['biaya_harian' => $biayaBulanan->nominal_default]);
                continue;
            }

            $siswaBiaya->update(['biaya_harian' => $biayaHarian]);
        }

        return redirect()->route('admin.biaya-bulanan.index', [
            'biaya_id' => $biayaBulanan->id,
            'kelas_id' => $request->kelas_id,
        ])->with('success', 'Biaya harian siswa berhasil disimpan.');
    }
}
