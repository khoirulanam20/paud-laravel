<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Cashflow;
use App\Services\AkuntansiService;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    public function __construct(
        private AkuntansiService $akuntansiService
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $cashflows = Cashflow::where('sekolah_id', $sekolahId)
            ->whereYear('date', $tahun)
            ->whereMonth('date', $bulan)
            ->with(['akun', 'jurnal'])
            ->orderBy('date', 'desc')
            ->paginate(15);

        $totalIn = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'in')->sum('amount');
        $totalOut = Cashflow::where('sekolah_id', $sekolahId)->where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        // Summary per kategori arus kas
        $summaryArusKas = Cashflow::where('sekolah_id', $sekolahId)
            ->whereYear('date', $tahun)
            ->whereMonth('date', $bulan)
            ->whereNotNull('akun_id')
            ->with('akun')
            ->get()
            ->groupBy(fn ($c) => $c->akun?->kategori_arus_kas ?? 'tidak_diketahui');

        $akuns = Akun::where('sekolah_id', $sekolahId)
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get();
        $setting = $this->akuntansiService->getSetting($sekolahId);

        return view('admin.cashflow.index', compact(
            'cashflows', 'totalIn', 'totalOut', 'balance',
            'summaryArusKas', 'bulan', 'tahun', 'akuns', 'setting'
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
        ]);

        $cashflow = Cashflow::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'akun_id' => $request->akun_id,
        ]);

        // Auto-buat jurnal
        $this->akuntansiService->buatJurnalDariCashflow($cashflow);

        return redirect()->route('admin.cashflow.index')->with('success', 'Transaksi berhasil ditambahkan.');
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
        ]);

        // Hapus jurnal lama jika ada
        if ($cashflow->jurnal_id) {
            $this->akuntansiService->hapusJurnal($cashflow->jurnal);
        }

        $cashflow->update([
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'akun_id' => $request->akun_id,
            'jurnal_id' => null,
        ]);

        // Buat jurnal baru
        $this->akuntansiService->buatJurnalDariCashflow($cashflow);

        return redirect()->route('admin.cashflow.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Cashflow $cashflow)
    {
        abort_if($cashflow->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($cashflow->jurnal_id) {
            $this->akuntansiService->hapusJurnal($cashflow->jurnal);
        } else {
            $cashflow->delete();
        }

        return redirect()->route('admin.cashflow.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
