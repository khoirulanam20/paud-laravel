<?php

use App\Http\Controllers\Admin\AnakController;
use App\Http\Controllers\Admin\CashflowController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\KritikSaranController as AdminKritikSaranController;
// Lembaga Controllers
use App\Http\Controllers\Admin\MatrikulasiController as AdminMatrikulasiController;
use App\Http\Controllers\Admin\SkalaPencapaianController;
use App\Http\Controllers\Admin\MenuMakananController;
use App\Http\Controllers\Admin\MonevController as AdminMonevController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\PengajarController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\SaranaController;
// Admin Sekolah Controllers
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Lembaga\AdminSekolahController;
use App\Http\Controllers\Lembaga\AiSettingController;
use App\Http\Controllers\Lembaga\CmsController;
use App\Http\Controllers\Lembaga\KritikSaranController as LembagaKritikSaranController;
use App\Http\Controllers\Lembaga\SekolahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminKelas\AnakController as AdminKelasAnakController;
use App\Http\Controllers\AdminKelas\MonevController as AdminKelasMonevController;
use App\Http\Controllers\AdminKelas\PresensiController as AdminKelasPresensiController;
use App\Http\Controllers\Admin\OrangTuaChatController;
use App\Http\Controllers\Admin\AiPersonaController;
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
    Route::get('ai-setting', [AiSettingController::class, 'index'])->name('ai-setting.index');
    Route::post('ai-setting', [AiSettingController::class, 'update'])->name('ai-setting.update');
    Route::post('ai-setting/test', [AiSettingController::class, 'testConnection'])->name('ai-setting.test');
});

// ─────────────────────────────────────────────
// ADMIN SEKOLAH
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Admin Sekolah'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('monev', [AdminMonevController::class, 'index'])->name('monev.index');
    Route::post('monev/generate', [AdminMonevController::class, 'generate'])->middleware('throttle:10,1')->name('monev.generate');
    Route::post('monev/bulk-generate', [AdminMonevController::class, 'bulkGenerate'])->middleware('throttle:10,1')->name('monev.bulk-generate');
    Route::post('monev/bulk-reset', [AdminMonevController::class, 'bulkReset'])->middleware('throttle:20,1')->name('monev.bulk-reset');
    Route::get('monev/generation/{generation}/status', [AdminMonevController::class, 'generationStatus'])->name('monev.generation.status');
    Route::get('monev/{anak}/pdf', [AdminMonevController::class, 'exportPdf'])->name('monev.export-pdf');
    Route::get('monev/{anak}', [AdminMonevController::class, 'show'])->name('monev.show');
    Route::resource('kelas', KelasController::class)->except(['create', 'edit', 'show']);
    Route::resource('matrikulasi', AdminMatrikulasiController::class)->except(['create', 'edit', 'show']);
    Route::resource('skala-pencapaian', SkalaPencapaianController::class)->except(['create', 'edit', 'show']);
    Route::resource('anak', AnakController::class)->except(['create', 'edit']);
    Route::resource('sarana', SaranaController::class)->except(['create', 'edit', 'show']);
    Route::resource('pengajar', PengajarController::class)->except(['create', 'edit', 'show']);
    Route::resource('menu-makanan', MenuMakananController::class)->except(['create', 'edit', 'show']);
});

Route::middleware(['auth', 'role:Admin Sekolah|Admin Kelas'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('kelas/{kelas}/siswa-modal', [KelasController::class, 'siswaModal'])->name('kelas.siswa-modal');
    Route::get('kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
    Route::resource('kegiatan', KegiatanController::class)->except(['create', 'edit', 'show']);
    Route::post('pencapaian/sync', [\App\Http\Controllers\Admin\PencapaianController::class, 'sync'])->name('pencapaian.sync');
    Route::delete('pencapaian/bundle', [\App\Http\Controllers\Admin\PencapaianController::class, 'destroyBundle'])->name('pencapaian.destroy-bundle');
    Route::resource('pencapaian', \App\Http\Controllers\Admin\PencapaianController::class)->only(['index', 'destroy']);
    Route::resource('cashflow', CashflowController::class)->except(['create', 'edit', 'show']);
    Route::get('presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::get('presensi/rekap', [PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::post('master-kegiatan-rutin/{master_kegiatan_rutin}/store-rutin', [\App\Http\Controllers\Admin\MasterKegiatanRutinController::class, 'storeRutin'])->name('master-kegiatan-rutin.store-rutin');
    Route::get('master-kegiatan-rutin/detail/{master_kegiatan_rutin}/{anak}', [\App\Http\Controllers\Admin\MasterKegiatanRutinController::class, 'detail'])->name('master-kegiatan-rutin.detail');
    Route::delete('master-kegiatan-rutin/rutin/{kegiatan_rutin}', [\App\Http\Controllers\Admin\MasterKegiatanRutinController::class, 'destroyRutinRecord'])->name('master-kegiatan-rutin.destroy-rutin');
    Route::resource('master-kegiatan-rutin', \App\Http\Controllers\Admin\MasterKegiatanRutinController::class);
    Route::get('kegiatan-rutin', [\App\Http\Controllers\Admin\KegiatanRutinController::class, 'index'])->name('kegiatan-rutin.index');
    Route::post('kegiatan-rutin', [\App\Http\Controllers\Admin\KegiatanRutinController::class, 'store'])->name('kegiatan-rutin.store');
    Route::get('kegiatan-rutin/detail/{anak}', [\App\Http\Controllers\Admin\KegiatanRutinController::class, 'detail'])->name('kegiatan-rutin.detail');
    Route::get('kritik-saran', [AdminKritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::get('kritik-saran/{kritik_saran}', [AdminKritikSaranController::class, 'show'])->name('kritik-saran.show');
    Route::patch('kritik-saran/{kritik_saran}', [AdminKritikSaranController::class, 'update'])->name('kritik-saran.update');
    Route::get('orangtua-chat', [OrangTuaChatController::class, 'index'])->name('orangtua-chat.index');
    Route::get('orangtua-chat/{orangtua_chat}', [OrangTuaChatController::class, 'show'])->name('orangtua-chat.show');
    Route::get('ai-persona', [AiPersonaController::class, 'index'])->name('ai-persona.index');
    Route::post('ai-persona', [AiPersonaController::class, 'update'])->name('ai-persona.update');
    Route::post('ai-persona/generate', [AiPersonaController::class, 'generate'])->middleware('throttle:10,1')->name('ai-persona.generate');
    // Pendaftaran approval
    Route::get('pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('pendaftaran/{anak}/approve', [PendaftaranController::class, 'approve'])->name('pendaftaran.approve');
    Route::post('pendaftaran/{anak}/reject', [PendaftaranController::class, 'reject'])->name('pendaftaran.reject');
    Route::get('kesehatan/history/{anak}', [\App\Http\Controllers\Admin\KesehatanController::class, 'history'])->name('kesehatan.history');
    Route::resource('kesehatan', \App\Http\Controllers\Admin\KesehatanController::class)->only(['index', 'store', 'destroy']);
    Route::get('presensi-guru', [\App\Http\Controllers\Admin\PresensiPengajarController::class, 'index'])->name('presensi-guru.index');
    Route::post('presensi-guru', [\App\Http\Controllers\Admin\PresensiPengajarController::class, 'store'])->name('presensi-guru.store');
    // AI Feedback Suggestions (web route, uses web session auth)
    Route::post('ai/feedback-suggestions', [\App\Http\Controllers\Api\AiFeedbackController::class, 'suggest'])->name('ai.feedback-suggestions');
});

// ─────────────────────────────────────────────
// ADMIN KELAS
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Admin Kelas'])->prefix('adminkelas')->name('adminkelas.')->group(function () {
    Route::resource('anak', AdminKelasAnakController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('presensi', [AdminKelasPresensiController::class, 'index'])->name('presensi.index');
    Route::post('presensi', [AdminKelasPresensiController::class, 'store'])->name('presensi.store');
    Route::get('kesehatan/history/{anak}', [\App\Http\Controllers\AdminKelas\KesehatanController::class, 'history'])->name('kesehatan.history');
    Route::resource('kesehatan', \App\Http\Controllers\AdminKelas\KesehatanController::class)->only(['index', 'store', 'destroy']);
    Route::get('matrikulasi', [MatrikulasiController::class, 'index'])->name('matrikulasi.index');
    Route::get('monev', [AdminKelasMonevController::class, 'index'])->name('monev.index');
    Route::post('monev/generate', [AdminKelasMonevController::class, 'generate'])->middleware('throttle:10,1')->name('monev.generate');
    Route::post('monev/bulk-generate', [AdminKelasMonevController::class, 'bulkGenerate'])->middleware('throttle:10,1')->name('monev.bulk-generate');
    Route::post('monev/bulk-reset', [AdminKelasMonevController::class, 'bulkReset'])->middleware('throttle:20,1')->name('monev.bulk-reset');
    Route::get('monev/generation/{generation}/status', [AdminKelasMonevController::class, 'generationStatus'])->name('monev.generation.status');
    Route::get('monev/{anak}/pdf', [AdminKelasMonevController::class, 'exportPdf'])->name('monev.export-pdf');
    Route::get('monev/{anak}', [AdminKelasMonevController::class, 'show'])->name('monev.show');
});

// ─────────────────────────────────────────────
// PENGAJAR
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Pengajar|Admin Kelas'])->prefix('pengajar')->name('pengajar.')->group(function () {
    Route::get('presensi', [PengajarPresensiController::class, 'index'])->name('presensi.index');
    Route::post('presensi', [PengajarPresensiController::class, 'store'])->name('presensi.store');
    Route::resource('kegiatan', PengajarKegiatanController::class)->except(['create', 'edit', 'show']);
    Route::resource('matrikulasi', MatrikulasiController::class)->only(['index']);
    Route::post('pencapaian/sync', [PencapaianController::class, 'sync'])->name('pencapaian.sync');
    Route::delete('pencapaian/bundle', [PencapaianController::class, 'destroyBundle'])->name('pencapaian.destroy-bundle');
    Route::resource('pencapaian', PencapaianController::class)->only(['index', 'destroy']);
    Route::post('master-kegiatan-rutin/{master_kegiatan_rutin}/store-rutin', [\App\Http\Controllers\Pengajar\MasterKegiatanRutinController::class, 'storeRutin'])->name('master-kegiatan-rutin.store-rutin');
    Route::get('master-kegiatan-rutin/detail/{master_kegiatan_rutin}/{anak}', [\App\Http\Controllers\Pengajar\MasterKegiatanRutinController::class, 'detail'])->name('master-kegiatan-rutin.detail');
    Route::delete('master-kegiatan-rutin/rutin/{kegiatan_rutin}', [\App\Http\Controllers\Pengajar\MasterKegiatanRutinController::class, 'destroyRutinRecord'])->name('master-kegiatan-rutin.destroy-rutin');
    Route::resource('master-kegiatan-rutin', \App\Http\Controllers\Pengajar\MasterKegiatanRutinController::class);
    Route::get('kegiatan-rutin', [\App\Http\Controllers\Pengajar\KegiatanRutinController::class, 'index'])->name('kegiatan-rutin.index');
    Route::post('kegiatan-rutin', [\App\Http\Controllers\Pengajar\KegiatanRutinController::class, 'store'])->name('kegiatan-rutin.store');
    Route::get('kegiatan-rutin/detail/{anak}', [\App\Http\Controllers\Pengajar\KegiatanRutinController::class, 'detail'])->name('kegiatan-rutin.detail');
    // AI Feedback Suggestions (web route, uses web session auth)
    Route::post('ai/feedback-suggestions', [\App\Http\Controllers\Api\AiFeedbackController::class, 'suggest'])->name('ai.feedback-suggestions');
});

// ─────────────────────────────────────────────
// ORANG TUA
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Orang Tua'])->prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('kegiatan', [OrangTuaKegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('kegiatan-rutin', [\App\Http\Controllers\OrangTua\KegiatanRutinController::class, 'index'])->name('kegiatan-rutin.index');
    Route::get('pencapaian', [OrangTuaPencapaianController::class, 'index'])->name('pencapaian.index');
    Route::get('monev', [\App\Http\Controllers\OrangTua\MonevController::class, 'index'])->name('monev.index');
    Route::get('monev/{anak}/pdf', [\App\Http\Controllers\OrangTua\MonevController::class, 'exportPdf'])->name('monev.export-pdf');
    Route::get('monev/{anak}', [\App\Http\Controllers\OrangTua\MonevController::class, 'show'])->name('monev.show');
    Route::get('menu-makanan', [OrangTuaMenuMakananController::class, 'index'])->name('menu-makanan.index');
    Route::redirect('kritik-saran/riwayat', '/orangtua/kritik-saran');
    Route::get('kritik-saran', [OrangTuaKritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::get('kritik-saran/{kritik_saran}', [OrangTuaKritikSaranController::class, 'show'])->name('kritik-saran.show');
    Route::post('kritik-saran', [OrangTuaKritikSaranController::class, 'store'])->name('kritik-saran.store');
    Route::patch('kritik-saran/{kritik_saran}', [OrangTuaKritikSaranController::class, 'update'])->name('kritik-saran.update');
    Route::delete('kritik-saran/{kritik_saran}', [OrangTuaKritikSaranController::class, 'destroy'])->name('kritik-saran.destroy');
    Route::get('kesehatan', [\App\Http\Controllers\OrangTua\KesehatanController::class, 'index'])->name('kesehatan.index');
    Route::get('presensi', [\App\Http\Controllers\OrangTua\PresensiController::class, 'index'])->name('presensi.index');
    Route::post('menu-makanan/vote', [\App\Http\Controllers\OrangTua\MenuMakananVoteController::class, 'vote'])->name('menu-makanan.vote');
    Route::get('chat', [\App\Http\Controllers\OrangTua\ChatController::class, 'index'])->name('chat.index');
    Route::post('chat/messages', [\App\Http\Controllers\OrangTua\ChatController::class, 'store'])->middleware('throttle:30,1')->name('chat.messages.store');
    Route::delete('chat', [\App\Http\Controllers\OrangTua\ChatController::class, 'destroy'])->name('chat.destroy');
});

require __DIR__.'/auth.php';
