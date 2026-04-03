<?php

use App\Http\Controllers\Admin\AnakController;
use App\Http\Controllers\Admin\CashflowController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\KritikSaranController as AdminKritikSaranController;
// Lembaga Controllers
use App\Http\Controllers\Admin\MatrikulasiController as AdminMatrikulasiController;
use App\Http\Controllers\Admin\MenuMakananController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\PengajarController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\SaranaController;
// Admin Sekolah Controllers
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Lembaga\AdminSekolahController;
use App\Http\Controllers\Lembaga\CmsController;
use App\Http\Controllers\Lembaga\KritikSaranController as LembagaKritikSaranController;
use App\Http\Controllers\Lembaga\SekolahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminKelas\AnakController as AdminKelasAnakController;
use App\Http\Controllers\AdminKelas\PresensiController as AdminKelasPresensiController;
use App\Http\Controllers\OrangTua\KegiatanController as OrangTuaKegiatanController;
use App\Http\Controllers\OrangTua\KritikSaranController as OrangTuaKritikSaranController;
use App\Http\Controllers\OrangTua\MenuMakananController as OrangTuaMenuMakananController;
use App\Http\Controllers\OrangTua\PencapaianController as OrangTuaPencapaianController;
use App\Http\Controllers\Pengajar\KegiatanController as PengajarKegiatanController;
use App\Http\Controllers\Pengajar\MatrikulasiController;
use App\Http\Controllers\Pengajar\PencapaianController;
use App\Http\Controllers\Pengajar\PresensiController as PengajarPresensiController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// PUBLIC GUEST ROUTES
// ─────────────────────────────────────────────
Route::get('/', [GuestController::class, 'beranda'])->name('guest.beranda');
Route::get('/tentang', [GuestController::class, 'tentang'])->name('guest.tentang');
Route::get('/fasilitas', [GuestController::class, 'fasilitas'])->name('guest.fasilitas');
Route::get('/galeri', [GuestController::class, 'galeri'])->name('guest.galeri');
Route::get('/pendaftaran', [GuestController::class, 'pendaftaran'])->name('guest.pendaftaran');
Route::post('/pendaftaran', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('guest.pendaftaran.store');
Route::get('/kontak', [GuestController::class, 'kontak'])->name('guest.kontak');
Route::post('/kontak', [GuestController::class, 'kontakSend'])->name('guest.kontak.send');

// ─────────────────────────────────────────────
// AUTH REQUIRED
// ─────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/profile/sekolah', [ProfileController::class, 'updateSekolah'])->name('profile.sekolah.update');
    Route::patch('/profile/pengajar', [ProfileController::class, 'updatePengajar'])->name('profile.pengajar.update');
    Route::patch('/profile/orangtua', [ProfileController::class, 'updateOrangTua'])->name('profile.orangtua.update');
    Route::patch('/profile/anak/{anak}', [ProfileController::class, 'updateAnak'])->name('profile.anak.update');
});

// ─────────────────────────────────────────────
// LEMBAGA
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Lembaga'])->prefix('lembaga')->name('lembaga.')->group(function () {
    Route::resource('sekolah', SekolahController::class)->except(['create', 'edit', 'show']);
    Route::resource('admin-sekolah', AdminSekolahController::class)->except(['create', 'edit', 'show']);
    Route::get('kritik-saran', [LembagaKritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::get('cms', [CmsController::class, 'index'])->name('cms.index');
    Route::post('cms', [CmsController::class, 'update'])->name('cms.update');
});

// ─────────────────────────────────────────────
// ADMIN SEKOLAH
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Admin Sekolah'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('kelas', KelasController::class)->except(['create', 'edit', 'show']);
    Route::resource('matrikulasi', AdminMatrikulasiController::class)->except(['create', 'edit', 'show']);
    Route::resource('anak', AnakController::class)->except(['create', 'edit']);
    Route::resource('sarana', SaranaController::class)->except(['create', 'edit', 'show']);
    Route::resource('pengajar', PengajarController::class)->except(['create', 'edit', 'show']);
    Route::resource('menu-makanan', MenuMakananController::class)->except(['create', 'edit', 'show']);
});

Route::middleware(['auth', 'role:Admin Sekolah|Admin Kelas'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::resource('cashflow', CashflowController::class)->except(['create', 'edit', 'show']);
    Route::get('presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::get('presensi/rekap', [PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::get('kritik-saran', [AdminKritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::get('kritik-saran/{kritik_saran}', [AdminKritikSaranController::class, 'show'])->name('kritik-saran.show');
    Route::patch('kritik-saran/{kritik_saran}', [AdminKritikSaranController::class, 'update'])->name('kritik-saran.update');
    // Pendaftaran approval
    Route::get('pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('pendaftaran/{anak}/approve', [PendaftaranController::class, 'approve'])->name('pendaftaran.approve');
    Route::post('pendaftaran/{anak}/reject', [PendaftaranController::class, 'reject'])->name('pendaftaran.reject');
    Route::resource('kesehatan', \App\Http\Controllers\Admin\KesehatanController::class)->only(['index', 'store']);
    Route::get('presensi-guru', [\App\Http\Controllers\Admin\PresensiPengajarController::class, 'index'])->name('presensi-guru.index');
    Route::post('presensi-guru', [\App\Http\Controllers\Admin\PresensiPengajarController::class, 'store'])->name('presensi-guru.store');
});

// ─────────────────────────────────────────────
// ADMIN KELAS
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Admin Kelas'])->prefix('adminkelas')->name('adminkelas.')->group(function () {
    Route::resource('anak', AdminKelasAnakController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('presensi', [AdminKelasPresensiController::class, 'index'])->name('presensi.index');
    Route::post('presensi', [AdminKelasPresensiController::class, 'store'])->name('presensi.store');
    Route::resource('kesehatan', \App\Http\Controllers\AdminKelas\KesehatanController::class)->only(['index', 'store']);
});

// ─────────────────────────────────────────────
// PENGAJAR
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {
    Route::get('presensi', [PengajarPresensiController::class, 'index'])->name('presensi.index');
    Route::post('presensi', [PengajarPresensiController::class, 'store'])->name('presensi.store');
    Route::resource('kegiatan', PengajarKegiatanController::class)->except(['create', 'edit', 'show']);
    Route::resource('matrikulasi', MatrikulasiController::class)->except(['create', 'edit', 'show']);
    Route::post('pencapaian/sync', [PencapaianController::class, 'sync'])->name('pencapaian.sync');
    Route::delete('pencapaian/bundle', [PencapaianController::class, 'destroyBundle'])->name('pencapaian.destroy-bundle');
    Route::resource('pencapaian', PencapaianController::class)->only(['index', 'destroy']);
    Route::get('kegiatan-rutin', [\App\Http\Controllers\Pengajar\KegiatanRutinController::class, 'index'])->name('kegiatan-rutin.index');
    Route::post('kegiatan-rutin', [\App\Http\Controllers\Pengajar\KegiatanRutinController::class, 'store'])->name('kegiatan-rutin.store');
});

// ─────────────────────────────────────────────
// ORANG TUA
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Orang Tua'])->prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('kegiatan', [OrangTuaKegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('pencapaian', [OrangTuaPencapaianController::class, 'index'])->name('pencapaian.index');
    Route::get('menu-makanan', [OrangTuaMenuMakananController::class, 'index'])->name('menu-makanan.index');
    Route::redirect('kritik-saran/riwayat', '/orangtua/kritik-saran');
    Route::get('kritik-saran', [OrangTuaKritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::get('kritik-saran/{kritik_saran}', [OrangTuaKritikSaranController::class, 'show'])->name('kritik-saran.show');
    Route::post('kritik-saran', [OrangTuaKritikSaranController::class, 'store'])->name('kritik-saran.store');
    Route::get('kesehatan', [\App\Http\Controllers\OrangTua\KesehatanController::class, 'index'])->name('kesehatan.index');
    Route::get('presensi', [\App\Http\Controllers\OrangTua\PresensiController::class, 'index'])->name('presensi.index');
    Route::post('menu-makanan/vote', [\App\Http\Controllers\OrangTua\MenuMakananVoteController::class, 'vote'])->name('menu-makanan.vote');
});

require __DIR__.'/auth.php';
