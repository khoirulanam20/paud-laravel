Tentu, ini adalah **Product Requirements Document (PRD)** yang detail dan terstruktur untuk pengembangan aplikasi manajemen PAUD/Daycare Anda, menyesuaikan dengan skema database SQL yang Anda berikan dan spesifikasi teknologi Laravel 11.

---

# Product Requirements Document (PRD) - Sistem Manajemen PAUD/Daycare

## 1. Pendahuluan
Aplikasi ini bertujuan untuk mendigitalisasi operasional harian PAUD atau Daycare, mulai dari manajemen yayasan (Lembaga), operasional sekolah, pelaporan guru, hingga transparansi data kepada orang tua murid (Anak).

## 2. Arsitektur Teknologi
* **Framework:** Laravel 11 (Blade Engine).
* **Database:** MySQL (berdasarkan skema `Paud.sql`).
* **Frontend UI:** Tailwind CSS dengan **shadcn/ui** (diadaptasi untuk Blade Components).
* **Autentikasi:** Laravel Breeze (Session-based).

---

## 3. Struktur Pengguna & Hak Akses (Role Matrix)

| Fitur | Lembaga (Yayasan) | Admin Sekolah | Pengajar (Guru) | Orang Tua (Anak) |
| :--- | :---: | :---: | :---: | :---: |
| **Dashboard** | Stats Global | Stats Sekolah | Jadwal & Siswa | Info Anak & Menu |
| **Kelola Sekolah** | CRUD | - | - | - |
| **Kelola Admin Sekolah** | CRUD | - | - | - |
| **Kelola Data Anak** | View Only | CRUD | View Only | View (Milik Sendiri) |
| **Kelola Pengajar** | View Only | CRUD | - | - |
| **Sarana & Prasarana** | - | CRUD | View | - |
| **Kegiatan & Matrikulasi** | - | CRUD | Input/Update | View |
| **Pencapaian Anak** | - | View | Input/Update | View |
| **Menu Makanan** | - | CRUD | View | View |
| **Cashflow (Keuangan)** | View | CRUD | - | - |
| **Kritik & Saran** | View | View | - | Create |

---

## 4. Spesifikasi Fitur Utama

### 4.1. Modul Lembaga (Super Admin)
* **Manajemen Sekolah:** Menambahkan unit sekolah baru di bawah naungan yayasan (Tabel `sekolah`).
* **Manajemen User (Admin Sekolah):** Membuat akun user dengan role 'Admin' yang terikat pada `lembaga_id`.
* **Monitor Kritik & Saran:** Melihat masukan dari semua orang tua murid di seluruh unit sekolah untuk bahan evaluasi yayasan.

### 4.2. Modul Admin Sekolah
* **Manajemen Siswa (Anak):** Pendaftaran siswa baru, input data orang tua, dan upload foto (Tabel `anak`).
* **Manajemen Staff:** Input data guru, jabatan, dan riwayat pendidikan (Tabel `pengajar`).
* **Manajemen Sarana:** Inventarisasi fasilitas sekolah seperti ruang kelas, mainan, atau alat peraga (Tabel `sarana`).
* **Manajemen Menu Makanan:** Input jadwal menu harian beserta informasi gizi dan foto makanan (Tabel `menu_makan`).
* **Manajemen Keuangan (Cashflow):** Pencatatan uang masuk (SPP/Pendaftaran) dan uang keluar (Operasional) (Tabel `cashflow`).

### 4.3. Modul Pengajar (Guru)
* **Input Kegiatan:** Mencatat jurnal harian atau agenda kelas (Tabel `kegiatan`).
* **Penilaian (Pencapaian):** Memberikan feedback pada setiap anak berdasarkan indikator matrikulasi yang sudah ditentukan (Tabel `pencapaian`).
* **Matrikulasi:** Melihat acuan tujuan dan strategi pembelajaran (Tabel `matrikulasi`).

### 4.4. Modul Orang Tua (Anak)
* **Laporan Kegiatan:** Melihat foto dan deskripsi kegiatan anak di sekolah.
* **Laporan Capaian:** Melihat progres perkembangan anak secara berkala (Indikator & Deskripsi).
* **Info Nutrisi:** Melihat menu makan anak hari ini untuk koordinasi makanan di rumah.
* **Feedback Loop:** Mengirimkan kritik atau saran langsung ke pihak sekolah dan yayasan (Tabel `kritik_saran`).

---

## 5. Panduan Desain UI (shadcn-inspired)
Karena menggunakan Laravel Blade, implementasi **shadcn/ui** akan dilakukan menggunakan **Blade Components** yang diberikan styling Tailwind CSS agar serupa dengan estetika shadcn (bersih, minimalis, penggunaan font Inter, dan radius border halus).

* **Layout:** Sidebar navigasi (kiri) dan Topbar (profil & notifikasi).
* **Components:**
    * `Card`: Untuk ringkasan statistik di dashboard.
    * `Table`: Dengan fitur search dan filter untuk data siswa/pengajar.
    * `Badge`: Status pembayaran atau kondisi sarana.
    * `Dialog/Modal`: Untuk input data cepat (misal: input cashflow).

---

## 6. Struktur Database (Mapping Table)
Berdasarkan SQL yang Anda berikan, berikut adalah relasi inti yang harus diperhatikan:
* `user` adalah pusat autentikasi yang memiliki `role`.
* `lembaga` adalah entitas induk (Yayasan).
* `sekolah` berelasi ke `lembaga`.
* `pengajar` dan `anak` berelasi ke `sekolah` dan memiliki akun di tabel `user`.
* `pencapaian` menghubungkan `kegiatan`, `anak`, dan `matrikulasi`.

---

## 7. Non-Functional Requirements
1.  **Security:** Proteksi CSRF, enkripsi password (Bcrypt), dan Role-Based Access Control (RBAC) menggunakan Middleware Laravel.
2.  **Performance:** Optimasi image upload (untuk foto kegiatan/anak) menggunakan Intervension Image agar ukuran file tidak membebani server.
3.  **Responsiveness:** Aplikasi harus dapat diakses dengan baik melalui smartphone (terutama untuk orang tua dan guru).

---

## 8. Langkah Selanjutnya
Apakah Anda ingin saya membantu dalam:
1.  **Membuat file Migration dan Model** Laravel berdasarkan schema SQL tersebut?
2.  **Membuat Controller** utama untuk salah satu role (misal: Admin Sekolah)?
3.  **Membuat template Dashboard** menggunakan Tailwind CSS ala shadcn?

saya ingin membuat sebuah aplikasi berbasis website untuk mengelola paud atau daycare saya. untuk role dan perannya sebagai berikut:
1. Lembaga:
- Dashboard
- Mengelola sekolah
- Input Admin sekolah
- Menu kritik dan saran
2. (Admin) Sekolah:
- Dashboard
- Mengelola data anak (orangtua) baru
- Mengelola sarana
- Mengelola pengajar 
- Mengelola Menu makanan
- Mengelola Kegiatan
- Mengelola Cashflow
3. Anak (orangtua)
- Dashboard
- Menu Kegiatan
- Menu Matrikulasi
- Menu Pencapaian
- Menu kritik dan saran
- Menu Menu Makanan
4. Pengajar
- Dashboard
- Menu Kegiatan Anak 
- Menu Matrikulasi 
- Menu Pencapaian Anak

dibuat menggunakan laravel 11 blade only, mysql untuk DB dan ui.shadcn untuk tampilannya. buatkan PRD lengkap dan detail