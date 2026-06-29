<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Cashflow;
use App\Models\SumberDana;
use App\Services\AkuntansiService;
use App\Services\KwitansiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Support\PaginationPerPage;

class CashflowController extends Controller
{
    public function __construct(
        private AkuntansiService $akuntansiService,
        private KwitansiService $kwitansiService,
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $cashflows = Cashflow::where('sekolah_id', $sekolahId)
            ->whereYear('date', $tahun)
            ->whereMonth('date', $bulan)
            ->with(['akun', 'akunLawan', 'sumberDana', 'jurnal'])
            ->orderBy('date', 'desc')
            ->paginate(PaginationPerPage::resolve($request))->withQueryString();

        $totalIn = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'in')->sum('amount');
        $totalOut = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        $summaryArusKas = Cashflow::where('sekolah_id', $sekolahId)
            ->whereYear('date', $tahun)
            ->whereMonth('date', $bulan)
            ->whereNotNull('akun_id')
            ->with('akun')
            ->get()
            ->groupBy(fn ($c) => $c->akun?->kategori_arus_kas ?? 'tidak_diketahui');

        $akunKas = Akun::where('sekolah_id', $sekolahId)->aktif()->sistem()->where('jenis', 'aset')->orderBy('kode')->get();
        $akunPendapatan = Akun::where('sekolah_id', $sekolahId)->aktif()->rkas()->where('jenis', 'pendapatan')->orderBy('kode')->get();
        $akunBeban = Akun::where('sekolah_id', $sekolahId)->aktif()->rkas()->where('jenis', 'beban')->orderBy('kode')->get();
        $setting = $this->akuntansiService->getSetting($sekolahId);
        $sumberDanas = SumberDana::where('sekolah_id', $sekolahId)->aktif()->orderBy('urutan')->get();

        return view('admin.cashflow.index', compact(
            'cashflows', 'totalIn', 'totalOut', 'balance',
            'summaryArusKas', 'bulan', 'tahun', 'akunKas', 'akunPendapatan', 'akunBeban', 'setting', 'sumberDanas',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'akun_id' => 'nullable|exists:akuns,id',
            'akun_lawan_id' => 'nullable|exists:akuns,id',
            'sumber_dana_id' => 'nullable|exists:sumber_danas,id',
        ]);

        $cashflow = Cashflow::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'akun_id' => $request->akun_id,
            'akun_lawan_id' => $request->akun_lawan_id,
            'sumber_dana_id' => $request->type === 'out' ? $request->sumber_dana_id : null,
        ]);

        $this->akuntansiService->buatJurnalDariCashflow($cashflow);

        $date = \Carbon\Carbon::parse($request->date);

        return redirect()->route('admin.cashflow.index', [
            'bulan' => $date->month,
            'tahun' => $date->year,
        ])->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, Cashflow $cashflow)
    {
        abort_if($cashflow->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'akun_id' => 'nullable|exists:akuns,id',
            'akun_lawan_id' => 'nullable|exists:akuns,id',
            'sumber_dana_id' => 'nullable|exists:sumber_danas,id',
        ]);

        if ($cashflow->jurnal_id) {
            $this->akuntansiService->hapusJurnal($cashflow->jurnal);
        }

        $cashflow->update([
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'akun_id' => $request->akun_id,
            'akun_lawan_id' => $request->akun_lawan_id,
            'sumber_dana_id' => $request->type === 'out' ? $request->sumber_dana_id : null,
            'jurnal_id' => null,
        ]);

        $this->akuntansiService->buatJurnalDariCashflow($cashflow->fresh());

        $date = \Carbon\Carbon::parse($request->date);

        return redirect()->route('admin.cashflow.index', [
            'bulan' => $date->month,
            'tahun' => $date->year,
        ])->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Cashflow $cashflow, Request $request)
    {
        abort_if($cashflow->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($cashflow->jurnal_id) {
            $this->akuntansiService->hapusJurnal($cashflow->jurnal);
        }

        $cashflow->delete();

        return redirect()->route('admin.cashflow.index', [
            'bulan' => $request->input('bulan', now()->month),
            'tahun' => $request->input('tahun', now()->year),
        ])->with('success', 'Transaksi berhasil dihapus.');
    }

    public function kwitansiDefaults(Cashflow $cashflow): JsonResponse
    {
        abort_if($cashflow->sekolah_id !== auth()->user()->sekolah_id, 403);

        return response()->json($this->kwitansiService->defaultsFromCashflow($cashflow));
    }

    public function kwitansiPdf(Request $request, Cashflow $cashflow)
    {
        abort_if($cashflow->sekolah_id !== auth()->user()->sekolah_id, 403);

        $data = $request->validate($this->kwitansiService->validationRules());

        return $this->kwitansiService->download(
            $data,
            $this->kwitansiService->jenisForCashflow($cashflow)
        );
    }
}
