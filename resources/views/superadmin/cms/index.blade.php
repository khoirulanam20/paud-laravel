@php use App\Support\GuestBrand; $brand = GuestBrand::name(); @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kelola Konten Website</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto" x-data="{
        isCompressing: false,
        compressedFiles: {},
        async handleFile(e, key) {
            const file = e.target.files[0];
            if (!file) return;
            this.isCompressing = true;
            try {
                this.compressedFiles[key] = await window.compressImage(file);
            } finally {
                this.isCompressing = false;
            }
        },
        submitWithCompression() {
            Object.keys(this.compressedFiles).forEach(name => {
                if (this.compressedFiles[name]) {
                    const dt = new DataTransfer();
                    dt.items.add(this.compressedFiles[name]);
                    const input = this.$refs.cmsForm.querySelector(`input[name='${name}']`);
                    if (input) input.files = dt.files;
                }
            });
            this.$refs.cmsForm.submit();
        }
    }">
        @if(session('success'))<div class="alert-success mb-6"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm" style="color:#6B6560;">Perubahan langsung terlihat di website publik (tema Ascent).</p>
            <div class="flex flex-wrap gap-2 text-xs">
                <a href="{{ route('guest.beranda') }}" target="_blank" class="btn-secondary py-1.5 px-2">Beranda</a>
                <a href="{{ route('guest.tentang') }}" target="_blank" class="btn-secondary py-1.5 px-2">Tentang</a>
                <a href="{{ route('guest.fasilitas') }}" target="_blank" class="btn-secondary py-1.5 px-2">Fitur</a>
                <a href="{{ route('guest.galeri') }}" target="_blank" class="btn-secondary py-1.5 px-2">Galeri</a>
                <a href="{{ route('guest.kontak') }}" target="_blank" class="btn-secondary py-1.5 px-2">Kontak</a>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.cms.update') }}" enctype="multipart/form-data" class="space-y-6 relative" x-ref="cmsForm" @submit.prevent="submitWithCompression()">
            <div x-show="isCompressing" class="fixed inset-0 z-[100] bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center text-center p-6" style="display:none;">
                <div class="h-12 w-12 border-4 border-teal-600/30 border-t-teal-600 rounded-full animate-spin"></div>
                <p class="mt-4 text-sm font-bold text-teal-800 uppercase tracking-widest">Mengoptimalkan Foto...</p>
            </div>
            @csrf

            {{-- BERANDA: Hero --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center justify-between gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <div class="flex items-center gap-2"><span class="text-xl">🌈</span><h3 class="section-title">Beranda — Hero</h3></div>
                    <a href="{{ route('guest.beranda') }}" target="_blank" class="text-xs text-teal-700 hover:underline">Preview</a>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div><label class="input-label">Judul Utama (H1)</label><input type="text" name="hero_title" value="{{ $cms['hero_title'] }}" class="input-field" maxlength="120"></div>
                    <div><label class="input-label">Subjudul</label><input type="text" name="hero_subtitle" value="{{ $cms['hero_subtitle'] }}" class="input-field" maxlength="320"></div>
                    <p class="text-sm" style="color:#6B6560;">Dua gambar dekor kiri &amp; kanan hero (desktop). Tampil ±150–175 px lebar, proporsi asli dipertahankan.</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label text-xs">Gambar kiri</label>
                            @if($cms['hero_left_photo'])<img src="{{ Storage::url($cms['hero_left_photo']) }}" class="h-20 w-full object-contain rounded-lg mb-2 bg-[#FFFBF0]">@endif
                            <input type="file" name="hero_left_photo" accept="image/*" class="input-field py-1.5 text-xs" @change="handleFile($event, 'hero_left_photo')">
                        </div>
                        <div>
                            <label class="input-label text-xs">Gambar kanan</label>
                            @if($cms['hero_right_photo'])<img src="{{ Storage::url($cms['hero_right_photo']) }}" class="h-20 w-full object-contain rounded-lg mb-2 bg-[#FFFBF0]">@endif
                            <input type="file" name="hero_right_photo" accept="image/*" class="input-field py-1.5 text-xs" @change="handleFile($event, 'hero_right_photo')">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4 pt-2 border-t" style="border-color:rgba(0,0,0,0.06);">
                        <div>
                            <label class="input-label text-xs">Gambar OG / meta (tidak ditampil di hero)</label>
                            @if($cms['hero_photo'])<img src="{{ Storage::url($cms['hero_photo']) }}" class="h-20 w-full object-contain rounded-lg mb-2 bg-[#FFFBF0]">@endif
                            <input type="file" name="hero_photo" accept="image/*" class="input-field py-1.5 text-xs" @change="handleFile($event, 'hero_photo')">
                        </div>
                        <div>
                            <label class="input-label text-xs">Alt gambar OG</label>
                            <input type="text" name="hero_photo_alt" value="{{ $cms['hero_photo_alt'] }}" class="input-field" placeholder="Ilustrasi kegiatan belajar">
                        </div>
                    </div>
                </div>
            </div>

            {{-- BERANDA: Stats --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;"><h3 class="section-title">Beranda — Nilai / Stats</h3></div>
                <div class="px-6 py-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="input-label text-xs">Judul section</label><input type="text" name="section_stats_title" value="{{ $cms['section_stats_title'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Subjudul</label><input type="text" name="section_stats_subtitle" value="{{ $cms['section_stats_subtitle'] }}" class="input-field"></div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach([1,2,3,4] as $i)
                        <div class="p-3 rounded-lg border" style="border-color:rgba(0,0,0,0.07);">
                            <p class="text-xs font-bold mb-2" style="color:#9E9790;">Kartu {{ $i }}</p>
                            <input type="text" name="stats_{{ $i }}_value" value="{{ $cms['stats_'.$i.'_value'] }}" class="input-field mb-2" placeholder="Nilai">
                            <input type="text" name="stats_{{ $i }}_label" value="{{ $cms['stats_'.$i.'_label'] }}" class="input-field" placeholder="Label">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tentang (beranda + halaman) --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <h3 class="section-title">💛 Tentang Kami</h3>
                    <a href="{{ route('guest.tentang') }}" target="_blank" class="text-xs text-teal-700 hover:underline">/tentang</a>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="input-label text-xs">H1 halaman Tentang</label><input type="text" name="page_tentang_h1" value="{{ $cms['page_tentang_h1'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Meta description</label><input type="text" name="seo_tentang_description" value="{{ $cms['seo_tentang_description'] }}" class="input-field" maxlength="320"></div>
                    </div>
                    <div><label class="input-label">Judul section (H2)</label><input type="text" name="about_title" value="{{ $cms['about_title'] }}" class="input-field"></div>
                    <div><label class="input-label">Isi teks</label><textarea name="about_text" rows="6" class="input-field">{{ $cms['about_text'] }}</textarea></div>
                    <div>
                        <label class="input-label">Foto utama (mengganti seluruh kolase)</label>
                        @if($cms['about_photo'])<img src="{{ Storage::url($cms['about_photo']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <input type="file" name="about_photo" accept="image/*" class="input-field py-2" @change="handleFile($event, 'about_photo')">
                        <p class="text-xs mt-1" style="color:#9E9790;">Disarankan ±960×720 px (4:3).</p>
                    </div>
                    <div><label class="input-label">Alt foto</label><input type="text" name="about_photo_alt" value="{{ $cms['about_photo_alt'] }}" class="input-field"></div>
                    <div>
                        <label class="input-label">Gambar kolase tentang (jika foto utama kosong)</label>
                        @if($cms['about_collage_image'])<img src="{{ Storage::url($cms['about_collage_image']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <input type="file" name="about_collage_image" accept="image/*" class="input-field py-2" @change="handleFile($event, 'about_collage_image')">
                        <p class="text-xs mt-1" style="color:#9E9790;">Kolom kolase: ±600×800 px (3:4). Foto utama di atas: ±960×720 px (4:3).</p>
                    </div>
                </div>
            </div>

            {{-- Fitur beranda + halaman --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <h3 class="section-title">🏫 Fitur / Modul</h3>
                    <a href="{{ route('guest.fasilitas') }}" target="_blank" class="text-xs text-teal-700 hover:underline">/fasilitas</a>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <p class="text-sm" style="color:#6B6560;">Judul &amp; 4 item dipakai di beranda (Programs + Layanan) dan halaman Fitur. Ikon emoji hanya untuk grid tema guest-public lama.</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="input-label text-xs">H1 halaman Fitur</label><input type="text" name="page_fasilitas_h1" value="{{ $cms['page_fasilitas_h1'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Meta description Fitur</label><input type="text" name="seo_fasilitas_description" value="{{ $cms['seo_fasilitas_description'] }}" class="input-field" maxlength="320"></div>
                        <div><label class="input-label text-xs">Judul section fitur</label><input type="text" name="section_features_title" value="{{ $cms['section_features_title'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Subjudul section</label><input type="text" name="section_features_subtitle" value="{{ $cms['section_features_subtitle'] }}" class="input-field"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach([1,2,3,4] as $i)
                        <div class="p-4 rounded-xl border" style="border-color:rgba(0,0,0,0.07);">
                            <p class="text-xs font-bold uppercase mb-3" style="color:#9E9790;">Modul {{ $i }}</p>
                            <input type="text" name="facility_{{ $i }}_icon" value="{{ $cms['facility_'.$i.'_icon'] }}" class="input-field mb-2 text-sm" placeholder="Ikon (guest-public)">
                            <input type="text" name="facility_{{ $i }}_title" value="{{ $cms['facility_'.$i.'_title'] }}" class="input-field mb-2">
                            <textarea name="facility_{{ $i }}_desc" rows="2" class="input-field">{{ $cms['facility_'.$i.'_desc'] }}</textarea>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-xs" style="color:#9E9790;">Kartu tambahan di halaman Fitur (setelah 4 modul di atas):</p>
                    @foreach([1,2,3] as $i)
                    <div class="grid sm:grid-cols-2 gap-3 p-3 border rounded-lg" style="border-color:rgba(0,0,0,0.06);">
                        <input type="text" name="pillar_{{ $i }}_title" value="{{ $cms['pillar_'.$i.'_title'] }}" class="input-field" placeholder="Pilar {{ $i }} — judul (kosongkan = default)">
                        <textarea name="pillar_{{ $i }}_desc" rows="2" class="input-field" placeholder="Deskripsi">{{ $cms['pillar_'.$i.'_desc'] }}</textarea>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- FAQ --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;"><h3 class="section-title">Beranda — FAQ</h3></div>
                <div class="px-6 py-6 space-y-4">
                    <div><label class="input-label">Judul section</label><input type="text" name="faq_title" value="{{ $cms['faq_title'] }}" class="input-field"></div>
                    @foreach([1,2,3] as $i)
                    <div class="space-y-2 p-3 border rounded-lg" style="border-color:rgba(0,0,0,0.06);">
                        <input type="text" name="faq_{{ $i }}_q" value="{{ $cms['faq_'.$i.'_q'] }}" class="input-field" placeholder="Pertanyaan {{ $i }}">
                        <textarea name="faq_{{ $i }}_a" rows="2" class="input-field" placeholder="Jawaban">{{ $cms['faq_'.$i.'_a'] }}</textarea>
                    </div>
                    @endforeach
                    <div>
                        <label class="input-label">Gambar samping FAQ</label>
                        @if($cms['faq_image'])<img src="{{ Storage::url($cms['faq_image']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <input type="file" name="faq_image" accept="image/*" class="input-field py-2" @change="handleFile($event, 'faq_image')">
                        <p class="text-xs mt-1" style="color:#9E9790;">Disarankan ±1056×792 px (4:3), maks. lebar tampil 528 px.</p>
                    </div>
                </div>
            </div>

            {{-- Student age + blog --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;"><h3 class="section-title">Beranda — Usia &amp; Blog</h3></div>
                <div class="px-6 py-6 space-y-4">
                    <div><label class="input-label">Judul section usia</label><input type="text" name="section_student_age_title" value="{{ $cms['section_student_age_title'] }}" class="input-field"></div>
                    <div><label class="input-label">Teks section usia</label><textarea name="section_student_age_text" rows="2" class="input-field">{{ $cms['section_student_age_text'] }}</textarea></div>
                    <div><label class="input-label">Judul section blog</label><input type="text" name="section_blog_title" value="{{ $cms['section_blog_title'] }}" class="input-field"><p class="text-xs mt-1" style="color:#9E9790;">Artikel masih placeholder; gambar bisa diganti di bawah.</p></div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        @foreach([1,2,3] as $i)
                        <div>
                            <p class="text-xs font-bold mb-2" style="color:#9E9790;">Gambar artikel {{ $i }}</p>
                            @if($cms['blog_'.$i.'_image'])<img src="{{ Storage::url($cms['blog_'.$i.'_image']) }}" class="h-20 w-full object-cover rounded-lg mb-2">@endif
                            <input type="file" name="blog_{{ $i }}_image" accept="image/*" class="input-field py-1.5 text-xs" @change="handleFile($event, 'blog_{{ $i }}_image')">
                            <p class="text-xs mt-1" style="color:#9E9790;">{{ $i < 3 ? '±420×315 px (4:3)' : '±800×500 px' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Testimonials --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;"><h3 class="section-title">Testimoni (beranda &amp; tentang)</h3></div>
                <div class="px-6 py-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="section_testimonial_title" value="{{ $cms['section_testimonial_title'] }}" class="input-field" placeholder="Judul section">
                        <input type="text" name="section_testimonial_subtitle" value="{{ $cms['section_testimonial_subtitle'] }}" class="input-field" placeholder="Subjudul">
                    </div>
                    @foreach([1,2,3] as $i)
                    <div class="p-4 border rounded-lg space-y-2" style="border-color:rgba(0,0,0,0.06);">
                        <p class="text-xs font-bold" style="color:#9E9790;">Testimoni {{ $i }}</p>
                        <input type="text" name="testimonial_{{ $i }}_title" value="{{ $cms['testimonial_'.$i.'_title'] }}" class="input-field" placeholder="Judul kutipan">
                        <textarea name="testimonial_{{ $i }}_quote" rows="2" class="input-field">{{ $cms['testimonial_'.$i.'_quote'] }}</textarea>
                        <div class="grid sm:grid-cols-2 gap-2">
                            <input type="text" name="testimonial_{{ $i }}_name" value="{{ $cms['testimonial_'.$i.'_name'] }}" class="input-field" placeholder="Nama">
                            <input type="text" name="testimonial_{{ $i }}_role" value="{{ $cms['testimonial_'.$i.'_role'] }}" class="input-field" placeholder="Peran">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;"><h3 class="section-title">CTA (beranda, kontak, dll.)</h3></div>
                <div class="px-6 py-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="section_cta_title" value="{{ $cms['section_cta_title'] }}" class="input-field" placeholder="Judul CTA">
                        <input type="text" name="section_cta_subtitle" value="{{ $cms['section_cta_subtitle'] }}" class="input-field" placeholder="Subjudul CTA">
                    </div>
                    <div>
                        <label class="input-label">Ilustrasi CTA / newsletter</label>
                        @if($cms['newsletter_image'])<img src="{{ Storage::url($cms['newsletter_image']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <input type="file" name="newsletter_image" accept="image/*" class="input-field py-2" @change="handleFile($event, 'newsletter_image')">
                        <p class="text-xs mt-1" style="color:#9E9790;">Disarankan ±600×680 px; tinggi tampil maks. 340 px.</p>
                    </div>
                </div>
            </div>

            {{-- Galeri --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <h3 class="section-title">📸 Halaman Galeri</h3>
                    <a href="{{ route('guest.galeri') }}" target="_blank" class="text-xs text-teal-700 hover:underline">/galeri</a>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="input-label text-xs">H1</label><input type="text" name="page_galeri_h1" value="{{ $cms['page_galeri_h1'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Meta description</label><input type="text" name="seo_galeri_description" value="{{ $cms['seo_galeri_description'] }}" class="input-field" maxlength="320"></div>
                        <div class="sm:col-span-2"><label class="input-label text-xs">Intro di bawah hero</label><input type="text" name="page_galeri_intro" value="{{ $cms['page_galeri_intro'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Judul grid galeri</label><input type="text" name="section_gallery_title" value="{{ $cms['section_gallery_title'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Subjudul / empty state</label><input type="text" name="section_gallery_subtitle" value="{{ $cms['section_gallery_subtitle'] }}" class="input-field"></div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach([1,2,3,4,5,6] as $i)
                        <div>
                            <p class="text-xs font-bold mb-2" style="color:#9E9790;">Foto {{ $i }}</p>
                            @if($cms['gallery_'.$i])<img src="{{ Storage::url($cms['gallery_'.$i]) }}" class="h-24 w-full object-cover rounded-xl mb-2">@endif
                            <input type="file" name="gallery_{{ $i }}" accept="image/*" class="input-field py-1.5 text-xs" @change="handleFile($event, 'gallery_{{ $i }}')">
                            <input type="text" name="gallery_{{ $i }}_alt" value="{{ $cms['gallery_'.$i.'_alt'] }}" class="input-field py-1.5 text-xs mt-2" placeholder="Alt SEO">
                            <p class="text-xs mt-1" style="color:#9E9790;">Kotak 1:1, ±800×800 px.</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kontak --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <h3 class="section-title">📞 Kontak &amp; Footer</h3>
                    <a href="{{ route('guest.kontak') }}" target="_blank" class="text-xs text-teal-700 hover:underline">/kontak</a>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="input-label text-xs">H1 halaman Kontak</label><input type="text" name="page_kontak_h1" value="{{ $cms['page_kontak_h1'] }}" class="input-field"></div>
                        <div><label class="input-label text-xs">Meta description</label><input type="text" name="seo_kontak_description" value="{{ $cms['seo_kontak_description'] }}" class="input-field" maxlength="320"></div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <textarea name="kontak_alamat" rows="2" class="input-field" placeholder="Alamat">{{ $cms['kontak_alamat'] }}</textarea>
                        <input type="text" name="kontak_telepon" value="{{ $cms['kontak_telepon'] }}" class="input-field" placeholder="Telepon / WA">
                        <input type="email" name="kontak_email" value="{{ $cms['kontak_email'] }}" class="input-field" placeholder="Email">
                        <input type="text" name="kontak_jam" value="{{ $cms['kontak_jam'] }}" class="input-field" placeholder="Jam operasional">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="contact_form_h2" value="{{ $cms['contact_form_h2'] }}" class="input-field" placeholder="Judul form kontak">
                        <input type="text" name="contact_form_lead" value="{{ $cms['contact_form_lead'] }}" class="input-field" placeholder="Lead form (opsional)">
                    </div>
                    <div><label class="input-label">Teks footer</label><input type="text" name="footer_text" value="{{ $cms['footer_text'] }}" class="input-field"></div>
                </div>
            </div>

            {{-- Formulir --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;"><h3 class="section-title">Formulir publik</h3></div>
                <div class="px-6 py-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="page_pendaftaran_h2" value="{{ $cms['page_pendaftaran_h2'] }}" class="input-field" placeholder="Judul /pendaftaran">
                        <textarea name="page_pendaftaran_intro" rows="2" class="input-field" placeholder="Intro pendaftaran ortu">{{ $cms['page_pendaftaran_intro'] }}</textarea>
                        <input type="text" name="page_daftar_sekolah_h1" value="{{ $cms['page_daftar_sekolah_h1'] }}" class="input-field" placeholder="Judul daftar sekolah">
                        <textarea name="page_daftar_sekolah_intro" rows="2" class="input-field">{{ $cms['page_daftar_sekolah_intro'] }}</textarea>
                        <input type="text" name="page_daftar_lembaga_h1" value="{{ $cms['page_daftar_lembaga_h1'] }}" class="input-field" placeholder="Judul daftar lembaga">
                        <textarea name="page_daftar_lembaga_intro" rows="2" class="input-field">{{ $cms['page_daftar_lembaga_intro'] }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SEO beranda --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;"><h3 class="section-title">🔍 SEO Beranda &amp; media</h3></div>
                <div class="px-6 py-6 space-y-4">
                    <p class="text-sm" style="color:#6B6560;">Gambar hero diatur di kartu Beranda — Hero (juga fallback OG).</p>
                    <input type="text" name="seo_meta_title" value="{{ $cms['seo_meta_title'] }}" class="input-field" maxlength="70" placeholder="Meta title beranda">
                    <textarea name="seo_meta_description" rows="3" class="input-field" maxlength="320">{{ $cms['seo_meta_description'] }}</textarea>
                    <input type="text" name="seo_focus_keyword" value="{{ $cms['seo_focus_keyword'] }}" class="input-field" placeholder="Keyword internal">
                    <div>
                        @if($cms['seo_og_image'])<img src="{{ Storage::url($cms['seo_og_image']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <label class="input-label">Gambar OG</label>
                        <input type="file" name="seo_og_image" accept="image/*" class="input-field py-2" @change="handleFile($event, 'seo_og_image')">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pb-2">
                <button type="submit" class="btn-primary px-8">Simpan Semua Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
