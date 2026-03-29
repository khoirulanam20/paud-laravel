<?php

namespace App\Http\Controllers\Api\V1\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnakController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sekolahId = $request->user()->sekolah_id;
        $anaks = Anak::query()
            ->where('user_id', $request->user()->id)
            ->where('sekolah_id', $sekolahId)
            ->with(['kelas:id,name'])
            ->orderBy('name')
            ->get()
            ->map(fn (Anak $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'dob' => $a->dob ? Carbon::parse($a->dob)->format('Y-m-d') : null,
                'status' => $a->status,
                'kelas' => $a->kelas ? ['id' => $a->kelas->id, 'name' => $a->kelas->name] : null,
            ]);

        return response()->json(['data' => $anaks]);
    }
}
