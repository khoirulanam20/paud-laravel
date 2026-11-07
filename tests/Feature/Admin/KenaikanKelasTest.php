<?php

namespace Tests\Feature\Admin;

use App\Models\Anak;
use App\Models\AnakKelasRiwayat;
use App\Models\Kelas;
use App\Models\KenaikanKelasBatch;
use App\Models\Lembaga;
use App\Models\Pencapaian;
use App\Models\Pengajar;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class KenaikanKelasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // RoleSeeder/PermissionSeeder repo assign role tanpa team (gagal di skema teams);
        // tests ini menyiapkan role/permission minimal sendiri.
        foreach (['Admin Sekolah', 'Pengajar', 'Orang Tua'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $perm = Permission::firstOrCreate(['name' => 'menu.kelola-kelas', 'guard_name' => 'web']);
        Role::findByName('Admin Sekolah', 'web')->givePermissionTo($perm);
    }

    protected function setTeam(int $sekolahId): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($sekolahId);
    }

    public function test_mode_semester_naik_satu_tingkat_dan_teratas_lulus(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        Carbon::setTestNow('2026-07-15');
        $preview = $this->actingAs($f['admin'])->get(route('admin.kenaikan-kelas.preview', [
            'mode' => 'semester',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-15',
        ]));
        $preview->assertOk();

        $items = [
            ['anak_id' => $f['anakA']->id, 'aksi' => 'naik', 'kelas_tujuan_id' => $f['kelasB']->id],
            ['anak_id' => $f['anakB']->id, 'aksi' => 'naik', 'kelas_tujuan_id' => null], // akan lulus
        ];
        // anakB di tingkat teratas: aksi lulus tanpa tujuan
        $items[1] = ['anak_id' => $f['anakB']->id, 'aksi' => 'lulus', 'kelas_tujuan_id' => null];

        $response = $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'semester',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-15',
            'items' => $items,
        ]);

        $batch = KenaikanKelasBatch::first();
        $response->assertRedirect(route('admin.kenaikan-kelas.show', $batch));

        $this->assertSame($f['kelasB']->id, $f['anakA']->fresh()->kelas_id);
        $this->assertNull($f['anakB']->fresh()->kelas_id);
        $this->assertSame('lulus', $f['anakB']->fresh()->status);

        $this->assertDatabaseHas('kenaikan_kelas_batch_items', [
            'batch_id' => $batch->id, 'anak_id' => $f['anakA']->id, 'aksi' => 'naik',
        ]);
        $this->assertSame(1, $batch->fresh()->jumlah_naik);
        $this->assertSame(1, $batch->fresh()->jumlah_lulus);
    }

    public function test_mode_usia_naik_vs_tinggal_dan_tanpa_dob(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        $response = $this->actingAs($f['admin'])->get(route('admin.kenaikan-kelas.preview', [
            'mode' => 'usia',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-15',
        ]));
        $response->assertOk();

        $items = [
            ['anak_id' => $f['anakA']->id, 'aksi' => 'naik', 'kelas_tujuan_id' => $f['kelasB']->id], // 5 thn
            ['anak_id' => $f['anakMuda']->id, 'aksi' => 'tinggal'],
            ['anak_id' => $f['anakNoDob']->id, 'aksi' => 'tinggal', 'catatan' => 'manual'],
        ];

        $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'usia',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-15',
            'items' => $items,
        ])->assertRedirect();

        $this->assertSame($f['kelasB']->id, $f['anakA']->fresh()->kelas_id);
        $this->assertSame($f['kelasA']->id, $f['anakMuda']->fresh()->kelas_id);
        $this->assertSame($f['kelasA']->id, $f['anakNoDob']->fresh()->kelas_id);
    }

    public function test_override_per_siswa_dan_guard_semester_ganda(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        // Override: siswa yang seharusnya naik dipaksa tinggal
        $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'semester',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-15',
            'items' => [
                ['anak_id' => $f['anakA']->id, 'aksi' => 'tinggal'],
                ['anak_id' => $f['anakB']->id, 'aksi' => 'lulus'],
                ['anak_id' => $f['anakMuda']->id, 'aksi' => 'tinggal'],
                ['anak_id' => $f['anakNoDob']->id, 'aksi' => 'tinggal'],
            ],
        ])->assertRedirect();

        $this->assertSame($f['kelasA']->id, $f['anakA']->fresh()->kelas_id);

        // Guard: semester ganda TA asal sama ditolak
        $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'semester',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-08-01',
            'items' => [
                ['anak_id' => $f['anakA']->id, 'aksi' => 'tinggal'],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(1, KenaikanKelasBatch::count());
    }

    public function test_batalkan_memulihkan_kelas_status_riwayat(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'semester',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-15',
            'items' => [
                ['anak_id' => $f['anakA']->id, 'aksi' => 'naik', 'kelas_tujuan_id' => $f['kelasB']->id],
                ['anak_id' => $f['anakB']->id, 'aksi' => 'lulus'],
                ['anak_id' => $f['anakMuda']->id, 'aksi' => 'tinggal'],
                ['anak_id' => $f['anakNoDob']->id, 'aksi' => 'tinggal'],
            ],
        ])->assertRedirect();

        $batch = KenaikanKelasBatch::first();

        $this->actingAs($f['admin'])->delete(route('admin.kenaikan-kelas.destroy', $batch))
            ->assertRedirect(route('admin.kenaikan-kelas.index'));

        $this->assertSame($f['kelasA']->id, $f['anakA']->fresh()->kelas_id);
        $this->assertSame($f['kelasC']->id, $f['anakB']->fresh()->kelas_id);
        $this->assertSame('approved', $f['anakB']->fresh()->status);
        $this->assertSame('dibatalkan', $batch->fresh()->status);

        // Riwayat anakA kembali satu baris terbuka di kelas A
        $terbuka = AnakKelasRiwayat::where('anak_id', $f['anakA']->id)->whereNull('selesai')->get();
        $this->assertCount(1, $terbuka);
        $this->assertSame($f['kelasA']->id, (int) $terbuka->first()->kelas_id);
    }

    public function test_hanya_batch_terbaru_yang_bisa_dibatalkan(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        $mkBatch = fn (string $efektif) => $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'usia',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => $efektif,
            'items' => [['anak_id' => $f['anakA']->id, 'aksi' => 'tinggal']],
        ]);

        $mkBatch('2026-07-15');
        $mkBatch('2026-08-01');
        $this->assertSame(2, KenaikanKelasBatch::count());

        $lama = KenaikanKelasBatch::orderBy('id')->first();
        $this->actingAs($f['admin'])->delete(route('admin.kenaikan-kelas.destroy', $lama))
            ->assertSessionHasErrors('batch');
        $this->assertSame('diterapkan', $lama->fresh()->status);
    }

    public function test_tanpa_permission_ditolak_dan_isolasi_sekolah(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        $pengajarUser = User::factory()->create([
            'email' => 'pengajar-noperm@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => $f['lembaga']->id,
            'sekolah_id' => $f['sekolah']->id,
        ]);
        $pengajarUser->assignRole('Pengajar');

        $this->actingAs($pengajarUser)->get(route('admin.kenaikan-kelas.index'))->assertForbidden();

        // Sekolah lain tak bisa lihat batch
        $sekolah2 = Sekolah::create(['lembaga_id' => $f['lembaga']->id, 'name' => 'Sekolah Lain']);
        $this->setTeam($sekolah2->id);
        $admin2 = User::factory()->create([
            'email' => 'admin2@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => $f['lembaga']->id,
            'sekolah_id' => $sekolah2->id,
        ]);
        $admin2->assignRole('Admin Sekolah');

        $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'usia',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-15',
            'items' => [['anak_id' => $f['anakA']->id, 'aksi' => 'tinggal']],
        ])->assertRedirect();

        $batch = KenaikanKelasBatch::first();
        $this->actingAs($admin2)->get(route('admin.kenaikan-kelas.show', $batch))->assertForbidden();
    }

    public function test_laporan_pencapaian_historis_per_kelas_lama(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');
        $f = $this->createFixtures();

        // Anak sudah ada sejak Januari agar data Mei tercakup riwayat.
        $f['anakA']->update(['created_at' => Carbon::parse('2026-01-05')]);
        AnakKelasRiwayat::where('anak_id', $f['anakA']->id)
            ->update(['mulai' => '2026-01-05']);

        $shift = \App\Models\PresensiShift::create([
            'sekolah_id' => $f['sekolah']->id,
            'name' => 'Shift Pagi',
            'is_default' => true,
        ]);
        $pengajar = Pengajar::create([
            'user_id' => $f['admin']->id,
            'sekolah_id' => $f['sekolah']->id,
            'presensi_shift_id' => $shift->id,
            'name' => 'Guru Uji',
        ]);
        $matrikulasi = \App\Models\Matrikulasi::create([
            'sekolah_id' => $f['sekolah']->id,
            'indicator' => 'Indikator uji',
            'aspek' => 'Aspek uji',
        ]);
        $p = Pencapaian::create([
            'anak_id' => $f['anakA']->id,
            'matrikulasi_id' => $matrikulasi->id,
            'pengajar_id' => $pengajar->id,
            'created_at' => Carbon::parse('2026-05-10'),
            'updated_at' => Carbon::parse('2026-05-10'),
        ]);

        // Naik ke kelas B per 1 Juli
        $this->actingAs($f['admin'])->post(route('admin.kenaikan-kelas.store'), [
            'mode' => 'semester',
            'tanggal_acuan' => '2026-07-01',
            'tanggal_efektif' => '2026-07-01',
            'items' => [
                ['anak_id' => $f['anakA']->id, 'aksi' => 'naik', 'kelas_tujuan_id' => $f['kelasB']->id],
                ['anak_id' => $f['anakB']->id, 'aksi' => 'lulus'],
                ['anak_id' => $f['anakMuda']->id, 'aksi' => 'tinggal'],
                ['anak_id' => $f['anakNoDob']->id, 'aksi' => 'tinggal'],
            ],
        ])->assertRedirect();

        // Data Mei tetap milik kelas A, bukan B
        $this->assertSame(1, Pencapaian::diKelasPada($f['kelasA']->id, 'pencapaians.created_at')->count());
        $this->assertSame(0, Pencapaian::diKelasPada($f['kelasB']->id, 'pencapaians.created_at')
            ->where('pencapaians.id', $p->id)->count());
    }

    /**
     * @return array{lembaga: Lembaga, sekolah: Sekolah, admin: User, kelasA: Kelas, kelasB: Kelas, kelasC: Kelas, anakA: Anak, anakB: Anak, anakMuda: Anak, anakNoDob: Anak}
     */
    protected function createFixtures(): array
    {
        $lembaga = Lembaga::create([
            'name' => 'Yayasan Kenaikan',
            'address' => 'Jl. Test No. 1',
            'phone' => '021-111',
        ]);
        $sekolah = Sekolah::create([
            'lembaga_id' => $lembaga->id,
            'name' => 'PAUD Kenaikan',
            'address' => 'Jl. Test No. 2',
            'phone' => '021-222',
        ]);
        $this->setTeam($sekolah->id);

        $admin = User::factory()->create([
            'email' => 'admin-kenaikan@test.com',
            'password' => Hash::make('password'),
            'lembaga_id' => $lembaga->id,
            'sekolah_id' => $sekolah->id,
        ]);
        $admin->assignRole('Admin Sekolah');

        $kelasA = Kelas::create(['sekolah_id' => $sekolah->id, 'name' => 'Kelas A', 'tingkat' => 1, 'usia_min_bulan' => 36]);
        $kelasB = Kelas::create(['sekolah_id' => $sekolah->id, 'name' => 'Kelas B', 'tingkat' => 2, 'usia_min_bulan' => 48]);
        $kelasC = Kelas::create(['sekolah_id' => $sekolah->id, 'name' => 'Kelas C', 'tingkat' => 3, 'usia_min_bulan' => 60]);

        $ortu = User::factory()->create([
            'email' => 'ortu-kenaikan@test.com',
            'sekolah_id' => $sekolah->id,
        ]);
        $ortu->assignRole('Orang Tua');

        $mkAnak = fn (string $name, int $kelasId, ?string $dob) => Anak::create([
            'user_id' => $ortu->id,
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelasId,
            'name' => $name,
            'dob' => $dob,
            'status' => 'approved',
        ]);

        $anakA = $mkAnak('Anak Lima Tahun', $kelasA->id, '2021-01-01'); // 5,5 thn per Jul 2026
        $anakB = $mkAnak('Anak Tertinggi', $kelasC->id, '2019-01-01');
        $anakMuda = $mkAnak('Anak Muda', $kelasA->id, '2023-06-01'); // 3 thn
        $anakNoDob = $mkAnak('Anak Tanpa Dob', $kelasA->id, null);

        return compact('lembaga', 'sekolah', 'admin', 'kelasA', 'kelasB', 'kelasC', 'anakA', 'anakB', 'anakMuda', 'anakNoDob');
    }
}
