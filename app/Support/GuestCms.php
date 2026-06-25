<?php

namespace App\Support;

use App\Models\CmsContent;

final class GuestCms
{
    /**
     * @return array<string, string>
     */
    public static function data(): array
    {
        return [
            'hero_title' => CmsContent::get('hero_title', 'Sistem Informasi PAUD Terpadu'),
            'hero_subtitle' => CmsContent::get('hero_subtitle', 'Menghubungkan orang tua dan sekolah, mempermudah operasional internal, dan mengotomasi pekerjaan rutin dengan AI dalam satu platform PAUD.'),
            'hero_photo' => CmsContent::get('hero_photo', ''),
            'about_title' => CmsContent::get('about_title', 'Mengapa SIPP?'),
            'about_text' => CmsContent::get('about_text', "SIPP hadir untuk tiga hal penting: menjembatani komunikasi orang tua dan sekolah secara transparan, menyederhanakan operasional harian tim admin dan guru, serta memanfaatkan AI untuk mengurangi pekerjaan dokumentasi yang repetitif.\n\nBukan sekadar software dengan banyak menu — SIPP dirancang agar setiap pihak merasakan manfaat nyata setiap hari."),
            'about_photo' => CmsContent::get('about_photo', ''),
            'facility_1_title' => CmsContent::get('facility_1_title', 'Penghubung Ortu & Sekolah'),
            'facility_1_desc' => CmsContent::get('facility_1_desc', 'Portal orang tua, presensi, pencapaian, monev, pembayaran, dan komunikasi dua arah.'),
            'facility_1_icon' => CmsContent::get('facility_1_icon', 'service-ortu.svg'),
            'facility_2_title' => CmsContent::get('facility_2_title', 'Operasional Internal'),
            'facility_2_desc' => CmsContent::get('facility_2_desc', 'Siswa, kelas, kegiatan, presensi guru, menu makanan, dan keuangan PSAK.'),
            'facility_2_icon' => CmsContent::get('facility_2_icon', 'service-operasional.svg'),
            'facility_3_title' => CmsContent::get('facility_3_title', 'Kemudahan dengan AI'),
            'facility_3_desc' => CmsContent::get('facility_3_desc', 'Chat AI orang tua, generate monev, saran feedback, dan persona yang dikustom.'),
            'facility_3_icon' => CmsContent::get('facility_3_icon', 'service-ai.svg'),
            'facility_4_title' => CmsContent::get('facility_4_title', 'Komunikasi & AI'),
            'facility_4_desc' => CmsContent::get('facility_4_desc', 'Chat orang tua, kritik saran, dan asisten AI berbasis data sekolah.'),
            'facility_4_icon' => CmsContent::get('facility_4_icon', 'service-komunikasi.svg'),
            'gallery_1' => CmsContent::get('gallery_1', ''),
            'gallery_2' => CmsContent::get('gallery_2', ''),
            'gallery_3' => CmsContent::get('gallery_3', ''),
            'gallery_4' => CmsContent::get('gallery_4', ''),
            'gallery_5' => CmsContent::get('gallery_5', ''),
            'gallery_6' => CmsContent::get('gallery_6', ''),
            'kontak_alamat' => CmsContent::get('kontak_alamat', 'Jl. Taman Lembayung No.47, Sendangguwo, Kec. Tembalang, Kota Semarang, Jawa Tengah (50273)'),
            'kontak_telepon' => CmsContent::get('kontak_telepon', GuestWhatsApp::DISPLAY),
            'kontak_email' => CmsContent::get('kontak_email', 'admin@firstudio.id'),
            'kontak_jam' => CmsContent::get('kontak_jam', 'Senin–Jumat: 08.00–17.00 WIB'),
            'footer_text' => CmsContent::get('footer_text', 'Menghubungkan orang tua dan sekolah, mempermudah operasional, didukung AI — untuk PAUD Indonesia.'),
        ];
    }
}
