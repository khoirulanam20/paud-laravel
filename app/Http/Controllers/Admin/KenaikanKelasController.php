<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KenaikanKelasBatch;
use App\Services\KenaikanKelasService;
use App\Support\PaginationPerPage;
use App\Support\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InvalidArgumentException;

class KenaikanKelasController extends Controller
{
    public function __construct(
        protected KenaikanKelasService $kenaikan
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        abort_if($sekolahId === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $periode = TahunAjaran::current();

        $batches = KenaikanKelasBatch::query()
            ->where('sekolah_id', $sekolahId)
            ->when($request->filled('tahun_ajaran'), fn ($q) => $q->where('tahun_ajaran_asal', $request->input('tahun_ajaran')))
            ->when($request->filled('mode'), fn ($q) => $q->where('mode', $request->input('mode')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->with('dibuatOleh')
            ->latest()
            ->paginate(PaginationPerPage::resolve($request))
            ->withQueryString();

        return view('admin.kenaikan-kelas.index', [
            'periode' => $periode,
            'batches' => $batches,
            'tahunAjaranOptions' => TahunAjaran::options(4),
        ]);
    }

    public function preview(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        abort_if($sekolahId === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $data = $request->validate([
            'mode' => 'required|in:semester,usia',
            'tanggal_acuan' => 'required|date',
            'tanggal_efektif' => 'required|date',
        ]);

        $tanggalAcuan = Carbon::parse($data['tanggal_acuan'])->startOfDay();
        $tanggalEfektif = Carbon::parse($data['tanggal_efektif'])->startOfDay();

        $usulan = $this->kenaikan->usulan($sekolahId, $data['mode'], $tanggalAcuan);

        $kelasTujuan = Kelas::where('sekolah_id', $sekolahId)
            ->whereNotNull('tingkat')
            ->orderBy('tingkat')
            ->orderBy('name')
            ->get();

        return view('admin.kenaikan-kelas.preview', [
            'mode' => $data['mode'],
            'tanggalAcuan' => $tanggalAcuan,
            'tanggalEfektif' => $tanggalEfektif,
            'items' => $usulan['items'],
            'warnings' => $usulan['warnings'],
            'kelasTujuan' => $kelasTujuan,
        ]);
    }

    public function store(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        abort_if($sekolahId === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $data = $request->validate([
            'mode' => 'required|in:semester,usia',
            'tanggal_acuan' => 'required|date',
            'tanggal_efektif' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.proses' => 'nullable|boolean',
            'items.*.anak_id' => 'required|integer|exists:anaks,id',
            'items.*.aksi' => 'required|in:naik,tinggal,lulus,pindah',
            'items.*.kelas_tujuan_id' => 'nullable|integer|exists:kelas,id',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        // Hanya baris yang dicentang; tanpa 'proses' (mis. klien lama) dianggap diproses.
        $data['items'] = array_values(array_filter($data['items'], fn ($i) => $i['proses'] ?? true));
        if ($data['items'] === []) {
            return back()->withInput()->withErrors(['items' => 'Pilih minimal satu siswa.']);
        }

        try {
            $batch = $this->kenaikan->terapkan(
                $sekolahId,
                $data['mode'],
                Carbon::parse($data['tanggal_acuan'])->startOfDay(),
                Carbon::parse($data['tanggal_efektif'])->startOfDay(),
                $data['items'],
                $request->user()
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.kenaikan-kelas.show', $batch)
            ->with('success', 'Kenaikan kelas berhasil diterapkan.');
    }

    public function show(KenaikanKelasBatch $batch)
    {
        abort_if((int) $batch->sekolah_id !== (int) auth()->user()->sekolah_id, 403);

        $batch->load(['items.anak', 'items.kelasAsal', 'items.kelasTujuan', 'dibuatOleh']);

        return view('admin.kenaikan-kelas.show', ['batch' => $batch]);
    }

    public function destroy(KenaikanKelasBatch $batch)
    {
        abort_if((int) $batch->sekolah_id !== (int) auth()->user()->sekolah_id, 403);

        try {
            $this->kenaikan->batalkan($batch, request()->user());
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['batch' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.kenaikan-kelas.index')
            ->with('success', 'Kenaikan kelas berhasil dibatalkan.');
    }
}
