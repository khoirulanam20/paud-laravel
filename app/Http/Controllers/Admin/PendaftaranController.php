<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;
use App\Support\PaginationPerPage;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;

        $pending  = Anak::with(['user.anaks'])->where('sekolah_id', $sekolahId)->where('status', 'pending')->latest()->get();
        $approved = Anak::with('user')->where('sekolah_id', $sekolahId)->where('status', 'approved')->latest()->paginate(PaginationPerPage::resolve($request, 'approved_per_page'), ['*'], 'approved_page')->withQueryString();
        $rejected = Anak::with('user')->where('sekolah_id', $sekolahId)->where('status', 'rejected')->latest()->paginate(PaginationPerPage::resolve($request, 'rejected_per_page'), ['*'], 'rejected_page')->withQueryString();

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
