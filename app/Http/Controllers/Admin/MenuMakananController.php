<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuMakananController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $menus = MenuMakanan::where('sekolah_id', $sekolah_id)
            ->withCount(['votes as likes_count' => fn($q) => $q->where('vote_type', 'like')])
            ->withCount(['votes as dislikes_count' => fn($q) => $q->where('vote_type', 'dislike')])
            ->orderBy('date', 'desc')
            ->paginate(10);
        return view('admin.menu_makanan.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'menu' => 'required|string|max:5000',
            'nutrition_info' => 'nullable|string',
            'photo' => 'nullable|image|max:2048', // max 2MB
            'photo_kegiatan' => 'nullable|image|max:2048',
        ]);

        $data = [
            'sekolah_id' => auth()->user()->sekolah_id,
            'date' => $request->date,
            'menu' => $request->menu,
            'nutrition_info' => $request->nutrition_info,
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('menu_makanan', 'public');
            $data['photo'] = $path;
        }
        if ($request->hasFile('photo_kegiatan')) {
            $data['photo_kegiatan'] = $request->file('photo_kegiatan')->store('menu_kegiatan', 'public');
        }

        MenuMakanan::create($data);

        return redirect()->route('admin.menu-makanan.index')->with('success', 'Menu Makanan berhasil ditambahkan.');
    }

    public function update(Request $request, MenuMakanan $menu_makanan)
    {
        abort_if($menu_makanan->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'date' => 'required|date',
            'menu' => 'required|string|max:5000',
            'nutrition_info' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'photo_kegiatan' => 'nullable|image|max:2048',
        ]);

        $data = [
            'date' => $request->date,
            'menu' => $request->menu,
            'nutrition_info' => $request->nutrition_info,
        ];

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($menu_makanan->photo) {
                Storage::disk('public')->delete($menu_makanan->photo);
            }
            $path = $request->file('photo')->store('menu_makanan', 'public');
            $data['photo'] = $path;
        }
        if ($request->hasFile('photo_kegiatan')) {
            if ($menu_makanan->photo_kegiatan) {
                Storage::disk('public')->delete($menu_makanan->photo_kegiatan);
            }
            $data['photo_kegiatan'] = $request->file('photo_kegiatan')->store('menu_kegiatan', 'public');
        }

        $menu_makanan->update($data);

        return redirect()->route('admin.menu-makanan.index')->with('success', 'Menu Makanan berhasil diperbarui.');
    }

    public function destroy(MenuMakanan $menu_makanan)
    {
        abort_if($menu_makanan->sekolah_id !== auth()->user()->sekolah_id, 403);
        
        if ($menu_makanan->photo) {
            Storage::disk('public')->delete($menu_makanan->photo);
        }
        if ($menu_makanan->photo_kegiatan) {
            Storage::disk('public')->delete($menu_makanan->photo_kegiatan);
        }
        $menu_makanan->delete();
        
        return redirect()->route('admin.menu-makanan.index')->with('success', 'Menu Makanan berhasil dihapus.');
    }
}
