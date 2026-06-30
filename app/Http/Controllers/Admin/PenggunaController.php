<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengajar;
use App\Models\User;
use App\Support\ActivityLogger;
use App\Support\PaginationPerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class PenggunaController extends Controller
{
    private const HIDDEN_ROLES = ['Superadmin', 'Lembaga'];

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $penggunas = User::where('sekolah_id', $sekolahId)
            ->with(['roles', 'kelas'])
            ->latest()
            ->paginate(PaginationPerPage::resolve($request))->withQueryString();
        $roles = Role::whereNotIn('name', self::HIDDEN_ROLES)->get();
        $kelas = Kelas::where('sekolah_id', $sekolahId)->orderBy('name')->get();

        return view('admin.pengguna.index', compact('penggunas', 'roles', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:'.Role::class.',name', Rule::notIn(self::HIDDEN_ROLES)],
            'kelas_id' => [
                Rule::requiredIf(fn () => $request->input('role') === 'Wali Kelas'),
                'nullable',
                'integer',
                Rule::exists('kelas', 'id')->where(fn ($query) => $query->where('sekolah_id', auth()->user()->sekolah_id)),
            ],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sekolah_id' => auth()->user()->sekolah_id,
            'kelas_id' => $request->input('role') === 'Wali Kelas' ? $request->integer('kelas_id') : null,
        ]);

        $user->assignRole($request->role);
        $this->syncWaliKelasAssignment($user, $request->input('role') === 'Wali Kelas' ? $request->integer('kelas_id') : null);

        ActivityLogger::log('Role ditetapkan ke pengguna', $user, [
            'role' => $request->role,
            'kelas_id' => $user->kelas_id,
        ]);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna "'.e($request->name).'" berhasil ditambahkan.');
    }

    public function update(Request $request, User $pengguna)
    {
        abort_if($pengguna->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$pengguna->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:'.Role::class.',name', Rule::notIn(self::HIDDEN_ROLES)],
            'kelas_id' => [
                Rule::requiredIf(fn () => $request->input('role') === 'Wali Kelas'),
                'nullable',
                'integer',
                Rule::exists('kelas', 'id')->where(fn ($query) => $query->where('sekolah_id', auth()->user()->sekolah_id)),
            ],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'kelas_id' => $request->input('role') === 'Wali Kelas' ? $request->integer('kelas_id') : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        // Sync role: hapus semua role lama, assign role baru
        $pengguna->syncRoles([$request->role]);
        $this->syncWaliKelasAssignment($pengguna, $request->input('role') === 'Wali Kelas' ? $request->integer('kelas_id') : null);

        ActivityLogger::log('Role pengguna diperbarui', $pengguna, [
            'role' => $request->role,
            'kelas_id' => $pengguna->kelas_id,
        ]);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data pengguna "'.e($request->name).'" berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        abort_if($pengguna->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($pengguna->id === auth()->id()) {
            return back()->withErrors(['pengguna' => 'Tidak dapat menghapus akun sendiri.']);
        }

        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna "'.e($pengguna->name).'" berhasil dihapus.');
    }

    private function syncWaliKelasAssignment(User $user, ?int $kelasId): void
    {
        $pengajar = $user->pengajar;

        if ($kelasId === null) {
            if (! $pengajar) {
                $user->forceFill(['kelas_id' => null])->save();

                return;
            }

            Kelas::where('wali_kelas_id', $pengajar->id)->update(['wali_kelas_id' => null]);
            $user->forceFill(['kelas_id' => null])->save();

            return;
        }

        if (! $pengajar) {
            $pengajar = Pengajar::create([
                'user_id' => $user->id,
                'sekolah_id' => $user->sekolah_id,
                'name' => $user->name,
                'jabatan' => 'Wali Kelas',
            ]);
        } else {
            $pengajar->update([
                'name' => $user->name,
                'sekolah_id' => $user->sekolah_id,
                'jabatan' => filled($pengajar->jabatan) ? $pengajar->jabatan : 'Wali Kelas',
            ]);
        }

        $oldWaliPengajarId = Kelas::whereKey($kelasId)->value('wali_kelas_id');

        Kelas::where('wali_kelas_id', $pengajar->id)
            ->where('id', '!=', $kelasId)
            ->update(['wali_kelas_id' => null]);

        Kelas::whereKey($kelasId)->update(['wali_kelas_id' => $pengajar->id]);
        $pengajar->kelas()->syncWithoutDetaching([$kelasId]);
        $user->forceFill(['kelas_id' => $kelasId])->save();

        if ($oldWaliPengajarId && (int) $oldWaliPengajarId !== (int) $pengajar->id) {
            $this->removeWaliKelasRoleIfNecessary((int) $oldWaliPengajarId);
        }
    }

    private function removeWaliKelasRoleIfNecessary(int $pengajarId): void
    {
        if (Kelas::where('wali_kelas_id', $pengajarId)->exists()) {
            return;
        }

        $pengajar = Pengajar::find($pengajarId);
        if ($pengajar?->user && $pengajar->user->hasRole('Wali Kelas')) {
            $pengajar->user->removeRole('Wali Kelas');
            $pengajar->user->forceFill(['kelas_id' => null])->save();
        }
    }
}
