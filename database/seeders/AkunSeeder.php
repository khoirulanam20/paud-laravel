<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\AkuntansiSetting;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class AkunSeeder extends Seeder
{
    private array $akunDefault = [
        // Aset (1)
        ['kode' => '1-1000', 'nama' => 'Kas', 'jenis' => 'aset', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '1-1100', 'nama' => 'Bank', 'jenis' => 'aset', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '1-1200', 'nama' => 'Piutang SPP', 'jenis' => 'aset', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '1-1300', 'nama' => 'Perlengkapan', 'jenis' => 'aset', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '1-1400', 'nama' => 'Peralatan', 'jenis' => 'aset', 'kategori_arus_kas' => 'investasi', 'saldo_normal' => 'debit'],
        ['kode' => '1-1500', 'nama' => 'Akum. Penyusutan', 'jenis' => 'aset', 'kategori_arus_kas' => 'investasi', 'saldo_normal' => 'kredit'],
        ['kode' => '1-1600', 'nama' => 'Tanah', 'jenis' => 'aset', 'kategori_arus_kas' => 'investasi', 'saldo_normal' => 'debit'],
        ['kode' => '1-1700', 'nama' => 'Gedung', 'jenis' => 'aset', 'kategori_arus_kas' => 'investasi', 'saldo_normal' => 'debit'],

        // Liabilitas (2)
        ['kode' => '2-1000', 'nama' => 'Utang Usaha', 'jenis' => 'liabilitas', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'kredit'],
        ['kode' => '2-1100', 'nama' => 'Utang Gaji', 'jenis' => 'liabilitas', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'kredit'],
        ['kode' => '2-1200', 'nama' => 'Pendapatan Diterima di Muka', 'jenis' => 'liabilitas', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'kredit'],

        // Ekuitas (3)
        ['kode' => '3-1000', 'nama' => 'Modal Yayasan', 'jenis' => 'ekuitas', 'kategori_arus_kas' => 'pendanaan', 'saldo_normal' => 'kredit'],
        ['kode' => '3-1100', 'nama' => 'Saldo Ditahan', 'jenis' => 'ekuitas', 'kategori_arus_kas' => 'pendanaan', 'saldo_normal' => 'kredit'],
        ['kode' => '3-1200', 'nama' => 'Surplus/Defisit Tahun Berjalan', 'jenis' => 'ekuitas', 'kategori_arus_kas' => null, 'saldo_normal' => 'kredit'],

        // Pendapatan (4)
        ['kode' => '4-1000', 'nama' => 'Pendapatan SPP', 'jenis' => 'pendapatan', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'kredit'],
        ['kode' => '4-1100', 'nama' => 'Pendapatan Pendaftaran', 'jenis' => 'pendapatan', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'kredit'],
        ['kode' => '4-1200', 'nama' => 'Pendapatan Lain-lain', 'jenis' => 'pendapatan', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'kredit'],

        // Beban (5)
        ['kode' => '5-1000', 'nama' => 'Beban Gaji', 'jenis' => 'beban', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '5-1100', 'nama' => 'Beban Listrik', 'jenis' => 'beban', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '5-1200', 'nama' => 'Beban Air', 'jenis' => 'beban', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '5-1300', 'nama' => 'Beban ATK', 'jenis' => 'beban', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '5-1400', 'nama' => 'Beban Pemeliharaan', 'jenis' => 'beban', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '5-1500', 'nama' => 'Beban Penyusutan', 'jenis' => 'beban', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
        ['kode' => '5-1600', 'nama' => 'Beban Lain-lain', 'jenis' => 'beban', 'kategori_arus_kas' => 'operasi', 'saldo_normal' => 'debit'],
    ];

    public function run(): void
    {
        $sekolahs = Sekolah::all();

        foreach ($sekolahs as $sekolah) {
            foreach ($this->akunDefault as $akun) {
                Akun::firstOrCreate(
                    ['sekolah_id' => $sekolah->id, 'kode' => $akun['kode']],
                    $akun + ['sekolah_id' => $sekolah->id, 'is_aktif' => true],
                );
            }

            AkuntansiSetting::forSekolah($sekolah->id);
        }
    }
}
