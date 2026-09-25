<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\AkuntansiTabunganAkun;
use App\Models\Cashflow;
use App\Services\AkuntansiService;
use App\Support\PaginationPerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabunganInvestasiController extends Controller
{
    public function __construct(
        private AkuntansiService $akuntansiService,
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $tabunganAkuns = AkuntansiTabunganAkun::where('sekolah_id', $sekolahId)
            ->with('akun')
            ->get();

        $akunIds = $tabunganAkuns->pluck('akun_id')->all();

        $saldos = [];
        foreach ($tabunganAkuns as $row) {
            $saldos[$row->akun_id] = $this->akuntansiService->saldoAkun($row->akun_id);
        }

        $mutasiQuery = Cashflow::where('sekolah_id', $sekolahId)
            ->where(function ($q) use ($akunIds) {
                $q->whereIn('akun_id', $akunIds)
                    ->orWhereIn('akun_lawan_id', $akunIds);
            })
            ->whereYear('date', $tahun)
            ->whereMonth('date', $bulan)
            ->with(['akun', 'akunLawan'])
            ->orderBy('date', 'desc');

        $mutasi = $akunIds === []
            ? Cashflow::whereRaw('1 = 0')->paginate(PaginationPerPage::resolve($request))
            : $mutasiQuery->paginate(PaginationPerPage::resolve($request))->withQueryString();

        $asetOptions = Akun::where('sekolah_id', $sekolahId)
            ->aktif()
            ->where('jenis', 'aset')
            ->orderBy('kode')
            ->get();

        $setting = $this->akuntansiService->getSetting($sekolahId);
        $selectedIds = $akunIds;

        return view('admin.tabungan-investasi.index', compact(
            'tabunganAkuns',
            'saldos',
            'mutasi',
            'bulan',
            'tahun',
            'asetOptions',
            'setting',
            'selectedIds',
        ));
    }

    public function updateSettings(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;

        $data = $request->validate([
            'akun_id' => 'nullable|array',
            'akun_id.*' => 'integer|exists:akuns,id',
        ]);

        $ids = $data['akun_id'] ?? [];

        $validIds = Akun::where('sekolah_id', $sekolahId)
            ->aktif()
            ->where('jenis', 'aset')
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($sekolahId, $validIds) {
            AkuntansiTabunganAkun::where('sekolah_id', $sekolahId)
                ->whereNotIn('akun_id', $validIds)
                ->delete();

            foreach ($validIds as $akunId) {
                AkuntansiTabunganAkun::firstOrCreate([
                    'sekolah_id' => $sekolahId,
                    'akun_id' => $akunId,
                ]);
            }
        });

        return redirect()->route('admin.tabungan-investasi.index')
            ->with('success', 'Akun tabungan/investasi berhasil disimpan.');
    }

    public function mutasi(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $setting = $this->akuntansiService->getSetting($sekolahId);

        $data = $request->validate([
            'aksi' => 'required|in:setor,tarik',
            'akun_tabungan_id' => 'required|integer|exists:akuns,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $isLinked = AkuntansiTabunganAkun::where('sekolah_id', $sekolahId)
            ->where('akun_id', $data['akun_tabungan_id'])
            ->exists();

        abort_unless($isLinked, 422, 'Akun tabungan tidak terdaftar di pengaturan.');

        $kasId = $setting->akun_kas_id;
        abort_unless($kasId, 422, 'Akun kas belum dikonfigurasi di setting akuntansi.');

        $tabungan = Akun::where('sekolah_id', $sekolahId)->findOrFail($data['akun_tabungan_id']);

        $type = $data['aksi'] === 'setor' ? 'out' : 'in';
        $deskripsi = $data['description']
            ?? ($data['aksi'] === 'setor'
                ? 'Setor ke '.$tabungan->nama
                : 'Tarik dari '.$tabungan->nama);

        DB::transaction(function () use ($sekolahId, $data, $type, $deskripsi, $kasId, $tabungan) {
            $cashflow = Cashflow::create([
                'sekolah_id' => $sekolahId,
                'date' => $data['date'],
                'type' => $type,
                'amount' => $data['amount'],
                'description' => $deskripsi,
                'akun_id' => $kasId,
                'akun_lawan_id' => $tabungan->id,
            ]);

            $this->akuntansiService->buatJurnalDariCashflow($cashflow);
        });

        return redirect()->route('admin.tabungan-investasi.index', [
            'bulan' => \Carbon\Carbon::parse($data['date'])->month,
            'tahun' => \Carbon\Carbon::parse($data['date'])->year,
        ])->with('success', 'Mutasi tabungan berhasil dicatat.');
    }
}
