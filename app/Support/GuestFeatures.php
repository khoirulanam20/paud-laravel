<?php

namespace App\Support;

final class GuestFeatures
{
    /**
     * @return list<array{value: string, label: string}>
     */
    public static function stats(): array
    {
        return [
            ['value' => 'Real-time', 'label' => 'Transparansi ortu–sekolah'],
            ['value' => 'Mudah', 'label' => 'Memudahkan operasional'],
            ['value' => 'AI', 'label' => 'Asisten cerdas bawaan'],
        ];
    }

    /**
     * @return list<array{id: string, title: string, tagline: string, desc: string, illustration: string, highlights: list<string>}>
     */
    public static function pillars(): array
    {
        return [
            [
                'id' => 'ortu-sekolah',
                'title' => 'Penghubung Ortu & Sekolah',
                'tagline' => 'Komunikasi dua arah, tanpa jarak',
                'desc' => 'Orang tua selalu tahu perkembangan anak — kehadiran, pencapaian, monev, tagihan — tanpa harus menunggu rapat atau chat manual.',
                'illustration' => 'pillar.ortu',
                'highlights' => [
                    'Portal orang tua: presensi, pencapaian, monev PDF',
                    'Pembayaran & invoice online',
                    'Kritik & saran langsung ke admin',
                    'Chat admin–orang tua terpusat',
                ],
            ],
            [
                'id' => 'operasional',
                'title' => 'Operasional Internal',
                'tagline' => 'Admin & guru bekerja lebih cepat',
                'desc' => 'Satu dashboard untuk mengelola siswa, kelas, kegiatan harian, presensi, menu makanan, hingga keuangan PSAK — tanpa spreadsheet terpisah.',
                'illustration' => 'pillar.operasional',
                'highlights' => [
                    'Siswa, kelas & approval pendaftaran',
                    'Agenda belajar & kegiatan rutin',
                    'Presensi siswa & guru',
                    'Biaya bulanan, jurnal & laporan PSAK',
                ],
            ],
            [
                'id' => 'ai',
                'title' => 'Kemudahan dengan AI',
                'tagline' => 'Otomasi yang menghemat waktu guru',
                'desc' => 'Asisten AI bantu jawab pertanyaan orang tua, generate ringkasan monev, dan sarankan umpan balik pencapaian — berbasis data sekolah yang nyata.',
                'illustration' => 'pillar.ai',
                'highlights' => [
                    'Chat AI orang tua berbasis data anak',
                    'Generate monev otomatis per siswa',
                    'Saran feedback pencapaian untuk guru',
                    'Persona AI dapat dikustom per sekolah',
                ],
            ],
        ];
    }

    /**
     * @return list<array{step: string, title: string, desc: string}>
     */
    public static function onboardingSteps(): array
    {
        return [
            ['step' => '01', 'title' => 'Setup Sekolah & Tim', 'desc' => 'Daftarkan cabang, kelas, pengajar, dan admin — siap operasional dalam hitungan jam.'],
            ['step' => '02', 'title' => 'Aktifkan Portal Orang Tua', 'desc' => 'Orang tua daftar online, pantau anak, bayar tagihan, dan kirim masukan langsung dari HP.'],
            ['step' => '03', 'title' => 'Manfaatkan AI & Laporan', 'desc' => 'Generate monev PDF, aktifkan chat AI, dan biarkan sistem yang mengurus dokumentasi rutin.'],
        ];
    }

    /**
     * @return list<array{title: string, quote: string, name: string, role: string, rating: int}>
     */
    public static function testimonials(): array
    {
        return [
            [
                'title' => 'Orang tua lebih tenang',
                'quote' => 'Saya bisa cek kehadiran dan pencapaian anak kapan saja. Tidak perlu menunggu rapat bulanan untuk tahu perkembangannya.',
                'name' => 'Ibu Sari',
                'role' => 'Orang Tua PAUD',
                'rating' => 5,
            ],
            [
                'title' => 'Admin jadi lebih efisien',
                'quote' => 'Pendaftaran, presensi, dan pembayaran dalam satu sistem. Tim kami hemat berjam-jam kerja administratif setiap minggu.',
                'name' => 'Budi Santoso',
                'role' => 'Admin Sekolah',
                'rating' => 5,
            ],
            [
                'title' => 'Mudah kelola multi-cabang',
                'quote' => 'Sebagai lembaga dengan tiga cabang, SIPP membantu kami memantau operasional dan komunikasi orang tua secara terpusat.',
                'name' => 'Dewi Lestari',
                'role' => 'Ketua Lembaga',
                'rating' => 5,
            ],
        ];
    }
}
