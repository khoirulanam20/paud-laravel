<?php

namespace App\Http\Controllers\Lembaga;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Support\AiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AiSettingController extends Controller
{
    public function index()
    {
        $lembaga_id = auth()->user()->lembaga_id;
        $aiSetting  = AiSetting::where('lembaga_id', $lembaga_id)->first();
        $providers  = AiProvider::all();

        return view('lembaga.ai_setting.index', compact('aiSetting', 'providers'));
    }

    public function testConnection(Request $request)
    {
        $lembaga_id = auth()->user()->lembaga_id;
        $aiSetting  = AiSetting::where('lembaga_id', $lembaga_id)->first();

        if (! $aiSetting || ! $aiSetting->hasValidApiKey()) {
            return response()->json([
                'ok'    => false,
                'error' => $aiSetting?->apiKeyNeedsReentry()
                    ? 'API Key perlu disimpan ulang (enkripsi tidak valid).'
                    : 'API Key belum dikonfigurasi. Simpan pengaturan lebih dulu.',
            ], 422);
        }

        try {
            $service     = $aiSetting->toAiService();
            $suggestions = $service->generateFeedbackSuggestions(
                'Anisa',
                'Mengenal Warna',
                'Kognitif: Mampu menyebutkan minimal 3 warna',
                'Berkembang Sesuai Harapan (BSH)'
            );

            return response()->json([
                'ok'          => true,
                'message'     => 'Koneksi berhasil! Provider: ' . $aiSetting->providerLabel() . ' · Model: ' . ($aiSetting->ai_model ?? '-'),
                'sample'      => $suggestions[0] ?? '',
            ]);
        } catch (\Throwable $e) {
            Log::warning('AI test connection failed', [
                'lembaga_id' => $lembaga_id,
                'message'    => $e->getMessage(),
            ]);

            return response()->json([
                'ok'    => false,
                'error' => 'Koneksi AI gagal. Periksa provider, API Key, dan model.',
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'ai_provider' => ['required', 'string', Rule::in(AiProvider::keys())],
            'ai_model'    => 'required|string|max:255',
            'ai_api_key'  => 'nullable|string|max:1000',
            'ai_base_url' => [
                'nullable',
                'required_if:ai_provider,' . AiProvider::CUSTOM,
                'url',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    if ($request->input('ai_provider') !== AiProvider::CUSTOM || ! filled($value)) {
                        return;
                    }

                    try {
                        AiProvider::assertSafeCustomBaseUrl(rtrim((string) $value, '/'));
                    } catch (\InvalidArgumentException $e) {
                        $fail($e->getMessage());
                    }
                },
            ],
        ]);

        $lembaga_id = auth()->user()->lembaga_id;

        $data = [
            'ai_provider' => $request->ai_provider,
            'ai_model'    => $request->ai_model,
            'ai_base_url' => $request->ai_provider === AiProvider::CUSTOM
                ? rtrim($request->ai_base_url, '/')
                : null,
        ];

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
