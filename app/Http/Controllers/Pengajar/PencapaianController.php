<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Pencapaian;
use App\Models\Pengajar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PencapaianController extends Controller
{
    private const SCORES = ['BB', 'MB', 'BSH', 'BSB'];

    private function getPengajar()
    {
        return Pengajar::where('user_id', auth()->id())->firstOrFail();
    }

    private function assertAnakDalamLingkupPencapaian(Anak $anak, Pengajar $pengajar): void
    {
        abort_if($anak->sekolah_id !== $pengajar->sekolah_id, 403);
        $kelasId = auth()->user()->kelas_id;
        if ($kelasId !== null) {
            abort_if((int) $anak->kelas_id !== (int) $kelasId, 403);
        }
    }

    public function index(Request $request)
    {
        $pengajar = $this->getPengajar();
        $sekolah_id = $pengajar->sekolah_id;

        $tanggalInput = $request->query('tanggal', date('Y-m-d'));
        try {
            $tanggal = Carbon::parse($tanggalInput)->format('Y-m-d');
        } catch (\Throwable) {
            $tanggal = date('Y-m-d');
        }

        $hariQuery = Pencapaian::query()
            ->where('pengajar_id', $pengajar->id)
            ->whereDate('created_at', $tanggal)
            ->with(['anak', 'kegiatan.matrikulasis', 'matrikulasi'])
            ->orderByDesc('updated_at');
        if (auth()->user()->kelas_id) {
            $hariQuery->whereHas('anak', fn ($q) => $q->where('kelas_id', auth()->user()->kelas_id));
        }
        $hari = $hariQuery->get();

        $groups = $hari->groupBy(fn ($p) => $p->anak_id.'_'.$p->kegiatan_id);
        $keys = $groups->keys()->values();
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $total = $keys->count();
        $sliceKeys = $keys->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $pageItems = $sliceKeys->mapWithKeys(fn ($k) => [$k => $groups[$k]]);

        $groupedPencapaian = new LengthAwarePaginator(
            $pageItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'page',
            ]
        );

        $editBundles = [];
        foreach ($groups as $k => $rows) {
            $first = $rows->first();
            $nilai = [];
            $catatan = [];
            foreach ($rows as $r) {
                if ($r->matrikulasi_id) {
                    $key = (string) $r->matrikulasi_id;
                    $nilai[$key] = $r->score;
                    $catatan[$key] = $r->feedback ?? '';
                }
            }
            $editBundles[$k] = [
                'bundle_key' => $k,
                'anak_id' => $first->anak_id,
                'kegiatan_id' => $first->kegiatan_id,
                'nilai' => $nilai,
                'catatan' => $catatan,
                'has_photo' => $rows->contains(fn ($r) => filled($r->photo)),
            ];
        }

        $kegiatans = Kegiatan::where('pengajar_id', $pengajar->id)
            ->with('matrikulasis')
            ->orderBy('date', 'desc')
            ->get();

        $anakQuery = Anak::query()->where('sekolah_id', $sekolah_id)->orderBy('name');
        if (auth()->user()->kelas_id) {
            $anakQuery->where('kelas_id', auth()->user()->kelas_id);
        }
        $anaks = $anakQuery->get();

        return view('pengajar.pencapaian.index', compact(
            'groupedPencapaian',
            'editBundles',
            'anaks',
            'kegiatans',
            'tanggal',
        ));
    }

    public function sync(Request $request)
    {
        $pengajar = $this->getPengajar();

        $request->validate([
            'anak_id' => 'required|exists:anaks,id',
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'nilai' => 'required|array',
            'nilai.*' => ['required', 'string', Rule::in(self::SCORES)],
            'catatan' => 'nullable|array',
            'catatan.*' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|max:2048',
        ]);

        $kegiatan = Kegiatan::query()
            ->where('id', $request->integer('kegiatan_id'))
            ->where('pengajar_id', $pengajar->id)
            ->with('matrikulasis')
            ->firstOrFail();

        $matIds = $kegiatan->matrikulasis->pluck('id')->all();
        if ($matIds === []) {
            return back()
                ->withInput()
                ->withErrors(['kegiatan_id' => 'Kegiatan ini belum memiliki indikator matrikulasi. Tambahkan di Jurnal Kegiatan terlebih dahulu.']);
        }

        $anak = Anak::findOrFail($request->integer('anak_id'));
        $this->assertAnakDalamLingkupPencapaian($anak, $pengajar);

        $nilaiInput = $request->input('nilai', []);
        foreach ($matIds as $mid) {
            $sk = (string) $mid;
            if (! array_key_exists($sk, $nilaiInput) && ! array_key_exists($mid, $nilaiInput)) {
                return back()
                    ->withInput()
                    ->withErrors(['nilai' => 'Setiap aspek matrikulasi wajib diberi nilai.']);
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $existing = Pencapaian::query()
                ->where('anak_id', $anak->id)
                ->where('kegiatan_id', $kegiatan->id)
                ->where('pengajar_id', $pengajar->id)
                ->whereNotNull('photo')
                ->first();
            if ($existing?->photo) {
                Storage::disk('public')->delete($existing->photo);
            }
            $photoPath = $request->file('photo')->store('pencapaian', 'public');
        } else {
            $photoPath = Pencapaian::query()
                ->where('anak_id', $anak->id)
                ->where('kegiatan_id', $kegiatan->id)
                ->where('pengajar_id', $pengajar->id)
                ->whereNotNull('photo')
                ->value('photo');
        }

        foreach ($matIds as $mid) {
            $sk = (string) $mid;
            $score = $nilaiInput[$sk] ?? $nilaiInput[$mid];
            $catatan = $request->input("catatan.$sk") ?? $request->input("catatan.$mid");

            Pencapaian::query()->updateOrCreate(
                [
                    'anak_id' => $anak->id,
                    'kegiatan_id' => $kegiatan->id,
                    'matrikulasi_id' => $mid,
                ],
                [
                    'pengajar_id' => $pengajar->id,
                    'score' => $score,
                    'feedback' => $catatan ? (string) $catatan : null,
                    'photo' => $photoPath,
                ]
            );
        }

        Pencapaian::query()
            ->where('anak_id', $anak->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->where('pengajar_id', $pengajar->id)
            ->whereNotIn('matrikulasi_id', $matIds)
            ->whereNotNull('matrikulasi_id')
            ->delete();

        return redirect()
            ->route('pengajar.pencapaian.index', ['tanggal' => $request->query('tanggal', date('Y-m-d'))])
            ->with('success', 'Pencapaian per aspek berhasil disimpan.');
    }

    public function destroy(Pencapaian $pencapaian)
    {
        $pengajar = $this->getPengajar();
        abort_if($pencapaian->pengajar_id !== $pengajar->id, 403);
        $pencapaian->loadMissing('anak');
        if ($pencapaian->anak) {
            $this->assertAnakDalamLingkupPencapaian($pencapaian->anak, $pengajar);
        }

        if ($pencapaian->photo) {
            $stillUsed = Pencapaian::query()
                ->where('anak_id', $pencapaian->anak_id)
                ->where('kegiatan_id', $pencapaian->kegiatan_id)
                ->where('pengajar_id', $pengajar->id)
                ->where('id', '!=', $pencapaian->id)
                ->where('photo', $pencapaian->photo)
                ->exists();
            if (! $stillUsed) {
                Storage::disk('public')->delete($pencapaian->photo);
            }
        }

        $pencapaian->delete();

        return redirect()
            ->route('pengajar.pencapaian.index', ['tanggal' => request('tanggal', date('Y-m-d'))])
            ->with('success', 'Satu baris pencapaian dihapus.');
    }

    public function destroyBundle(Request $request)
    {
        $pengajar = $this->getPengajar();
        $request->validate([
            'anak_id' => 'required|exists:anaks,id',
            'kegiatan_id' => 'required|exists:kegiatans,id',
        ]);

        $anak = Anak::findOrFail($request->integer('anak_id'));
        $this->assertAnakDalamLingkupPencapaian($anak, $pengajar);
        Kegiatan::query()
            ->where('id', $request->integer('kegiatan_id'))
            ->where('pengajar_id', $pengajar->id)
            ->firstOrFail();

        $rows = Pencapaian::query()
            ->where('pengajar_id', $pengajar->id)
            ->where('anak_id', $request->integer('anak_id'))
            ->where('kegiatan_id', $request->integer('kegiatan_id'))
            ->get();

        foreach ($rows as $pencapaian) {
            if ($pencapaian->photo) {
                $stillUsed = Pencapaian::query()
                    ->where('photo', $pencapaian->photo)
                    ->where('id', '!=', $pencapaian->id)
                    ->exists();
                if (! $stillUsed) {
                    Storage::disk('public')->delete($pencapaian->photo);
                }
            }
            $pencapaian->delete();
        }

        return redirect()
            ->route('pengajar.pencapaian.index', ['tanggal' => $request->query('tanggal', date('Y-m-d'))])
            ->with('success', 'Seluruh pencapaian untuk kegiatan ini dihapus.');
    }
}
