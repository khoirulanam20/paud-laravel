# To-Do List Audit: paud-laravel (branch: demo)

**Sumber:** `paud-laravel-audit.md`  
**Tanggal Audit:** 2026-06-22  
**Dibuat:** 2026-06-28  

---

## Ringkasan

Berdasarkan hasil audit, terdapat **21 task** yang perlu diselesaikan, dibagi dalam 3 prioritas:
- **P1 (Harus Sebelum Launch):** 9 task — security & data integrity kritis
- **P2 (Minggu Ini):** 7 task — security tambahan & robustness
- **P3 (Bulan Ini):** 5 task — tech debt & maintainability

---

## P1 — Harus Sebelum Launch

### [S-1] Set APP_DEBUG=false
- **File:** `.env.example` (line 4)
- **Aksi:** Ganti `APP_DEBUG=true` → `APP_DEBUG=false`
- **Risiko jika tidak:** Stack trace & environment detail bocor ke user di production
- **Status:** `[ ] Belum dikerjakan`

---

### [S-2] Set SESSION_ENCRYPT=true
- **File:** `.env.example` (line 31)
- **Aksi:** Ganti `SESSION_ENCRYPT=false` → `SESSION_ENCRYPT=true`
- **Risiko jika tidak:** Data session bisa dibaca plain jika storage diakses langsung
- **Status:** `[ ] Belum dikerjakan`

---

### [S-3] Set Sanctum Token Expiration
- **File:** `config/sanctum.php`
- **Aksi:** Ganti `'expiration' => null` menjadi:
  ```php
  'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 10080), // 7 hari
  ```
  Tambahkan `SANCTUM_TOKEN_EXPIRATION=10080` di `.env.example`
- **Risiko jika tidak:** Token yang bocor bisa dipakai selamanya tanpa batas
- **Status:** `[ ] Belum dikerjakan`

---

### [S-4] Enkripsi AI API Key di Database
- **File:** `app/Models/AiSetting.php`
- **Aksi:** Tambahkan cast `'encrypted'` pada field `ai_api_key`:
  ```php
  protected $casts = [
      'ai_api_key' => 'encrypted',
  ];
  ```
  > Catatan: Model sudah ada accessor/mutator manual. Pastikan tidak konflik dengan Attribute cast yang sudah ada.
- **Risiko jika tidak:** Jika DB bocor, semua AI API key vendor (OpenAI, Groq, DeepSeek) ikut bocor — berbayar
- **Status:** `[ ] Belum dikerjakan`

---

### [S-5] Fix CanUploadImage — Ganti Fallback MIME ke finfo
- **File:** `app/Http/Traits/CanUploadImage.php`
- **Aksi:** Ganti deteksi MIME dari `getimagesize()` + fallback `getClientMimeType()` menjadi `finfo`:
  ```php
  $finfo = new \finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($imagePath);

  $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  if (!in_array($mime, $allowedMimes)) {
      throw new \InvalidArgumentException('File bukan gambar yang valid.');
  }
  ```
- **Risiko jika tidak:** Attacker bisa upload file PHP disamarkan sebagai gambar (bypass validasi)
- **Status:** `[ ] Belum dikerjakan`

---

### [S-7 / R-3] Fix AkuntansiService — Validasi Akun Tidak Null
- **File:** `app/Services/AkuntansiService.php`
- **Aksi:** Tambahkan guard sebelum membuat jurnal:
  ```php
  if (!$kas || !$counter) {
      throw new \RuntimeException(
          'Konfigurasi akuntansi belum lengkap. Pastikan akun kas dan counter telah dipilih.'
      );
  }
  ```
- **Risiko jika tidak:** Jurnal akuntansi dibuat tanpa debit/kredit yang valid → data corrupt
- **Status:** `[ ] Belum dikerjakan`

---

### [S-5] Tambah Rate Limiting di Web Login
- **File:** `routes/auth.php`
- **Aksi:** Tambahkan middleware throttle pada route POST login:
  ```php
  Route::post('login', [AuthenticatedSessionController::class, 'store'])
      ->middleware('throttle:5,1');
  ```
- **Risiko jika tidak:** Brute force password login web tidak terbatas
- **Status:** `[ ] Belum dikerjakan`

---

### [R-1] Fix GenerateMonevSummaryJob — Tambah Tries, Timeout, onFailure
- **File:** `app/Jobs/GenerateMonevSummaryJob.php`
- **Aksi:** Tambahkan property dan method `failed()`:
  ```php
  public int $tries = 3;
  public int $timeout = 120;
  public int $backoff = 30;

  public function failed(\Throwable $e): void
  {
      Log::error('Monev generation job failed', [
          'job_id' => $this->job->getJobId(),
          'error'  => $e->getMessage(),
      ]);
  }
  ```
- **Risiko jika tidak:** Job retry tak terbatas → menguras AI token sekolah saat AI provider error
- **Status:** `[ ] Belum dikerjakan`

---

### [UAT] Jalankan Seluruh UAT Checklist (Manual)
- **Aksi:** Lakukan pengujian manual untuk 17 skenario berikut:

  **Auth & Access Control:**
  - [ ] U-1: Login orang tua dengan anak belum approved → ditolak dengan pesan jelas
  - [ ] U-2: Admin sekolah A akses data sekolah B via URL → 403/redirect
  - [ ] U-3: Login Lembaga tanpa cabang aktif → redirect + warning
  - [ ] U-4: Token API expired → 401 + pesan jelas
  - [ ] U-5: Brute force login web > 5x → throttle 429

  **File Upload:**
  - [ ] U-6: Upload foto .php disamarkan sebagai image → ditolak
  - [ ] U-7: Upload foto 10MB → ditolak (max 2048KB)
  - [ ] U-8: Upload foto WebP → diterima dan ditampilkan

  **AI & Chat:**
  - [ ] U-9: Chat ketika saldo AI token 0 → error jelas, tidak hang
  - [ ] U-10: Admin disable chat, ortu coba kirim → ditolak dengan pesan
  - [ ] U-11: Generate monev dengan AI API key invalid → error user-friendly
  - [ ] U-12: Generate monev 2x bersamaan → hanya 1 jalan, satunya ditolak

  **Akuntansi & Pembayaran:**
  - [ ] U-13: Cashflow tanpa konfigurasi akuntansi → error jelas, bukan PHP error
  - [ ] U-14: Pembayaran ganda bulan yang sama → ditolak / duplikasi ter-handle
  - [ ] U-15: Generate RKAS PDF 50+ akun → berhasil dalam < 10 detik

  **Monev & Presensi:**
  - [ ] U-16: Monev manual trigger di hari bukan akhir bulan → jalan + ada warning
  - [ ] U-17: Presensi siswa non-approved → tidak muncul di list

- **Status:** `[ ] Belum dikerjakan`

---

## P2 — Minggu Ini

### [T-1] Hapus .cursor dari Repo dan Gitignore
- **Aksi:**
  ```bash
  git rm -r --cached .cursor/
  echo ".cursor/" >> .gitignore
  git commit -m "chore: remove .cursor IDE folder from tracking"
  ```
- **Status:** `[ ] Belum dikerjakan`

---

### [T-2] Pindah kode rekening.xlsx ke database/data/
- **File:** `kode rekening.xlsx` (di root project)
- **Aksi:** Pindah ke `database/data/kode-rekening.xlsx` atau buat seeder PHP, lalu hapus dari git tracking
- **Status:** `[ ] Belum dikerjakan`

---

### [R-6] Fix phpunit.xml — Ganti DB Hardcoded ke SQLite Memory
- **File:** `phpunit.xml`
- **Aksi:** Ganti konfigurasi DB test:
  ```xml
  <env name="DB_CONNECTION" value="sqlite"/>
  <env name="DB_DATABASE" value=":memory:"/>
  ```
  Hapus baris `DB_HOST`, `DB_PORT`, `DB_DATABASE` yang hardcoded ke MAMP (port 8889)
- **Status:** `[ ] Belum dikerjakan`

---

### [R-5] Tambah Rate Limiting Chat Orang Tua per User
- **File:** `app/Services/OrangTuaChatService.php` (atau controller yang memanggil service)
- **Aksi:** Tambahkan throttle sebelum `sendMessage()`:
  ```php
  $key = 'chat_limit_' . $user->id;
  if (Cache::get($key, 0) >= 20) {
      return response()->json(['message' => 'Terlalu banyak pesan. Tunggu beberapa saat.'], 429);
  }
  Cache::increment($key);
  Cache::expire($key, 60);
  ```
- **Status:** `[ ] Belum dikerjakan`

---

### [S-7] Tambah Prompt Injection Boundary di AI Context Builder
- **File:** `app/Services/OrangTuaChatService.php` (atau context builder terkait)
- **Aksi:** Bungkus user content dengan delimiter literal:
  ```php
  $userSection = "[USER MESSAGE START]\n" . $content . "\n[USER MESSAGE END]";
  ```
  Tambahkan validasi panjang pesan (misal max 1000 karakter)
- **Status:** `[ ] Belum dikerjakan`

---

### [S-8] Proteksi Scramble Docs di Staging/Production
- **File:** `config/scramble.php`
- **Aksi:** Tambahkan auth middleware:
  ```php
  'middleware' => ['auth'],
  ```
  Pastikan `.env` production/staging: `APP_ENV=production` dan `SCRAMBLE_DOCS_ENABLED=false`
- **Status:** `[ ] Belum dikerjakan`

---

### [R-4] Tambah Cleanup File di CanUploadImage jika Processing Gagal
- **File:** `app/Http/Traits/CanUploadImage.php`
- **Aksi:** Bungkus GD processing dalam try-catch dengan cleanup:
  ```php
  try {
      // ... image processing
      Storage::disk('public')->put($fullPath, $imageContent);
  } catch (\Throwable $e) {
      Storage::disk('public')->delete($fullPath);
      throw $e;
  }
  ```
- **Status:** `[ ] Belum dikerjakan`

---

## P3 — Bulan Ini

### [R-2] Validasi Null AI Service Sebelum Dispatch Job
- **File:** `app/Services/MonevSummaryService.php`
- **Aksi:** Tambahkan pengecekan sebelum dispatch job:
  ```php
  if (!$this->monevService->resolveAiServiceForSekolah($sekolahId)) {
      throw new MonevManualGenerationException('API Key AI belum dikonfigurasi.');
  }
  GenerateMonevSummaryJob::dispatch($sekolahId);
  ```
- **Status:** `[ ] Belum dikerjakan`

---

### [R-7] Tambah onFailure Monitoring di Scheduled Commands
- **File:** `bootstrap/app.php`
- **Aksi:** Tambahkan callback `onFailure()` pada setiap scheduled command:
  ```php
  $schedule->command('monev:generate')
      ->monthlyOn(1, '02:00')
      ->onFailure(function () {
          Log::channel('security')->error('monev:generate cron FAILED');
      });
  ```
- **Status:** `[ ] Belum dikerjakan`

---

### [T-3] Evaluasi Duplikasi Admin vs AdminKelas Controllers
- **Files:** `app/Http/Controllers/Admin/` vs `app/Http/Controllers/AdminKelas/`
- **Aksi:** Review controller berikut untuk identifikasi logika duplikat:
  - `Admin/AnakController.php` vs `AdminKelas/AnakController.php`
  - `Admin/MonevController.php` vs `AdminKelas/MonevController.php`
  - `Admin/PresensiController.php` vs `AdminKelas/PresensiController.php`
  - `Admin/KesehatanController.php` vs `AdminKelas/KesehatanController.php`

  Putuskan: gabungkan dengan policy/permission, atau extract shared logic ke service layer
- **Status:** `[ ] Belum dikerjakan`

---

### [T-6] Tambah Laravel Policy untuk Model Authorization
- **Aksi:**
  ```bash
  php artisan make:policy AnakPolicy --model=Anak
  # Buat policy untuk model utama lainnya
  ```
  Ganti `auth()->user()->sekolah_id` check manual di controller dengan `$this->authorize('view', $anak)`
- **Status:** `[ ] Belum dikerjakan`

---

### [T-5] Extract Permission String ke Enum
- **File:** `config/admin-menu.php`
- **Aksi:** Buat enum PHP:
  ```php
  enum AdminPermission: string {
      case ManageAnak     = 'manage anak';
      case ManagePresensi = 'manage presensi';
      // ... tambah lainnya
  }
  ```
  Ganti magic string di `admin-menu.php` dengan referensi enum
- **Status:** `[ ] Belum dikerjakan`

---

## Progress Tracker

| ID | Task | Prioritas | Status |
|----|------|-----------|--------|
| S-1 | Set APP_DEBUG=false | P1 | [ ] |
| S-2 | Set SESSION_ENCRYPT=true | P1 | [ ] |
| S-3 | Set Sanctum token expiration | P1 | [ ] |
| S-4 | Enkripsi AI API key | P1 | [ ] |
| S-5 | Fix CanUploadImage MIME validation | P1 | [ ] |
| S-5b | Tambah rate limiting web login | P1 | [ ] |
| R-3 | Fix AkuntansiService null check | P1 | [ ] |
| R-1 | Fix GenerateMonevSummaryJob | P1 | [ ] |
| UAT | Jalankan seluruh UAT checklist | P1 | [ ] |
| T-1 | Hapus .cursor dari repo | P2 | [ ] |
| T-2 | Pindah kode rekening.xlsx | P2 | [ ] |
| R-6 | Fix phpunit.xml DB config | P2 | [ ] |
| R-5 | Rate limiting chat orang tua | P2 | [ ] |
| S-7 | Prompt injection boundary | P2 | [ ] |
| S-8 | Proteksi Scramble docs | P2 | [ ] |
| R-4 | Cleanup file upload jika gagal | P2 | [ ] |
| R-2 | Validasi null AI service | P3 | [ ] |
| R-7 | onFailure monitoring cron | P3 | [ ] |
| T-3 | Evaluasi duplikasi controllers | P3 | [ ] |
| T-6 | Tambah Laravel Policy | P3 | [ ] |
| T-5 | Extract permission ke enum | P3 | [ ] |

**Total: 21 task** | P1: 9 | P2: 7 | P3: 5
