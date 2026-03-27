<x-guest-public :cms="$cms ?? []">
    <x-slot name="title">Pendaftaran Anak Baru</x-slot>

    <section class="py-16" style="background: linear-gradient(135deg,#FFF3E0,#FFF0F5);">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10">
                <span class="badge-pill">🌟 Pendaftaran</span>
                <h1 style="font-family:'Baloo 2',sans-serif; font-size:2.2rem; font-weight:800; color:#1F2937; margin-top:.75rem;">Daftarkan Buah Hati Anda!</h1>
                <p class="text-gray-500 mt-2">Isi formulir di bawah ini. Admin sekolah akan meninjau dan menghubungi Anda.</p>
            </div>

            @if(session('status'))
            <div class="rounded-2xl px-5 py-4 mb-6 flex gap-3" style="background:#D1FAE5; color:#065F46;">
                <span class="text-xl">🎉</span>
                <p class="font-semibold text-sm">{{ session('status') }}</p>
            </div>
            @endif

            <div class="card-facility p-8 sm:p-10">
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div class="pb-4 mb-4 border-b border-orange-100">
                        <p class="text-sm font-bold text-orange-500 uppercase tracking-wider">👩‍👧 Data Orang Tua</p>
                    </div>
                    <div>
                        <label class="input-label">Nama Lengkap Orang Tua *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="input-field" placeholder="Ibu/Bapak ...">
                        @error('name')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="input-label">Alamat Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input-field" placeholder="email@anda.com">
                        @error('email')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="input-label">Password *</label>
                            <input type="password" name="password" required class="input-field" placeholder="Min. 8 karakter">
                            @error('password')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="input-label">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" required class="input-field" placeholder="Ulangi password">
                        </div>
                    </div>

                    <div class="pb-4 mb-1 border-b border-orange-100 pt-2">
                        <p class="text-sm font-bold text-orange-500 uppercase tracking-wider">🧒 Data Anak</p>
                    </div>
                    <div>
                        <label class="input-label">Nama Lengkap Anak *</label>
                        <input type="text" name="anak_name" value="{{ old('anak_name') }}" required class="input-field" placeholder="Nama anak sesuai akta">
                        @error('anak_name')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="input-label">Tanggal Lahir Anak *</label>
                        <input type="date" name="anak_dob" value="{{ old('anak_dob') }}" required class="input-field" max="{{ date('Y-m-d') }}">
                        @error('anak_dob')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="input-label">Pilih Cabang Sekolah *</label>
                        <select name="sekolah_id" required class="input-field">
                            <option value="">— Pilih Sekolah Terdekat —</option>
                            @foreach($sekolahs as $s)
                            <option value="{{ $s->id }}" {{ old('sekolah_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}@if($s->address) — {{ $s->address }}@endif
                            </option>
                            @endforeach
                        </select>
                        @error('sekolah_id')<p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="input-label">Catatan Tambahan (opsional)</label>
                        <textarea name="catatan_ortu" rows="3" class="input-field" placeholder="Alergi makanan, kondisi khusus, atau pertanyaan lainnya...">{{ old('catatan_ortu') }}</textarea>
                    </div>

                    <div class="rounded-2xl px-4 py-3 text-sm" style="background:#FFF9C4; color:#92400E;">
                        ℹ️ Pendaftaran Anda akan ditinjau oleh Admin Sekolah. Anda akan mendapat konfirmasi melalui email atau dapat login kembali untuk melihat status.
                    </div>

                    <button type="submit" class="btn-cta w-full justify-center py-3.5 text-base">
                        🌈 Kirim Pendaftaran
                    </button>

                    <p class="text-center text-sm text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold" style="color:#FF8C42;">Masuk di sini</a></p>
                </form>
            </div>
        </div>
    </section>
</x-guest-public>
