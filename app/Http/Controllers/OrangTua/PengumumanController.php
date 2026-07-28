<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use Illuminate\Http\JsonResponse;

class PengumumanController extends Controller
{
    public function markAsRead(Pengumuman $pengumuman): JsonResponse
    {
        abort_if($pengumuman->sekolah_id !== auth()->user()->sekolah_id, 403);

        PengumumanRead::firstOrCreate(
            [
                'pengumuman_id' => $pengumuman->id,
                'user_id' => auth()->id(),
            ],
            ['read_at' => now()]
        );

        return response()->json(['ok' => true]);
    }
}
