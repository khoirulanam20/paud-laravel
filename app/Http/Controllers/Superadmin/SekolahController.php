<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use App\Models\Sekolah;
use App\Support\PaginationPerPage;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    public function index(Request $request)
    {
        $query = Sekolah::with('lembaga')->latest();

        if ($lembagaId = $request->integer('lembaga_id')) {
            $query->where('lembaga_id', $lembagaId);
        }

        $sekolahs = $query->paginate(PaginationPerPage::resolve($request))->withQueryString();
        $lembagas = Lembaga::orderBy('name')->get();

        return view('superadmin.sekolah.index', compact('sekolahs', 'lembagas'));
    }
}
