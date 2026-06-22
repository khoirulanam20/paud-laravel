<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected array $defaultRoles = ['Lembaga', 'Admin Sekolah', 'Wali Kelas', 'Pengajar', 'Orang Tua'];

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
            ['name' => 'menu.data-pengajar', 'label' => 'Data Pengajar'],
            ['name' => 'menu.presensi-guru', 'label' => 'Presensi Guru'],
            ['name' => 'menu.sarana', 'label' => 'Sarana'],
            ['name' => 'menu.menu-makanan', 'label' => 'Menu Makanan'],
        ],
        'Akuntansi' => [
            ['name' => 'menu.akun-coa', 'label' => 'Akun (COA)'],
            ['name' => 'menu.cashflow', 'label' => 'Cashflow'],
            ['name' => 'menu.jurnal-umum', 'label' => 'Jurnal Umum'],
            ['name' => 'menu.setting-akuntansi', 'label' => 'Setting Akuntansi'],
        ],
        'Biaya & Pembayaran' => [
            ['name' => 'menu.biaya-harian', 'label' => 'Biaya Bulanan'],
            ['name' => 'menu.diskon', 'label' => 'Diskon'],
            ['name' => 'menu.rekap-pembayaran', 'label' => 'Rekap Pembayaran'],
        ],
        'Laporan Keuangan' => [
            ['name' => 'menu.arus-kas', 'label' => 'Arus Kas (PSAK 2)'],
            ['name' => 'menu.neraca', 'label' => 'Neraca'],
            ['name' => 'menu.laba-rugi', 'label' => 'Laba Rugi'],
        ],
        'Masukan & Komunikasi' => [
            ['name' => 'menu.kritik-saran', 'label' => 'Kritik & Saran'],
            ['name' => 'menu.chat-orangtua', 'label' => 'Chat Orang Tua'],
            ['name' => 'menu.pengaturan-ai', 'label' => 'Pengaturan AI'],
        ],
        'Manajemen Akses' => [
            ['name' => 'menu.role', 'label' => 'Role'],
            ['name' => 'menu.pengguna', 'label' => 'Pengguna'],
        ],
    ];

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissionGroups = $this->permissionGroups;
        return view('admin.role.index', compact('roles', 'permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . Role::class . ',name'],
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role "' . e($request->name) . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, $this->defaultRoles)) {
            return back()->withErrors(['role' => 'Role default tidak dapat diubah.']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . Role::class . ',name,' . $role->id],
        ]);

        $role->update(['name' => $request->name]);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:' . Permission::class . ',name'],
        ]);

        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.role.index')
            ->with('success', 'Akses menu untuk role "' . e($role->name) . '" berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, $this->defaultRoles)) {
            return back()->withErrors(['role' => 'Role default tidak dapat dihapus.']);
        }

        $role->delete();

        return redirect()->route('admin.role.index')
            ->with('success', 'Role "' . e($role->name) . '" berhasil dihapus.');
    }
}
