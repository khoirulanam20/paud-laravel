<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Matrikulasi;
use App\Services\AiPersonaService;
use App\Services\SumopodAIService;
use App\Support\AiPersonaScope;
use App\Support\LabelSkorPencapaian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AiFeedbackController extends Controller
{
    public function __construct(
        protected AiPersonaService $personaService
    ) {}

    public function suggest(Request $request): JsonResponse
    {
        $user = auth()->user();
        $lembaga_id = $user->lembaga_id;

        if (! $lembaga_id && $user->sekolah_id) {
            $sekolah = $user->sekolah()->first();
            $lembaga_id = $sekolah?->lembaga_id;
        }

        $anak = Anak::findOrFail($request->integer('anak_id'));
        $sekolahId = (int) ($anak->sekolah_id ?? $user->sekolah_id ?? 0);
        $scoreCodes = $sekolahId > 0
            ? LabelSkorPencapaian::codesForSekolah($sekolahId)
            : LabelSkorPencapaian::CODES;

        $request->validate([
            'anak_id'        => 'required|integer|exists:anaks,id',
            'kegiatan_id'    => 'required|integer|exists:kegiatans,id',
            'matrikulasi_id' => 'required|integer|exists:matrikulasis,id',
            'score'          => ['required', 'string', Rule::in($scoreCodes)],
        ]);

        if (! $lembaga_id) {
            return response()->json(['error' => 'Tidak dapat menemukan lembaga pengguna.'], 403);
        }

        $aiSetting = AiSetting::where('lembaga_id', $lembaga_id)->first();

        if (! $aiSetting || ! $aiSetting->hasValidApiKey()) {
            return response()->json([
                'error' => 'Pengaturan AI belum dikonfigurasi. Minta admin lembaga untuk mengisi API Key di menu Pengaturan AI.',
            ], 422);
        }

        $kegiatan    = Kegiatan::findOrFail($request->kegiatan_id);
        $matrikulasi = Matrikulasi::findOrFail($request->matrikulasi_id);

        $matrikulasiLabel = ($matrikulasi->aspek ? $matrikulasi->aspek . ': ' : '') . $matrikulasi->indicator;
        $scoreLabel = LabelSkorPencapaian::scoreLabelForAi($request->score, $sekolahId ?: null);
        $sekolahName = $anak->sekolah?->name ?? 'PAUD';
        $personaPrompt = $sekolahId > 0
            ? $this->personaService->resolveActivePrompt($sekolahId, AiPersonaScope::FEEDBACK_PENCAPAIAN, $sekolahName)
            : null;

        try {
            $service = new SumopodAIService($aiSetting->ai_api_key, $aiSetting->ai_model ?? 'gpt-4o-mini');
            $suggestions = $service->generateFeedbackSuggestions(
                $anak->displayName(),
                $kegiatan->title,
                $matrikulasiLabel,
                $scoreLabel,
                $personaPrompt
            );

            return response()->json(['suggestions' => $suggestions]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Gagal menghubungi layanan AI: ' . $e->getMessage(),
            ], 500);
        }
    }
}
