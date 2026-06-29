<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    protected array $defaultRoles = ['Superadmin', 'Lembaga', 'Admin Sekolah', 'Wali Kelas', 'Pengajar', 'Orang Tua'];

    protected array $hiddenRoles = ['Superadmin', 'Lembaga'];

    protected array $permissionGroups = [
        'Manajemen Siswa' => [
            ['name' => 'menu.siswa', 'label' => 'Siswa Sekolah'],
            ['name' => 'menu.pendaftaran', 'label' => 'Pendaftaran'],
            ['name' => 'menu.kelola-kelas', 'label' => 'Kelola Kelas'],
            ['name' => 'menu.presensi-siswa', 'label' => 'Presensi Siswa'],
            ['name' => 'menu.kesehatan-siswa', 'label' => 'Kesehatan Siswa'],
        ],
        'Kurikulum' => [
            ['name' => 'menu.matrikulasi', 'label' => 'Matrikulasi'],
            ['name' => 'menu.skala-capaian', 'label' => 'Skala Capaian'],
            ['name' => 'menu.agenda-belajar', 'label' => 'Agenda Belajar'],
            ['name' => 'menu.kegiatan-rutin', 'label' => 'Kegiatan Rutin'],
            ['name' => 'menu.pencapaian-siswa', 'label' => 'Pencapaian Siswa'],
            ['name' => 'menu.monev', 'label' => 'Monev'],
        ],
        'Lembaga & Guru' => [
            ['name' => 'menu.data-pengajar', 'label' => 'Data Guru'],
            ['name' => 'menu.presensi-guru', 'label' => 'Presensi Guru'],
            ['name' => 'menu.sarana', 'label' => 'Sarana'],
            ['name' => 'menu.menu-makanan', 'label' => 'Menu Makanan'],
        ],
        'Akuntansi' => [
            ['name' => 'menu.akun-coa', 'label' => 'Kode Rekening & Akun'],
            ['name' => 'menu.cashflow', 'label' => 'Cashflow'],
            ['name' => 'menu.jurnal-umum', 'label' => 'Jurnal Umum'],
        ],
        'RKAS' => [
            ['name' => 'menu.sumber-dana', 'label' => 'Sumber Dana'],
            ['name' => 'menu.rkas', 'label' => 'RKAS'],
            ['name' => 'menu.laporan-rkas', 'label' => 'Laporan RKAS'],
        ],
        'Biaya & Pembayaran' => [
            ['name' => 'menu.biaya-harian', 'label' => 'Biaya Bulanan'],
            ['name' => 'menu.diskon', 'label' => 'Diskon'],
            ['name' => 'menu.rekap-pembayaran', 'label' => 'Rekap Pembayaran'],
        ],
        'Masukan & Komunikasi' => [
            ['name' => 'menu.kritik-saran', 'label' => 'Kritik & Saran'],
            ['name' => 'menu.chat-orangtua', 'label' => 'Chat Orang Tua'],
        ],
        'Pengaturan' => [
            ['name' => 'menu.role', 'label' => 'Role'],
            ['name' => 'menu.pengguna', 'label' => 'Pengguna'],
            ['name' => 'menu.log-aktivitas', 'label' => 'Log Aktivitas'],
        ],
        'Pengaturan Akuntansi' => [
            ['name' => 'menu.setting-akuntansi', 'label' => 'Setting Akuntansi'],
        ],
        'Pengaturan AI' => [
            ['name' => 'menu.pengaturan-ai', 'label' => 'Pengaturan AI'],
        ],
    ];

    public function index()
    {
        $roles = Role::with('permissions')->whereNotIn('name', $this->hiddenRoles)->get();
        $permissionGroups = $this->permissionGroups;

        return view('admin.role.index', compact('roles', 'permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.Role::class.',name'],
        ]);

        $role = Role::create(['name' => $request->name]);

        ActivityLogger::log('Role dibuat', null, ['role' => $role->name]);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role "'.e($request->name).'" berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, $this->defaultRoles)) {
            return back()->withErrors(['role' => 'Role default tidak dapat diubah.']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.Role::class.',name,'.$role->id],
        ]);

        $role->update(['name' => $request->name]);

        ActivityLogger::log('Role diperbarui', null, ['role' => $role->name]);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:'.Permission::class.',name'],
        ]);

        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        ActivityLogger::log('Permission role diperbarui', null, [
            'role' => $role->name,
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.role.index')
            ->with('success', 'Akses menu untuk role "'.e($role->name).'" berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, $this->defaultRoles)) {
            return back()->withErrors(['role' => 'Role default tidak dapat dihapus.']);
        }

        $roleName = $role->name;
        $role->delete();

        ActivityLogger::log('Role dihapus', null, ['role' => $roleName]);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role "'.e($role->name).'" berhasil dihapus.');
    }
}
