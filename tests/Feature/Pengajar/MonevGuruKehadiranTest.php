<?php

namespace Tests\Feature\Pengajar;

use App\Models\AiSetting;
use App\Models\Lembaga;
use App\Models\MonevGuruKriteria;
use App\Models\Pengajar;
use App\Models\PresensiPengajar;
use App\Models\Sekolah;
use App\Models\SekolahJamKerja;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\SeedsAiTokens;
use Tests\TestCase;

class MonevGuruKehadiranTest extends TestCase
{
    use RefreshDatabase;
    use SeedsAiTokens;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        // ponytail: RoleSeeder global pivot tidak cocok teams=true; assign per sekolah.
        Role::firstOrCreate(['name' => 'Admin Sekolah', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Pengajar', 'guard_name' => 'web']);
    }

    private function assignRoleForSchool(User $user, string $role, int $sekolahId): void
    {
        $registrar = app(PermissionRegistrar::class);
        $previous = $registrar->getPermissionsTeamId();

        $registrar->setPermissionsTeamId($sekolahId);
        $user->assignRole($role);
        $registrar->setPermissionsTeamId($previous);
        $registrar->forgetCachedPermissions();
    }

    /**
     * @return array{
     *     lembaga: Lembaga,
     *     sekolah: Sekolah,
     *     admin: User,
     *     pengajar: Pengajar,
     *     kriteriaPresensi: MonevGuruKriteria,
     *     kriteriaLain: MonevGuruKriteria
     * }
     */
    private function fixtures(): array
    {
        $lembaga = Lembaga::create([
            'name' => 'Yayasan Monev Kehadiran',
            'status' => Lembaga::STATUS_ACTIVE,
        ]);

        $sekolah = Sekolah::create([
            'lembaga_id' => $lembaga->id,
            'name' => 'PAUD Monev Kehadiran',
            'address' => 'Jl. Test',
            'phone' => '021',
            'status' => Sekolah::STATUS_ACTIVE,
        ]);

        $admin = User::factory()->create([
            'email' => 'admin-monev-kehadiran@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => $lembaga->id,
            'sekolah_id' => $sekolah->id,
        ]);
        $this->assignRoleForSchool($admin, 'Admin Sekolah', $sekolah->id);

        $guruUser = User::factory()->create([
            'email' => 'guru-monev-kehadiran@test.com',
            'sekolah_id' => $sekolah->id,
        ]);
        $this->assignRoleForSchool($guruUser, 'Pengajar', $sekolah->id);

        $pengajar = Pengajar::create([
            'user_id' => $guruUser->id,
            'sekolah_id' => $sekolah->id,
            'name' => 'Guru Kehadiran',
        ]);

        MonevGuruKriteria::seedDefaultsForSekolah($sekolah->id);

        $kriteriaPresensi = MonevGuruKriteria::query()
            ->where('sekolah_id', $sekolah->id)
            ->where('is_presensi', true)
            ->firstOrFail();

        $kriteriaLain = MonevGuruKriteria::query()
            ->where('sekolah_id', $sekolah->id)
            ->where('is_presensi', false)
            ->firstOrFail();

        AiSetting::create([
            'lembaga_id' => $lembaga->id,
            'ai_provider' => 'sumopod',
            'ai_api_key' => 'test-api-key',
            'ai_model' => 'gpt-4o-mini',
        ]);

        $this->seedAiTokens($sekolah, 10, $admin);

        return compact('lembaga', 'sekolah', 'admin', 'pengajar', 'kriteriaPresensi', 'kriteriaLain');
    }

    private function seedJadwalKerja(Sekolah $sekolah, bool $semuaLibur = false): void
    {
        foreach (range(1, 7) as $hari) {
            $kerja = $semuaLibur ? false : $hari <= 5;

            SekolahJamKerja::withoutSekolahScope()->create([
                'sekolah_id' => $sekolah->id,
                'hari' => $hari,
                'is_kerja' => $kerja,
                'jam_masuk' => $kerja ? '07:00:00' : null,
                'jam_pulang' => $kerja ? '13:00:00' : null,
                'toleransi_masuk_menit' => 10,
                'toleransi_pulang_menit' => 10,
            ]);
        }
    }

    public function test_endpoint_mengembalikan_skor_dan_catatan_dari_presensi(): void
    {
        $f = $this->fixtures();
        $this->seedJadwalKerja($f['sekolah']);

        // Senin–Rabu 2026-10-26..28
        foreach (['2026-10-26', '2026-10-27', '2026-10-28'] as $tanggal) {
            PresensiPengajar::withoutSekolahScope()->create([
                'sekolah_id' => $f['sekolah']->id,
                'pengajar_id' => $f['pengajar']->id,
                'tanggal' => $tanggal,
                'hadir' => true,
                'status' => 'hadir',
                'check_in_at' => $tanggal.' 07:05:00',
                'check_out_at' => $tanggal.' 13:00:00',
                'sumber' => PresensiPengajar::SUMBER_SELF,
                'terlambat_masuk' => false,
                'telat_pulang' => false,
            ]);
        }

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => '{"skor":72,"catatan":"Kehadiran baik dengan sedikit keterlambatan."}',
                    ],
                ]],
            ], 200),
        ]);

        $response = $this->actingAs($f['admin'])->postJson(route('admin.ai.monev-guru-kehadiran-suggestions'), [
            'pengajar_id' => $f['pengajar']->id,
            'kriteria_id' => $f['kriteriaPresensi']->id,
            'periode_mulai' => '2026-10-26',
            'periode_selesai' => '2026-10-28',
        ]);

        $response->assertOk();
        $response->assertJsonPath('skor', 72);
        $response->assertJsonPath('catatan', 'Kehadiran baik dengan sedikit keterlambatan.');
        $response->assertJsonStructure(['rekap' => ['hari_kerja', 'hadir', 'persentase'], 'token_balance']);
        $this->assertSame(3, $response->json('rekap.hadir'));
        $this->assertSame(3, $response->json('rekap.hari_kerja'));
    }

    public function test_endpoint_gagal_kalau_periode_tidak_ada_hari_kerja(): void
    {
        $f = $this->fixtures();
        $this->seedJadwalKerja($f['sekolah'], semuaLibur: true);

        $response = $this->actingAs($f['admin'])->postJson(route('admin.ai.monev-guru-kehadiran-suggestions'), [
            'pengajar_id' => $f['pengajar']->id,
            'kriteria_id' => $f['kriteriaPresensi']->id,
            'periode_mulai' => '2026-10-26',
            'periode_selesai' => '2026-10-28',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonPath('error', 'Tidak ada hari kerja pada periode ini. Periksa pengaturan jadwal.');
        Http::assertNothingSent();
    }

    public function test_endpoint_gagal_kalau_kriteria_bukan_is_presensi(): void
    {
        $f = $this->fixtures();
        $this->seedJadwalKerja($f['sekolah']);

        $response = $this->actingAs($f['admin'])->postJson(route('admin.ai.monev-guru-kehadiran-suggestions'), [
            'pengajar_id' => $f['pengajar']->id,
            'kriteria_id' => $f['kriteriaLain']->id,
            'periode_mulai' => '2026-10-26',
            'periode_selesai' => '2026-10-28',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['kriteria_id']);
    }

    public function test_endpoint_gagal_kalau_pengajar_bukan_milik_sekolah(): void
    {
        $f = $this->fixtures();
        $this->seedJadwalKerja($f['sekolah']);

        $lembagaLain = Lembaga::create([
            'name' => 'Yayasan Lain',
            'status' => Lembaga::STATUS_ACTIVE,
        ]);
        $sekolahLain = Sekolah::create([
            'lembaga_id' => $lembagaLain->id,
            'name' => 'PAUD Lain',
            'address' => 'Jl. Lain',
            'phone' => '022',
            'status' => Sekolah::STATUS_ACTIVE,
        ]);
        $guruLain = User::factory()->create(['sekolah_id' => $sekolahLain->id]);
        $this->assignRoleForSchool($guruLain, 'Pengajar', $sekolahLain->id);
        $pengajarLain = Pengajar::create([
            'user_id' => $guruLain->id,
            'sekolah_id' => $sekolahLain->id,
            'name' => 'Guru Sekolah Lain',
        ]);

        $response = $this->actingAs($f['admin'])->postJson(route('admin.ai.monev-guru-kehadiran-suggestions'), [
            'pengajar_id' => $pengajarLain->id,
            'kriteria_id' => $f['kriteriaPresensi']->id,
            'periode_mulai' => '2026-10-26',
            'periode_selesai' => '2026-10-28',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['pengajar_id']);
    }

    public function test_is_presensi_unique_per_sekolah_divalidasi(): void
    {
        $f = $this->fixtures();

        $response = $this->actingAs($f['admin'])->post(route('admin.monev-guru.kriteria.store'), [
            'nama' => 'Kriteria Presensi Kedua',
            'deskripsi' => 'Duplikat',
            'bobot' => 10,
            'urutan' => 99,
            'is_active' => 1,
            'is_presensi' => 1,
        ]);

        $response->assertSessionHasErrors(['is_presensi']);
        $this->assertDatabaseMissing('monev_guru_kriterias', [
            'sekolah_id' => $f['sekolah']->id,
            'nama' => 'Kriteria Presensi Kedua',
        ]);
    }

    public function test_is_presensi_dapat_dipindah_ke_kriteria_lain(): void
    {
        $f = $this->fixtures();

        $this->actingAs($f['admin'])->put(route('admin.monev-guru.kriteria.update', $f['kriteriaPresensi']), [
            'nama' => $f['kriteriaPresensi']->nama,
            'deskripsi' => $f['kriteriaPresensi']->deskripsi,
            'bobot' => $f['kriteriaPresensi']->bobot,
            'urutan' => $f['kriteriaPresensi']->urutan,
            'is_active' => 1,
            // unchecked → is_presensi false
        ])->assertRedirect(route('admin.monev-guru.kriteria.index'));

        $this->assertFalse($f['kriteriaPresensi']->fresh()->is_presensi);

        $this->actingAs($f['admin'])->put(route('admin.monev-guru.kriteria.update', $f['kriteriaLain']), [
            'nama' => $f['kriteriaLain']->nama,
            'deskripsi' => $f['kriteriaLain']->deskripsi,
            'bobot' => $f['kriteriaLain']->bobot,
            'urutan' => $f['kriteriaLain']->urutan,
            'is_active' => 1,
            'is_presensi' => 1,
        ])->assertRedirect(route('admin.monev-guru.kriteria.index'));

        $this->assertTrue($f['kriteriaLain']->fresh()->is_presensi);
        $this->assertFalse($f['kriteriaPresensi']->fresh()->is_presensi);
    }
}
