<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;

class MenuMakananController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $menus = MenuMakanan::where('sekolah_id', $sekolah_id)
            ->orderBy('date', 'desc')
            ->paginate(10);
            
        return view('orangtua.menu_makanan.index', compact('menus'));
    }
}
