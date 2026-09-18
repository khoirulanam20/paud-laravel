<x-guest-layout>
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#2C2C2C]">Daftarkan Lembaga Anda</h1>
            <p class="text-sm text-[#6B6560] mt-2">Isi formulir di bawah. Tim kami akan meninjau pendaftaran sebelum akun diaktifkan.</p>
        </div>

        @if($errors->any())
            <div class="alert-danger mb-6">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('guest.daftar-lembaga.store') }}" class="card p-6 space-y-6">
            @csrf

            <div>
                <h2 class="section-title mb-4">Data Lembaga / Yayasan</h2>
                <div class="space-y-4">
                    <div>
                        <label class="input-label" for="lembaga_name">Nama lembaga</label>
                        <input id="lembaga_name" name="lembaga_name" type="text" class="input-field" value="{{ old('lembaga_name') }}" required>
                    </div>
                    <div>
                        <label class="input-label" for="lembaga_address">Alamat</label>
                        <textarea id="lembaga_address" name="lembaga_address" rows="2" class="input-field">{{ old('lembaga_address') }}</textarea>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label" for="lembaga_phone">Telepon</label>
                            <input id="lembaga_phone" name="lembaga_phone" type="text" class="input-field" value="{{ old('lembaga_phone') }}">
                        </div>
                        <div>
                            <label class="input-label" for="organisasi">Organisasi</label>
                            <input id="organisasi" name="organisasi" type="text" class="input-field" value="{{ old('organisasi') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="section-title mb-4">Sekolah / Cabang Pertama</h2>
                <div class="space-y-4">
                    <div>
                        <label class="input-label" for="sekolah_name">Nama sekolah</label>
                        <input id="sekolah_name" name="sekolah_name" type="text" class="input-field" value="{{ old('sekolah_name') }}" required>
                    </div>
                    <div>
                        <label class="input-label" for="sekolah_address">Alamat sekolah</label>
                        <textarea id="sekolah_address" name="sekolah_address" rows="2" class="input-field">{{ old('sekolah_address') }}</textarea>
                    </div>
                    <div>
                        <label class="input-label" for="sekolah_phone">Telepon sekolah</label>
                        <input id="sekolah_phone" name="sekolah_phone" type="text" class="input-field" value="{{ old('sekolah_phone') }}">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="section-title mb-4">Admin Lembaga (Kontak)</h2>
                <div class="space-y-4">
                    <div>
                        <label class="input-label" for="contact_name">Nama lengkap</label>
                        <input id="contact_name" name="contact_name" type="text" class="input-field" value="{{ old('contact_name') }}" required>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label" for="contact_email">Email login</label>
                            <input id="contact_email" name="contact_email" type="email" class="input-field" value="{{ old('contact_email') }}" required>
                        </div>
                        <div>
                            <label class="input-label" for="contact_phone">Telepon</label>
                            <input id="contact_phone" name="contact_phone" type="text" class="input-field" value="{{ old('contact_phone') }}">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label" for="password">Password</label>
                            <input id="password" name="password" type="password" class="input-field" required>
                        </div>
                        <div>
                            <label class="input-label" for="password_confirmation">Konfirmasi password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="input-field" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="btn-primary justify-center">Kirim pendaftaran</button>
                <a href="{{ route('guest.beranda') }}" class="btn-secondary justify-center text-center">Kembali</a>
            </div>
        </form>
    </div>
</x-guest-layout>
