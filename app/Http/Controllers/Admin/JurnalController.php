<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Jurnal;
use App\Services\AkuntansiService;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    public function __construct(
        private AkuntansiService $akuntansiService
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $jurnals = Jurnal::where('sekolah_id', $sekolahId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->with(['lines.akun', 'createdBy'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_jurnal', 'desc')
            ->paginate(15);

        return view('admin.jurnal.index', compact('jurnals', 'bulan', 'tahun'));
    }

    public function create()
    {
        $sekolahId = auth()->user()->sekolah_id;
        $akuns = Akun::where('sekolah_id', $sekolahId)
            ->where('is_aktif', true)
            ->orderBy('kode')
            ->get()
            ->groupBy('jenis');

        return view('admin.jurnal.create', compact('akuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.akun_id' => 'required|exists:akuns,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.kredit' => 'nullable|numeric|min:0',
            'lines.*.keterangan' => 'nullable|string',
        ]);

        $lines = [];
        foreach ($request->lines as $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $kredit = (float) ($line['kredit'] ?? 0);
            if ($debit <= 0 && $kredit <= 0) {
                continue;
            }
            $lines[] = ['akun_id' => $line['akun_id'], 'debit' => $debit, 'kredit' => $kredit, 'keterangan' => $line['keterangan'] ?? null];
        }

        if (count($lines) < 2) {
            return back()->withErrors(['lines' => 'Minimal 2 baris jurnal diperlukan.'])->withInput();
        }

        if (! $this->akuntansiService->validasiSaldo($lines)) {
            return back()->withErrors(['lines' => 'Total debit dan kredit harus sama dan lebih dari 0.'])->withInput();
        }

        $sekolahId = auth()->user()->sekolah_id;
        $jurnal = Jurnal::create([
            'sekolah_id' => $sekolahId,
            'no_jurnal' => $this->akuntansiService->generateNoJurnal($sekolahId),
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'created_by' => auth()->id(),
            'source' => 'manual',
        ]);

        foreach ($lines as $line) {
            $jurnal->lines()->create($line);
        }

        // Sync ke cashflow jika ada akun Kas
        $akunKasIds = Akun::where('sekolah_id', $sekolahId)
            ->where('jenis', 'aset')
            ->whereIn('kode', ['1-1000', '1-1100'])
            ->pluck('id');

        foreach ($lines as $line) {
            if ($akunKasIds->contains($line['akun_id'])) {
                $type = $line['debit'] > 0 ? 'in' : 'out';
                $amount = max($line['debit'], $line['kredit']);
                if ($amount > 0) {
                    \App\Models\Cashflow::create([
                        'sekolah_id' => $sekolahId,
                        'akun_id' => $line['akun_id'],
                        'jurnal_id' => $jurnal->id,
                        'type' => $type,
                        'amount' => $amount,
                        'description' => 'Jurnal: ' . $request->deskripsi,
                        'date' => $request->tanggal,
                    ]);
                }
            }
        }

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil dibuat.');
    }

    public function show(Jurnal $jurnal)
    {
        abort_if($jurnal->sekolah_id !== auth()->user()->sekolah_id, 403);

        $jurnal->load(['lines.akun', 'createdBy']);

        return view('admin.jurnal.show', compact('jurnal'));
    }

    public function destroy(Jurnal $jurnal)
    {
        abort_if($jurnal->sekolah_id !== auth()->user()->sekolah_id, 403);

        $this->akuntansiService->hapusJurnal($jurnal);

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil dihapus.');
    }
}
