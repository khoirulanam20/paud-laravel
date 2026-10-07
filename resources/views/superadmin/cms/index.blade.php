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

        <div class="mb-5 flex items-center justify-between">
            <p class="text-sm" style="color:#6B6560;">Perubahan akan langsung terlihat di halaman website publik Anda.</p>
            <a href="{{ route('guest.beranda') }}" target="_blank" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                Lihat Website
            </a>
        </div>
        <form method="POST" action="{{ route('superadmin.cms.update') }}" enctype="multipart/form-data" class="space-y-6 relative" x-ref="cmsForm" @submit.prevent="submitWithCompression()">
            {{-- Compressing Overlay --}}
            <div x-show="isCompressing" class="fixed inset-0 z-[100] bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center text-center p-6" style="display:none;">
                <div class="h-12 w-12 border-4 border-teal-600/30 border-t-teal-600 rounded-full animate-spin"></div>
                <p class="mt-4 text-sm font-bold text-teal-800 uppercase tracking-widest">Mengoptimalkan Foto...</p>
                <p class="mt-1 text-xs text-teal-600">Mohon tunggu, sedang memperkecil ukuran gambar</p>
            </div>
            @csrf

            {{-- HERO --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <span class="text-xl">🌈</span><h3 class="section-title">Section Hero (Halaman Utama)</h3>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="input-label">Judul Utama (H1)</label>
                        <input type="text" name="hero_title" value="{{ $cms['hero_title'] }}" class="input-field" maxlength="120">
                        <p class="text-xs mt-1" style="color:#9E9790;">40–70 karakter disarankan. Memuat kata kunci utama, mis. &quot;Sistem Informasi PAUD Terpadu&quot;.</p>
                    </div>
                    <div>
                        <label class="input-label">Subjudul / Tagline</label>
                        <input type="text" name="hero_subtitle" value="{{ $cms['hero_subtitle'] }}" class="input-field" maxlength="320">
                        <p class="text-xs mt-1" style="color:#9E9790;">120–160 karakter. Manfaat untuk ortu, sekolah, dan operasional.</p>
                    </div>
                    <div>
                        <label class="input-label">Foto Hero</label>
                        @if($cms['hero_photo'])<img src="{{ Storage::url($cms['hero_photo']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <input type="file" name="hero_photo" accept="image/*" class="input-field py-2" @change="handleFile($event, 'hero_photo')">
                    </div>
                    <div>
                        <label class="input-label">Teks Alt Foto Hero</label>
                        <input type="text" name="hero_photo_alt" value="{{ $cms['hero_photo_alt'] }}" class="input-field" placeholder="Ilustrasi dashboard DaycareAI untuk PAUD">
                    </div>
                </div>
            </div>

            {{-- TENTANG --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <span class="text-xl">💛</span><h3 class="section-title">Section Tentang Kami</h3>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="input-label">Judul (H2)</label>
                        <input type="text" name="about_title" value="{{ $cms['about_title'] }}" class="input-field">
                        <p class="text-xs mt-1" style="color:#9E9790;">30–60 karakter, mis. pertanyaan manfaat.</p>
                    </div>
                    <div>
                        <label class="input-label">Isi Teks (gunakan Enter untuk paragraf baru)</label>
                        <textarea name="about_text" rows="6" class="input-field">{{ $cms['about_text'] }}</textarea>
                        <p class="text-xs mt-1" style="color:#9E9790;">400–800 karakter total. Paragraf pertama = ringkasan nilai DaycareAI.</p>
                    </div>
                    <div>
                        <label class="input-label">Foto</label>
                        @if($cms['about_photo'])<img src="{{ Storage::url($cms['about_photo']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <input type="file" name="about_photo" accept="image/*" class="input-field py-2" @change="handleFile($event, 'about_photo')">
                    </div>
                    <div>
                        <label class="input-label">Teks Alt Foto Tentang</label>
                        <input type="text" name="about_photo_alt" value="{{ $cms['about_photo_alt'] }}" class="input-field">
                    </div>
                </div>
            </div>

            {{-- JUDUL SECTION BERANDA --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <span class="text-xl">📋</span><h3 class="section-title">Judul Section Beranda</h3>
                </div>
                <div class="px-6 py-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="input-label text-xs">Stats — Judul</label><input type="text" name="section_stats_title" value="{{ $cms['section_stats_title'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">Stats — Subjudul</label><input type="text" name="section_stats_subtitle" value="{{ $cms['section_stats_subtitle'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">Fitur — Judul</label><input type="text" name="section_features_title" value="{{ $cms['section_features_title'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">Fitur — Subjudul</label><input type="text" name="section_features_subtitle" value="{{ $cms['section_features_subtitle'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">Galeri — Judul</label><input type="text" name="section_gallery_title" value="{{ $cms['section_gallery_title'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">Galeri — Subjudul</label><input type="text" name="section_gallery_subtitle" value="{{ $cms['section_gallery_subtitle'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">Testimoni — Judul</label><input type="text" name="section_testimonial_title" value="{{ $cms['section_testimonial_title'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">Testimoni — Subjudul</label><input type="text" name="section_testimonial_subtitle" value="{{ $cms['section_testimonial_subtitle'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">CTA — Judul</label><input type="text" name="section_cta_title" value="{{ $cms['section_cta_title'] }}" class="input-field"></div>
                    <div><label class="input-label text-xs">CTA — Subjudul</label><input type="text" name="section_cta_subtitle" value="{{ $cms['section_cta_subtitle'] }}" class="input-field"></div>
                </div>
            </div>

            {{-- FASILITAS --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <span class="text-xl">🏫</span><h3 class="section-title">Section Fasilitas (4 item)</h3>
                </div>
                <div class="px-6 py-6">
                    <p class="text-sm mb-4" style="color:#6B6560;">Judul &amp; deskripsi dipakai di beranda (tema Ascent) dan halaman Fitur. Beranda memakai gambar template Ascent; field emoji/ikon di bawah hanya untuk halaman Fitur (tema lama).</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach([1,2,3,4] as $i)
                        <div class="p-4 rounded-xl border" style="border-color:rgba(0,0,0,0.07);">
                            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#9E9790;">Fasilitas {{ $i }}</p>
                            <div class="space-y-3">
                                <div><label class="input-label text-xs">Emoji / Ikon (halaman Fitur saja)</label><input type="text" name="facility_{{ $i }}_icon" value="{{ $cms['facility_'.$i.'_icon'] }}" class="input-field" placeholder="🎠"></div>
                                <div><label class="input-label text-xs">Judul (H3)</label><input type="text" name="facility_{{ $i }}_title" value="{{ $cms['facility_'.$i.'_title'] }}" class="input-field"></div>
                                <div><label class="input-label text-xs">Deskripsi</label><textarea name="facility_{{ $i }}_desc" rows="2" class="input-field">{{ $cms['facility_'.$i.'_desc'] }}</textarea><p class="text-xs mt-1" style="color:#9E9790;">80–160 karakter, jelaskan manfaat.</p></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- GALERI --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <span class="text-xl">📸</span><h3 class="section-title">Galeri Foto (maks. 6 foto)</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach([1,2,3,4,5,6] as $i)
                        <div>
                            <p class="text-xs font-bold uppercase mb-2" style="color:#9E9790;">Foto {{ $i }}</p>
                            @if($cms['gallery_'.$i])<img src="{{ Storage::url($cms['gallery_'.$i]) }}" class="h-24 w-full object-cover rounded-xl mb-2">@endif
                            <input type="file" name="gallery_{{ $i }}" accept="image/*" class="input-field py-1.5 text-xs" @change="handleFile($event, 'gallery_{{ $i }}')">
                            <input type="text" name="gallery_{{ $i }}_alt" value="{{ $cms['gallery_'.$i.'_alt'] }}" class="input-field py-1.5 text-xs mt-2" placeholder="Teks alt SEO">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- KONTAK --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <span class="text-xl">📞</span><h3 class="section-title">Informasi Kontak</h3>
                </div>
                <div class="px-6 py-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="input-label">Alamat</label><textarea name="kontak_alamat" rows="2" class="input-field">{{ $cms['kontak_alamat'] }}</textarea></div>
                    <div><label class="input-label">Nomor Telepon</label><input type="text" name="kontak_telepon" value="{{ $cms['kontak_telepon'] }}" class="input-field"></div>
                    <div><label class="input-label">Email</label><input type="email" name="kontak_email" value="{{ $cms['kontak_email'] }}" class="input-field"></div>
                    <div><label class="input-label">Jam Operasional</label><input type="text" name="kontak_jam" value="{{ $cms['kontak_jam'] }}" class="input-field"></div>
                    <div class="col-span-2">
                        <label class="input-label">Teks Footer</label>
                        <input type="text" name="footer_text" value="{{ $cms['footer_text'] }}" class="input-field">
                        <p class="text-xs mt-1" style="color:#9E9790;">80–160 karakter value proposition.</p>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color:rgba(0,0,0,0.06); background:#FFFBF0;">
                    <span class="text-xl">🔍</span><h3 class="section-title">SEO Halaman Utama</h3>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="input-label">Meta Title</label>
                        <input type="text" name="seo_meta_title" value="{{ $cms['seo_meta_title'] }}" class="input-field" maxlength="70" placeholder="Kosongkan untuk pakai judul hero">
                        <p class="text-xs mt-1" style="color:#9E9790;">Maks. 70 karakter. Tampil di tab browser &amp; hasil Google.</p>
                    </div>
                    <div>
                        <label class="input-label">Meta Description</label>
                        <textarea name="seo_meta_description" rows="3" class="input-field" maxlength="320" placeholder="Kosongkan untuk pakai subjudul hero">{{ $cms['seo_meta_description'] }}</textarea>
                        <p class="text-xs mt-1" style="color:#9E9790;">120–160 karakter ideal. Ringkasan ajakan klik.</p>
                    </div>
                    <div>
                        <label class="input-label">Fokus Keyword (internal)</label>
                        <input type="text" name="seo_focus_keyword" value="{{ $cms['seo_focus_keyword'] }}" class="input-field" placeholder="sistem informasi paud">
                        <p class="text-xs mt-1" style="color:#9E9790;">Hanya panduan tim; tidak ditampilkan di website.</p>
                    </div>
                    <div>
                        <label class="input-label">Gambar OG (share sosial)</label>
                        @if($cms['seo_og_image'])<img src="{{ Storage::url($cms['seo_og_image']) }}" class="h-24 w-32 object-cover rounded-xl mb-2">@endif
                        <input type="file" name="seo_og_image" accept="image/*" class="input-field py-2" @change="handleFile($event, 'seo_og_image')">
                        <p class="text-xs mt-1" style="color:#9E9790;">Kosongkan untuk pakai foto hero. Rasio 1.91:1 disarankan.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pb-2">
                <a href="{{ route('guest.beranda') }}" target="_blank" class="btn-secondary">Preview Website</a>
                <button type="submit" class="btn-primary px-8">💾 Simpan Semua Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
