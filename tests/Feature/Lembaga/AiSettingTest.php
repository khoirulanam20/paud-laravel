<?php

namespace Tests\Feature\Lembaga;

use App\Models\AiSetting;
use App\Models\Lembaga;
use App\Models\User;
use App\Support\AiProvider;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /**
     * @return array{lembaga: Lembaga, user: User}
     */
    protected function createFixtures(): array
    {
        $lembaga = Lembaga::firstOrFail();

        $user = User::factory()->create([
            'email' => 'lembaga-ai@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => $lembaga->id,
            'sekolah_id' => null,
        ]);
        $user->assignRole('Lembaga');

        return compact('lembaga', 'user');
    }

    public function test_lembaga_can_view_ai_setting_page_with_provider_dropdown(): void
    {
        $fixtures = $this->createFixtures();

        $response = $this->actingAs($fixtures['user'])
            ->get(route('lembaga.ai-setting.index'));

        $response->assertOk();
        $response->assertSee('Konfigurasi AI Provider');
        $response->assertSee('SumoPod AI');
        $response->assertSee('Custom (OpenAI-compatible)');
    }

    public function test_lembaga_can_view_ai_setting_page_when_no_settings_exist(): void
    {
        $fixtures = $this->createFixtures();

        AiSetting::where('lembaga_id', $fixtures['lembaga']->id)->delete();

        $response = $this->actingAs($fixtures['user'])
            ->get(route('lembaga.ai-setting.index'));

        $response->assertOk();
        $response->assertSee('AI Belum Dikonfigurasi');
        $response->assertSee('Konfigurasi AI Provider');
    }

    public function test_custom_provider_rejects_http_base_url(): void
    {
        $fixtures = $this->createFixtures();

        $response = $this->actingAs($fixtures['user'])
            ->from(route('lembaga.ai-setting.index'))
            ->post(route('lembaga.ai-setting.update'), [
                'ai_provider' => 'custom',
                'ai_base_url' => 'http://proxy.example.com/v1',
                'ai_model' => 'my-model',
                'ai_api_key' => 'secret-key',
            ]);

        $response->assertRedirect(route('lembaga.ai-setting.index'));
        $response->assertSessionHasErrors('ai_base_url');
    }

    public function test_custom_provider_rejects_localhost_base_url(): void
    {
        $fixtures = $this->createFixtures();

        $response = $this->actingAs($fixtures['user'])
            ->from(route('lembaga.ai-setting.index'))
            ->post(route('lembaga.ai-setting.update'), [
                'ai_provider' => 'custom',
                'ai_base_url' => 'https://127.0.0.1/v1',
                'ai_model' => 'my-model',
                'ai_api_key' => 'secret-key',
            ]);

        $response->assertRedirect(route('lembaga.ai-setting.index'));
        $response->assertSessionHasErrors('ai_base_url');
    }

    public function test_test_connection_does_not_expose_api_error_body(): void
    {
        $fixtures = $this->createFixtures();

        AiSetting::updateOrCreate(
            ['lembaga_id' => $fixtures['lembaga']->id],
            [
                'ai_provider' => 'sumopod',
                'ai_api_key' => 'test-api-key',
                'ai_model' => 'gpt-4o-mini',
            ]
        );

        Http::fake([
            'ai.sumopod.com/*' => Http::response([
                'error' => ['message' => 'secret-billing-detail-should-not-leak'],
            ], 401),
        ]);

        $response = $this->actingAs($fixtures['user'])
            ->postJson(route('lembaga.ai-setting.test'));

        $response->assertStatus(500);
        $response->assertJson([
            'ok' => false,
            'error' => 'Koneksi AI gagal. Periksa provider, API Key, dan model.',
        ]);
        $response->assertJsonMissing(['secret-billing-detail-should-not-leak']);
    }

    public function test_lembaga_can_save_custom_provider_with_base_url(): void
    {
        $fixtures = $this->createFixtures();
        $lembaga = $fixtures['lembaga'];

        $response = $this->actingAs($fixtures['user'])
            ->post(route('lembaga.ai-setting.update'), [
                'ai_provider' => 'custom',
                'ai_base_url' => 'https://llm.internal.example/v1/',
                'ai_model' => 'my-model',
                'ai_api_key' => 'secret-key',
            ]);

        $response->assertRedirect(route('lembaga.ai-setting.index'));
        $response->assertSessionHas('success');

        $setting = AiSetting::where('lembaga_id', $lembaga->id)->first();
        $this->assertNotNull($setting);
        $this->assertSame('custom', $setting->ai_provider);
        $this->assertSame('https://llm.internal.example/v1', $setting->ai_base_url);
        $this->assertSame('my-model', $setting->ai_model);
        $this->assertTrue($setting->hasValidApiKey());
    }

    public function test_custom_provider_without_base_url_returns_validation_error(): void
    {
        $fixtures = $this->createFixtures();

        $response = $this->actingAs($fixtures['user'])
            ->from(route('lembaga.ai-setting.index'))
            ->post(route('lembaga.ai-setting.update'), [
                'ai_provider' => 'custom',
                'ai_model' => 'my-model',
                'ai_api_key' => 'secret-key',
            ]);

        $response->assertRedirect(route('lembaga.ai-setting.index'));
        $response->assertSessionHasErrors('ai_base_url');
    }

    public function test_preset_provider_clears_custom_base_url(): void
    {
        $fixtures = $this->createFixtures();
        $lembaga = $fixtures['lembaga'];

        AiSetting::create([
            'lembaga_id' => $lembaga->id,
            'ai_provider' => 'custom',
            'ai_base_url' => 'https://old.example/v1',
            'ai_api_key' => encrypt('old-key'),
            'ai_model' => 'old-model',
        ]);

        $response = $this->actingAs($fixtures['user'])
            ->post(route('lembaga.ai-setting.update'), [
                'ai_provider' => 'openai',
                'ai_model' => 'gpt-4o-mini',
            ]);

        $response->assertRedirect(route('lembaga.ai-setting.index'));

        $setting = AiSetting::where('lembaga_id', $lembaga->id)->firstOrFail();
        $this->assertSame('openai', $setting->ai_provider);
        $this->assertNull($setting->ai_base_url);
    }

    public function test_to_ai_service_resolves_base_url_per_preset(): void
    {
        $lembaga = Lembaga::firstOrFail();

        $setting = AiSetting::create([
            'lembaga_id' => $lembaga->id,
            'ai_provider' => 'groq',
            'ai_api_key' => encrypt('test-key'),
            'ai_model' => 'llama-3.3-70b-versatile',
        ]);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => "1. Saran pertama\n2. Saran kedua\n3. Saran ketiga"]],
                ],
            ], 200),
        ]);

        $service = $setting->toAiService();
        $suggestions = $service->generateFeedbackSuggestions(
            'Anisa',
            'Mengenal Warna',
            'Kognitif: Mampu menyebutkan minimal 3 warna',
            'Berkembang Sesuai Harapan (BSH)'
        );

        $this->assertCount(3, $suggestions);
        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://api.groq.com/openai/v1/chat/completions');
        });
    }

    public function test_to_ai_service_resolves_custom_base_url(): void
    {
        $lembaga = Lembaga::firstOrFail();

        $setting = AiSetting::create([
            'lembaga_id' => $lembaga->id,
            'ai_provider' => 'custom',
            'ai_base_url' => 'https://proxy.example.com/v1',
            'ai_api_key' => encrypt('test-key'),
            'ai_model' => 'custom-model',
        ]);

        Http::fake([
            'proxy.example.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => "1. Satu\n2. Dua\n3. Tiga"]],
                ],
            ], 200),
        ]);

        $setting->toAiService()->generateFeedbackSuggestions(
            'Anisa',
            'Mengenal Warna',
            'Kognitif: Mampu menyebutkan minimal 3 warna',
            'Berkembang Sesuai Harapan (BSH)'
        );

        Http::assertSent(function ($request) {
            return $request->url() === 'https://proxy.example.com/v1/chat/completions';
        });
    }

    public function test_ai_provider_resolve_base_url_uses_sumopod_default(): void
    {
        $this->assertSame(
            'https://ai.sumopod.com/v1',
            AiProvider::resolveBaseUrl('sumopod', null)
        );
    }
}
