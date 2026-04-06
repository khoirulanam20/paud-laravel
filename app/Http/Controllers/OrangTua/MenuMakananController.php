<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;

class MenuMakananController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;
        
        $startDate = $request->input('start_date', now()->startOfWeek(\Carbon\CarbonInterface::MONDAY)->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek(\Carbon\CarbonInterface::SUNDAY)->toDateString());

        $menus = MenuMakanan::where('sekolah_id', $sekolah_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->withCount(['votes as likes_count' => fn($q) => $q->where('vote_type', 'like')])
            ->withCount(['votes as dislikes_count' => fn($q) => $q->where('vote_type', 'dislike')])
            ->with(['votes' => fn($q) => $q->where('user_id', $user->id)])
            ->orderBy('date', 'desc')
            ->get();
            
        return view('orangtua.menu_makanan.index', compact('menus', 'startDate', 'endDate'));
    }
}
