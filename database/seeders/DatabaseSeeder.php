<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Memanggil DemoSeeder yang meliputi RoleSeeder
     * ditambah data demo (kelas, anak, matrikulasi, pencapaian, token AI).
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
        $this->call(AkunSeeder::class);
        $this->call(SumberDanaSeeder::class);
        $this->call(PermissionSeeder::class);
    }
}
