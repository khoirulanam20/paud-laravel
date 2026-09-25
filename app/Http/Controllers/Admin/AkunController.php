<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\DownloadsExcel;
use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Support\PaginationPerPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    use DownloadsExcel;

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $filter = $request->input('filter', 'all');

        $query = $this->baseQuery($sekolahId, $filter, $request);
        $akunList = $query->paginate(PaginationPerPage::resolve($request))->withQueryString();

        $kelompokOptions = $this->distinctKelompok($sekolahId);
        $subkelompokOptions = $this->distinctSubkelompok($sekolahId, $request->input('kelompok'));

        return view('admin.akun.index', compact(
            'akunList',
            'filter',
            'kelompokOptions',
            'subkelompokOptions',
        ));
    }

    public function export(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $filter = $request->input('filter', 'all');

        $rows = $this->baseQuery($sekolahId, $filter, $request)
            ->get()
            ->map(fn (Akun $a) => [
                $a->kode,
                ucfirst($a->jenis ?? '-'),
                $a->nama,
                $a->snp ?? '-',
                $a->komponen ?? '-',
                $a->uraian ?? '-',
            ])->all();

        return $this->downloadExcel(
            ['Kode Akun', 'Jenis', 'Nama Akun', 'Kelompok', 'Subkelompok', 'Uraian'],
            $rows,
            'kode-rekening-'.now()->format('Y-m-d').'.xlsx',
            'Kode Rekening'
        );
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

    private function baseQuery(int $sekolahId, string $filter, Request $request): Builder
    {
        $query = Akun::where('sekolah_id', $sekolahId)->aktif()->orderBy('kode');

        $query = match ($filter) {
            'sistem' => $query->sistem(),
            'belanja' => $query->rkas()->where('jenis', 'beban'),
            default => $query,
        };

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('uraian', 'like', "%{$search}%")
                    ->orWhere('snp', 'like', "%{$search}%")
                    ->orWhere('komponen', 'like', "%{$search}%");
            });
        }

        if ($kelompok = $request->input('kelompok')) {
            $query->where('snp', $kelompok);
        }

        if ($subkelompok = $request->input('subkelompok')) {
            $query->where('komponen', $subkelompok);
        }

        return $query;
    }

    /**
     * @return list<string>
     */
    private function distinctKelompok(int $sekolahId): array
    {
        return Akun::where('sekolah_id', $sekolahId)
            ->aktif()
            ->whereNotNull('snp')
            ->where('snp', '!=', '')
            ->distinct()
            ->orderBy('snp')
            ->pluck('snp')
            ->all();
    }

    /**
     * @return list<string>
     */
    private function distinctSubkelompok(int $sekolahId, ?string $kelompok): array
    {
        $q = Akun::where('sekolah_id', $sekolahId)
            ->aktif()
            ->whereNotNull('komponen')
            ->where('komponen', '!=', '');

        if ($kelompok) {
            $q->where('snp', $kelompok);
        }

        return $q->distinct()->orderBy('komponen')->pluck('komponen')->all();
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
