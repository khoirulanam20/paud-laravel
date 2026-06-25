<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\AkuntansiSetting;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AkunSeeder extends Seeder
{
    private array $systemAkuns = [
        ['kode' => 'SYS.KAS', 'nama' => 'Kas', 'jenis' => 'aset', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => 'SYS.BANK', 'nama' => 'Bank', 'jenis' => 'aset', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => 'SYS.PIUTANG', 'nama' => 'Piutang SPP', 'jenis' => 'aset', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => 'SYS.PDM', 'nama' => 'Pendapatan Diterima di Muka', 'jenis' => 'liabilitas', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'kredit'],
    ];

    private array $pendapatan = [
        ['kode' => 'P.01', 'nama' => 'Pendapatan SPP', 'uraian' => 'Pendapatan SPP / Iuran Bulanan'],
        ['kode' => 'P.02', 'nama' => 'Pendapatan Pendaftaran', 'uraian' => 'Pendapatan Uang Pangkal / Pendaftaran'],
        ['kode' => 'P.03', 'nama' => 'Pendapatan BOS', 'uraian' => 'Pendapatan BOS'],
        ['kode' => 'P.04', 'nama' => 'Pendapatan BOSDA', 'uraian' => 'Pendapatan BOSDA'],
        ['kode' => 'P.05', 'nama' => 'Pendapatan Komite', 'uraian' => 'Pendapatan Komite Sekolah'],
        ['kode' => 'P.06', 'nama' => 'Pendapatan Swadaya', 'uraian' => 'Pendapatan Swadaya Masyarakat'],
        ['kode' => 'P.99', 'nama' => 'Pendapatan Lain-lain', 'uraian' => 'Pendapatan Lain-lain'],
    ];

    public function run(): void
    {
        $belanjaRows = require database_path('data/kode_rekening_belanja.php');

        foreach (Sekolah::all() as $sekolah) {
            foreach ($this->systemAkuns as $akun) {
                Akun::firstOrCreate(
                    ['sekolah_id' => $sekolah->id, 'kode' => $akun['kode']],
                    $akun + ['sekolah_id' => $sekolah->id, 'tipe' => 'sistem', 'is_aktif' => true],
                );
            }

            foreach ($belanjaRows as $row) {
                Akun::firstOrCreate(
                    [
                        'sekolah_id' => $sekolah->id,
                        'kode' => $row['kode'],
                        'snp' => $row['snp'],
                        'komponen' => $row['komponen'],
                    ],
                    [
                        'nama' => Str::limit($row['uraian'], 80, ''),
                        'uraian' => $row['uraian'],
                        'jenis' => 'beban',
                        'tipe' => 'rkas',
                        'kategori_arus_kas' => 'operasi',
                        'saldo_normal' => 'debit',
                        'is_aktif' => true,
                    ],
                );
            }

            foreach ($this->pendapatan as $row) {
                Akun::firstOrCreate(
                    [
                        'sekolah_id' => $sekolah->id,
                        'kode' => $row['kode'],
                        'komponen' => 'Pendapatan',
                    ],
                    [
                        'nama' => $row['nama'],
                        'uraian' => $row['uraian'],
                        'jenis' => 'pendapatan',
                        'tipe' => 'rkas',
                        'kategori_arus_kas' => 'operasi',
                        'saldo_normal' => 'kredit',
                        'is_aktif' => true,
                    ],
                );
            }

            AkuntansiSetting::forSekolah($sekolah->id);
        }
    }
}
