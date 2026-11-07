<?php

namespace Tests\Feature\Admin;

use App\Models\Anak;
use App\Models\AnakKelasRiwayat;
use App\Models\Kelas;
use App\Models\Lembaga;
use App\Models\Sekolah;
use App\Models\User;
use App\Services\KelasRiwayatService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class KelasRiwayatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['Admin Sekolah', 'Orang Tua'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    public function test_observer_membuat_dan_menutup_riwayat(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        $this->assertSame(1, AnakKelasRiwayat::where('anak_id', $f['anak']->id)->count());
        $this->assertSame('masuk', AnakKelasRiwayat::where('anak_id', $f['anak']->id)->first()->alasan);

        Carbon::setTestNow('2026-08-01 10:00:00');
        $f['anak']->update(['kelas_id' => $f['kelasB']->id]);

        $rows = AnakKelasRiwayat::where('anak_id', $f['anak']->id)->orderBy('id')->get();
        $this->assertCount(2, $rows);
        $this->assertSame('2026-08-01', Carbon::parse($rows[0]->selesai)->format('Y-m-d'));
        $this->assertSame('pindah', $rows[0]->alasan_selesai);
        $this->assertSame('pindah', $rows[1]->alasan);
        $this->assertNull($rows[1]->selesai);
    }

    public function test_kelas_pada_memakai_riwayat(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        Carbon::setTestNow('2026-08-01 10:00:00');
        $f['anak']->update(['kelas_id' => $f['kelasB']->id]);
        $f['anak']->refresh();

        $this->assertSame($f['kelasA']->id, $f['anak']->kelasPada(Carbon::parse('2026-07-15')));
        $this->assertSame($f['kelasB']->id, $f['anak']->kelasPada(Carbon::parse('2026-08-05')));
        $this->assertSame(
            $f['kelasB']->id,
            KelasRiwayatService::kelasPada($f['anak']->id, Carbon::parse('2026-08-05'))
        );
    }

    public function test_backfill_dari_migrasi_membentuk_baris_masuk(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        $row = AnakKelasRiwayat::where('anak_id', $f['anak']->id)->first();
        $this->assertNotNull($row);
        $this->assertSame($f['kelasA']->id, (int) $row->kelas_id);
        $this->assertSame($f['kelasA']->name, $row->kelas_nama);
        $this->assertSame('masuk', $row->alasan);
        $this->assertNotNull($row->tahun_ajaran);
    }

    /**
     * @return array{sekolah: Sekolah, admin: User, kelasA: Kelas, kelasB: Kelas, anak: Anak}
     */
    protected function createFixtures(): array
    {
        $lembaga = Lembaga::create([
            'name' => 'Yayasan Riwayat',
            'address' => 'Jl. Test No. 1',
            'phone' => '021-111',
        ]);
        $sekolah = Sekolah::create([
            'lembaga_id' => $lembaga->id,
            'name' => 'PAUD Riwayat',
            'address' => 'Jl. Test No. 2',
            'phone' => '021-222',
        ]);
        app(PermissionRegistrar::class)->setPermissionsTeamId($sekolah->id);

        $admin = User::factory()->create([
            'email' => 'admin-riwayat@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => $lembaga->id,
            'sekolah_id' => $sekolah->id,
        ]);
        $admin->assignRole('Admin Sekolah');

        $kelasA = Kelas::create(['sekolah_id' => $sekolah->id, 'name' => 'Kelas A', 'tingkat' => 1]);
        $kelasB = Kelas::create(['sekolah_id' => $sekolah->id, 'name' => 'Kelas B', 'tingkat' => 2]);

        $ortu = User::factory()->create(['email' => 'ortu-riwayat@test.com', 'sekolah_id' => $sekolah->id]);
        $ortu->assignRole('Orang Tua');

        $anak = Anak::create([
            'user_id' => $ortu->id,
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelasA->id,
            'name' => 'Anak Riwayat',
            'status' => 'approved',
        ]);

        return compact('sekolah', 'admin', 'kelasA', 'kelasB', 'anak');
    }
}
