<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengajarController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $pengajars = Pengajar::where('sekolah_id', $sekolah_id)->with(['user.kelas'])->latest()->paginate(10);
        $kelas = \App\Models\Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();
        return view('admin.pengajar.index', compact('pengajars', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'jabatan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $sekolah_id = auth()->user()->sekolah_id;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'sekolah_id' => $sekolah_id,
            'kelas_id' => $request->kelas_id,
        ]);
        $user->assignRole('Pengajar');

        Pengajar::create([
            'user_id' => $user->id,
            'sekolah_id' => $sekolah_id,
            'name' => $request->name,
            'jabatan' => $request->jabatan,
            'nik' => $request->nik,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'pendidikan' => $request->pendidikan,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return redirect()->route('admin.pengajar.index')->with('success', 'Data Pengajar berhasil ditambahkan. Password default: password123');
    }

    public function update(Request $request, Pengajar $pengajar)
    {
        abort_if($pengajar->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $pengajar->update([
            'name' => $request->name,
            'jabatan' => $request->jabatan,
            'nik' => $request->nik,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'pendidikan' => $request->pendidikan,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);
        
        $user = $pengajar->user;
        $user->update([
            'name' => $request->name,
            'kelas_id' => $request->kelas_id,
        ]);

        return redirect()->route('admin.pengajar.index')->with('success', 'Data Pengajar berhasil diperbarui.');
    }

    public function destroy(Pengajar $pengajar)
    {
        abort_if($pengajar->sekolah_id !== auth()->user()->sekolah_id, 403);
        $pengajar->delete();
        return redirect()->route('admin.pengajar.index')->with('success', 'Data Pengajar berhasil dihapus.');
    }
}
