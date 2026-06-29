<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Rkas;
use App\Models\RkasRealisasi;
use App\Models\SumberDana;
use App\Services\RkasRealisasiService;
use App\Services\RkasService;
use App\Support\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Support\PaginationPerPage;

class RkasController extends Controller
{
    public function __construct(
        private RkasService $rkasService,
        private RkasRealisasiService $realisasiService,
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $tahunAjaran = $request->input('tahun_ajaran', TahunAjaran::current()['tahun_ajaran']);

        $rkasList = Rkas::where('sekolah_id', $sekolahId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->orderBy('semester')
            ->withCount('lines')
            ->get();

        $tahunOptions = TahunAjaran::options();

        return view('admin.rkas.index', compact('rkasList', 'tahunAjaran', 'tahunOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:1,2',
        ]);

        $exists = Rkas::where('sekolah_id', auth()->user()->sekolah_id)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester)
            ->exists();

        if ($exists) {
            return back()->withErrors(['semester' => 'RKAS untuk periode ini sudah ada.']);
        }

        $rkas = $this->rkasService->create(
            auth()->user()->sekolah_id,
            $request->tahun_ajaran,
            (int) $request->semester,
        );

        return redirect()->route('admin.rkas.edit', $rkas)->with('success', 'RKAS berhasil dibuat.');
    }

    public function edit(Request $request, Rkas $rka)
    {
        abort_if($rka->sekolah_id !== auth()->user()->sekolah_id, 403);

        $rka->load(['lines.anggarans', 'lines.akun']);

        $sumberDanas = SumberDana::where('sekolah_id', $rka->sekolah_id)->aktif()->orderBy('urutan')->get();
        $akunBelanja = Akun::where('sekolah_id', $rka->sekolah_id)->aktif()->rkas()->where('jenis', 'beban')->orderBy('kode')->paginate(PaginationPerPage::resolve($request, 'belanja_per_page'), ['*'], 'belanja_page')->withQueryString();
        $akunPendapatan = Akun::where('sekolah_id', $rka->sekolah_id)->aktif()->rkas()->where('jenis', 'pendapatan')->orderBy('kode')->get();
        $selected = $rka->lines->keyBy('akun_id');

        return view('admin.rkas.edit', compact('rka', 'sumberDanas', 'akunBelanja', 'akunPendapatan', 'selected'));
    }

    public function update(Request $request, Rkas $rka)
    {
        abort_if($rka->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate(['lines' => 'array']);

        $this->rkasService->saveLines($rka, $request->input('lines', []));

        return redirect()->route('admin.rkas.edit', $rka)->with('success', 'Anggaran RKAS berhasil disimpan.');
    }

    public function finalize(Rkas $rka)
    {
        abort_if($rka->sekolah_id !== auth()->user()->sekolah_id, 403);
        $this->rkasService->finalize($rka);

        return back()->with('success', 'RKAS ditandai final.');
    }

    public function reopen(Rkas $rka)
    {
        abort_if($rka->sekolah_id !== auth()->user()->sekolah_id, 403);
        $this->rkasService->reopen($rka);

        return back()->with('success', 'RKAS dibuka kembali untuk diedit.');
    }

    public function sync(Rkas $rka)
    {
        abort_if($rka->sekolah_id !== auth()->user()->sekolah_id, 403);
        $result = $this->realisasiService->sync($rka);

        return back()->with('success', "Realisasi disync. {$result['unallocated']['count']} transaksi belum dialokasikan.");
    }

    public function laporan(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $tahunAjaran = $request->input('tahun_ajaran', TahunAjaran::current()['tahun_ajaran']);
        $semester = (int) $request->input('semester', TahunAjaran::current()['semester']);

        $rka = Rkas::where('sekolah_id', $sekolahId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->first();

        if (! $rka) {
            return view('admin.rkas.laporan', [
                'rka' => null,
                'rows' => collect(),
                'sumberDanas' => collect(),
                'health' => null,
                'tahunAjaran' => $tahunAjaran,
                'semester' => $semester,
                'tahunOptions' => TahunAjaran::options(),
            ]);
        }

        $laporan = $this->realisasiService->buildLaporan($rka);
        $rka->load(['lines.akun', 'lines.realisasis.sumberDana']);

        return view('admin.rkas.laporan', array_merge($laporan, [
            'rka' => $rka,
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester,
            'tahunOptions' => TahunAjaran::options(),
        ]));
    }

    public function updateRealisasi(Request $request, Rkas $rka)
    {
        abort_if($rka->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'realisasi' => 'array',
            'realisasi.*.nominal_manual' => 'nullable|numeric|min:0',
            'realisasi.*.catatan' => 'nullable|string|max:500',
        ]);

        foreach ($request->input('realisasi', []) as $id => $data) {
            $realisasi = RkasRealisasi::whereHas('rkasLine', fn ($q) => $q->where('rkas_id', $rka->id))->find($id);
            if (! $realisasi) {
                continue;
            }

            $manual = $data['nominal_manual'] ?? null;
            $realisasi->update([
                'nominal_manual' => $manual !== '' && $manual !== null ? $manual : null,
                'catatan' => $data['catatan'] ?? null,
            ]);
        }

        return back()->with('success', 'Koreksi realisasi disimpan.');
    }

    public function exportPdf(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $tahunAjaran = $request->input('tahun_ajaran', TahunAjaran::current()['tahun_ajaran']);
        $semester = (int) $request->input('semester', TahunAjaran::current()['semester']);

        $rka = Rkas::where('sekolah_id', $sekolahId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->firstOrFail();

        $laporan = $this->realisasiService->buildLaporan($rka);

        $pdf = Pdf::loadView('admin.rkas.pdf', array_merge($laporan, ['rka' => $rka]));

        return $pdf->download("rkas-{$tahunAjaran}-sem{$semester}.pdf");
    }

    public function destroy(Rkas $rka)
    {
        abort_if($rka->sekolah_id !== auth()->user()->sekolah_id, 403);
        abort_if($rka->isFinal(), 422, 'RKAS final tidak bisa dihapus.');

        $rka->delete();

        return redirect()->route('admin.rkas.index')->with('success', 'RKAS berhasil dihapus.');
    }
}
