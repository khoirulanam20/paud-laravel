<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index()
    {
        $sekolahId = auth()->user()->sekolah_id;

        $pending  = Anak::with('user')->where('sekolah_id', $sekolahId)->where('status', 'pending')->latest()->get();
        $approved = Anak::with('user')->where('sekolah_id', $sekolahId)->where('status', 'approved')->latest()->paginate(10, ['*'], 'approved_page');
        $rejected = Anak::with('user')->where('sekolah_id', $sekolahId)->where('status', 'rejected')->latest()->paginate(10, ['*'], 'rejected_page');

        return view('admin.pendaftaran.index', compact('pending', 'approved', 'rejected'));
    }

    public function approve(Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $anak->update(['status' => 'approved']);

        // Also update user's sekolah_id for role-based scoping
        $anak->user->update(['sekolah_id' => $anak->sekolah_id]);

        // Assign Orang Tua role if not already
        if (!$anak->user->hasRole('Orang Tua')) {
            $anak->user->assignRole('Orang Tua');
        }

        return back()->with('success', "Pendaftaran {$anak->name} telah disetujui! ✅");
    }

    public function reject(Request $request, Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate(['catatan_admin' => 'nullable|string|max:500']);

        $anak->update([
            'status'        => 'rejected',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', "Pendaftaran {$anak->name} telah ditolak.");
    }
}
