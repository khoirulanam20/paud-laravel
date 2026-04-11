<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\Anak;
use App\Models\Kegiatan;
use App\Models\Matrikulasi;
use App\Services\SumopodAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiFeedbackController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $request->validate([
            'anak_id'        => 'required|integer|exists:anaks,id',
            'kegiatan_id'    => 'required|integer|exists:kegiatans,id',
            'matrikulasi_id' => 'required|integer|exists:matrikulasis,id',
            'score'          => 'required|string|in:BB,MB,BSH,BSB',
        ]);

        $user       = auth()->user();
        $lembaga_id = $user->lembaga_id;

        // Admin Sekolah / Pengajar have sekolah_id but not lembaga_id directly
        if (! $lembaga_id && $user->sekolah_id) {
            $sekolah    = $user->sekolah()->first();
            $lembaga_id = $sekolah?->lembaga_id;
        }

        if (! $lembaga_id) {
            return response()->json(['error' => 'Tidak dapat menemukan lembaga pengguna.'], 403);
        }

        $aiSetting = AiSetting::where('lembaga_id', $lembaga_id)->first();

        if (! $aiSetting || ! $aiSetting->ai_api_key) {
            return response()->json([
                'error' => 'Pengaturan AI belum dikonfigurasi. Minta admin lembaga untuk mengisi API Key di menu Pengaturan AI.',
            ], 422);
        }

        $anak        = Anak::findOrFail($request->anak_id);
        $kegiatan    = Kegiatan::findOrFail($request->kegiatan_id);
        $matrikulasi = Matrikulasi::findOrFail($request->matrikulasi_id);

        $matrikulasiLabel = ($matrikulasi->aspek ? $matrikulasi->aspek . ': ' : '') . $matrikulasi->indicator;

        try {
            $service = new SumopodAIService($aiSetting->ai_api_key, $aiSetting->ai_model ?? 'gpt-4o-mini');
            $suggestions = $service->generateFeedbackSuggestions(
                $anak->name,
                $kegiatan->title,
                $matrikulasiLabel,
                $request->score
            );

            return response()->json(['suggestions' => $suggestions]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Gagal menghubungi layanan AI: ' . $e->getMessage(),
            ], 500);
        }
    }
}
