<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\DownloadsExcel;
use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Cashflow;
use App\Models\SumberDana;
use App\Services\AkuntansiService;
use App\Services\KwitansiService;
use App\Support\PaginationPerPage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    use DownloadsExcel;

    public function __construct(
        private AkuntansiService $akuntansiService,
        private KwitansiService $kwitansiService,
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $filtered = $this->filteredCashflowQuery($sekolahId, $request);

        $cashflows = (clone $filtered)
            ->with(['akun', 'akunLawan', 'sumberDana', 'jurnal'])
            ->orderBy('date', 'desc')
            ->paginate(PaginationPerPage::resolve($request))->withQueryString();

        $totalIn = (float) (clone $filtered)->where('type', 'in')->sum('amount');
        $totalOut = (float) (clone $filtered)->where('type', 'out')->sum('amount');

        $balance = (float) Cashflow::where('sekolah_id', $sekolahId)->where('type', 'in')->sum('amount')
            - (float) Cashflow::where('sekolah_id', $sekolahId)->where('type', 'out')->sum('amount');

        $summaryArusKas = (clone $filtered)
            ->whereNotNull('akun_id')
            ->with('akun')
            ->get()
            ->groupBy(fn ($c) => $c->akun?->kategori_arus_kas ?? 'tidak_diketahui');

        $kelompokOptions = $this->distinctAkunKelompok($sekolahId);
        $subkelompokOptions = $this->distinctAkunSubkelompok($sekolahId, $request->input('kelompok'));

        $akunAset = Akun::where('sekolah_id', $sekolahId)->aktif()->where('jenis', 'aset')->orderBy('kode')->get();
        $akunOptions = Akun::where('sekolah_id', $sekolahId)->aktif()->orderBy('kode')->get();
        $setting = $this->akuntansiService->getSetting($sekolahId);
        $sumberDanas = SumberDana::where('sekolah_id', $sekolahId)->aktif()->orderBy('urutan')->get();

        return view('admin.cashflow.index', compact(
            'cashflows', 'totalIn', 'totalOut', 'balance',
            'summaryArusKas', 'bulan', 'tahun', 'akunAset', 'akunOptions', 'setting', 'sumberDanas',
            'kelompokOptions', 'subkelompokOptions',
        ));
    }

    public function export(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $cashflows = $this->filteredCashflowQuery($sekolahId, $request)
            ->with(['akun', 'akunLawan', 'sumberDana'])
            ->orderBy('date', 'desc')
            ->get();

        $rows = $cashflows->map(fn (Cashflow $c) => [
            $c->date?->format('Y-m-d') ?? '-',
            $c->akun?->nama ?? '-',
            $c->description ?? '-',
            $c->type === 'in' ? 'Masuk' : 'Keluar',
            (float) $c->amount,
        ])->all();

        return $this->downloadExcel(
            ['Tanggal', 'Akun', 'Keterangan', 'Jenis', 'Nominal (Rp)'],
            $rows,
            sprintf('cashflow-%02d-%d.xlsx', $bulan, $tahun),
            'Cashflow',
            [4],
        );
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

        DB::transaction(function () use ($request) {
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
        });

        $date = Carbon::parse($request->date);

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

        DB::transaction(function () use ($request, $cashflow) {
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
        });

        $date = Carbon::parse($request->date);

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

    private function filteredCashflowQuery(int $sekolahId, Request $request): Builder
    {
        $query = Cashflow::where('sekolah_id', $sekolahId);
        $this->applyPeriodFilter($query, $request);
        $this->applyTypeFilter($query, $request);
        $this->applyKelompokFilter($query, $request);

        return $query;
    }

    private function applyPeriodFilter(Builder $query, Request $request): void
    {
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('date', [$request->input('dari'), $request->input('sampai')]);

            return;
        }

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $query->whereYear('date', $tahun)->whereMonth('date', $bulan);
    }

    private function applyTypeFilter(Builder $query, Request $request): void
    {
        $type = $request->input('type', 'all');
        if (in_array($type, ['in', 'out'], true)) {
            $query->where('type', $type);
        }
    }

    private function applyKelompokFilter(Builder $query, Request $request): void
    {
        $kelompok = $request->input('kelompok');
        $subkelompok = $request->input('subkelompok');

        if (! $kelompok && ! $subkelompok) {
            return;
        }

        $query->where(function (Builder $q) use ($kelompok, $subkelompok) {
            $q->where(function (Builder $inner) use ($kelompok, $subkelompok) {
                $inner->whereHas('akun', fn (Builder $a) => $this->scopeAkunKelompok($a, $kelompok, $subkelompok));
            })->orWhere(function (Builder $inner) use ($kelompok, $subkelompok) {
                $inner->whereHas('akunLawan', fn (Builder $a) => $this->scopeAkunKelompok($a, $kelompok, $subkelompok));
            });
        });
    }

    private function scopeAkunKelompok(Builder $query, ?string $kelompok, ?string $subkelompok): void
    {
        if ($kelompok) {
            $query->where('snp', $kelompok);
        }
        if ($subkelompok) {
            $query->where('komponen', $subkelompok);
        }
    }

    /**
     * @return list<string>
     */
    private function distinctAkunKelompok(int $sekolahId): array
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
    private function distinctAkunSubkelompok(int $sekolahId, ?string $kelompok): array
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
}
