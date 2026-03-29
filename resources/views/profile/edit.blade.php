<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Profil Akun Saya</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-6">

        @if(session('status') && in_array(session('status'), ['profile-updated','profile-sekolah-updated','profile-pengajar-updated','profile-orangtua-updated','password-updated']))
            <div class="alert-success"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Perubahan berhasil disimpan.</div>
        @endif

        @if($errors->any())
            <div class="alert-danger"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
        @endif

        {{-- SECTION: Akun Login --}}
        <div class="card">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Informasi Akun Login</h3>
                <p class="section-subtitle">Nama tampilan dan email untuk masuk ke sistem.</p>
            </div>
            <form method="post" action="{{ route('profile.update') }}" class="px-6 py-5 space-y-4">
                @csrf @method('patch')
                <div><label class="input-label">Nama Tampilan</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-field"></div>
                <div><label class="input-label">Alamat Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-field"></div>
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <p class="text-xs text-yellow-600">Email Anda belum diverifikasi.</p>
                @endif
                <div class="flex justify-end"><button type="submit" class="btn-primary">Simpan Akun</button></div>
            </form>
        </div>

        {{-- SECTION: Profil Sekolah --}}
        @if($user->hasRole('Admin Sekolah') && $sekolah)
        <div class="card">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Profil Sekolah</h3>
                <p class="section-subtitle">Informasi resmi sekolah yang Anda kelola.</p>
            </div>
            <form method="post" action="{{ route('profile.sekolah.update') }}" class="px-6 py-5 space-y-4">
                @csrf @method('patch')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2"><label class="input-label">Nama Sekolah</label><input type="text" name="name" value="{{ $sekolah->name }}" required class="input-field"></div>
                    <div><label class="input-label">NISN</label><input type="text" name="nisn" value="{{ $sekolah->nisn }}" class="input-field"></div>
                    <div><label class="input-label">Kontak / HP</label><input type="text" name="phone" value="{{ $sekolah->phone }}" class="input-field"></div>
                    <div class="col-span-2"><label class="input-label">Alamat Lengkap</label><textarea name="alamat" class="input-field" rows="3">{{ $sekolah->alamat }}</textarea></div>
                </div>
                <div class="flex justify-end"><button type="submit" class="btn-primary">Simpan Profil Sekolah</button></div>
            </form>
        </div>
        @endif

        {{-- SECTION: Profil Pengajar / Admin Kelas --}}
        @if(($user->hasRole('Pengajar') || $user->hasRole('Admin Kelas')) && $pengajar)
        <div class="card">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Profil Pendidik</h3>
                <p class="section-subtitle">Data personal Anda sebagai tenaga pengajar.</p>
            </div>
            <form method="post" action="{{ route('profile.pengajar.update') }}" class="px-6 py-5 space-y-4">
                @csrf @method('patch')
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="input-label">NIK (KTP)</label><input type="text" name="nik" value="{{ $pengajar->nik }}" class="input-field"></div>
                    <div><label class="input-label">Kontak / WA</label><input type="text" name="phone" value="{{ $pengajar->phone }}" class="input-field"></div>
                    <div>
                        <label class="input-label">Pendidikan terakhir</label>
                        <x-pendidikan-select name="pendidikan" :value="$pengajar->pendidikan" />
                    </div>
                    <div><label class="input-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="input-field">
                            <option value="">Pilih...</option>
                            <option value="Pria" {{ $pengajar->jenis_kelamin == 'Pria' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Wanita" {{ $pengajar->jenis_kelamin == 'Wanita' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-span-2"><label class="input-label">Alamat Lengkap</label><textarea name="alamat" class="input-field" rows="3">{{ $pengajar->alamat }}</textarea></div>
                </div>
                <div class="flex justify-end"><button type="submit" class="btn-primary">Simpan Profil Pendidik</button></div>
            </form>
        </div>
        @endif

        {{-- SECTION: Profil Wali Murid --}}
        @if($user->hasRole('Orang Tua') && $anaks && $anaks->count() > 0)
        <div class="card">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Profil Wali Murid</h3>
                <p class="section-subtitle">Data orang tua / wali yang tercatat di sistem.</p>
            </div>
            <form method="post" action="{{ route('profile.orangtua.update') }}" class="px-6 py-5 space-y-4">
                @csrf @method('patch')
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="input-label">Nama Bapak</label><input type="text" name="nama_bapak" value="{{ $anaks->first()->nama_bapak }}" class="input-field"></div>
                    <div><label class="input-label">NIK Bapak</label><input type="text" name="nik_bapak" value="{{ $anaks->first()->nik_bapak }}" class="input-field"></div>
                    <div><label class="input-label">Nama Ibu</label><input type="text" name="nama_ibu" value="{{ $anaks->first()->nama_ibu }}" class="input-field"></div>
                    <div><label class="input-label">NIK Ibu</label><input type="text" name="nik_ibu" value="{{ $anaks->first()->nik_ibu }}" class="input-field"></div>
                </div>
                <div class="flex justify-end"><button type="submit" class="btn-primary">Simpan Data Wali</button></div>
            </form>
        </div>
        @endif

        {{-- SECTION: Ganti Password --}}
        <div class="card">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Ganti Password</h3>
                <p class="section-subtitle">Pastikan akun Anda menggunakan password yang kuat.</p>
            </div>
            <form method="post" action="{{ route('password.update') }}" class="px-6 py-5 space-y-4">
                @csrf @method('put')
                <div><label class="input-label">Password Saat Ini</label><input type="password" name="current_password" class="input-field" autocomplete="current-password"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="input-label">Password Baru</label><input type="password" name="password" class="input-field" autocomplete="new-password"></div>
                    <div><label class="input-label">Konfirmasi Password Baru</label><input type="password" name="password_confirmation" class="input-field" autocomplete="new-password"></div>
                </div>
                <div class="flex justify-end"><button type="submit" class="btn-primary">Ganti Password</button></div>
            </form>
        </div>

    </div>
</x-app-layout>
