<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class KelasController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        // Load the kelas and the users who act as Admin Kelas for it
        $kelasList = Kelas::with(['users' => function($query) {
            $query->role('Admin Kelas');
        }])->withCount('anaks')->where('sekolah_id', $sekolah_id)->latest()->paginate(10);
        
        $pengajars = User::role('Pengajar')->where('sekolah_id', $sekolah_id)->get();
        return view('admin.kelas.index', compact('kelasList', 'pengajars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $sekolah_id = auth()->user()->sekolah_id;

        $newKelas = Kelas::create([
            'sekolah_id' => $sekolah_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->admin_id) {
            $admin = User::find($request->admin_id);
            if ($admin && $admin->sekolah_id === $sekolah_id) {
                $admin->kelas_id = $newKelas->id;
                $admin->assignRole('Admin Kelas');
                $admin->save();
            }
        }

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        abort_if($kelas->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $kelas->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $oldAdminId = $kelas->users()->first()->id ?? null;
        if ((string)$oldAdminId !== (string)$request->admin_id) {
            // Remove old admin
            if ($oldAdminId) {
                $oldAdmin = User::find($oldAdminId);
                $oldAdmin->kelas_id = null;
                $oldAdmin->removeRole('Admin Kelas');
                $oldAdmin->save();
            }
            // Assign new admin
            if ($request->admin_id) {
                $newAdmin = User::find($request->admin_id);
                if ($newAdmin && $newAdmin->sekolah_id === auth()->user()->sekolah_id) {
                    $newAdmin->kelas_id = $kelas->id;
                    $newAdmin->assignRole('Admin Kelas');
                    $newAdmin->save();
                }
            }
        }

        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        abort_if($kelas->sekolah_id !== auth()->user()->sekolah_id, 403);
        
        $kelas->delete();
        
        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil dihapus.');
    }
}
