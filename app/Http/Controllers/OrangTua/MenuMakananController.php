<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;

class MenuMakananController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sekolah_id = $user->sekolah_id;
        $menus = MenuMakanan::where('sekolah_id', $sekolah_id)
            ->withCount(['votes as likes_count' => fn($q) => $q->where('vote_type', 'like')])
            ->withCount(['votes as dislikes_count' => fn($q) => $q->where('vote_type', 'dislike')])
            ->with(['votes' => fn($q) => $q->where('user_id', $user->id)])
            ->orderBy('date', 'desc')
            ->paginate(10);
            
        return view('orangtua.menu_makanan.index', compact('menus'));
    }
}
