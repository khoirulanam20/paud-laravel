<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use Illuminate\Http\Request;
use App\Support\PaginationPerPage;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $filter = $request->input('filter', 'all');

        $query = Akun::where('sekolah_id', $sekolahId)->aktif()->orderBy('kode');

        $query = match ($filter) {
            'sistem' => $query->sistem(),
            'belanja' => $query->rkas()->where('jenis', 'beban'),
            'pendapatan' => $query->rkas()->where('jenis', 'pendapatan'),
            default => $query,
        };

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('uraian', 'like', "%{$search}%");
            });
        }

        $akunList = $query->paginate(PaginationPerPage::resolve($request))->withQueryString();

        return view('admin.akun.index', compact('akunList', 'filter'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $sekolahId = auth()->user()->sekolah_id;

        if ($this->kodeExists($sekolahId, $data['kode'], $data['snp'] ?? null, $data['komponen'] ?? null)) {
            return back()->withErrors(['kode' => 'Kode akun sudah ada.']);
        }

        Akun::create($data + [
            'sekolah_id' => $sekolahId,
            'tipe' => $request->input('tipe', 'rkas'),
            'is_aktif' => true,
        ]);

        return redirect()->route('admin.akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, Akun $akun)
    {
        abort_if($akun->sekolah_id !== auth()->user()->sekolah_id, 403);

        $data = $this->validated($request);

        if ($this->kodeExists($akun->sekolah_id, $data['kode'], $data['snp'] ?? null, $data['komponen'] ?? null, $akun->id)) {
            return back()->withErrors(['kode' => 'Kode akun sudah ada.']);
        }

        if ($akun->isSistem()) {
            unset($data['tipe'], $data['jenis']);
        }

        $akun->update($data);

        return redirect()->route('admin.akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Akun $akun)
    {
        abort_if($akun->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($akun->isSistem()) {
            return back()->withErrors(['akun' => 'Akun sistem tidak bisa dihapus.']);
        }

        if ($akun->jurnalLines()->count() > 0) {
            $akun->update(['is_aktif' => false]);

            return redirect()->route('admin.akun.index')->with('success', 'Akun dinonaktifkan karena memiliki riwayat transaksi.');
        }

        $akun->delete();

        return redirect()->route('admin.akun.index')->with('success', 'Akun berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:200',
            'snp' => 'nullable|string|max:255',
            'komponen' => 'nullable|string|max:255',
            'uraian' => 'nullable|string',
            'tipe' => 'nullable|in:sistem,rkas',
            'jenis' => 'required|in:aset,liabilitas,ekuitas,pendapatan,beban',
            'kategori_arus_kas' => 'nullable|in:operasi,investasi,pendanaan',
            'saldo_normal' => 'required|in:debit,kredit',
            'induk_id' => 'nullable|exists:akuns,id',
            'deskripsi' => 'nullable|string',
        ]);
    }

    private function kodeExists(int $sekolahId, string $kode, ?string $snp, ?string $komponen, ?int $exceptId = null): bool
    {
        $q = Akun::where('sekolah_id', $sekolahId)
            ->where('kode', $kode)
            ->where('snp', $snp)
            ->where('komponen', $komponen);

        if ($exceptId) {
            $q->where('id', '!=', $exceptId);
        }

        return $q->exists();
    }
}
