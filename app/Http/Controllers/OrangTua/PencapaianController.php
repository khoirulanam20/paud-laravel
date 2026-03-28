<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Pencapaian;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PencapaianController extends Controller
{
    public function index(Request $request)
    {
        $anakIds = Anak::where('user_id', auth()->id())->pluck('id');

        $all = Pencapaian::query()
            ->with(['anak', 'kegiatan', 'matrikulasi', 'pengajar'])
            ->whereIn('anak_id', $anakIds)
            ->orderByDesc('updated_at')
            ->get();

        $groups = $all->groupBy(fn ($p) => $p->anak_id.'_'.$p->kegiatan_id);
        $keys = $groups->keys()->sortByDesc(function ($k) use ($groups) {
            return $groups[$k]->max('updated_at');
        })->values();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $total = $keys->count();
        $sliceKeys = $keys->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $pageItems = $sliceKeys->mapWithKeys(fn ($k) => [$k => $groups[$k]]);

        $groupedPencapaian = new LengthAwarePaginator(
            $pageItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'page',
            ]
        );

        return view('orangtua.pencapaian.index', compact('groupedPencapaian'));
    }
}
