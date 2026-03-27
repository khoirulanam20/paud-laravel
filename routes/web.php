<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Lembaga Controllers
use App\Http\Controllers\Lembaga\SekolahController;
use App\Http\Controllers\Lembaga\AdminSekolahController;
use App\Http\Controllers\Lembaga\KritikSaranController as LembagaKritikSaranController;

// Admin Sekolah Controllers
use App\Http\Controllers\Admin\AnakController;
use App\Http\Controllers\Admin\SaranaController;
use App\Http\Controllers\Admin\PengajarController;
use App\Http\Controllers\Admin\MenuMakananController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\CashflowController;
use App\Http\Controllers\Admin\PresensiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Lembaga'])->prefix('lembaga')->name('lembaga.')->group(function () {
    Route::resource('sekolah', SekolahController::class)->except(['create', 'edit', 'show']);
    Route::resource('admin-sekolah', AdminSekolahController::class)->except(['create', 'edit', 'show']);
    Route::get('kritik-saran', [LembagaKritikSaranController::class, 'index'])->name('kritik-saran.index');
});

Route::middleware(['auth', 'role:Admin Sekolah'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('anak', AnakController::class)->except(['create', 'edit', 'show']);
    Route::resource('sarana', SaranaController::class)->except(['create', 'edit', 'show']);
    Route::resource('pengajar', PengajarController::class)->except(['create', 'edit', 'show']);
    Route::resource('menu-makanan', MenuMakananController::class)->except(['create', 'edit', 'show']);
    Route::resource('kegiatan', KegiatanController::class)->except(['create', 'edit', 'show']);
    Route::resource('cashflow', CashflowController::class)->except(['create', 'edit', 'show']);
    Route::get('presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('presensi', [PresensiController::class, 'store'])->name('presensi.store');
});

// Pengajar Controllers
use App\Http\Controllers\Pengajar\KegiatanController as PengajarKegiatanController;
use App\Http\Controllers\Pengajar\MatrikulasiController;
use App\Http\Controllers\Pengajar\PencapaianController;

Route::middleware(['auth', 'role:Pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {
    Route::resource('kegiatan', PengajarKegiatanController::class)->except(['create', 'edit', 'show']);
    Route::resource('matrikulasi', MatrikulasiController::class)->except(['create', 'edit', 'show']);
    Route::resource('pencapaian', PencapaianController::class)->except(['create', 'edit', 'show']);
});

// Orang Tua Controllers
use App\Http\Controllers\OrangTua\KegiatanController as OrangTuaKegiatanController;
use App\Http\Controllers\OrangTua\PencapaianController as OrangTuaPencapaianController;
use App\Http\Controllers\OrangTua\MenuMakananController as OrangTuaMenuMakananController;
use App\Http\Controllers\OrangTua\KritikSaranController as OrangTuaKritikSaranController;

Route::middleware(['auth', 'role:Orang Tua'])->prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('kegiatan', [OrangTuaKegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('pencapaian', [OrangTuaPencapaianController::class, 'index'])->name('pencapaian.index');
    Route::get('menu-makanan', [OrangTuaMenuMakananController::class, 'index'])->name('menu-makanan.index');
    Route::redirect('kritik-saran/riwayat', '/orangtua/kritik-saran');
    Route::get('kritik-saran', [OrangTuaKritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::post('kritik-saran', [OrangTuaKritikSaranController::class, 'store'])->name('kritik-saran.store');
});

require __DIR__.'/auth.php';
