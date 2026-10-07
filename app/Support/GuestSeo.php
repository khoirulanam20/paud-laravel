<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class GuestSeo
{
    /**
     * @param  array<string, string>  $cms
     * @return array{title: string, description: string, og_image_url: string|null, canonical: string}
     */
    public static function forHome(array $cms): array
    {
        $title = trim($cms['seo_meta_title'] ?? '') ?: trim($cms['hero_title'] ?? GuestBrand::NAME);
        $description = trim($cms['seo_meta_description'] ?? '') ?: trim($cms['hero_subtitle'] ?? '');
        $ogPath = trim($cms['seo_og_image'] ?? '') ?: trim($cms['hero_photo'] ?? '');
        $ogImageUrl = $ogPath ? url(Storage::url($ogPath)) : GuestAscent::asset('images/banner/banner-v2-thumb.png');

        return [
            'title' => $title,
            'description' => $description,
            'og_image_url' => $ogImageUrl,
            'canonical' => url('/'),
        ];
    }

    /**
     * @param  array<string, string>  $cms
     */
    public static function galleryAlt(array $cms, int $index): string
    {
        $key = 'gallery_'.$index.'_alt';
        $alt = trim($cms[$key] ?? '');
        if ($alt !== '') {
            return $alt;
        }

        return 'Galeri '.GuestBrand::NAME.' '.$index;
    }

    public static function heroPhotoAlt(array $cms): string
    {
        $alt = trim($cms['hero_photo_alt'] ?? '');
        if ($alt !== '') {
            return $alt;
        }

        return 'Ilustrasi platform PAUD '.GuestBrand::NAME;
    }

    public static function aboutPhotoAlt(array $cms): string
    {
        $alt = trim($cms['about_photo_alt'] ?? '');
        if ($alt !== '') {
            return $alt;
        }

        return 'Tentang platform PAUD '.GuestBrand::NAME;
    }

    /**
     * @return array{title: string, description: string, og_image_url: string|null, canonical: string}
     */
    public static function forPage(string $title, string $description, string $canonical, ?string $ogImageUrl = null): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'og_image_url' => $ogImageUrl,
            'canonical' => $canonical,
        ];
    }
}
