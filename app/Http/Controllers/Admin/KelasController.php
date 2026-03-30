<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class KelasController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        abort_if($sekolah_id === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $this->ensureKelasPageRolesExist();

        // User dengan kelas_id = kelas ini seharusnya wali/admin kelas (Sudah dihapus fiturnya)
        $kelasList = Kelas::query()
            ->where('sekolah_id', $sekolah_id)
            ->withCount('anaks')
            ->latest()
            ->paginate(10);

        return view('admin.kelas.index', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        abort_if($sekolah_id === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $newKelas = Kelas::create([
            'sekolah_id' => $sekolah_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        abort_if($sekolah_id === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $kelas = Kelas::findOrFail($id);
        abort_if((int) $kelas->sekolah_id !== (int) $sekolah_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $kelas->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        abort_if($sekolah_id === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $kelas = Kelas::findOrFail($id);
        abort_if((int) $kelas->sekolah_id !== (int) $sekolah_id, 403);

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil dihapus.');
    }

    private function ensureAdminKelasRoleExists(): void
    {
        Role::firstOrCreate(
            ['name' => 'Admin Kelas', 'guard_name' => 'web'],
        );
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** Pastikan peran yang dipakai halaman kelas ada (deploy tanpa seeder). */
    private function ensureKelasPageRolesExist(): void
    {
        foreach (['Pengajar', 'Admin Kelas'] as $name) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
            );
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
