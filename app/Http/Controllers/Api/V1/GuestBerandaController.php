<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Support\ApiStorageUrl;
use App\Support\GuestCms;
use Illuminate\Http\JsonResponse;

class GuestBerandaController extends Controller
{
    public function show(): JsonResponse
    {
        $cms = GuestCms::data();
        $mediaKeys = ['hero_photo', 'about_photo', 'gallery_1', 'gallery_2', 'gallery_3', 'gallery_4', 'gallery_5', 'gallery_6'];
        foreach ($mediaKeys as $key) {
            $cms[$key.'_url'] = ApiStorageUrl::optional($cms[$key] ?? '');
        }

        $sekolahs = Sekolah::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Sekolah $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'address' => $s->address,
                'phone' => $s->phone,
                'photo_url' => ApiStorageUrl::optional($s->photo),
            ]);

        return response()->json([
            'cms' => $cms,
            'sekolahs' => $sekolahs,
        ]);
    }
}
