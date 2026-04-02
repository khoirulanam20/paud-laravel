<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\User;
use App\Support\PresensiPeriodeFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnakController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $presensiFilter = PresensiPeriodeFilter::resolve($request);

        $anaks = Anak::where('sekolah_id', $sekolah_id)
            ->where('status', 'approved')
            ->with(['user', 'kelas'])
            ->latest()
            ->paginate(10);

        $hadirPeriode = Presensi::query()
            ->where('sekolah_id', $sekolah_id)
            ->whereBetween('tanggal', [$presensiFilter['from'], $presensiFilter['to']])
            ->where('hadir', true)
            ->selectRaw('anak_id, count(*) as total')
            ->groupBy('anak_id')
            ->pluck('total', 'anak_id');

        $kelas = Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        return view('admin.anak.index', compact('anaks', 'kelas', 'hadirPeriode', 'presensiFilter'));
    }

    public function show(Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $anak->load([
            'user',
            'kelas',
            'kesehatans' => fn($q) => $q->orderBy('tanggal_pemeriksaan', 'desc')
        ]);

        return view('admin.anak.show', compact('anak'));
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

        $request->validate([
            'parent_email' => 'unique:users,email',
        ]);

        $user = User::create([
            'name' => $request->parent_name,
            'email' => $request->parent_email,
            'password' => Hash::make('password123'),
            'sekolah_id' => $sekolah_id,
        ]);
        $user->assignRole('Orang Tua');

        $data = [
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
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('anak', 'public');
        }

        Anak::create($data);

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
            'parent_name' => 'required|string|max:255',
            'parent_email' => 'required|email|max:255',
        ]);

        // If parent email changed, we need to handle it carefully
        $user = $anak->user;
        if ($user && $user->email !== $request->parent_email) {
            // Check if new email is taken by someone else
            $exists = User::where('email', $request->parent_email)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['parent_email' => 'Email wali sudah digunakan oleh pengguna lain.']);
            }
            $user->update(['email' => $request->parent_email]);
        }

        if ($user) {
            $user->update(['name' => $request->parent_name]);
        }

        $dataArr = [
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
            'parent_name' => $request->parent_name,
        ];

        if ($request->hasFile('photo')) {
            if ($anak->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($anak->photo);
            }
            $dataArr['photo'] = $request->file('photo')->store('anak', 'public');
        }

        $anak->update($dataArr);

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak berhasil diperbarui.');
    }

    public function destroy(Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);
        
        $user = $anak->user;
        if ($anak->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($anak->photo);
        }
        $anak->delete();
        
        if ($user && $user->hasRole('Orang Tua')) {
            $user->delete();
        }

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak dan akun Orang Tua berhasil dihapus.');
    }
}
