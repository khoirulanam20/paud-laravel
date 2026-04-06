<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Matrikulasi;
use App\Models\Pencapaian;
use App\Models\Pengajar;
use App\Models\Kelas;
use App\Support\FilterAspekPencapaian;
use App\Support\LabelSkorPencapaian;
use App\Support\TanggalRentang;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PencapaianController extends Controller
{
    private const SCORES = LabelSkorPencapaian::CODES;

    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;

        $range = TanggalRentang::dariSampaiQuery($request, null);
        $tanggalDari = $range ? $range[0] : null;
        $tanggalSampai = $range ? $range[1] : null;

        $anakQuery = Anak::query()->where('sekolah_id', $sekolah_id)->orderBy('name');
        if ($request->filled('filter_kelas_id')) {
            $kid = (int) $request->input('filter_kelas_id');
            $anakQuery->where('kelas_id', $kid);
        }
        $anaks = $anakQuery->get();

        $filterAspekRaw = (string) $request->input('aspek', '');
        $filterAspek = $filterAspekRaw === '' ? null : $filterAspekRaw;

        $hariQuery = Pencapaian::query()
            ->with(['anak', 'kegiatan.matrikulasis', 'matrikulasi'])
            ->orderByDesc('updated_at');

        if ($tanggalDari) {
            $hariQuery->whereDate('created_at', '>=', $tanggalDari);
        }
        if ($tanggalSampai) {
            $hariQuery->whereDate('created_at', '<=', $tanggalSampai);
        }

        $hariQuery->whereHas('anak', fn ($q) => $q->where('sekolah_id', $sekolah_id));
        
        if ($request->filled('filter_anak_id')) {
            $aid = (int) $request->input('filter_anak_id');
            $hariQuery->where('anak_id', $aid);
        }
        if ($request->filled('filter_kelas_id')) {
            $kid = (int) $request->input('filter_kelas_id');
            $hariQuery->whereHas('anak', fn ($q) => $q->where('kelas_id', $kid));
        }

        $hariAll = $hariQuery->get();
        $groupsAll = $hariAll->groupBy(fn ($p) => $p->anak_id.'_'.$p->kegiatan_id);

        $keysFiltered = $groupsAll->keys()->values()->filter(function ($k) use ($groupsAll, $filterAspek) {
            return FilterAspekPencapaian::groupHasMatch($filterAspek, $groupsAll[$k]);
        })->values();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $total = $keysFiltered->count();
        $sliceKeys = $keysFiltered->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $pageItems = $sliceKeys->mapWithKeys(fn ($k) => [$k => $groupsAll[$k]]);

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
        foreach ($groupsAll as $k => $rows) {
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

        $kegiatanQuery = Kegiatan::where('sekolah_id', $sekolah_id)->with('matrikulasis')->orderBy('date', 'desc');
        if ($request->filled('filter_kelas_id')) {
             $kegiatanQuery->where('kelas_id', $request->filter_kelas_id);
        }
        $kegiatans = $kegiatanQuery->get();

        $aspekPilihan = Matrikulasi::query()
            ->where('sekolah_id', $sekolah_id)
            ->whereNotNull('aspek')
            ->where('aspek', '!=', '')
            ->distinct()
            ->orderBy('aspek')
            ->pluck('aspek');

        $filterAnakId = $request->filled('filter_anak_id') ? (int) $request->input('filter_anak_id') : null;
        $filterKelasId = $request->filled('filter_kelas_id') ? (int) $request->input('filter_kelas_id') : null;
        $availableKelas = Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        return view('admin.pencapaian.index', compact(
            'groupedPencapaian',
            'editBundles',
            'anaks',
            'kegiatans',
            'availableKelas',
            'tanggalDari',
            'tanggalSampai',
            'filterAnakId',
            'filterKelasId',
            'filterAspek',
            'filterAspekRaw',
            'aspekPilihan',
        ));
    }

    public function sync(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;

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
            ->where('sekolah_id', $sekolah_id)
            ->with('matrikulasis')
            ->firstOrFail();

        $matIds = $kegiatan->matrikulasis->pluck('id')->all();
        if ($matIds === []) {
            return back()
                ->withInput()
                ->withErrors(['kegiatan_id' => 'Kegiatan ini belum memiliki indikator matrikulasi. Tambahkan di Jurnal Kegiatan terlebih dahulu.']);
        }

        $anak = Anak::where('id', $request->integer('anak_id'))->where('sekolah_id', $sekolah_id)->firstOrFail();

        if ($anak->kelas_id !== $kegiatan->kelas_id) {
            return back()
                ->withInput()
                ->withErrors(['kegiatan_id' => 'Data Siswa dan Jurnal Kegiatan harus berada di kelas yang sama.']);
        }

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
                    'pengajar_id' => $kegiatan->pengajar_id,
                    'score' => $score,
                    'feedback' => $catatan ? (string) $catatan : null,
                    'photo' => $photoPath,
                ]
            );
        }

        Pencapaian::query()
            ->where('anak_id', $anak->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->whereNotIn('matrikulasi_id', $matIds)
            ->whereNotNull('matrikulasi_id')
            ->delete();

        return redirect()
            ->route('admin.pencapaian.index', $this->pencapaianFilterQuery($request))
            ->with('success', 'Pencapaian per aspek berhasil disimpan.');
    }

    public function destroy(Pencapaian $pencapaian)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $pencapaian->loadMissing('anak');
        abort_if($pencapaian->anak?->sekolah_id !== $sekolah_id, 403);

        if ($pencapaian->photo) {
            $stillUsed = Pencapaian::query()
                ->where('anak_id', $pencapaian->anak_id)
                ->where('kegiatan_id', $pencapaian->kegiatan_id)
                ->where('id', '!=', $pencapaian->id)
                ->where('photo', $pencapaian->photo)
                ->exists();
            if (! $stillUsed) {
                Storage::disk('public')->delete($pencapaian->photo);
            }
        }

        $pencapaian->delete();

        return redirect()
            ->route('admin.pencapaian.index', $this->pencapaianFilterQuery(request()))
            ->with('success', 'Satu baris pencapaian dihapus.');
    }

    public function destroyBundle(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $request->validate([
            'anak_id' => 'required|exists:anaks,id',
            'kegiatan_id' => 'required|exists:kegiatans,id',
        ]);

        $anak = Anak::where('id', $request->integer('anak_id'))->where('sekolah_id', $sekolah_id)->firstOrFail();
        $kegiatan = Kegiatan::where('id', $request->integer('kegiatan_id'))->where('sekolah_id', $sekolah_id)->firstOrFail();

        $rows = Pencapaian::query()
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
            ->route('admin.pencapaian.index', $this->pencapaianFilterQuery($request))
            ->with('success', 'Seluruh pencapaian untuk kegiatan ini dihapus.');
    }

    /** @return array<string, string> */
    private function pencapaianFilterQuery(Request $request): array
    {
        $range = TanggalRentang::dariSampaiQuery($request, null);
        $q = $range ? TanggalRentang::toQueryParams($range[0], $range[1]) : [];
        if ($request->filled('filter_anak_id')) {
            $q['filter_anak_id'] = (string) (int) $request->input('filter_anak_id');
        }
        if ($request->filled('filter_kelas_id')) {
            $q['filter_kelas_id'] = (string) (int) $request->input('filter_kelas_id');
        }
        if ($request->filled('aspek')) {
            $q['aspek'] = (string) $request->input('aspek');
        }

        return $q;
    }
}
