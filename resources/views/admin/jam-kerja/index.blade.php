<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-tour="page-header">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg flex items-center justify-center shrink-0" style="background: #1A6B6B;">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl" style="color: #2C2C2C;">Pengaturan Presensi Guru</h2>
                    <p class="text-sm" style="color:#6B6560;">Hari kerja, jam masuk/pulang, radius lokasi, dan hari libur.</p>
                </div>
            </div>
            <a href="{{ route('admin.presensi-guru.index') }}" class="btn-secondary px-4 py-2 rounded-xl text-sm font-bold">Kembali ke Presensi</a>
        </div>
    </x-slot>

    <div class="app-page-shell">
        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-danger mb-5">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if(! $sekolah->location_coordinate)
            <div class="alert-danger mb-5">
                Titik koordinat sekolah belum diatur, jadi presensi guru belum bisa dibuka. Isi di bagian
                <strong>Lokasi &amp; Radius</strong> di bawah: klik <strong>"Deteksi lokasi saya"</strong> saat berada
                di sekolah, atau isi manual dengan format <code>-6.200000,106.816666</code>, lalu simpan.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.presensi-guru.pengaturan.update') }}">
            @csrf
            @method('PUT')

            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <h3 class="section-title">Lokasi &amp; Radius</h3>
                    <p class="section-subtitle">Titik koordinat sekolah dipakai sebagai pusat radius presensi guru.</p>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="input-label">Titik koordinat sekolah (lat,lng)</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="location_coordinate" name="location_coordinate"
                                value="{{ old('location_coordinate', $sekolah->location_coordinate) }}"
                                placeholder="-6.200000,106.816666" class="input-field w-full">
                            <button type="button" id="btn-deteksi-lokasi"
                                class="btn-secondary px-3 py-2 rounded-xl text-xs font-bold whitespace-nowrap">
                                Deteksi lokasi saya
                            </button>
                        </div>
                        <p class="text-xs mt-2" style="color:#9A9188;">
                            Klik tombol saat berada <strong>di lokasi sekolah</strong>, atau isi manual.
                        </p>
                        <p id="deteksi-status" class="text-xs mt-1" style="color:#9A9188;"></p>
                    </div>
                    <div>
                        <label class="input-label">Radius presensi (meter)</label>
                        <input type="number" name="radius_m" min="10" max="5000"
                            value="{{ old('radius_m', $sekolah->radius_m) }}"
                            placeholder="100" class="input-field w-full sm:max-w-xs">
                        <p class="text-xs mt-2" style="color:#9A9188;">Kosongkan untuk memakai default 100 meter.</p>
                    </div>
                </div>
                <div class="px-6 pb-5 -mt-2">
                    <div class="text-xs rounded-xl px-4 py-3" style="background:#FAF6F0;color:#6B6560;">
                        Hasil deteksi (read-only):
                        <span id="deteksi-hasil" class="font-bold" style="color:#2C2C2C;">{{ $sekolah->location_coordinate ?: 'belum ada koordinat tersimpan' }}</span>
                    </div>
                </div>
            </div>

            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <h3 class="section-title">Jadwal Jam Kerja</h3>
                    <p class="section-subtitle">Hari tidak dicentang berarti libur (presensi ditutup).</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-amber-50/50">
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Hari</th>
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40 text-center">Kerja</th>
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Jam Masuk</th>
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Jam Pulang</th>
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Toleransi Masuk (mnt)</th>
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Toleransi Pulang (mnt)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                            @foreach($jadwals as $hari => $jadwal)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 text-sm">{{ \App\Models\SekolahJamKerja::HARI[$hari] ?? $hari }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <input type="checkbox" name="jadwal[{{ $hari }}][is_kerja]" value="1"
                                            @checked(old("jadwal.$hari.is_kerja", $jadwal->is_kerja))
                                            class="h-4 w-4 rounded border-gray-300">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="time" name="jadwal[{{ $hari }}][jam_masuk]"
                                            value="{{ old("jadwal.$hari.jam_masuk", $jadwal->jam_masuk ? \Illuminate\Support\Carbon::parse($jadwal->jam_masuk)->format('H:i') : '') }}"
                                            class="input-field py-1.5 text-sm w-32">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="time" name="jadwal[{{ $hari }}][jam_pulang]"
                                            value="{{ old("jadwal.$hari.jam_pulang", $jadwal->jam_pulang ? \Illuminate\Support\Carbon::parse($jadwal->jam_pulang)->format('H:i') : '') }}"
                                            class="input-field py-1.5 text-sm w-32">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="jadwal[{{ $hari }}][toleransi_masuk_menit]" min="0" max="600"
                                            value="{{ old("jadwal.$hari.toleransi_masuk_menit", $jadwal->toleransi_masuk_menit) }}"
                                            class="input-field py-1.5 text-sm w-24">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="jadwal[{{ $hari }}][toleransi_pulang_menit]" min="0" max="600"
                                            value="{{ old("jadwal.$hari.toleransi_pulang_menit", $jadwal->toleransi_pulang_menit) }}"
                                            class="input-field py-1.5 text-sm w-24">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t flex justify-end" style="border-color: rgba(0,0,0,0.06);">
                    <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm">Simpan Pengaturan</button>
                </div>
            </div>
        </form>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: rgba(0,0,0,0.06);">
                <h3 class="section-title">Hari Libur Khusus</h3>
                <p class="section-subtitle">Tanggal merah, libur semester, dan sejenisnya. Presensi ditutup pada tanggal ini.</p>
            </div>

            <form method="POST" action="{{ route('admin.presensi-guru.pengaturan.libur.store') }}" class="px-6 py-5 flex flex-col sm:flex-row sm:items-end gap-3 border-b" style="border-color: rgba(0,0,0,0.06);">
                @csrf
                <div>
                    <label class="input-label">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="input-field" required>
                </div>
                <div class="flex-1">
                    <label class="input-label">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" maxlength="255" placeholder="Contoh: Libur Idul Fitri" class="input-field w-full">
                </div>
                <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl font-bold text-sm">Tambah</button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-amber-50/50">
                            <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Tanggal</th>
                            <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40">Keterangan</th>
                            <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-amber-900/40 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-50">
                        @forelse($hariLiburs as $libur)
                            <tr class="hover:bg-amber-50/30 transition">
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $libur->tanggal?->translatedFormat('d F Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $libur->keterangan ?: '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.presensi-guru.pengaturan.libur.destroy', $libur) }}" onsubmit="return confirm('Hapus hari libur ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FDECEA;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm" style="color:#9A9188;">Belum ada hari libur khusus.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('btn-deteksi-lokasi');
            const input = document.getElementById('location_coordinate');
            const status = document.getElementById('deteksi-status');
            const hasil = document.getElementById('deteksi-hasil');

            if (!button || !input) {
                return;
            }

            button.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    status.textContent = 'Browser tidak mendukung deteksi lokasi.';
                    status.style.color = '#C0392B';
                    return;
                }

                button.disabled = true;
                status.textContent = 'Mengambil lokasi…';
                status.style.color = '#6B6560';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude.toFixed(6);
                        const lng = position.coords.longitude.toFixed(6);
                        input.value = lat + ',' + lng;
                        hasil.textContent = lat + ', ' + lng + ' (akurasi ±' + Math.round(position.coords.accuracy) + ' m)';
                        status.textContent = 'Lokasi terdeteksi. Klik Simpan Pengaturan untuk menerapkan.';
                        status.style.color = '#1A6B6B';
                        button.disabled = false;
                    },
                    function () {
                        status.textContent = 'Gagal mengambil lokasi. Izinkan akses lokasi lalu coba lagi.';
                        status.style.color = '#C0392B';
                        button.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        });
    </script>
</x-app-layout>
