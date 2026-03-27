<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AnakController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $anaks = Anak::where('sekolah_id', $sekolah_id)->with('user')->latest()->paginate(10);
        return view('admin.anak.index', compact('anaks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'parent_name' => 'required|string|max:255',
            'parent_email' => 'required|email|max:255',
            // Default password for parents can be specified or generated
        ]);

        $sekolah_id = auth()->user()->sekolah_id;

        // Check if parent user already exists
        $user = User::where('email', $request->parent_email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $request->parent_name,
                'email' => $request->parent_email,
                'password' => Hash::make('password123'), // standard initial password
                'sekolah_id' => $sekolah_id,
            ]);
            $user->assignRole('Orang Tua');
        }

        Anak::create([
            'user_id' => $user->id,
            'sekolah_id' => $sekolah_id,
            'name' => $request->name,
            'dob' => $request->dob,
            'parent_name' => $user->name,
        ]);

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak dan Orang Tua berhasil ditambahkan. Password default Ortu: password123');
    }

    public function update(Request $request, Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
        ]);

        $anak->update([
            'name' => $request->name,
            'dob' => $request->dob,
        ]);

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak berhasil diperbarui.');
    }

    public function destroy(Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);
        $anak->delete();
        return redirect()->route('admin.anak.index')->with('success', 'Data Anak berhasil dihapus.');
    }
}
