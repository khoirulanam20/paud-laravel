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

        // Hindari $query->role() di dalam eager load (rawan error SQL di beberapa server).
        // User dengan kelas_id = kelas ini seharusnya wali/admin kelas.
        $kelasList = Kelas::query()
            ->where('sekolah_id', $sekolah_id)
            ->with(['users' => fn ($q) => $q->orderBy('name')])
            ->withCount('anaks')
            ->latest()
            ->paginate(10);

        $pengajars = User::query()
            ->where('sekolah_id', $sekolah_id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'Pengajar')->where('guard_name', 'web'))
            ->orderBy('name')
            ->get();

        return view('admin.kelas.index', compact('kelasList', 'pengajars'));
    }

    public function store(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        abort_if($sekolah_id === null, 403, 'Akun tidak terikat sekolah. Hubungi lembaga.');

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $newKelas = Kelas::create([
            'sekolah_id' => $sekolah_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->filled('admin_id')) {
            $admin = User::find($request->admin_id);
            if ($admin && (int) $admin->sekolah_id === (int) $sekolah_id) {
                $this->ensureAdminKelasRoleExists();
                $admin->kelas_id = $newKelas->id;
                $admin->assignRole('Admin Kelas');
                $admin->save();
            }
        }

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
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $kelas->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $oldAdmin = User::role('Admin Kelas')->where('kelas_id', $kelas->id)->first();
        $oldAdminId = $oldAdmin?->id;
        $newAdminId = $request->filled('admin_id') ? (int) $request->admin_id : null;

        if ($oldAdminId !== $newAdminId) {
            if ($oldAdmin) {
                $oldAdmin->kelas_id = null;
                $oldAdmin->removeRole('Admin Kelas');
                $oldAdmin->save();
            }
            if ($newAdminId) {
                $newAdmin = User::find($newAdminId);
                if ($newAdmin && (int) $newAdmin->sekolah_id === (int) $sekolah_id) {
                    $this->ensureAdminKelasRoleExists();
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
