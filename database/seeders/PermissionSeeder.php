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
                ['name' => 'menu.data-pengajar', 'label' => 'Data Pengajar'],
                ['name' => 'menu.presensi-guru', 'label' => 'Presensi Guru'],
                ['name' => 'menu.sarana', 'label' => 'Sarana'],
                ['name' => 'menu.menu-makanan', 'label' => 'Menu Makanan'],
            ],
            'Akuntansi' => [
                ['name' => 'menu.akun-coa', 'label' => 'Akun (COA)'],
                ['name' => 'menu.cashflow', 'label' => 'Cashflow'],
                ['name' => 'menu.jurnal-umum', 'label' => 'Jurnal Umum'],
                ['name' => 'menu.setting-akuntansi', 'label' => 'Setting Akuntansi'],
            ],
            'Biaya & Pembayaran' => [
                ['name' => 'menu.biaya-harian', 'label' => 'Biaya Bulanan'],
                ['name' => 'menu.diskon', 'label' => 'Diskon'],
                ['name' => 'menu.rekap-pembayaran', 'label' => 'Rekap Pembayaran'],
            ],
            'Laporan Keuangan' => [
                ['name' => 'menu.arus-kas', 'label' => 'Arus Kas (PSAK 2)'],
                ['name' => 'menu.neraca', 'label' => 'Neraca'],
                ['name' => 'menu.laba-rugi', 'label' => 'Laba Rugi'],
            ],
            'Masukan & Komunikasi' => [
                ['name' => 'menu.kritik-saran', 'label' => 'Kritik & Saran'],
                ['name' => 'menu.chat-orangtua', 'label' => 'Chat Orang Tua'],
                ['name' => 'menu.pengaturan-ai', 'label' => 'Pengaturan AI'],
            ],
            'Manajemen Akses' => [
                ['name' => 'menu.role', 'label' => 'Role'],
                ['name' => 'menu.pengguna', 'label' => 'Pengguna'],
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
            'Lembaga' => $allPermissionNames, // Super admin: akses semua
            'Admin Sekolah' => $allPermissionNames, // Full akses semua menu admin
            'Wali Kelas' => $allPermissionNames, // Share route dgn Admin Sekolah via middleware role:Admin Sekolah|Wali Kelas
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
