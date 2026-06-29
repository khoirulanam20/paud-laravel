<?php

namespace Tests\Feature\Superadmin;

use App\Models\AiSetting;
use App\Models\Lembaga;
use App\Models\Sekolah;
use App\Models\SekolahAiToken;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\SeedsAiTokens;
use Tests\TestCase;

class AiSettingTest extends TestCase
{
    use RefreshDatabase;
    use SeedsAiTokens;

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
            'email' => 'superadmin-ai@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => null,
            'sekolah_id' => null,
        ]);
        $user->assignRole('Superadmin');

        return compact('lembaga', 'user');
    }

    public function test_superadmin_can_view_ai_setting_page_with_provider_dropdown(): void
    {
        $fixtures = $this->createFixtures();

        $response = $this->actingAs($fixtures['user'])
            ->get(route('superadmin.ai-setting.index', ['lembaga_id' => $fixtures['lembaga']->id]));

        $response->assertOk();
        $response->assertSee('Konfigurasi AI Provider');
        $response->assertSee('SumoPod AI');
        $response->assertSee('Custom (OpenAI-compatible)');
    }

    public function test_lembaga_cannot_access_ai_setting(): void
    {
        $lembaga = Lembaga::firstOrFail();
        $user = User::factory()->create([
            'lembaga_id' => $lembaga->id,
            'sekolah_id' => null,
        ]);
        $user->assignRole('Lembaga');

        $this->actingAs($user)
            ->get(route('superadmin.ai-setting.index', ['lembaga_id' => $lembaga->id]))
            ->assertForbidden();
    }

    public function test_superadmin_can_view_ai_setting_page_when_no_settings_exist(): void
    {
        $fixtures = $this->createFixtures();

        AiSetting::where('lembaga_id', $fixtures['lembaga']->id)->delete();

        $response = $this->actingAs($fixtures['user'])
            ->get(route('superadmin.ai-setting.index', ['lembaga_id' => $fixtures['lembaga']->id]));

        $response->assertOk();
        $response->assertSee('AI Belum Dikonfigurasi');
        $response->assertSee('Konfigurasi AI Provider');
    }

    public function test_custom_provider_allows_http_base_url(): void
    {
        $fixtures = $this->createFixtures();

        $response = $this->actingAs($fixtures['user'])
            ->post(route('superadmin.ai-setting.update'), [
                'lembaga_id' => $fixtures['lembaga']->id,
                'ai_provider' => 'custom',
                'ai_base_url' => 'http://proxy.example.com/v1',
                'ai_model' => 'my-model',
                'ai_api_key' => 'secret-key',
            ]);

        $response->assertRedirect(route('superadmin.ai-setting.index', ['lembaga_id' => $fixtures['lembaga']->id, 'tab' => 'provider']));
        $response->assertSessionHas('success');

        $setting = AiSetting::where('lembaga_id', $fixtures['lembaga']->id)->first();
        $this->assertNotNull($setting);
        $this->assertSame('custom', $setting->ai_provider);
        $this->assertSame('http://proxy.example.com/v1', $setting->ai_base_url);
    }

    public function test_superadmin_can_save_custom_provider_with_base_url(): void
    {
        $fixtures = $this->createFixtures();
        $lembaga = $fixtures['lembaga'];

        $response = $this->actingAs($fixtures['user'])
            ->post(route('superadmin.ai-setting.update'), [
                'lembaga_id' => $lembaga->id,
                'ai_provider' => 'custom',
                'ai_base_url' => 'https://llm.internal.example/v1/',
                'ai_model' => 'my-model',
                'ai_api_key' => 'secret-key',
            ]);

        $response->assertRedirect(route('superadmin.ai-setting.index', ['lembaga_id' => $lembaga->id, 'tab' => 'provider']));
        $response->assertSessionHas('success');

        $setting = AiSetting::where('lembaga_id', $lembaga->id)->first();
        $this->assertNotNull($setting);
        $this->assertSame('custom', $setting->ai_provider);
        $this->assertSame('https://llm.internal.example/v1', $setting->ai_base_url);
    }

    public function test_superadmin_can_top_up_tokens_for_school(): void
    {
        $fixtures = $this->createFixtures();
        $sekolah = Sekolah::firstOrFail();

        $response = $this->actingAs($fixtures['user'])
            ->post(route('superadmin.ai-setting.tokens.store'), [
                'lembaga_id' => $fixtures['lembaga']->id,
                'sekolah_id' => $sekolah->id,
                'amount' => 25,
                'description' => 'Paket awal',
            ]);

        $response->assertRedirect(route('superadmin.ai-setting.index', [
            'lembaga_id' => $fixtures['lembaga']->id,
            'tab' => 'tokens',
        ]));
        $response->assertSessionHas('success');

        $this->assertSame(25, SekolahAiToken::query()->where('sekolah_id', $sekolah->id)->value('balance'));
    }
}
