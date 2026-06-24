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
            ['value' => '1', 'label' => 'Sistem terintegrasi'],
            ['value' => 'AI', 'label' => 'Asisten cerdas bawaan'],
        ];
    }

    /**
     * @return list<array{id: string, title: string, tagline: string, desc: string, icon: string, highlights: list<string>}>
     */
    public static function pillars(): array
    {
        return [
            [
                'id' => 'ortu-sekolah',
                'title' => 'Penghubung Ortu & Sekolah',
                'tagline' => 'Komunikasi dua arah, tanpa jarak',
                'desc' => 'Orang tua selalu tahu perkembangan anak — kehadiran, pencapaian, monev, tagihan — tanpa harus menunggu rapat atau chat manual.',
                'icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
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
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
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
                'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
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
}
