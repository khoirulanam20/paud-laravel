<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\CanUploadImage;
use App\Models\Pengumuman;
use App\Support\PaginationPerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    use CanUploadImage;

    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $pengumumans = Pengumuman::where('sekolah_id', $sekolah_id)
            ->latest('mulai_tayang')
            ->latest('id')
            ->paginate(PaginationPerPage::resolve($request))
            ->withQueryString();

        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePengumuman($request);

        $data = [
            'sekolah_id' => auth()->user()->sekolah_id,
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'isi' => $validated['isi'],
            'mulai_tayang' => $validated['mulai_tayang'],
            'selesai_tayang' => $validated['selesai_tayang'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->uploadImage($request->file('gambar'), 'pengumuman');
        }

        Pengumuman::create($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        abort_if($pengumuman->sekolah_id !== auth()->user()->sekolah_id, 403);

        $validated = $this->validatePengumuman($request);

        $data = [
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'isi' => $validated['isi'],
            'mulai_tayang' => $validated['mulai_tayang'],
            'selesai_tayang' => $validated['selesai_tayang'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('gambar')) {
            if ($pengumuman->gambar) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $data['gambar'] = $this->uploadImage($request->file('gambar'), 'pengumuman');
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        abort_if($pengumuman->sekolah_id !== auth()->user()->sekolah_id, 403);

        if ($pengumuman->gambar) {
            Storage::disk('public')->delete($pengumuman->gambar);
        }

        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function validatePengumuman(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'isi' => 'required|string|max:10000',
            'mulai_tayang' => 'required|date',
            'selesai_tayang' => ['required', 'date', 'after_or_equal:mulai_tayang'],
            'gambar' => 'nullable|image|max:2048',
            'is_active' => ['nullable', Rule::in(['0', '1', 0, 1, true, false])],
        ]);
    }
}
