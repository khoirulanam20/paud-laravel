<?php

require __DIR__.'/helpers.php';

return [

    // ─── LEMBAGA ───────────────────────────────────────────────

    'lembaga.sekolah.index' => array_merge(
        [
            ['element' => '[data-tour="nav-lembaga.sekolah.index"]', 'title' => 'Menu Sekolah', 'description' => 'Kelola daftar cabang sekolah di bawah yayasan Anda dari menu ini.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Data Sekolah', 'description' => 'Halaman ini menampilkan seluruh cabang sekolah yang terdaftar di sistem.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'lembaga-sekolah',
            'Tambah Cabang',
            'Klik tombol ini untuk mendaftarkan cabang sekolah baru.',
            'Edit',
            'Perbarui nama, alamat, dan informasi cabang sekolah.',
            'Hapus',
            'Hapus cabang sekolah secara permanen setelah konfirmasi.',
        ),
    ),

    'lembaga.admin-sekolah.index' => array_merge(
        [
            ['element' => '[data-tour="nav-lembaga.admin-sekolah.index"]', 'title' => 'Menu Admin Sekolah', 'description' => 'Kelola akun admin sekolah yang mengelola operasional tiap cabang.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Admin Sekolah', 'description' => 'Daftar administrator yang ditugaskan ke masing-masing cabang sekolah.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'lembaga-admin',
            'Tambah Admin',
            'Buat akun admin baru dan tentukan cabang sekolah yang dikelolanya.',
            'Edit',
            'Ubah data admin dan penugasan cabang sekolah.',
            'Hapus',
            'Hapus akun admin setelah memastikan tidak ada ketergantungan aktif.',
        ),
    ),

    'lembaga.kritik-saran.index' => [
        ['element' => '[data-tour="nav-lembaga.kritik-saran.index"]', 'title' => 'Menu Kritik & Saran', 'description' => 'Pantau masukan dari orang tua di seluruh cabang sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Kritik & Saran', 'description' => 'Semua pesan masukan dari wali murid dikumpulkan di halaman ini.', 'side' => 'bottom'],
        ['element' => '[data-tour="lembaga-kritik-list"]', 'title' => 'Daftar Masukan', 'description' => 'Setiap kartu menampilkan pengirim, isi pesan, dan status tanggapan.', 'side' => 'top'],
    ],

    'lembaga.ai-setting.index' => [
        ['element' => '[data-tour="nav-lembaga.ai-setting.index"]', 'title' => 'Menu Pengaturan AI', 'description' => 'Konfigurasi layanan AI dan token untuk seluruh yayasan dari sini.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Pengaturan AI', 'description' => 'Atur provider, model, API key, dan saldo token per sekolah.', 'side' => 'bottom'],
        ['element' => '[data-tour="lembaga-ai-tabs"]', 'title' => 'Tab Pengaturan', 'description' => 'Pilih tab Provider & API atau Token Sekolah.', 'side' => 'bottom'],
        ['element' => '[data-tour="lembaga-ai-status"]', 'title' => 'Status AI', 'description' => 'Banner ini menunjukkan apakah layanan AI aktif atau belum dikonfigurasi.', 'side' => 'bottom'],
        ['element' => '[data-tour="lembaga-ai-form"]', 'title' => 'Form Konfigurasi', 'description' => 'Isi provider, model, dan API key. Gunakan tombol uji koneksi sebelum menyimpan.', 'side' => 'top'],
    ],

    // ─── ADMIN SEKOLAH ─────────────────────────────────────────

    'admin.anak.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.anak.index"]', 'title' => 'Menu Siswa Sekolah', 'description' => 'Kelola data seluruh siswa yang terdaftar di sekolah Anda.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Siswa Sekolah', 'description' => 'Daftar lengkap siswa beserta data orang tua dan kelasnya.', 'side' => 'bottom'],
            ['element' => '[data-tour="admin-anak-filter"]', 'title' => 'Filter & Pencarian', 'description' => 'Cari siswa berdasarkan nama atau filter berdasarkan kelas.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-anak',
            'Registrasi Siswa',
            'Klik untuk membuka form pendaftaran siswa baru beserta data orang tua.',
            'Edit',
            'Perbarui profil siswa, kelas, atau data orang tua.',
            'Hapus',
            'Hapus data siswa dari sekolah setelah konfirmasi.',
            'Detail',
            'Buka halaman profil lengkap siswa.',
        ),
    ),

    'admin.pendaftaran.index' => [
        ['element' => '[data-tour="nav-admin.pendaftaran.index"]', 'title' => 'Menu Pendaftaran', 'description' => 'Tinjau dan proses pendaftaran siswa baru dari orang tua.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Pendaftaran Siswa Baru', 'description' => 'Kelola permohonan pendaftaran yang masuk ke sekolah Anda.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-pendaftaran-pending"]', 'title' => 'Menunggu Persetujuan', 'description' => 'Pendaftar baru yang perlu Anda setujui atau tolak. Badge menunjukkan jumlah pending.', 'side' => 'top'],
        ['element' => '[data-tour="admin-pendaftaran-action-approve"]', 'title' => 'Setujui', 'description' => 'Terima pendaftaran siswa baru ke sekolah.', 'side' => 'left'],
        ['element' => '[data-tour="admin-pendaftaran-action-reject"]', 'title' => 'Tolak', 'description' => 'Tolak pendaftaran dengan alasan penolakan.', 'side' => 'left'],
        ['element' => '[data-tour="admin-pendaftaran-approved"]', 'title' => 'Sudah Disetujui', 'description' => 'Daftar pendaftar yang telah diterima dan aktif di sistem.', 'side' => 'top'],
    ],

    'admin.kelas.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.kelas.index"]', 'title' => 'Menu Kelola Kelas', 'description' => 'Atur kelas, wali kelas, dan penempatan siswa.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Kelola Kelas', 'description' => 'Manajemen kelas dan penempatan siswa di sekolah.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-kelas',
            'Buat Kelas',
            'Tambahkan kelas baru beserta wali kelas dan deskripsinya.',
            'Edit',
            'Ubah nama kelas, wali kelas, atau deskripsi.',
            'Hapus',
            'Hapus kelas yang tidak lagi digunakan.',
            'Detail',
            'Lihat daftar siswa yang terdaftar di kelas ini.',
        ),
    ),

    'admin.presensi.index' => [
        ['element' => '[data-tour="nav-admin.presensi.index"]', 'title' => 'Menu Presensi Siswa', 'description' => 'Catat dan pantau kehadiran siswa harian.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Presensi Siswa', 'description' => 'Input kehadiran siswa per kelas dan tanggal.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-presensi-filter"]', 'title' => 'Filter Kelas & Tanggal', 'description' => 'Pilih kelas dan tanggal untuk menampilkan daftar siswa yang akan diabsen.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-presensi-checklist"]', 'title' => 'Checklist Kehadiran', 'description' => 'Centang siswa yang hadir, lalu simpan. Ringkasan statistik ditampilkan di atas.', 'side' => 'top'],
    ],

    'admin.kesehatan.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.kesehatan.index"]', 'title' => 'Menu Kesehatan Siswa', 'description' => 'Pantau dan catat data kesehatan seluruh siswa.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Kesehatan Siswa', 'description' => 'Data berat badan, tinggi badan, alergi, dan kebersihan siswa.', 'side' => 'bottom'],
            ['element' => '[data-tour="admin-kesehatan-filter"]', 'title' => 'Filter Siswa', 'description' => 'Saring berdasarkan kelas atau cari nama siswa tertentu.', 'side' => 'bottom'],
            ['element' => '[data-tour="admin-kesehatan-action-riwayat"]', 'title' => 'Riwayat', 'description' => 'Lihat riwayat pemeriksaan kesehatan dan kebersihan siswa.', 'side' => 'left'],
            ['element' => '[data-tour="admin-kesehatan-action-input"]', 'title' => 'Input Data', 'description' => 'Catat pemeriksaan kesehatan dan kebersihan siswa.', 'side' => 'left'],
        ],
    ),

    'admin.matrikulasi.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.matrikulasi.index"]', 'title' => 'Menu Matrikulasi', 'description' => 'Kelola indikator pembelajaran dan kurikulum sekolah.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Matrikulasi', 'description' => 'Daftar indikator capaian pembelajaran yang menjadi acuan evaluasi.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-matrikulasi',
            'Tambah Indikator',
            'Buat indikator matrikulasi baru dengan aspek dan tujuan pembelajaran.',
            'Edit',
            'Perbarui indikator matrikulasi yang sudah tersimpan.',
            'Hapus',
            'Hapus indikator yang salah atau duplikat.',
            'Detail',
            'Baca tujuan, strategi, dan deskripsi lengkap indikator.',
        ),
    ),

    'admin.skala-pencapaian.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.skala-pencapaian.index"]', 'title' => 'Menu Skala Capaian', 'description' => 'Atur skala penilaian pencapaian siswa (mis. Berkembang, Cakap).', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Skala Capaian', 'description' => 'Definisi tingkat pencapaian yang dipakai saat evaluasi siswa.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-skala',
            'Tambah Skala',
            'Buat level capaian baru dengan kode, label, dan warna.',
            'Edit',
            'Sesuaikan deskripsi atau level skala pencapaian.',
            'Hapus',
            'Hapus skala yang tidak lagi dipakai.',
        ),
    ),

    'admin.kegiatan.index' => [
        ['element' => '[data-tour="nav-admin.kegiatan.index"]', 'title' => 'Menu Agenda Belajar', 'description' => 'Kelola jurnal kegiatan belajar harian di sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Agenda Belajar', 'description' => 'Kalender jurnal kegiatan belajar seluruh kelas dan pengajar.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-kegiatan-filter"]', 'title' => 'Filter Kalender', 'description' => 'Saring jurnal berdasarkan kelas atau pengajar tertentu.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-kegiatan-add-btn"]', 'title' => 'Buat Jurnal', 'description' => 'Tambahkan jurnal kegiatan baru beserta dokumentasi foto.', 'side' => 'left'],
        ['element' => '[data-tour="admin-kegiatan-calendar"]', 'title' => 'Kalender Jurnal', 'description' => 'Klik tanggal di kalender untuk melihat, edit, atau hapus jurnal.', 'side' => 'top'],
    ],

    'admin.master-kegiatan-rutin.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.master-kegiatan-rutin.index"]', 'title' => 'Menu Kegiatan Rutin', 'description' => 'Kelola kegiatan rutin harian seperti makan, tidur, dan toilet training.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Kegiatan Rutin', 'description' => 'Master kegiatan rutin yang dicatat setiap hari untuk setiap siswa.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'pg-master-rutin',
            'Tambah Kegiatan',
            'Buat jenis kegiatan rutin baru dan tautkan ke matrikulasi.',
            'Edit',
            'Ubah template kegiatan rutin.',
            'Hapus',
            'Hapus kegiatan rutin yang tidak digunakan.',
            'Detail',
            'Lihat capaian dan catatan siswa per kegiatan.',
        ),
    ),

    'admin.pencapaian.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.pencapaian.index"]', 'title' => 'Menu Pencapaian Siswa', 'description' => 'Evaluasi dan catat pencapaian belajar siswa.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Pencapaian Siswa', 'description' => 'Rekap evaluasi pencapaian per kegiatan dan per siswa.', 'side' => 'bottom'],
            ['element' => '[data-tour="admin-pencapaian-filter"]', 'title' => 'Filter Aspek', 'description' => 'Saring pencapaian berdasarkan aspek pembelajaran tertentu.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-pencapaian',
            'Buat Evaluasi',
            'Tambahkan penilaian pencapaian baru dengan bantuan AI.',
            'Edit',
            'Sesuaikan nilai atau catatan evaluasi.',
            'Hapus',
            'Hapus evaluasi yang dibuat secara keliru.',
        ),
    ),

    'admin.monev.index' => [
        ['element' => '[data-tour="nav-admin.monev.index"]', 'title' => 'Menu Monev', 'description' => 'Monitoring dan evaluasi perkembangan siswa bulanan.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Monitoring & Evaluasi', 'description' => 'Generate dan kelola laporan monev perkembangan siswa.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-monev-filters"]', 'title' => 'Filter Laporan', 'description' => 'Cari siswa, pilih kelas, dan tentukan periode bulan/tahun.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-monev-table"]', 'title' => 'Daftar Siswa', 'description' => 'Generate laporan monev per siswa atau sekaligus untuk seluruh kelas.', 'side' => 'top'],
    ],

    'admin.pengajar.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.pengajar.index"]', 'title' => 'Menu Data Pengajar', 'description' => 'Kelola akun dan data guru/pengajar di sekolah.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Data Pengajar', 'description' => 'Daftar pengajar beserta jabatan, kelas, dan kontak.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-pengajar',
            'Registrasi Pengajar',
            'Daftarkan pengajar baru beserta akun login dan penugasan kelas.',
            'Edit',
            'Ubah data pengajar dan penugasan kelas.',
            'Hapus',
            'Nonaktifkan atau hapus akun pengajar.',
        ),
    ),

    'admin.presensi-guru.index' => [
        ['element' => '[data-tour="nav-admin.presensi-guru.index"]', 'title' => 'Menu Presensi Guru', 'description' => 'Catat kehadiran pengajar setiap hari.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Presensi Guru', 'description' => 'Input absensi harian seluruh pengajar di sekolah.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-presensi-guru-date"]', 'title' => 'Pilih Tanggal', 'description' => 'Tentukan tanggal presensi yang ingin dicatat atau diubah.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-presensi-guru-input-table"]', 'title' => 'Daftar Pengajar', 'description' => 'Tandai Hadir/Tidak Hadir dan isi keterangan, lalu simpan.', 'side' => 'top'],
    ],

    'admin.sarana.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.sarana.index"]', 'title' => 'Menu Sarana', 'description' => 'Inventarisasi sarana dan prasarana sekolah.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Sarana & Prasarana', 'description' => 'Kelola daftar inventaris fasilitas sekolah.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-sarana',
            'Tambah Sarana',
            'Catat sarana baru beserta foto, lokasi, dan kondisi.',
            'Edit',
            'Perbarui informasi sarana dan prasarana.',
            'Hapus',
            'Hapus data sarana dari inventaris.',
        ),
    ),

    'admin.menu-makanan.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.menu-makanan.index"]', 'title' => 'Menu Makanan', 'description' => 'Kelola menu makan harian yang dilihat orang tua.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Menu Makanan', 'description' => 'Input dan pantau menu makan siswa per hari.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-menu',
            'Input Menu',
            'Tambahkan menu makan baru beserta info gizi dan foto.',
            'Edit',
            'Perbarui daftar hidangan, gizi, atau foto menu.',
            'Hapus',
            'Hapus jadwal menu yang sudah tidak relevan.',
        ),
    ),

    'admin.cashflow.index' => array_merge(
        [
            ['element' => '[data-tour="nav-admin.cashflow.index"]', 'title' => 'Menu Cashflow', 'description' => 'Catat pemasukan dan pengeluaran keuangan sekolah.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Cashflow', 'description' => 'Ringkasan keuangan dan riwayat transaksi sekolah.', 'side' => 'bottom'],
            ['element' => '[data-tour="admin-cashflow-stats"]', 'title' => 'Ringkasan Keuangan', 'description' => 'Kartu statistik menampilkan total pemasukan, pengeluaran, dan saldo.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'admin-cashflow',
            'Catat Transaksi',
            'Input pemasukan atau pengeluaran kas sekolah.',
            'Edit',
            'Koreksi nominal, kategori, atau keterangan transaksi.',
            'Hapus',
            'Hapus catatan transaksi yang salah input.',
        ),
    ),

    'admin.kritik-saran.index' => [
        ['element' => '[data-tour="nav-admin.kritik-saran.index"]', 'title' => 'Menu Kritik & Saran', 'description' => 'Baca dan tanggapi masukan dari orang tua.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Kritik & Saran', 'description' => 'Kotak masukan dari wali murid sekolah Anda.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-kritik-table"]', 'title' => 'Daftar Masukan', 'description' => 'Lihat pengirim, kelas anak, dan ringkasan pesan.', 'side' => 'top'],
        ['element' => '[data-tour="admin-kritik-action-detail"]', 'title' => 'Detail', 'description' => 'Buka halaman detail untuk membaca dan menanggapi masukan.', 'side' => 'left'],
    ],

    'admin.orangtua-chat.index' => [
        ['element' => '[data-tour="nav-admin.orangtua-chat.index"]', 'title' => 'Menu Chat Orang Tua', 'description' => 'Pantau percakapan AI antara orang tua dan asisten sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Chat Orang Tua', 'description' => 'Riwayat percakapan chatbot yang digunakan orang tua.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-chat-table"]', 'title' => 'Riwayat Chat', 'description' => 'Lihat orang tua, anak terkait, jumlah pesan, dan cuplikan terakhir.', 'side' => 'top'],
        ['element' => '[data-tour="admin-chat-action-detail"]', 'title' => 'Detail', 'description' => 'Buka thread percakapan lengkap dengan orang tua.', 'side' => 'left'],
    ],

    'admin.ai-persona.index' => [
        ['element' => '[data-tour="nav-admin.ai-persona.index"]', 'title' => 'Menu Pengaturan AI', 'description' => 'Atur persona, fallback, dan pantau saldo token sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Pengaturan AI', 'description' => 'Konfigurasi persona AI, pesan fallback, dan saldo token untuk fitur AI sekolah.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-ai-token-balance"]', 'title' => 'Saldo Token', 'description' => 'Lihat sisa token AI sekolah. Setiap generate memakai 1 token.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-ai-persona-tabs"]', 'title' => 'Tab Persona', 'description' => 'Pilih scope persona: chat orang tua, evaluasi pencapaian, atau monev.', 'side' => 'bottom'],
        ['element' => '[data-tour="admin-ai-persona-form"]', 'title' => 'Form Persona', 'description' => 'Tulis instruksi persona atau gunakan tombol Generate AI untuk membuatnya otomatis.', 'side' => 'top'],
    ],

    // ─── ADMIN KELAS ───────────────────────────────────────────

    'adminkelas.anak.index' => array_merge(
        [
            ['element' => '[data-tour="nav-adminkelas.anak.index"]', 'title' => 'Menu Siswa Kelasku', 'description' => 'Kelola siswa di kelas yang Anda ampu.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Siswa Kelasku', 'description' => 'Daftar siswa di kelas Anda sebagai wali kelas.', 'side' => 'bottom'],
            ['element' => '[data-tour="ak-anak-filter"]', 'title' => 'Filter Kelas', 'description' => 'Pilih kelas untuk melihat daftar siswanya.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'ak-anak',
            'Registrasi Siswa',
            'Klik untuk mendaftarkan siswa baru ke kelas Anda.',
            'Edit',
            'Perbarui data siswa di kelas Anda.',
            'Hapus',
            'Hapus data siswa dari kelas.',
            'Detail',
            'Buka halaman profil lengkap siswa.',
        ),
    ),

    'adminkelas.presensi.index' => [
        ['element' => '[data-tour="nav-adminkelas.presensi.index"]', 'title' => 'Menu Presensi Kelasku', 'description' => 'Absensi harian siswa di kelas Anda.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Presensi Kelasku', 'description' => 'Catat kehadiran siswa di kelas yang Anda kelola.', 'side' => 'bottom'],
        ['element' => '[data-tour="ak-presensi-filter"]', 'title' => 'Filter Tanggal', 'description' => 'Pilih kelas dan tanggal untuk menampilkan daftar absensi.', 'side' => 'bottom'],
        ['element' => '[data-tour="ak-presensi-checklist"]', 'title' => 'Checklist Kehadiran', 'description' => 'Centang siswa yang hadir lalu simpan presensi hari ini.', 'side' => 'top'],
    ],

    'adminkelas.kesehatan.index' => array_merge(
        [
            ['element' => '[data-tour="nav-adminkelas.kesehatan.index"]', 'title' => 'Menu Kesehatan Kelasku', 'description' => 'Pantau kesehatan siswa di kelas Anda.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Kesehatan Kelasku', 'description' => 'Data kesehatan siswa di kelas yang Anda ampu.', 'side' => 'bottom'],
            ['element' => '[data-tour="ak-kesehatan-filter"]', 'title' => 'Pencarian Siswa', 'description' => 'Cari siswa tertentu di kelas Anda.', 'side' => 'bottom'],
            ['element' => '[data-tour="ak-kesehatan-action-riwayat"]', 'title' => 'Riwayat', 'description' => 'Lihat riwayat pemeriksaan kesehatan siswa di kelas Anda.', 'side' => 'left'],
            ['element' => '[data-tour="ak-kesehatan-action-input"]', 'title' => 'Input Data', 'description' => 'Catat pemeriksaan kesehatan siswa di kelas Anda.', 'side' => 'left'],
        ],
    ),

    'adminkelas.monev.index' => [
        ['element' => '[data-tour="nav-adminkelas.monev.index"]', 'title' => 'Menu Monev', 'description' => 'Generate laporan monev siswa di kelas Anda.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Monev Kelas', 'description' => 'Monitoring dan evaluasi perkembangan siswa kelas Anda.', 'side' => 'bottom'],
        ['element' => '[data-tour="ak-monev-filters"]', 'title' => 'Filter Periode', 'description' => 'Pilih bulan dan tahun laporan monev yang ingin dibuat.', 'side' => 'bottom'],
        ['element' => '[data-tour="ak-monev-table"]', 'title' => 'Daftar Siswa', 'description' => 'Generate laporan monev per siswa atau sekaligus untuk seluruh kelas.', 'side' => 'top'],
    ],

    // ─── PENGAJAR ──────────────────────────────────────────────

    'pengajar.master-kegiatan-rutin.index' => array_merge(
        [
            ['element' => '[data-tour="nav-pengajar.master-kegiatan-rutin.index"]', 'title' => 'Menu Kegiatan Rutin', 'description' => 'Kelola dan input kegiatan rutin harian siswa.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Kegiatan Rutin', 'description' => 'Master kegiatan rutin yang Anda catat setiap hari.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'pg-master-rutin',
            'Tambah Kegiatan',
            'Buat jenis kegiatan rutin baru jika belum ada di daftar.',
            'Edit',
            'Ubah template kegiatan rutin.',
            'Hapus',
            'Hapus kegiatan rutin yang tidak digunakan.',
            'Detail',
            'Input harian atau lihat capaian siswa.',
        ),
    ),

    'pengajar.kegiatan.index' => [
        ['element' => '[data-tour="nav-pengajar.kegiatan.index"]', 'title' => 'Menu Agenda Belajar', 'description' => 'Buat dan kelola jurnal kegiatan belajar harian.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Agenda Belajar', 'description' => 'Kalender jurnal kegiatan belajar yang Anda ampu.', 'side' => 'bottom'],
        ['element' => '[data-tour="pg-kegiatan-add-btn"]', 'title' => 'Buat Jurnal', 'description' => 'Tambahkan jurnal kegiatan baru dengan dokumentasi foto.', 'side' => 'left'],
        ['element' => '[data-tour="pg-kegiatan-calendar"]', 'title' => 'Kalender Jurnal', 'description' => 'Klik tanggal di kalender untuk melihat, edit, atau hapus jurnal.', 'side' => 'top'],
    ],

    'pengajar.matrikulasi.index' => [
        ['element' => '[data-tour="nav-pengajar.matrikulasi.index"]', 'title' => 'Menu Matrikulasi', 'description' => 'Lihat indikator pembelajaran yang menjadi acuan evaluasi.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Matrikulasi', 'description' => 'Daftar indikator capaian pembelajaran (baca saja).', 'side' => 'bottom'],
        ['element' => '[data-tour="pg-matrikulasi-table"]', 'title' => 'Tabel Indikator', 'description' => 'Lihat kode, aspek, dan klik Detail untuk membaca tujuan & strategi.', 'side' => 'top'],
    ],

    'pengajar.pencapaian.index' => array_merge(
        [
            ['element' => '[data-tour="nav-pengajar.pencapaian.index"]', 'title' => 'Menu Pencapaian Siswa', 'description' => 'Evaluasi pencapaian belajar siswa di kelas Anda.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Pencapaian Siswa', 'description' => 'Catat dan tinjau evaluasi pencapaian per kegiatan.', 'side' => 'bottom'],
            ['element' => '[data-tour="pg-pencapaian-filter"]', 'title' => 'Filter Aspek', 'description' => 'Saring evaluasi berdasarkan aspek pembelajaran.', 'side' => 'bottom'],
        ],
        tour_table_actions(
            'pg-pencapaian',
            'Buat Evaluasi',
            'Tambahkan penilaian pencapaian siswa.',
            'Edit',
            'Ubah nilai atau umpan balik evaluasi.',
            'Hapus',
            'Hapus evaluasi yang keliru.',
        ),
    ),

    // ─── ORANG TUA ─────────────────────────────────────────────

    'orangtua.pencapaian.index' => [
        ['element' => '[data-tour="nav-orangtua.pencapaian.index"]', 'title' => 'Menu Pencapaian', 'description' => 'Lihat perkembangan belajar anak Anda.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Pencapaian Anak', 'description' => 'Laporan pencapaian belajar anak dari kegiatan di sekolah.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-pencapaian-filter"]', 'title' => 'Filter Laporan', 'description' => 'Saring berdasarkan aspek, tanggal, atau pilih anak jika punya lebih dari satu.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-pencapaian-reports"]', 'title' => 'Laporan Pencapaian', 'description' => 'Setiap kartu berisi kegiatan, skala capaian, dan catatan guru.', 'side' => 'top'],
    ],

    'orangtua.chat.index' => [
        ['element' => '[data-tour="nav-orangtua.chat.index"]', 'title' => 'Menu Chat', 'description' => 'Tanya jawab dengan asisten AI sekolah tentang anak Anda.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Chat Asisten', 'description' => 'Percakapan dengan chatbot sekolah untuk informasi seputar anak.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-chat-suggestions"]', 'title' => 'Pertanyaan Contoh', 'description' => 'Klik salah satu pertanyaan umum untuk memulai percakapan dengan cepat.', 'side' => 'top'],
        ['element' => '[data-tour="ortu-chat-input"]', 'title' => 'Ketik Pesan', 'description' => 'Tulis pertanyaan Anda di sini lalu kirim. Riwayat tersimpan otomatis.', 'side' => 'top'],
    ],

    'orangtua.monev.index' => [
        ['element' => '[data-tour="nav-orangtua.monev.index"]', 'title' => 'Menu Monev', 'description' => 'Baca laporan monitoring & evaluasi bulanan anak.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Laporan Monev', 'description' => 'Ringkasan perkembangan anak per bulan dari sekolah.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-monev-filters"]', 'title' => 'Pilih Anak & Periode', 'description' => 'Pilih anak (jika ada lebih dari satu) serta bulan dan tahun laporan.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-monev-report"]', 'title' => 'Isi Laporan', 'description' => 'Baca analisis perkembangan anak yang dihasilkan sekolah.', 'side' => 'top'],
    ],

    'orangtua.kegiatan.index' => [
        ['element' => '[data-tour="nav-orangtua.kegiatan.index"]', 'title' => 'Menu Agenda Belajar', 'description' => 'Lihat kegiatan belajar anak di sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Agenda Belajar', 'description' => 'Kalender jurnal kegiatan belajar anak Anda.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-kegiatan-calendar"]', 'title' => 'Kalender Kegiatan', 'description' => 'Klik tanggal yang ada jurnalnya untuk melihat detail kegiatan dan foto.', 'side' => 'top'],
    ],

    'orangtua.kegiatan-rutin.index' => [
        ['element' => '[data-tour="nav-orangtua.kegiatan-rutin.index"]', 'title' => 'Menu Kegiatan Rutin', 'description' => 'Pantau kegiatan rutin harian anak seperti makan dan tidur.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Kegiatan Rutin', 'description' => 'Riwayat kegiatan rutin anak yang dicatat guru setiap hari.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-rutin-filters"]', 'title' => 'Filter Riwayat', 'description' => 'Pilih anak, jenis kegiatan, dan rentang tanggal.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-rutin-results"]', 'title' => 'Hasil Kegiatan', 'description' => 'Klik item untuk melihat detail dan foto dokumentasi.', 'side' => 'top'],
    ],

    'orangtua.presensi.index' => [
        ['element' => '[data-tour="nav-orangtua.presensi.index"]', 'title' => 'Menu Kehadiran', 'description' => 'Pantau riwayat kehadiran anak di sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Kehadiran Anak', 'description' => 'Rekap absensi harian dan mingguan anak Anda.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-presensi-period-filter"]', 'title' => 'Filter Periode', 'description' => 'Pilih bulan atau minggu untuk melihat rekap kehadiran.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-presensi-summary-cards"]', 'title' => 'Ringkasan Kehadiran', 'description' => 'Statistik hadir, izin, dan sakit per anak ditampilkan di sini.', 'side' => 'top'],
    ],

    'orangtua.menu-makanan.index' => [
        ['element' => '[data-tour="nav-orangtua.menu-makanan.index"]', 'title' => 'Menu Makanan', 'description' => 'Lihat menu makan harian anak di sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Menu Makanan', 'description' => 'Daftar menu makan yang disajikan untuk siswa.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-menu-date-filter"]', 'title' => 'Filter Tanggal', 'description' => 'Pilih rentang tanggal lalu klik Tampilkan untuk melihat menu.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-menu-list"]', 'title' => 'Daftar Menu', 'description' => 'Setiap hari menampilkan menu, foto, dan tombol suka/tidak suka.', 'side' => 'top'],
    ],

    'orangtua.kritik-saran.index' => array_merge(
        [
            ['element' => '[data-tour="nav-orangtua.kritik-saran.index"]', 'title' => 'Menu Saran & Kritik', 'description' => 'Kirim masukan atau saran ke pihak sekolah.', 'side' => 'right'],
            ['element' => '[data-tour="page-header"]', 'title' => 'Saran & Kritik', 'description' => 'Sampaikan masukan Anda dan lihat tanggapan dari sekolah.', 'side' => 'bottom'],
            ['element' => '[data-tour="ortu-kritik-feed"]', 'title' => 'Riwayat Masukan', 'description' => 'Pesan yang sudah dikirim beserta tanggapan sekolah (jika ada).', 'side' => 'top'],
        ],
        tour_table_actions(
            'ortu-kritik',
            'Kirim Masukan',
            'Klik untuk menulis kritik, saran, atau pertanyaan baru.',
            'Edit',
            'Ubah pesan yang masih berstatus terkirim.',
            'Hapus',
            'Hapus masukan yang sudah tidak ingin dikirim.',
        ),
    ),

    'orangtua.kesehatan.index' => [
        ['element' => '[data-tour="nav-orangtua.kesehatan.index"]', 'title' => 'Menu Kesehatan Anak', 'description' => 'Pantau data kesehatan anak dari sekolah.', 'side' => 'right'],
        ['element' => '[data-tour="page-header"]', 'title' => 'Kesehatan Anak', 'description' => 'Riwayat pemeriksaan kesehatan yang dicatat guru.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-kesehatan-metrics"]', 'title' => 'Data Terkini', 'description' => 'Berat badan, tinggi badan, lingkar kepala, dan info alergi terbaru.', 'side' => 'bottom'],
        ['element' => '[data-tour="ortu-kesehatan-timeline"]', 'title' => 'Riwayat Pemeriksaan', 'description' => 'Timeline lengkap pemeriksaan kesehatan dari waktu ke waktu.', 'side' => 'top'],
    ],

];
