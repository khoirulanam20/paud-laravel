<?php

namespace App\Http\Controllers\Lembaga;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Services\SumopodAIService;
use Illuminate\Http\Request;

class AiSettingController extends Controller
{
    public function index()
    {
        $lembaga_id = auth()->user()->lembaga_id;
        $aiSetting  = AiSetting::where('lembaga_id', $lembaga_id)->first();

        return view('lembaga.ai_setting.index', compact('aiSetting'));
    }

    public function testConnection(Request $request)
    {
        $lembaga_id = auth()->user()->lembaga_id;
        $aiSetting  = AiSetting::where('lembaga_id', $lembaga_id)->first();

        if (! $aiSetting || ! $aiSetting->ai_api_key) {
            return response()->json([
                'ok'    => false,
                'error' => 'API Key belum dikonfigurasi. Simpan pengaturan lebih dulu.',
            ], 422);
        }

        try {
            $service     = new SumopodAIService($aiSetting->ai_api_key, $aiSetting->ai_model ?? 'gpt-4o-mini');
            $suggestions = $service->generateFeedbackSuggestions(
                'Anisa',
                'Mengenal Warna',
                'Kognitif: Mampu menyebutkan minimal 3 warna',
                'BSH'
            );
            return response()->json([
                'ok'          => true,
                'message'     => 'Koneksi berhasil! Model: ' . ($aiSetting->ai_model ?? '-'),
                'sample'      => $suggestions[0] ?? '',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'    => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'ai_model'   => 'required|string|max:255',
            'ai_api_key' => 'nullable|string|max:1000',
        ]);

        $lembaga_id = auth()->user()->lembaga_id;

        $data = [
            'ai_provider' => 'sumopod',
            'ai_model'    => $request->ai_model,
        ];

        // Only update key if a new one is provided
        if ($request->filled('ai_api_key')) {
            $data['ai_api_key'] = $request->ai_api_key;
        }

        AiSetting::updateOrCreate(
            ['lembaga_id' => $lembaga_id],
            $data
        );

        return redirect()->route('lembaga.ai-setting.index')
            ->with('success', 'Pengaturan AI berhasil disimpan.');
    }
}
