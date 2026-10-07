<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Traits\CanUploadImage;
use App\Models\CmsContent;
use App\Support\GuestCms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    use CanUploadImage;

    private array $textKeys = [
        'hero_title', 'hero_subtitle', 'hero_photo_alt',
        'about_title', 'about_text', 'about_photo_alt',
        'facility_1_title', 'facility_1_desc', 'facility_1_icon',
        'facility_2_title', 'facility_2_desc', 'facility_2_icon',
        'facility_3_title', 'facility_3_desc', 'facility_3_icon',
        'facility_4_title', 'facility_4_desc', 'facility_4_icon',
        'gallery_1_alt', 'gallery_2_alt', 'gallery_3_alt',
        'gallery_4_alt', 'gallery_5_alt', 'gallery_6_alt',
        'kontak_alamat', 'kontak_telepon', 'kontak_email', 'kontak_jam',
        'footer_text',
        'seo_meta_title', 'seo_meta_description', 'seo_focus_keyword',
        'section_stats_title', 'section_stats_subtitle',
        'section_features_title', 'section_features_subtitle',
        'section_gallery_title', 'section_gallery_subtitle',
        'section_testimonial_title', 'section_testimonial_subtitle',
        'section_cta_title', 'section_cta_subtitle',
        'stats_1_value', 'stats_1_label', 'stats_2_value', 'stats_2_label',
        'stats_3_value', 'stats_3_label', 'stats_4_value', 'stats_4_label',
        'faq_title', 'faq_1_q', 'faq_1_a', 'faq_2_q', 'faq_2_a', 'faq_3_q', 'faq_3_a',
        'section_student_age_title', 'section_student_age_text', 'section_blog_title',
        'testimonial_1_title', 'testimonial_1_quote', 'testimonial_1_name', 'testimonial_1_role',
        'testimonial_2_title', 'testimonial_2_quote', 'testimonial_2_name', 'testimonial_2_role',
        'testimonial_3_title', 'testimonial_3_quote', 'testimonial_3_name', 'testimonial_3_role',
        'page_tentang_h1', 'seo_tentang_description',
        'page_fasilitas_h1', 'seo_fasilitas_description',
        'page_galeri_h1', 'page_galeri_intro', 'seo_galeri_description',
        'page_kontak_h1', 'seo_kontak_description',
        'contact_form_h2', 'contact_form_lead',
        'pillar_1_title', 'pillar_1_desc', 'pillar_2_title', 'pillar_2_desc', 'pillar_3_title', 'pillar_3_desc',
        'page_pendaftaran_h2', 'page_pendaftaran_intro',
        'page_daftar_sekolah_h1', 'page_daftar_sekolah_intro',
        'page_daftar_lembaga_h1', 'page_daftar_lembaga_intro',
    ];

    private array $photoKeys = [
        'hero_photo', 'hero_left_photo', 'hero_right_photo', 'about_photo', 'about_collage_image',
        'faq_image', 'blog_1_image', 'blog_2_image', 'blog_3_image',
        'newsletter_image',
        'gallery_1', 'gallery_2', 'gallery_3',
        'gallery_4', 'gallery_5', 'gallery_6',
        'seo_og_image',
    ];

    public function index()
    {
        $cms = GuestCms::data();

        return view('superadmin.cms.index', compact('cms'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'seo_meta_title' => 'nullable|string|max:70',
            'seo_meta_description' => 'nullable|string|max:320',
            'seo_tentang_description' => 'nullable|string|max:320',
            'seo_fasilitas_description' => 'nullable|string|max:320',
            'seo_galeri_description' => 'nullable|string|max:320',
            'seo_kontak_description' => 'nullable|string|max:320',
        ]);

        foreach ($this->textKeys as $key) {
            CmsContent::set($key, $request->input($key), null);
        }

        foreach ($this->photoKeys as $key) {
            if ($request->hasFile($key)) {
                $request->validate([$key => 'image|max:3072']);
                $old = CmsContent::get($key, '');
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
                $path = $this->uploadImage($request->file($key), 'cms');
                CmsContent::set($key, $path, null);
            }
        }

        return back()->with('success', 'Konten website berhasil diperbarui! 🎉');
    }
}
