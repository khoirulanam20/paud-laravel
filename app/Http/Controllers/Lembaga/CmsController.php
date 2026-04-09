<?php

namespace App\Http\Controllers\Lembaga;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\CanUploadImage;

class CmsController extends Controller
{
    use CanUploadImage;
    private array $textKeys = [
        'hero_title', 'hero_subtitle',
        'about_title', 'about_text',
        'facility_1_title', 'facility_1_desc', 'facility_1_icon',
        'facility_2_title', 'facility_2_desc', 'facility_2_icon',
        'facility_3_title', 'facility_3_desc', 'facility_3_icon',
        'facility_4_title', 'facility_4_desc', 'facility_4_icon',
        'kontak_alamat', 'kontak_telepon', 'kontak_email', 'kontak_jam',
        'footer_text',
    ];

    private array $photoKeys = [
        'hero_photo', 'about_photo',
        'gallery_1', 'gallery_2', 'gallery_3',
        'gallery_4', 'gallery_5', 'gallery_6',
    ];

    public function index()
    {
        $cms = [];
        foreach (array_merge($this->textKeys, $this->photoKeys) as $key) {
            $cms[$key] = CmsContent::get($key, '');
        }
        return view('lembaga.cms.index', compact('cms'));
    }

    public function update(Request $request)
    {
        // Validate text fields
        foreach ($this->textKeys as $key) {
            CmsContent::set($key, $request->input($key), null);
        }

        // Handle photo uploads
        foreach ($this->photoKeys as $key) {
            if ($request->hasFile($key)) {
                $request->validate([$key => 'image|max:3072']);
                $old = CmsContent::get($key, '');
                if ($old) Storage::disk('public')->delete($old);
                $path = $this->uploadImage($request->file($key), 'cms');
                CmsContent::set($key, $path, null);
            }
        }

        return back()->with('success', 'Konten website berhasil diperbarui! 🎉');
    }
}
