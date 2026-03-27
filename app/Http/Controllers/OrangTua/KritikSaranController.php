<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;
use Illuminate\Http\Request;

class KritikSaranController extends Controller
{
    public function index()
    {
        $feedbacks = KritikSaran::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('orangtua.kritik_saran.index', compact('feedbacks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ]);

        KritikSaran::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'Terkirim',
        ]);

        return redirect()->route('orangtua.kritik-saran.index')->with('success', 'Kritik atau saran Anda berhasil dikirimkan ke pihak sekolah/yayasan.');
    }
}
