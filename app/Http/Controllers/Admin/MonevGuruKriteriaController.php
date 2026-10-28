<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MonevGuruKriteriaRequest;
use App\Models\MonevGuruEvaluasi;
use App\Models\MonevGuruKriteria;
use App\Support\PaginationPerPage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MonevGuruKriteriaController extends Controller
{
    private function sekolahId(): ?int
    {
        $id = auth()->user()->sekolah_id;

        return $id !== null ? (int) $id : null;
    }

    private function assertUniqueIsPresensi(int $sekolahId, bool $isPresensi, ?int $exceptId = null): void
    {
        if (! $isPresensi) {
            return;
        }

        $exists = MonevGuruKriteria::query()
            ->where('sekolah_id', $sekolahId)
            ->where('is_presensi', true)
            ->when($exceptId !== null, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'is_presensi' => 'Sudah ada kriteria yang ditandai sebagai sumber presensi. Nonaktifkan yang lama dulu.',
            ]);
        }
    }

    public function index(Request $request)
    {
        $sekolahId = $this->sekolahId();
        abort_if($sekolahId === null, 403, 'Akun tidak terikat sekolah.');

        $kriterias = MonevGuruKriteria::query()
            ->where('sekolah_id', $sekolahId)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->paginate(PaginationPerPage::resolve($request))
            ->withQueryString();

        return view('admin.monev-guru.kriteria.index', compact('kriterias'));
    }

    public function store(MonevGuruKriteriaRequest $request)
    {
        $sekolahId = $this->sekolahId();
        abort_if($sekolahId === null, 403);

        $isPresensi = $request->boolean('is_presensi');
        $this->assertUniqueIsPresensi($sekolahId, $isPresensi);

        MonevGuruKriteria::create([
            'sekolah_id' => $sekolahId,
            'nama' => $request->string('nama')->toString(),
            'deskripsi' => $request->input('deskripsi'),
            'bobot' => (int) $request->input('bobot'),
            'urutan' => (int) $request->input('urutan', 0),
            'is_active' => $request->boolean('is_active', true),
            'is_presensi' => $isPresensi,
        ]);

        return redirect()->route('admin.monev-guru.kriteria.index')
            ->with('success', 'Kriteria penilaian berhasil ditambahkan.');
    }

    public function update(MonevGuruKriteriaRequest $request, MonevGuruKriteria $monev_guru_kriteria)
    {
        $sekolahId = $this->sekolahId();
        abort_if($sekolahId === null, 403);
        abort_if((int) $monev_guru_kriteria->sekolah_id !== $sekolahId, 403);

        $isPresensi = $request->boolean('is_presensi');
        $this->assertUniqueIsPresensi($sekolahId, $isPresensi, (int) $monev_guru_kriteria->id);

        $monev_guru_kriteria->update([
            'nama' => $request->string('nama')->toString(),
            'deskripsi' => $request->input('deskripsi'),
            'bobot' => (int) $request->input('bobot'),
            'urutan' => (int) $request->input('urutan', 0),
            'is_active' => $request->boolean('is_active'),
            'is_presensi' => $isPresensi,
        ]);

        return redirect()->route('admin.monev-guru.kriteria.index')
            ->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    public function destroy(MonevGuruKriteria $monev_guru_kriteria)
    {
        $sekolahId = $this->sekolahId();
        abort_if($sekolahId === null, 403);
        abort_if((int) $monev_guru_kriteria->sekolah_id !== $sekolahId, 403);

        $inUse = MonevGuruEvaluasi::query()
            ->where('sekolah_id', $sekolahId)
            ->whereHas('items', fn ($q) => $q->where('monev_guru_kriteria_id', $monev_guru_kriteria->id))
            ->exists();

        if ($inUse) {
            return redirect()->route('admin.monev-guru.kriteria.index')
                ->withErrors(['delete' => 'Kriteria masih dipakai di evaluasi. Nonaktifkan saja.']);
        }

        $monev_guru_kriteria->delete();

        return redirect()->route('admin.monev-guru.kriteria.index')
            ->with('success', 'Kriteria penilaian berhasil dihapus.');
    }
}
