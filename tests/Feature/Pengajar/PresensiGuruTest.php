<?php

namespace Tests\Feature\Pengajar;

use App\Models\Lembaga;
use App\Models\Pengajar;
use App\Models\PresensiPengajar;
use App\Models\Sekolah;
use App\Models\SekolahHariLibur;
use App\Models\SekolahJamKerja;
use App\Models\User;
use App\Services\PresensiPengajarService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PresensiGuruTest extends TestCase
{
    use RefreshDatabase;

    private const SEKOLAH_LAT = -6.200000;

    private const SEKOLAH_LNG = 106.816666;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        // Sengaja tidak memakai RoleSeeder: seeder itu mengisi role global
        // (pivot sekolah_id NULL) yang tidak cocok untuk skema teams=true.
        // Role di sini dibuat langsung dan di-assign per sekolah.
        Role::firstOrCreate(['name' => 'Pengajar', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin Sekolah', 'guard_name' => 'web']);
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
     * @return array{sekolah: Sekolah, guruUser: User, pengajar: Pengajar, admin: User}
     */
    private function fixtures(): array
    {
        $lembaga = Lembaga::create([
            'name' => 'Yayasan Presensi Test',
            'status' => Lembaga::STATUS_ACTIVE,
        ]);

        $sekolah = Sekolah::create([
            'lembaga_id' => $lembaga->id,
            'name' => 'PAUD Presensi Test',
            'address' => 'Jl. Test',
            'phone' => '021',
            'location_coordinate' => self::SEKOLAH_LAT.', '.self::SEKOLAH_LNG,
            'radius_m' => 100,
            'status' => Sekolah::STATUS_ACTIVE,
        ]);

        $guruUser = User::factory()->create([
            'email' => 'guru-presensi@test.com',
            'password' => Hash::make('password'),
            'sekolah_id' => $sekolah->id,
        ]);
        $this->assignRoleForSchool($guruUser, 'Pengajar', $sekolah->id);

        $pengajar = Pengajar::create([
            'user_id' => $guruUser->id,
            'sekolah_id' => $sekolah->id,
            'name' => 'Guru Presensi',
        ]);

        $admin = User::factory()->create([
            'email' => 'admin-presensi@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => $lembaga->id,
            'sekolah_id' => $sekolah->id,
        ]);
        $this->assignRoleForSchool($admin, 'Admin Sekolah', $sekolah->id);

        return compact('sekolah', 'guruUser', 'pengajar', 'admin');
    }

    private function seedJadwal(Sekolah $sekolah): void
    {
        foreach (range(1, 7) as $hari) {
            $kerja = $hari <= 5;

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

    /** Koordinat di dalam radius (sekitar 22 meter dari titik sekolah). */
    private function dalamRadius(): array
    {
        return ['latitude' => -6.2002, 'longitude' => 106.816666, 'accuracy' => 10];
    }

    /** Koordinat jauh di luar radius. */
    private function luarRadius(): array
    {
        return ['latitude' => -6.300000, 'longitude' => 106.816666, 'accuracy' => 10];
    }

    public function test_pengajar_dapat_check_in_di_dalam_radius(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse('2026-10-27 07:05:00')); // Selasa

        $response = $this->actingAs($f['guruUser'])
            ->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius());

        $response->assertRedirect(route('pengajar.presensi-guru.index'));

        $this->assertDatabaseHas('presensi_pengajars', [
            'pengajar_id' => $f['pengajar']->id,
            'sekolah_id' => $f['sekolah']->id,
            'hadir' => 1,
            'sumber' => 'self',
            'terlambat_masuk' => 0,
        ]);

        Carbon::setTestNow();
    }

    public function test_check_in_ditolak_di_luar_radius(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse('2026-10-27 07:05:00'));

        $this->actingAs($f['guruUser'])
            ->from(route('pengajar.presensi-guru.index'))
            ->post(route('pengajar.presensi-guru.check-in'), $this->luarRadius())
            ->assertSessionHasErrors('presensi');

        $this->assertDatabaseCount('presensi_pengajars', 0);

        Carbon::setTestNow();
    }

    public function test_check_in_ditolak_kalau_koordinat_sekolah_belum_diset(): void
    {
        $f = $this->fixtures();
        $f['sekolah']->update(['location_coordinate' => null]);

        Carbon::setTestNow(Carbon::parse('2026-10-27 07:05:00'));

        $this->actingAs($f['guruUser'])
            ->from(route('pengajar.presensi-guru.index'))
            ->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius())
            ->assertSessionHasErrors('presensi');

        $this->assertDatabaseCount('presensi_pengajars', 0);

        Carbon::setTestNow();
    }

    public function test_check_in_ditolak_saat_hari_libur(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        $tanggal = '2026-10-27';
        SekolahHariLibur::withoutSekolahScope()->create([
            'sekolah_id' => $f['sekolah']->id,
            'tanggal' => $tanggal,
            'keterangan' => 'Libur khusus',
        ]);

        Carbon::setTestNow(Carbon::parse($tanggal.' 07:05:00'));

        $this->actingAs($f['guruUser'])
            ->from(route('pengajar.presensi-guru.index'))
            ->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius())
            ->assertSessionHasErrors('presensi');

        $this->assertDatabaseCount('presensi_pengajars', 0);

        Carbon::setTestNow();
    }

    public function test_check_in_ganda_ditolak(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse('2026-10-27 07:05:00'));

        $this->actingAs($f['guruUser'])->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius());

        $this->actingAs($f['guruUser'])
            ->from(route('pengajar.presensi-guru.index'))
            ->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius())
            ->assertSessionHasErrors('presensi');

        $this->assertDatabaseCount('presensi_pengajars', 1);

        Carbon::setTestNow();
    }

    public function test_check_out_tanpa_check_in_ditolak(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse('2026-10-27 13:05:00'));

        $this->actingAs($f['guruUser'])
            ->from(route('pengajar.presensi-guru.index'))
            ->post(route('pengajar.presensi-guru.check-out'), $this->dalamRadius())
            ->assertSessionHasErrors('presensi');

        $this->assertDatabaseCount('presensi_pengajars', 0);

        Carbon::setTestNow();
    }

    public function test_check_in_lalu_check_out_menyimpan_jam_pulang(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse('2026-10-27 07:05:00'));
        $this->actingAs($f['guruUser'])->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius());

        Carbon::setTestNow(Carbon::parse('2026-10-27 13:00:00'));
        $this->actingAs($f['guruUser'])
            ->post(route('pengajar.presensi-guru.check-out'), $this->dalamRadius())
            ->assertRedirect(route('pengajar.presensi-guru.index'));

        $presensi = PresensiPengajar::withoutSekolahScope()->first();
        $this->assertNotNull($presensi->check_in_at);
        $this->assertNotNull($presensi->check_out_at);
        $this->assertFalse($presensi->telat_pulang);

        Carbon::setTestNow();
    }

    public function test_telat_masuk_dihitung_dari_toleransi(): void
    {
        // Jam masuk 07:00 + toleransi 10 menit. Check-in 07:30 => telat 20 menit.
        $presensi = $this->checkInAt('2026-10-27 07:30:00');

        $this->assertTrue($presensi->terlambat_masuk);
        $this->assertEquals(20, $presensi->menit_telat_masuk);
    }

    public function test_telat_pulang_dihitung_dari_toleransi(): void
    {
        // Jam pulang 13:00 + toleransi 10 menit. Check-out 13:40 => telat 30 menit.
        $presensi = $this->checkInOut('2026-10-27 07:05:00', '2026-10-27 13:40:00');

        $this->assertTrue($presensi->telat_pulang);
        $this->assertEquals(30, $presensi->menit_telat_pulang);
    }

    public function test_hari_libur_mingguan_ditolak(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        // 2026-11-01 adalah Minggu (is_kerja false).
        Carbon::setTestNow(Carbon::parse('2026-11-01 07:05:00'));

        $this->actingAs($f['guruUser'])
            ->from(route('pengajar.presensi-guru.index'))
            ->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius())
            ->assertSessionHasErrors('presensi');

        $this->assertDatabaseCount('presensi_pengajars', 0);

        Carbon::setTestNow();
    }

    public function test_rekap_kehadiran_menghitung_hari_kerja_dan_telat(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse('2026-10-27 07:30:00')); // Selasa, telat
        $this->actingAs($f['guruUser'])->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius());
        Carbon::setTestNow();

        $rekap = app(PresensiPengajarService::class)->rekapKehadiran(
            $f['pengajar'],
            Carbon::parse('2026-10-26'), // Senin
            Carbon::parse('2026-10-30'), // Jumat
        );

        $this->assertEquals(5, $rekap['hari_kerja']);
        $this->assertEquals(1, $rekap['hadir']);
        $this->assertEquals(1, $rekap['terlambat']);
        $this->assertEquals(20.0, $rekap['persentase']);
    }

    public function test_admin_dapat_mengubah_pengaturan_jam_kerja(): void
    {
        $f = $this->fixtures();

        $this->actingAs($f['admin'])
            ->put(route('admin.presensi-guru.pengaturan.update'), [
                'radius_m' => 250,
                'jadwal' => [
                    1 => ['is_kerja' => 1, 'jam_masuk' => '08:00', 'jam_pulang' => '14:00', 'toleransi_masuk_menit' => 15, 'toleransi_pulang_menit' => 5],
                ],
            ])
            ->assertRedirect(route('admin.presensi-guru.pengaturan'));

        $this->assertEquals(250, $f['sekolah']->fresh()->radius_m);

        $senin = SekolahJamKerja::withoutSekolahScope()
            ->where('sekolah_id', $f['sekolah']->id)
            ->where('hari', 1)
            ->first();

        $this->assertTrue($senin->is_kerja);
        $this->assertEquals('08:00:00', $senin->jam_masuk);
        $this->assertEquals(15, $senin->toleransi_masuk_menit);
    }

    public function test_admin_dapat_menambah_dan_menghapus_hari_libur(): void
    {
        $f = $this->fixtures();

        $this->actingAs($f['admin'])
            ->post(route('admin.presensi-guru.pengaturan.libur.store'), [
                'tanggal' => '2026-12-25',
                'keterangan' => 'Natal',
            ])
            ->assertRedirect(route('admin.presensi-guru.pengaturan'));

        $this->assertDatabaseHas('sekolah_hari_liburs', [
            'sekolah_id' => $f['sekolah']->id,
            'tanggal' => '2026-12-25',
            'keterangan' => 'Natal',
        ]);

        $libur = SekolahHariLibur::withoutSekolahScope()
            ->where('sekolah_id', $f['sekolah']->id)
            ->first();

        $this->actingAs($f['admin'])
            ->delete(route('admin.presensi-guru.pengaturan.libur.destroy', $libur))
            ->assertRedirect(route('admin.presensi-guru.pengaturan'));

        $this->assertDatabaseCount('sekolah_hari_liburs', 0);
    }

    public function test_admin_dapat_mengisi_jam_masuk_manual(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        $this->actingAs($f['admin'])
            ->post(route('admin.presensi-guru.store'), [
                'tanggal' => '2026-10-27',
                'presensi' => [
                    $f['pengajar']->id => [
                        'hadir' => 1,
                        'status' => null,
                        'check_in' => '07:30',
                    ],
                ],
            ])
            ->assertRedirect();

        $presensi = PresensiPengajar::withoutSekolahScope()->first();

        $this->assertEquals('07:30', $presensi->check_in_at->format('H:i'));
        $this->assertEquals('manual', $presensi->sumber);
        $this->assertTrue($presensi->terlambat_masuk);
    }

    public function test_halaman_presensi_pengajar_dapat_dibuka(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        $this->actingAs($f['guruUser'])
            ->get(route('pengajar.presensi-guru.index'))
            ->assertOk()
            ->assertSee('Presensi Saya');
    }

    public function test_admin_dapat_membuka_halaman_pengaturan_presensi(): void
    {
        $f = $this->fixtures();

        $this->actingAs($f['admin'])
            ->get(route('admin.presensi-guru.pengaturan'))
            ->assertOk()
            ->assertSee('Pengaturan Presensi Guru');

        // Jadwal 7 hari otomatis dibuat kalau belum ada.
        $this->assertDatabaseCount('sekolah_jam_kerjas', 7);
    }

    public function test_admin_dapat_membuka_halaman_presensi_harian(): void
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        $this->actingAs($f['admin'])
            ->get(route('admin.presensi-guru.index'))
            ->assertOk()
            ->assertSee('Presensi Guru');
    }

    private function checkInAt(string $waktu): PresensiPengajar
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse($waktu));
        $this->actingAs($f['guruUser'])->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius());
        Carbon::setTestNow();

        return PresensiPengajar::withoutSekolahScope()->firstOrFail();
    }

    private function checkInOut(string $masuk, string $pulang): PresensiPengajar
    {
        $f = $this->fixtures();
        $this->seedJadwal($f['sekolah']);

        Carbon::setTestNow(Carbon::parse($masuk));
        $this->actingAs($f['guruUser'])->post(route('pengajar.presensi-guru.check-in'), $this->dalamRadius());

        Carbon::setTestNow(Carbon::parse($pulang));
        $this->actingAs($f['guruUser'])->post(route('pengajar.presensi-guru.check-out'), $this->dalamRadius());
        Carbon::setTestNow();

        return PresensiPengajar::withoutSekolahScope()->firstOrFail();
    }
}
