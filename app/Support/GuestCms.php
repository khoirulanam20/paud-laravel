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
            'hero_title' => CmsContent::get('hero_title', 'Kelola Operasional PAUD & Pantau Tumbuh Kembang Anak Lebih Praktis'),
            'hero_subtitle' => CmsContent::get('hero_subtitle', 'Satu platform untuk admin, guru, dan orang tua—presensi, komunikasi, laporan harian, dan dokumentasi tumbuh kembang tanpa pekerjaan berulang.'),
            'hero_photo' => CmsContent::get('hero_photo', ''),
            'hero_photo_alt' => CmsContent::get('hero_photo_alt', ''),
            'about_title' => CmsContent::get('about_title', 'Mengapa DaycareAI?'),
            'about_text' => CmsContent::get('about_text', "DaycareAI hadir untuk tiga hal penting: menjembatani komunikasi orang tua dan sekolah secara transparan, menyederhanakan operasional harian tim admin dan guru, serta mempermudah dokumentasi dan pelaporan yang repetitif.\n\nBukan sekadar software dengan banyak menu — DaycareAI dirancang agar setiap pihak merasakan manfaat nyata setiap hari."),
            'about_photo' => CmsContent::get('about_photo', ''),
            'about_photo_alt' => CmsContent::get('about_photo_alt', ''),
            'facility_1_title' => CmsContent::get('facility_1_title', 'Penghubung Ortu & Sekolah'),
            'facility_1_desc' => CmsContent::get('facility_1_desc', 'Portal orang tua, presensi, pencapaian, monev, pembayaran, dan komunikasi dua arah.'),
            'facility_1_icon' => CmsContent::get('facility_1_icon', 'service-ortu.svg'),
            'facility_2_title' => CmsContent::get('facility_2_title', 'Operasional Internal'),
            'facility_2_desc' => CmsContent::get('facility_2_desc', 'Siswa, kelas, kegiatan, presensi guru, menu makanan, dan keuangan PSAK.'),
            'facility_2_icon' => CmsContent::get('facility_2_icon', 'service-operasional.svg'),
            'facility_3_title' => CmsContent::get('facility_3_title', 'Laporan & Dokumentasi'),
            'facility_3_desc' => CmsContent::get('facility_3_desc', 'Monev, pencapaian, dan ringkasan kegiatan yang rapi untuk orang tua dan arsip sekolah.'),
            'facility_3_icon' => CmsContent::get('facility_3_icon', 'service-ai.svg'),
            'facility_4_title' => CmsContent::get('facility_4_title', 'Komunikasi Sekolah'),
            'facility_4_desc' => CmsContent::get('facility_4_desc', 'Chat orang tua, kritik saran, dan pengumuman dalam satu saluran terpusat.'),
            'facility_4_icon' => CmsContent::get('facility_4_icon', 'service-komunikasi.svg'),
            'gallery_1' => CmsContent::get('gallery_1', ''),
            'gallery_2' => CmsContent::get('gallery_2', ''),
            'gallery_3' => CmsContent::get('gallery_3', ''),
            'gallery_4' => CmsContent::get('gallery_4', ''),
            'gallery_5' => CmsContent::get('gallery_5', ''),
            'gallery_6' => CmsContent::get('gallery_6', ''),
            'gallery_1_alt' => CmsContent::get('gallery_1_alt', ''),
            'gallery_2_alt' => CmsContent::get('gallery_2_alt', ''),
            'gallery_3_alt' => CmsContent::get('gallery_3_alt', ''),
            'gallery_4_alt' => CmsContent::get('gallery_4_alt', ''),
            'gallery_5_alt' => CmsContent::get('gallery_5_alt', ''),
            'gallery_6_alt' => CmsContent::get('gallery_6_alt', ''),
            'kontak_alamat' => CmsContent::get('kontak_alamat', 'Jl. Taman Lembayung No.47, Sendangguwo, Kec. Tembalang, Kota Semarang, Jawa Tengah (50273)'),
            'kontak_telepon' => CmsContent::get('kontak_telepon', GuestWhatsApp::DISPLAY),
            'kontak_email' => CmsContent::get('kontak_email', 'admin@firstudio.id'),
            'kontak_jam' => CmsContent::get('kontak_jam', 'Senin–Jumat: 08.00–17.00 WIB'),
            'footer_text' => CmsContent::get('footer_text', 'Menghubungkan orang tua dan sekolah, mempermudah operasional, dan mendukung pertumbuhan PAUD Indonesia.'),
            'seo_meta_title' => CmsContent::get('seo_meta_title', ''),
            'seo_meta_description' => CmsContent::get('seo_meta_description', ''),
            'seo_og_image' => CmsContent::get('seo_og_image', ''),
            'seo_focus_keyword' => CmsContent::get('seo_focus_keyword', ''),
            'section_stats_title' => CmsContent::get('section_stats_title', 'Nilai yang Anda dapatkan'),
            'section_stats_subtitle' => CmsContent::get('section_stats_subtitle', 'DaycareAI dirancang untuk transparansi ortu–sekolah, operasional yang lebih ringan, dan data yang terkelola dengan baik.'),
            'section_features_title' => CmsContent::get('section_features_title', 'Fitur utama DaycareAI'),
            'section_features_subtitle' => CmsContent::get('section_features_subtitle', 'Empat pilar yang mendukung operasional PAUD Anda setiap hari.'),
            'section_gallery_title' => CmsContent::get('section_gallery_title', 'Galeri kegiatan'),
            'section_gallery_subtitle' => CmsContent::get('section_gallery_subtitle', 'Cuplikan aktivitas dan lingkungan belajar yang dikelola lewat DaycareAI.'),
            'section_testimonial_title' => CmsContent::get('section_testimonial_title', 'Apa kata pengguna'),
            'section_testimonial_subtitle' => CmsContent::get('section_testimonial_subtitle', 'Cerita dari admin sekolah, lembaga, dan orang tua yang memakai DaycareAI.'),
            'section_cta_title' => CmsContent::get('section_cta_title', 'Mulai digitalisasi PAUD Anda'),
            'section_cta_subtitle' => CmsContent::get('section_cta_subtitle', 'Daftarkan sekolah atau hubungi tim kami untuk demo dan konsultasi.'),
        ];
    }
}
