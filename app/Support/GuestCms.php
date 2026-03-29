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
            'hero_title' => CmsContent::get('hero_title', 'Selamat Datang di PAUD Kita! 🌈'),
            'hero_subtitle' => CmsContent::get('hero_subtitle', 'Tempat terbaik untuk tumbuh, belajar, dan bermain bersama teman-teman baru!'),
            'hero_photo' => CmsContent::get('hero_photo', ''),
            'about_title' => CmsContent::get('about_title', 'Tentang Kami'),
            'about_text' => CmsContent::get('about_text', 'Kami adalah lembaga PAUD/Daycare terpercaya yang mengutamakan keceriaan dan perkembangan anak.'),
            'about_photo' => CmsContent::get('about_photo', ''),
            'facility_1_title' => CmsContent::get('facility_1_title', 'Ruang Bermain'),
            'facility_1_desc' => CmsContent::get('facility_1_desc', 'Area bermain indoor dan outdoor yang aman dan nyaman.'),
            'facility_1_icon' => CmsContent::get('facility_1_icon', '🎠'),
            'facility_2_title' => CmsContent::get('facility_2_title', 'Ruang Belajar'),
            'facility_2_desc' => CmsContent::get('facility_2_desc', 'Kelas yang menyenangkan dengan alat peraga edukatif.'),
            'facility_2_icon' => CmsContent::get('facility_2_icon', '📚'),
            'facility_3_title' => CmsContent::get('facility_3_title', 'Kantin Sehat'),
            'facility_3_desc' => CmsContent::get('facility_3_desc', 'Menu bergizi seimbang yang disiapkan setiap hari.'),
            'facility_3_icon' => CmsContent::get('facility_3_icon', '🍱'),
            'facility_4_title' => CmsContent::get('facility_4_title', 'UKS & Keamanan'),
            'facility_4_desc' => CmsContent::get('facility_4_desc', 'Tenaga kesehatan dan sistem keamanan yang terpantau.'),
            'facility_4_icon' => CmsContent::get('facility_4_icon', '🏥'),
            'gallery_1' => CmsContent::get('gallery_1', ''),
            'gallery_2' => CmsContent::get('gallery_2', ''),
            'gallery_3' => CmsContent::get('gallery_3', ''),
            'gallery_4' => CmsContent::get('gallery_4', ''),
            'gallery_5' => CmsContent::get('gallery_5', ''),
            'gallery_6' => CmsContent::get('gallery_6', ''),
            'kontak_alamat' => CmsContent::get('kontak_alamat', 'Jl. Contoh No. 1, Kota'),
            'kontak_telepon' => CmsContent::get('kontak_telepon', '021-1234567'),
            'kontak_email' => CmsContent::get('kontak_email', 'info@paud.com'),
            'kontak_jam' => CmsContent::get('kontak_jam', 'Senin–Jumat: 07.00–16.00 WIB'),
            'footer_text' => CmsContent::get('footer_text', 'Tumbuh bersama, bahagia bersama. 💛'),
        ];
    }
}
