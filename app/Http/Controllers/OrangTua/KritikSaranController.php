<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;
use Illuminate\Http\Request;

class KritikSaranController extends Controller
{
    public function index()
    {
        $feedbacks = KritikSaran::query()
            ->where('user_id', auth()->id())
            ->with('sekolah')
            ->latest()
            ->paginate(15);

        return view('orangtua.kritik_saran.index', compact('feedbacks'));
    }

    public function show(KritikSaran $kritik_saran)
    {
        abort_if((int) $kritik_saran->user_id !== (int) auth()->id(), 403);

        $kritik_saran->load('sekolah');

        return view('orangtua.kritik_saran.show', compact('kritik_saran'));
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
