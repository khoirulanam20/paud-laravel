<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $sekolah = null;
        $lembaga = null;
        $pengajar = null;
        $anaks = null;

        if ($user->hasRole('Lembaga')) {
            $lembaga = \App\Models\Lembaga::find($user->lembaga_id);
        }
        if ($user->hasRole('Admin Sekolah')) {
            $sekolah = \App\Models\Sekolah::find($user->sekolah_id);
        }
        if ($user->hasRole('Pengajar') || $user->hasRole('Admin Kelas')) {
            $pengajar = \App\Models\Pengajar::where('user_id', $user->id)->first();
        }
        if ($user->hasRole('Orang Tua')) {
            $anaks = \App\Models\Anak::where('user_id', $user->id)->get();
        }

        return view('profile.edit', compact('user', 'sekolah', 'lembaga', 'pengajar', 'anaks'));
    }

    public function updateSekolah(Request $request): RedirectResponse
    {
        $sekolah = \App\Models\Sekolah::findOrFail($request->user()->sekolah_id);
        $request->validate([
            'name' => 'required|string',
            'alamat' => 'nullable|string',
            'nisn' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        $sekolah->update($request->only('name', 'alamat', 'nisn', 'phone'));
        return Redirect::route('profile.edit')->with('status', 'profile-sekolah-updated');
    }

    public function updatePengajar(Request $request): RedirectResponse
    {
        $pengajar = \App\Models\Pengajar::where('user_id', $request->user()->id)->firstOrFail();
        $request->validate([
            'nik' => 'nullable|string',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'jenis_kelamin' => 'nullable|string',
        ]);
        $pengajar->update($request->only('nik', 'alamat', 'phone', 'pendidikan', 'jenis_kelamin'));
        return Redirect::route('profile.edit')->with('status', 'profile-pengajar-updated');
    }

    public function updateOrangTua(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_bapak' => 'nullable|string',
            'nik_bapak' => 'nullable|string',
            'nama_ibu' => 'nullable|string',
            'nik_ibu' => 'nullable|string',
        ]);
        \App\Models\Anak::where('user_id', $request->user()->id)->update($request->only('nama_bapak', 'nik_bapak', 'nama_ibu', 'nik_ibu'));
        return Redirect::route('profile.edit')->with('status', 'profile-orangtua-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
