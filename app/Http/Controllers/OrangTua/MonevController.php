<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Concerns\HandlesMonevPdfExport;
use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\MonevSummary;
use App\Services\MonevSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MonevController extends Controller
{
    use HandlesMonevPdfExport;

    public function __construct(
        protected MonevSummaryService $monevService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        [$tahun, $bulan] = $this->monevService->parsePeriodeFromRequest(
            $request->integer('tahun') ?: null,
            $request->integer('bulan') ?: null
        );

        $anaks = Anak::query()
            ->where('user_id', $user->id)
            ->where('sekolah_id', $user->sekolah_id)
            ->where('status', 'approved')
            ->with('kelas')
            ->orderBy('name')
            ->get();

        $selectedAnak = $this->resolveSelectedAnak($anaks, $request->integer('anak_id') ?: null);

        $summary = null;
        if ($selectedAnak) {
            $summary = MonevSummary::query()
                ->where('anak_id', $selectedAnak->id)
                ->forPeriode($tahun, $bulan)
                ->first();
        }

        return view('orangtua.monev.index', compact('anaks', 'selectedAnak', 'summary', 'tahun', 'bulan'));
    }

    public function show(Anak $anak, Request $request)
    {
        $user = auth()->user();
        abort_unless((int) $anak->user_id === (int) $user->id, 403);
        abort_unless((int) $anak->sekolah_id === (int) $user->sekolah_id, 403);
        abort_unless($anak->status === 'approved', 404);

        [$tahun, $bulan] = $this->monevService->parsePeriodeFromRequest(
            $request->integer('tahun') ?: null,
            $request->integer('bulan') ?: null
        );

        return redirect()->route('orangtua.monev.index', [
            'anak_id' => $anak->id,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }

    public function exportPdf(Anak $anak, Request $request)
    {
        $user = auth()->user();
        abort_unless((int) $anak->user_id === (int) $user->id, 403);
        abort_unless((int) $anak->sekolah_id === (int) $user->sekolah_id, 403);
        abort_unless($anak->status === 'approved', 404);

        $summary = $this->resolveMonevSummaryForExport($anak, $request, $this->monevService);

        return $this->downloadMonevPdf($anak, $summary);
    }

    /**
     * @param  Collection<int, Anak>  $anaks
     */
    protected function resolveSelectedAnak($anaks, ?int $anakId): ?Anak
    {
        if ($anaks->isEmpty()) {
            return null;
        }

        if ($anakId && $anaks->contains('id', $anakId)) {
            return $anaks->firstWhere('id', $anakId);
        }

        return $anaks->first();
    }
}
