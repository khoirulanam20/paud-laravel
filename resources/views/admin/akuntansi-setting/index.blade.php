<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Pengaturan Akuntansi</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-danger mb-5">
                <ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.akuntansi-setting.update') }}" method="POST">
            @csrf @method('PUT')

            <!-- Section 1: Metode Pencatatan -->
            <div class="card mb-6">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                    <h3 class="section-title">Metode Pencatatan</h3>
                    <p class="section-subtitle">Tentukan kapan pendapatan diakui dalam jurnal.</p>
                </div>
                <div x-data="{ metode: '{{ $setting->metode_pencatatan }}' }" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label @click="metode = 'cash'"
                        class="p-4 border-2 rounded-xl cursor-pointer transition"
                        :class="metode === 'cash' ? 'border-[#1A6B6B] bg-[#D0E8E8]' : 'border-[#E0D6C8]'">
                        <input type="radio" name="metode_pencatatan" value="cash" class="sr-only" {{ $setting->isCash() ? 'checked' : '' }}>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 h-5 w-5 rounded-full border-2 flex items-center justify-center"
                                :class="metode === 'cash' ? 'border-[#1A6B6B]' : 'border-[#C4B89A]'">
                                <div x-show="metode === 'cash'" class="h-2.5 w-2.5 rounded-full" style="background:#1A6B6B;"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-sm" style="color:#2C2C2C;">Cash Basis</p>
                                <p class="text-xs mt-1" style="color:#9E9790;">Pendapatan diakui saat pembayaran diterima. Sederhana, cocok untuk PAUD kecil.</p>
                            </div>
                        </div>
                    </label>

                    <label @click="metode = 'accrual'"
                        class="p-4 border-2 rounded-xl cursor-pointer transition"
                        :class="metode === 'accrual' ? 'border-[#1A6B6B] bg-[#D0E8E8]' : 'border-[#E0D6C8]'">
                        <input type="radio" name="metode_pencatatan" value="accrual" class="sr-only" {{ $setting->isAccrual() ? 'checked' : '' }}>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 h-5 w-5 rounded-full border-2 flex items-center justify-center"
                                :class="metode === 'accrual' ? 'border-[#1A6B6B]' : 'border-[#C4B89A]'">
                                <div x-show="metode === 'accrual'" class="h-2.5 w-2.5 rounded-full" style="background:#1A6B6B;"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-sm" style="color:#2C2C2C;">Accrual Basis (PSAK)</p>
                                <p class="text-xs mt-1" style="color:#9E9790;">Pendapatan diakui saat tagihan diterbitkan. Jurnal piutang dibuat otomatis. Lebih akurat, sesuai PSAK.</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Section 2: Default Akun Cashflow -->
            <div class="card mb-6">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                    <h3 class="section-title">Default Akun Cashflow Manual</h3>
                    <p class="section-subtitle">Akun yang digunakan saat mencatat transaksi di menu Cashflow. Jurnal double-entry dibuat otomatis.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="input-label">Akun Kas</label>
                        <select name="akun_kas_id" class="input-field">
                            @foreach($akunAset as $a)
                                <option value="{{ $a->id }}" {{ $setting->akun_kas_id == $a->id ? 'selected' : '' }}>
                                    {{ $a->kode }} - {{ $a->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:#9E9790;">Akun yang didebit/kredit saat catat cashflow</p>
                    </div>
                    <div>
                        <label class="input-label">Counter Pemasukan</label>
                        <select name="akun_untuk_in" class="input-field">
                            @foreach($akunPendapatan as $a)
                                <option value="{{ $a->id }}" {{ $setting->akun_untuk_in == $a->id ? 'selected' : '' }}>
                                    {{ $a->kode }} - {{ $a->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:#9E9790;">Akun counter saat catat cashflow masuk</p>
                    </div>
                    <div>
                        <label class="input-label">Counter Pengeluaran</label>
                        <select name="akun_untuk_out" class="input-field">
                            @foreach($akunBeban as $a)
                                <option value="{{ $a->id }}" {{ $setting->akun_untuk_out == $a->id ? 'selected' : '' }}>
                                    {{ $a->kode }} - {{ $a->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:#9E9790;">Akun counter saat catat cashflow keluar</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Default Akun Auto-Jurnal Pembayaran -->
            <div class="card mb-6">
                <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                    <h3 class="section-title">Default Akun Auto-Jurnal Pembayaran</h3>
                    <p class="section-subtitle">Akun yang digunakan saat sistem otomatis membuat jurnal dari pembayaran bulanan.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="input-label">Akun Pendapatan SPP</label>
                        <select name="akun_pendapatan_id" class="input-field">
                            <option value="">— Pilih —</option>
                            @foreach($akunPendapatan as $a)
                                <option value="{{ $a->id }}" {{ $setting->akun_pendapatan_id == $a->id ? 'selected' : '' }}>
                                    {{ $a->kode }} - {{ $a->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:#9E9790;">Dikredit saat cash basis / accrual basis (generate)</p>
                    </div>
                    <div>
                        <label class="input-label">Akun Piutang SPP <span class="text-xs" style="color:#9E9790;">(hanya accrual)</span></label>
                        <select name="akun_piutang_id" class="input-field">
                            <option value="">— Pilih —</option>
                            @foreach($akunAset as $a)
                                <option value="{{ $a->id }}" {{ $setting->akun_piutang_id == $a->id ? 'selected' : '' }}>
                                    {{ $a->kode }} - {{ $a->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:#9E9790;">Didebit saat generate tagihan (accrual)</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-8">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</x-app-layout>
