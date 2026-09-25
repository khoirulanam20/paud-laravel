<?php

/**
 * COA standar (satu set per sekolah).
 * Kolom: kode, jenis, nama, uraian, kelompok (snp), subkelompok (komponen), saldo_normal
 */
return [
    ['kode' => '1101', 'jenis' => 'aset', 'nama' => 'Kas Besar', 'uraian' => 'Kas utama perusahaan', 'kelompok' => 'Aset Lancar', 'subkelompok' => 'Kas dan Setara Kas', 'saldo_normal' => 'debit'],
    ['kode' => '1102', 'jenis' => 'aset', 'nama' => 'Kas Kecil', 'uraian' => 'Dana kas untuk pengeluaran kecil', 'kelompok' => 'Aset Lancar', 'subkelompok' => 'Kas dan Setara Kas', 'saldo_normal' => 'debit'],
    ['kode' => '1103', 'jenis' => 'aset', 'nama' => 'Bank BCA', 'uraian' => 'Rekening bank BCA perusahaan', 'kelompok' => 'Aset Lancar', 'subkelompok' => 'Kas dan Setara Kas', 'saldo_normal' => 'debit'],
    ['kode' => '1104', 'jenis' => 'aset', 'nama' => 'Bank Mandiri', 'uraian' => 'Rekening bank Mandiri perusahaan', 'kelompok' => 'Aset Lancar', 'subkelompok' => 'Kas dan Setara Kas', 'saldo_normal' => 'debit'],
    ['kode' => '1105', 'jenis' => 'aset', 'nama' => 'Piutang Usaha', 'uraian' => 'Piutang dari pelanggan', 'kelompok' => 'Aset Lancar', 'subkelompok' => 'Piutang', 'saldo_normal' => 'debit'],
    ['kode' => '1106', 'jenis' => 'aset', 'nama' => 'Persediaan Barang', 'uraian' => 'Stok barang dagangan', 'kelompok' => 'Aset Lancar', 'subkelompok' => 'Persediaan', 'saldo_normal' => 'debit'],
    ['kode' => '1201', 'jenis' => 'aset', 'nama' => 'Peralatan Kantor', 'uraian' => 'Inventaris kantor', 'kelompok' => 'Aset Tetap', 'subkelompok' => 'Peralatan', 'saldo_normal' => 'debit'],
    ['kode' => '1202', 'jenis' => 'aset', 'nama' => 'Akumulasi Penyusutan Peralatan', 'uraian' => 'Akumulasi penyusutan peralatan', 'kelompok' => 'Aset Tetap', 'subkelompok' => 'Akumulasi Penyusutan', 'saldo_normal' => 'kredit'],
    ['kode' => '1203', 'jenis' => 'aset', 'nama' => 'Kendaraan', 'uraian' => 'Kendaraan operasional', 'kelompok' => 'Aset Tetap', 'subkelompok' => 'Kendaraan', 'saldo_normal' => 'debit'],
    ['kode' => '1204', 'jenis' => 'aset', 'nama' => 'Akumulasi Penyusutan Kendaraan', 'uraian' => 'Akumulasi penyusutan kendaraan', 'kelompok' => 'Aset Tetap', 'subkelompok' => 'Akumulasi Penyusutan', 'saldo_normal' => 'kredit'],
    ['kode' => '2101', 'jenis' => 'liabilitas', 'nama' => 'Hutang Usaha', 'uraian' => 'Hutang kepada pemasok', 'kelompok' => 'Liabilitas Lancar', 'subkelompok' => 'Hutang', 'saldo_normal' => 'kredit'],
    ['kode' => '2102', 'jenis' => 'liabilitas', 'nama' => 'Hutang Gaji', 'uraian' => 'Hutang gaji karyawan', 'kelompok' => 'Liabilitas Lancar', 'subkelompok' => 'Hutang', 'saldo_normal' => 'kredit'],
    ['kode' => '2201', 'jenis' => 'liabilitas', 'nama' => 'Hutang Bank', 'uraian' => 'Hutang pinjaman bank', 'kelompok' => 'Liabilitas Jangka Panjang', 'subkelompok' => 'Hutang Bank', 'saldo_normal' => 'kredit'],
    ['kode' => '3101', 'jenis' => 'ekuitas', 'nama' => 'Modal Pemilik', 'uraian' => 'Modal yang disetor pemilik', 'kelompok' => 'Ekuitas', 'subkelompok' => 'Modal', 'saldo_normal' => 'kredit'],
    ['kode' => '3201', 'jenis' => 'ekuitas', 'nama' => 'Prive', 'uraian' => 'Pengambilan pribadi pemilik', 'kelompok' => 'Ekuitas', 'subkelompok' => 'Modal', 'saldo_normal' => 'debit'],
    ['kode' => '3301', 'jenis' => 'ekuitas', 'nama' => 'Laba Ditahan', 'uraian' => 'Laba yang tidak dibagi', 'kelompok' => 'Ekuitas', 'subkelompok' => 'Saldo Laba', 'saldo_normal' => 'kredit'],
    ['kode' => '4101', 'jenis' => 'pendapatan', 'nama' => 'Pendapatan Jasa', 'uraian' => 'Pendapatan dari layanan', 'kelompok' => 'Pendapatan', 'subkelompok' => 'Pendapatan Operasional', 'saldo_normal' => 'kredit'],
    ['kode' => '4102', 'jenis' => 'pendapatan', 'nama' => 'Pendapatan Penjualan', 'uraian' => 'Pendapatan dari penjualan barang', 'kelompok' => 'Pendapatan', 'subkelompok' => 'Pendapatan Operasional', 'saldo_normal' => 'kredit'],
    ['kode' => '4201', 'jenis' => 'pendapatan', 'nama' => 'Pendapatan Bunga', 'uraian' => 'Pendapatan dari bunga bank', 'kelompok' => 'Pendapatan', 'subkelompok' => 'Pendapatan Non-Operasional', 'saldo_normal' => 'kredit'],
    ['kode' => '5101', 'jenis' => 'beban', 'nama' => 'Beban Gaji', 'uraian' => 'Biaya gaji karyawan', 'kelompok' => 'Beban', 'subkelompok' => 'Beban Operasional', 'saldo_normal' => 'debit'],
    ['kode' => '5102', 'jenis' => 'beban', 'nama' => 'Beban Sewa', 'uraian' => 'Biaya sewa kantor', 'kelompok' => 'Beban', 'subkelompok' => 'Beban Operasional', 'saldo_normal' => 'debit'],
    ['kode' => '5103', 'jenis' => 'beban', 'nama' => 'Beban Listrik, Air, dan Telepon', 'uraian' => 'Biaya utilitas', 'kelompok' => 'Beban', 'subkelompok' => 'Beban Operasional', 'saldo_normal' => 'debit'],
    ['kode' => '5104', 'jenis' => 'beban', 'nama' => 'Beban Penyusutan', 'uraian' => 'Biaya penyusutan aset', 'kelompok' => 'Beban', 'subkelompok' => 'Beban Operasional', 'saldo_normal' => 'debit'],
    ['kode' => '5201', 'jenis' => 'beban', 'nama' => 'Beban Administrasi Bank', 'uraian' => 'Biaya admin bank', 'kelompok' => 'Beban', 'subkelompok' => 'Beban Non-Operasional', 'saldo_normal' => 'debit'],
    ['kode' => '5202', 'jenis' => 'beban', 'nama' => 'Beban Pajak', 'uraian' => 'Biaya pajak', 'kelompok' => 'Beban', 'subkelompok' => 'Beban Non-Operasional', 'saldo_normal' => 'debit'],
];
