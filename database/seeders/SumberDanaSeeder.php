<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\SumberDana;
use Illuminate\Database\Seeder;

class SumberDanaSeeder extends Seeder
{
    private array $defaults = [
        ['kode' => 'BOS', 'nama' => 'BOS', 'urutan' => 1],
        ['kode' => 'BOSDA', 'nama' => 'BOSDA', 'urutan' => 2],
        ['kode' => 'KOMITE', 'nama' => 'Komite Sekolah', 'urutan' => 3],
        ['kode' => 'SPP', 'nama' => 'Dana SPP / Swasta', 'urutan' => 4],
        ['kode' => 'LAIN', 'nama' => 'Lain-lain', 'urutan' => 5],
    ];

    public function run(): void
    {
        foreach (Sekolah::all() as $sekolah) {
            foreach ($this->defaults as $row) {
                SumberDana::firstOrCreate(
                    ['sekolah_id' => $sekolah->id, 'kode' => $row['kode']],
                    $row + ['sekolah_id' => $sekolah->id, 'is_aktif' => true],
                );
            }
        }
    }
}
