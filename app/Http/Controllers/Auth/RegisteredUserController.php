<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'sekolah_id'    => ['required', 'exists:sekolahs,id'],
            'anak_name'     => ['required', 'string', 'max:255'],
            'anak_dob'      => ['required', 'date', 'before:today'],
            'catatan_ortu'  => ['nullable', 'string', 'max:1000'],
        ]);

        // Create user WITHOUT logging them in yet — they must wait for approval
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'sekolah_id' => $request->sekolah_id,
        ]);

        // Assign role Orang Tua (pending — not yet approved but role set)
        $user->assignRole('Orang Tua');

        event(new Registered($user));

        // Create child record with pending status
        Anak::create([
            'user_id'      => $user->id,
            'sekolah_id'   => $request->sekolah_id,
            'name'         => $request->anak_name,
            'dob'          => $request->anak_dob,
            'parent_name'  => $request->name,
            'status'       => 'pending',
            'catatan_ortu' => $request->catatan_ortu,
        ]);

        // Do NOT auto-login. Redirect to login with success message.
        return redirect()->route('login')->with('status',
            'Pendaftaran berhasil! 🎉 Akun Anda sedang menunggu persetujuan Admin Sekolah. Anda akan bisa login setelah disetujui.'
        );
    }
}
