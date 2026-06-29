<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsByGroup = [
            'Manajemen Siswa' => [
                ['name' => 'menu.siswa', 'label' => 'Siswa Sekolah'],
                ['name' => 'menu.pendaftaran', 'label' => 'Pendaftaran'],
                ['name' => 'menu.kelola-kelas', 'label' => 'Kelola Kelas'],
                ['name' => 'menu.presensi-siswa', 'label' => 'Presensi Siswa'],
                ['name' => 'menu.kesehatan-siswa', 'label' => 'Kesehatan Siswa'],
            ],
            'Kurikulum' => [
                ['name' => 'menu.matrikulasi', 'label' => 'Matrikulasi'],
                ['name' => 'menu.skala-capaian', 'label' => 'Skala Capaian'],
                ['name' => 'menu.agenda-belajar', 'label' => 'Agenda Belajar'],
                ['name' => 'menu.kegiatan-rutin', 'label' => 'Kegiatan Rutin'],
                ['name' => 'menu.pencapaian-siswa', 'label' => 'Pencapaian Siswa'],
                ['name' => 'menu.monev', 'label' => 'Monev'],
            ],
            'Lembaga & Guru' => [
                ['name' => 'menu.data-pengajar', 'label' => 'Data Guru'],
                ['name' => 'menu.presensi-guru', 'label' => 'Presensi Guru'],
                ['name' => 'menu.sarana', 'label' => 'Sarana'],
                ['name' => 'menu.menu-makanan', 'label' => 'Menu Makanan'],
            ],
            'Akuntansi' => [
                ['name' => 'menu.akun-coa', 'label' => 'Kode Rekening & Akun'],
                ['name' => 'menu.cashflow', 'label' => 'Cashflow'],
                ['name' => 'menu.jurnal-umum', 'label' => 'Jurnal Umum'],
            ],
            'RKAS' => [
                ['name' => 'menu.sumber-dana', 'label' => 'Sumber Dana'],
                ['name' => 'menu.rkas', 'label' => 'RKAS'],
                ['name' => 'menu.laporan-rkas', 'label' => 'Laporan RKAS'],
            ],
            'Biaya & Pembayaran' => [
                ['name' => 'menu.biaya-harian', 'label' => 'Biaya Bulanan'],
                ['name' => 'menu.diskon', 'label' => 'Diskon'],
                ['name' => 'menu.rekap-pembayaran', 'label' => 'Rekap Pembayaran'],
            ],
            'Masukan & Komunikasi' => [
                ['name' => 'menu.kritik-saran', 'label' => 'Kritik & Saran'],
                ['name' => 'menu.chat-orangtua', 'label' => 'Chat Orang Tua'],
            ],
            'Pengaturan' => [
                ['name' => 'menu.role', 'label' => 'Role'],
                ['name' => 'menu.pengguna', 'label' => 'Pengguna'],
                ['name' => 'menu.log-aktivitas', 'label' => 'Log Aktivitas'],
            ],
            'Pengaturan Akuntansi' => [
                ['name' => 'menu.setting-akuntansi', 'label' => 'Setting Akuntansi'],
            ],
            'Pengaturan AI' => [
                ['name' => 'menu.pengaturan-ai', 'label' => 'Pengaturan AI'],
            ],
        ];

        $allPermissionNames = [];

        foreach ($permissionsByGroup as $group => $permissions) {
            foreach ($permissions as $perm) {
                Permission::firstOrCreate([
                    'name' => $perm['name'],
                    'guard_name' => 'web',
                ]);
                $allPermissionNames[] = $perm['name'];
            }
        }

        // Mapping hak akses per role default (sesuai akses sebelum RBAC)
        $rolePermissions = [
            'Superadmin' => [],
            'Lembaga' => $allPermissionNames,
            'Admin Sekolah' => $allPermissionNames,
            'Wali Kelas' => [
                'menu.matrikulasi',
                'menu.agenda-belajar',
                'menu.kegiatan-rutin',
                'menu.pencapaian-siswa',
                'menu.presensi-siswa',
                'menu.kesehatan-siswa',
                'menu.monev',
                'menu.kritik-saran',
                'menu.chat-orangtua',
                'menu.pengaturan-ai',
                'menu.rekap-pembayaran',
            ],
            'Pengajar' => [
                'menu.kegiatan-rutin',
                'menu.agenda-belajar',
                'menu.matrikulasi',
                'menu.pencapaian-siswa',
                'menu.presensi-siswa',
            ],
            'Orang Tua' => [], // Route group terpisah sendiri, tanpa akses sidebar admin
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
}
