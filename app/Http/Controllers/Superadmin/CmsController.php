<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Traits\CanUploadImage;
use App\Models\CmsContent;
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
    ];

    private array $photoKeys = [
        'hero_photo', 'about_photo',
        'gallery_1', 'gallery_2', 'gallery_3',
        'gallery_4', 'gallery_5', 'gallery_6',
        'seo_og_image',
    ];

    public function index()
    {
        $cms = [];
        foreach (array_merge($this->textKeys, $this->photoKeys) as $key) {
            $cms[$key] = CmsContent::get($key, '');
        }

        return view('superadmin.cms.index', compact('cms'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'seo_meta_title' => 'nullable|string|max:70',
            'seo_meta_description' => 'nullable|string|max:320',
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
