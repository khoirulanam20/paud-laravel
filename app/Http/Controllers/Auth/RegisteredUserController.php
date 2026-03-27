<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $sekolahs = Sekolah::orderBy('name')->get();

        return view('auth.register', compact('sekolahs'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'anak_name' => ['required', 'string', 'max:255'],
            'anak_dob' => ['required', 'date', 'before:today'],
            'catatan_ortu' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'sekolah_id' => $request->sekolah_id,
            ]);

            // Production sering lupa seed role → assignRole melempar exception
            Role::firstOrCreate(
                ['name' => 'Orang Tua', 'guard_name' => 'web']
            );
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $user->assignRole('Orang Tua');

            event(new Registered($user));

            $anakData = [
                'user_id' => $user->id,
                'sekolah_id' => $request->sekolah_id,
                'name' => $request->anak_name,
                'dob' => $request->anak_dob,
                'parent_name' => $request->name,
            ];

            if (Schema::hasColumn('anaks', 'status')) {
                $anakData['status'] = 'pending';
            }
            if (Schema::hasColumn('anaks', 'catatan_ortu')) {
                $anakData['catatan_ortu'] = $request->catatan_ortu;
            }

            Anak::create($anakData);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Pendaftaran gagal diproses. Pastikan server sudah menjalankan migrasi (php artisan migrate --force) dan cache dibersihkan (php artisan optimize:clear). Hubungi admin jika masalah berlanjut.',
                ]);
        }

        return redirect()->route('login')->with(
            'status',
            'Pendaftaran berhasil! 🎉 Akun Anda sedang menunggu persetujuan Admin Sekolah. Anda akan bisa login setelah disetujui.'
        );
    }
}
