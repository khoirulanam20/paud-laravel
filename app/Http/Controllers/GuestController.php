<?php

namespace App\Http\Controllers;

use App\Models\CmsContent;
use App\Models\Sekolah;
use App\Models\Kegiatan;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    private function cms(): array
    {
        return [
            'hero_title'       => CmsContent::get('hero_title', 'Selamat Datang di PAUD Kita! 🌈'),
            'hero_subtitle'    => CmsContent::get('hero_subtitle', 'Tempat terbaik untuk tumbuh, belajar, dan bermain bersama teman-teman baru!'),
            'hero_photo'       => CmsContent::get('hero_photo', ''),
            'about_title'      => CmsContent::get('about_title', 'Tentang Kami'),
            'about_text'       => CmsContent::get('about_text', 'Kami adalah lembaga PAUD/Daycare terpercaya yang mengutamakan keceriaan dan perkembangan anak.'),
            'about_photo'      => CmsContent::get('about_photo', ''),
            'facility_1_title' => CmsContent::get('facility_1_title', 'Ruang Bermain'),
            'facility_1_desc'  => CmsContent::get('facility_1_desc', 'Area bermain indoor dan outdoor yang aman dan nyaman.'),
            'facility_1_icon'  => CmsContent::get('facility_1_icon', '🎠'),
            'facility_2_title' => CmsContent::get('facility_2_title', 'Ruang Belajar'),
            'facility_2_desc'  => CmsContent::get('facility_2_desc', 'Kelas yang menyenangkan dengan alat peraga edukatif.'),
            'facility_2_icon'  => CmsContent::get('facility_2_icon', '📚'),
            'facility_3_title' => CmsContent::get('facility_3_title', 'Kantin Sehat'),
            'facility_3_desc'  => CmsContent::get('facility_3_desc', 'Menu bergizi seimbang yang disiapkan setiap hari.'),
            'facility_3_icon'  => CmsContent::get('facility_3_icon', '🍱'),
            'facility_4_title' => CmsContent::get('facility_4_title', 'UKS & Keamanan'),
            'facility_4_desc'  => CmsContent::get('facility_4_desc', 'Tenaga kesehatan dan sistem keamanan yang terpantau.'),
            'facility_4_icon'  => CmsContent::get('facility_4_icon', '🏥'),
            'gallery_1'        => CmsContent::get('gallery_1', ''),
            'gallery_2'        => CmsContent::get('gallery_2', ''),
            'gallery_3'        => CmsContent::get('gallery_3', ''),
            'gallery_4'        => CmsContent::get('gallery_4', ''),
            'gallery_5'        => CmsContent::get('gallery_5', ''),
            'gallery_6'        => CmsContent::get('gallery_6', ''),
            'kontak_alamat'    => CmsContent::get('kontak_alamat', 'Jl. Contoh No. 1, Kota'),
            'kontak_telepon'   => CmsContent::get('kontak_telepon', '021-1234567'),
            'kontak_email'     => CmsContent::get('kontak_email', 'info@paud.com'),
            'kontak_jam'       => CmsContent::get('kontak_jam', 'Senin–Jumat: 07.00–16.00 WIB'),
            'footer_text'      => CmsContent::get('footer_text', 'Tumbuh bersama, bahagia bersama. 💛'),
        ];
    }

    public function beranda()
    {
        $cms      = $this->cms();
        $sekolahs = Sekolah::orderBy('name')->get();
        return view('guest.beranda', compact('cms', 'sekolahs'));
    }

    public function tentang()
    {
        $cms = $this->cms();
        return view('guest.tentang', compact('cms'));
    }

    public function fasilitas()
    {
        $cms = $this->cms();
        return view('guest.fasilitas', compact('cms'));
    }

    public function galeri()
    {
        $cms = $this->cms();
        return view('guest.galeri', compact('cms'));
    }

    public function pendaftaran()
    {
        $sekolahs = Sekolah::orderBy('name')->get();
        return view('guest.pendaftaran', compact('sekolahs'));
    }

    public function kontak()
    {
        $cms = $this->cms();
        return view('guest.kontak', compact('cms'));
    }

    public function kontakSend(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pesan' => 'required|string|min:10',
        ]);

        // Store as kritik_saran (guest, no user_id)
        \App\Models\KritikSaran::create([
            'sekolah_id' => null,
            'user_id'    => null,
            'message'    => "[{$request->nama} – {$request->email}]: {$request->pesan}",
            'status'     => 'Terkirim',
        ]);

        return back()->with('kontak_success', 'Pesan Anda berhasil terkirim! Kami akan segera menghubungi Anda. 😊');
    }
}
