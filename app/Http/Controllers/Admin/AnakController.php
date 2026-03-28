<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AnakController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $anaks = Anak::where('sekolah_id', $sekolah_id)
            ->where('status', 'approved')
            ->with(['user', 'kelas'])
            ->latest()
            ->paginate(10);
            
        $kelas = \App\Models\Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();
        return view('admin.anak.index', compact('anaks', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'nik_bapak' => 'nullable|string|max:50',
            'nama_bapak' => 'nullable|string|max:255',
            'nik_ibu' => 'nullable|string|max:50',
            'nama_ibu' => 'nullable|string|max:255',
            'parent_name' => 'required|string|max:255',
            'parent_email' => 'required|email|max:255',
            // Default password for parents can be specified or generated
        ]);

        $sekolah_id = auth()->user()->sekolah_id;

        // Check if parent user already exists
        $user = User::where('email', $request->parent_email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $request->parent_name,
                'email' => $request->parent_email,
                'password' => Hash::make('password123'), // standard initial password
                'sekolah_id' => $sekolah_id,
            ]);
            $user->assignRole('Orang Tua');
        }

        Anak::create([
            'user_id' => $user->id,
            'sekolah_id' => $sekolah_id,
            'kelas_id' => $request->kelas_id,
            'name' => $request->name,
            'dob' => $request->dob,
            'nik' => $request->nik,
            'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nik_bapak' => $request->nik_bapak,
            'nama_bapak' => $request->nama_bapak,
            'nik_ibu' => $request->nik_ibu,
            'nama_ibu' => $request->nama_ibu,
            'parent_name' => $user->name,
            'status' => 'approved',
        ]);

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak dan Orang Tua berhasil ditambahkan. Password default Ortu: password123');
    }

    public function update(Request $request, Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'nik_bapak' => 'nullable|string|max:50',
            'nama_bapak' => 'nullable|string|max:255',
            'nik_ibu' => 'nullable|string|max:50',
            'nama_ibu' => 'nullable|string|max:255',
        ]);

        $anak->update([
            'name' => $request->name,
            'dob' => $request->dob,
            'kelas_id' => $request->kelas_id,
            'nik' => $request->nik,
            'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nik_bapak' => $request->nik_bapak,
            'nama_bapak' => $request->nama_bapak,
            'nik_ibu' => $request->nik_ibu,
            'nama_ibu' => $request->nama_ibu,
        ]);

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak berhasil diperbarui.');
    }

    public function destroy(Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);
        $anak->delete();
        return redirect()->route('admin.anak.index')->with('success', 'Data Anak berhasil dihapus.');
    }
}
