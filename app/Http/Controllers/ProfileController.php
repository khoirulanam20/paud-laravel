<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Anak;
use App\Models\Lembaga;
use App\Models\Pengajar;
use App\Models\Sekolah;
use App\Support\PendidikanTerakhir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
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
            $lembaga = Lembaga::find($user->lembaga_id);
        }
        if ($user->hasRole('Admin Sekolah')) {
            $sekolah = Sekolah::find($user->sekolah_id);
        }
        if ($user->hasRole('Pengajar') || $user->hasRole('Admin Kelas')) {
            $pengajar = Pengajar::where('user_id', $user->id)->first();
        }
        if ($user->hasRole('Orang Tua')) {
            $anaks = Anak::where('user_id', $user->id)->get();
        }

        return view('profile.edit', compact('user', 'sekolah', 'lembaga', 'pengajar', 'anaks'));
    }

    public function updateSekolah(Request $request): RedirectResponse
    {
        $sekolah = Sekolah::findOrFail($request->user()->sekolah_id);
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
        $pengajar = Pengajar::where('user_id', $request->user()->id)->firstOrFail();
        $pendidikanPilihan = PendidikanTerakhir::options();
        if (filled($pengajar->pendidikan) && ! in_array($pengajar->pendidikan, $pendidikanPilihan, true)) {
            $pendidikanPilihan[] = $pengajar->pendidikan;
        }
        $request->validate([
            'nik' => 'nullable|string',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string',
            'pendidikan' => ['nullable', Rule::in($pendidikanPilihan)],
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
        Anak::where('user_id', $request->user()->id)->update($request->only('nama_bapak', 'nik_bapak', 'nama_ibu', 'nik_ibu'));

        return Redirect::route('profile.edit')->with('status', 'profile-orangtua-updated');
    }

    public function updateAnak(Request $request, Anak $anak): RedirectResponse
    {
        abort_if($anak->user_id !== $request->user()->id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'nik' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'dob', 'nik', 'jenis_kelamin', 'alamat');

        if ($request->hasFile('photo')) {
            if ($anak->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($anak->photo);
            }
            $data['photo'] = $request->file('photo')->store('anak', 'public');
        }

        $anak->update($data);

        return Redirect::route('profile.edit')->with('status', 'profile-anak-updated');
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
