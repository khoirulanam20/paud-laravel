<?php

namespace App\Http\Controllers;

use App\Models\KritikSaran;
use App\Models\Sekolah;
use App\Support\GuestCms;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function beranda()
    {
        $cms = GuestCms::data();
        $sekolahs = Sekolah::orderBy('name')->get();

        return view('guest.beranda', compact('cms', 'sekolahs'));
    }

    public function tentang()
    {
        $cms = GuestCms::data();

        return view('guest.tentang', compact('cms'));
    }

    public function fasilitas()
    {
        $cms = GuestCms::data();

        return view('guest.fasilitas', compact('cms'));
    }

    public function galeri()
    {
        $cms = GuestCms::data();

        return view('guest.galeri', compact('cms'));
    }

    public function pendaftaran()
    {
        $sekolahs = Sekolah::orderBy('name')->get();

        return view('guest.pendaftaran', compact('sekolahs'));
    }

    public function kontak()
    {
        $cms = GuestCms::data();

        return view('guest.kontak', compact('cms'));
    }

    public function kontakSend(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pesan' => 'required|string|min:10',
        ]);

        // Store as kritik_saran (guest, no user_id)
        KritikSaran::create([
            'sekolah_id' => null,
            'user_id' => null,
            'message' => "[{$request->nama} – {$request->email}]: {$request->pesan}",
            'status' => 'Terkirim',
        ]);

        return back()->with('kontak_success', 'Pesan Anda berhasil terkirim! Kami akan segera menghubungi Anda. 😊');
    }
}
