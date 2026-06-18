<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use App\Models\Kelas;
use App\Models\PembayaranBulanan;
use App\Services\RekapBiayaService;
use Illuminate\Http\Request;

class PembayaranBulananController extends Controller
{
    public function __construct(
        private RekapBiayaService $rekapBiayaService
    ) {}

    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $kelasId = $request->input('kelas_id');
        $status = $request->input('status');

        $query = PembayaranBulanan::where('sekolah_id', $sekolah_id)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->with(['anak', 'anak.kelas', 'biayaBulananSekolah', 'diskon', 'approvedBy']);

        if ($kelasId) {
            $query->whereHas('anak', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20);

        $kelas = Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        $summary = PembayaranBulanan::where('sekolah_id', $sekolah_id)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->selectRaw('
                COUNT(*) as total_tagihan,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(total_bayar) as total_nominal,
                SUM(CASE WHEN status = "approved" THEN total_bayar ELSE 0 END) as nominal_approved
            ')
            ->first();

        return view('admin.pembayaran-bulanan.index', compact(
            'pembayarans', 'kelas', 'bulan', 'tahun', 'kelasId', 'status', 'summary'
        ));
    }

    public function show(PembayaranBulanan $pembayaran)
    {
        abort_if($pembayaran->sekolah_id !== auth()->user()->sekolah_id, 403);

        $pembayaran->load(['anak', 'anak.kelas', 'biayaBulananSekolah', 'diskon', 'approvedBy', 'details.editedBy']);

        return view('admin.pembayaran-bulanan.show', compact('pembayaran'));
    }

    public function generatePreview(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $diskons = Diskon::where('sekolah_id', $sekolahId)
            ->where('is_aktif', true)
            ->orderBy('nama_diskon')
            ->get();

        $preview = [];
        foreach ($this->rekapBiayaService->getSiswaDenganBiayaHarian($sekolahId) as $assignment) {
            $anak = $assignment->anak;
            $biaya = $assignment->biayaBulananSekolah;
            if (! $anak || ! $biaya) {
                continue;
            }

            $biayaPerHari = (float) $assignment->biaya_harian;
            $hariHadir = $this->rekapBiayaService->hitungHariHadir($anak->id, $bulan, $tahun);
            $preview[] = [
                'key' => $anak->id . '_' . $biaya->id,
                'anak_id' => $anak->id,
                'anak_name' => $anak->name,
                'kelas_name' => $anak->kelas->name ?? '-',
                'biaya_id' => $biaya->id,
                'biaya_name' => $biaya->nama_biaya,
                'hari_hadir' => $hariHadir,
                'biaya_per_hari' => $biayaPerHari,
                'subtotal' => $biayaPerHari * $hariHadir,
            ];
        }

        usort($preview, fn ($a, $b) => strcmp($a['anak_name'], $b['anak_name']));

        return response()->json([
            'preview' => $preview,
            'diskons' => $diskons,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'tagihan' => 'required|array|min:1',
            'tagihan.*' => 'required|string|regex:/^\d+_\d+$/',
            'diskon' => 'nullable|array',
            'diskon.*' => 'nullable|exists:diskons,id',
        ]);

        $sekolah_id = auth()->user()->sekolah_id;
        $validKeys = $this->validTagihanKeys($sekolah_id);
        $selectedKeys = array_values(array_intersect($request->tagihan, $validKeys));

        if ($selectedKeys === []) {
            return back()->withErrors(['tagihan' => 'Tidak ada tagihan valid yang dipilih.']);
        }

        $diskonPerTagihan = [];

        if ($request->filled('diskon')) {
            foreach ($request->diskon as $key => $diskonId) {
                if ($diskonId && in_array((string) $key, $selectedKeys, true)) {
                    $diskonPerTagihan[(string) $key] = (int) $diskonId;
                }
            }
        }

        $pembayarans = $this->rekapBiayaService->generateTagihan(
            $sekolah_id,
            $request->bulan,
            $request->tahun,
            $diskonPerTagihan,
            $selectedKeys
        );

        return redirect()
            ->route('admin.pembayaran-bulanan.index', [
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ])
            ->with('success', "Berhasil generate {$pembayarans->count()} tagihan.");
    }

    public function destroy(PembayaranBulanan $pembayaran)
    {
        abort_if($pembayaran->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($pembayaran->status !== 'pending') {
            return redirect()
                ->route('admin.pembayaran-bulanan.index', [
                    'bulan' => $pembayaran->periode_bulan,
                    'tahun' => $pembayaran->periode_tahun,
                ])
                ->withErrors(['tagihan' => 'Hanya tagihan berstatus Menunggu yang bisa dihapus.']);
        }

        $bulan = $pembayaran->periode_bulan;
        $tahun = $pembayaran->periode_tahun;
        $pembayaran->delete();

        return redirect()
            ->route('admin.pembayaran-bulanan.index', compact('bulan', 'tahun'))
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    /** @return list<string> */
    private function validTagihanKeys(int $sekolahId): array
    {
        $keys = [];
        foreach ($this->rekapBiayaService->getSiswaDenganBiayaHarian($sekolahId) as $assignment) {
            if ($assignment->anak && $assignment->biayaBulananSekolah) {
                $keys[] = $assignment->anak_id . '_' . $assignment->biaya_bulanan_sekolah_id;
            }
        }

        return $keys;
    }

    public function updateHariHadir(Request $request, PembayaranBulanan $pembayaran)
    {
        abort_if($pembayaran->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'hari_hadir' => 'required|integer|min:0',
        ]);

        $this->rekapBiayaService->updateHariHadir(
            $pembayaran,
            $request->hari_hadir,
            auth()->id()
        );

        return redirect()
            ->route('admin.pembayaran-bulanan.show', $pembayaran)
            ->with('success', 'Jumlah hari hadir berhasil diperbarui.');
    }

    public function updateHariEfektif(Request $request, PembayaranBulanan $pembayaran)
    {
        abort_if($pembayaran->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'hari_efektif' => 'required|integer|min:1',
        ]);

        $this->rekapBiayaService->updateHariEfektif(
            $pembayaran,
            $request->hari_efektif,
            auth()->id()
        );

        return redirect()
            ->route('admin.pembayaran-bulanan.show', $pembayaran)
            ->with('success', 'Jumlah hari efektif berhasil diperbarui.');
    }

    public function updateDiskon(Request $request, PembayaranBulanan $pembayaran)
    {
        abort_if($pembayaran->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'diskon_id' => 'nullable|exists:diskons,id',
        ]);

        $pembayaran->update(['diskon_id' => $request->diskon_id]);
        $this->rekapBiayaService->hitungBiaya($pembayaran);

        return redirect()
            ->route('admin.pembayaran-bulanan.show', $pembayaran)
            ->with('success', 'Diskon berhasil diperbarui.');
    }

    public function approve(Request $request, PembayaranBulanan $pembayaran)
    {
        abort_if($pembayaran->sekolah_id !== auth()->user()->sekolah_id, 403);

        $this->rekapBiayaService->approvePembayaran(
            $pembayaran,
            auth()->id(),
            $request->catatan_admin
        );

        return redirect()
            ->route('admin.pembayaran-bulanan.show', $pembayaran)
            ->with('success', 'Pembayaran berhasil disetujui.');
    }

    public function reject(Request $request, PembayaranBulanan $pembayaran)
    {
        abort_if($pembayaran->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'catatan_admin' => 'required|string',
        ]);

        $this->rekapBiayaService->rejectPembayaran(
            $pembayaran,
            auth()->id(),
            $request->catatan_admin
        );

        return redirect()
            ->route('admin.pembayaran-bulanan.show', $pembayaran)
            ->with('success', 'Pembayaran berhasil ditolak.');
    }
}
