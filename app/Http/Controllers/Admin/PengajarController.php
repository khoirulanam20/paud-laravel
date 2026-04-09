<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengajar;
use App\Models\User;
use App\Support\PendidikanTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\CanUploadImage;
use Illuminate\Validation\Rule;

class PengajarController extends Controller
{
    use CanUploadImage;
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $pengajars = Pengajar::where('sekolah_id', $sekolah_id)->with(['user', 'kelas'])->latest()->paginate(10);
        $kelas = Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();
        $pendidikanOptions = PendidikanTerakhir::options();
        foreach ($pengajars as $p) {
            if (filled($p->pendidikan) && ! in_array($p->pendidikan, $pendidikanOptions, true)) {
                $pendidikanOptions[] = $p->pendidikan;
            }
        }
        $pendidikanOptions = array_values(array_unique($pendidikanOptions));

        return view('admin.pengajar.index', compact('pengajars', 'kelas', 'pendidikanOptions'));
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
            'pendidikan' => ['nullable', Rule::in(PendidikanTerakhir::options())],
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id',
        ]);

        $sekolah_id = auth()->user()->sekolah_id;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'sekolah_id' => $sekolah_id,
        ]);
        $user->assignRole('Pengajar');

        $data = [
            'user_id' => $user->id,
            'sekolah_id' => $sekolah_id,
            'name' => $request->name,
            'jabatan' => $request->jabatan,
            'nik' => $request->nik,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'pendidikan' => $request->pendidikan,
            'jenis_kelamin' => $request->jenis_kelamin,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadImage($request->file('photo'), 'pengajar');
        }

        $pengajar = Pengajar::create($data);

        if ($request->has('kelas_id')) {
            $pengajar->kelas()->sync($request->kelas_id);
        }

        return redirect()->route('admin.pengajar.index')->with('success', 'Data Pengajar berhasil ditambahkan. Password default: password123');
    }

    public function update(Request $request, Pengajar $pengajar)
    {
        abort_if($pengajar->sekolah_id !== auth()->user()->sekolah_id, 403);

        $pendidikanPilihan = PendidikanTerakhir::options();
        if (filled($pengajar->pendidikan) && ! in_array($pengajar->pendidikan, $pendidikanPilihan, true)) {
            $pendidikanPilihan[] = $pengajar->pendidikan;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'pendidikan' => ['nullable', Rule::in($pendidikanPilihan)],
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id',
        ]);

        $dataArr = [
            'name' => $request->name,
            'jabatan' => $request->jabatan,
            'nik' => $request->nik,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'pendidikan' => $request->pendidikan,
            'jenis_kelamin' => $request->jenis_kelamin,
        ];

        if ($request->hasFile('photo')) {
            if ($pengajar->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengajar->photo);
            }
            $dataArr['photo'] = $this->uploadImage($request->file('photo'), 'pengajar');
        }

        $pengajar->update($dataArr);

        $user = $pengajar->user;
        $user->update([
            'name' => $request->name,
        ]);

        $pengajar->kelas()->sync($request->kelas_id ?? []);

        return redirect()->route('admin.pengajar.index')->with('success', 'Data Pengajar berhasil diperbarui.');
    }

    public function destroy(Pengajar $pengajar)
    {
        abort_if($pengajar->sekolah_id !== auth()->user()->sekolah_id, 403);
        
        $user = $pengajar->user;
        if ($pengajar->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengajar->photo);
        }
        $pengajar->delete();
        
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.pengajar.index')->with('success', 'Data Pengajar dan akun login berhasil dihapus.');
    }
}
