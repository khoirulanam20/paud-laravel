<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Support\PaginationPerPage;

class PenggunaController extends Controller
{
    private const HIDDEN_ROLES = ['Superadmin', 'Lembaga'];
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $penggunas = User::where('sekolah_id', $sekolahId)
            ->with('roles')
            ->latest()
            ->paginate(PaginationPerPage::resolve($request))->withQueryString();
        $roles = Role::whereNotIn('name', self::HIDDEN_ROLES)->get();

        return view('admin.pengguna.index', compact('penggunas', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:' . Role::class . ',name', Rule::notIn(self::HIDDEN_ROLES)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sekolah_id' => auth()->user()->sekolah_id,
        ]);

        $user->assignRole($request->role);

        ActivityLogger::log('Role ditetapkan ke pengguna', $user, ['role' => $request->role]);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna "' . e($request->name) . '" berhasil ditambahkan.');
    }

    public function update(Request $request, User $pengguna)
    {
        abort_if($pengguna->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $pengguna->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:' . Role::class . ',name', Rule::notIn(self::HIDDEN_ROLES)],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        // Sync role: hapus semua role lama, assign role baru
        $pengguna->syncRoles([$request->role]);

        ActivityLogger::log('Role pengguna diperbarui', $pengguna, ['role' => $request->role]);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data pengguna "' . e($request->name) . '" berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        abort_if($pengguna->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($pengguna->id === auth()->id()) {
            return back()->withErrors(['pengguna' => 'Tidak dapat menghapus akun sendiri.']);
        }

        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna "' . e($pengguna->name) . '" berhasil dihapus.');
    }
}
