<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;
use Illuminate\Http\Request;

class KritikSaranController extends Controller
{
    public function index()
    {
        $sekolahId = auth()->user()->sekolah_id;

        $feedbacks = KritikSaran::query()
            ->where('sekolah_id', $sekolahId)
            ->with(['user.anaks.kelas', 'sekolah'])
            ->latest()
            ->paginate(15);

        return view('admin.kritik_saran.index', compact('feedbacks'));
    }

    public function show(KritikSaran $kritik_saran)
    {
        abort_if($kritik_saran->sekolah_id !== auth()->user()->sekolah_id, 404);

        $kritik_saran->load(['user.anaks.kelas', 'sekolah']);

        return view('admin.kritik_saran.show', compact('kritik_saran'));
    }

    public function update(Request $request, KritikSaran $kritik_saran)
    {
        abort_if($kritik_saran->sekolah_id !== auth()->user()->sekolah_id, 404);

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'umpan_balik' => ['nullable', 'string', 'max:5000'],
        ]);

        $kritik_saran->update($validated);

        return redirect()
            ->route('admin.kritik-saran.show', $kritik_saran)
            ->with('success', 'Status dan tanggapan berhasil disimpan.');
    }
}
