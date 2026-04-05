<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg
                    class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laporan Pencapaian Anak</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden mb-5">
            <div class="px-5 sm:px-6 py-5 border-b space-y-5" style="border-color:rgba(0,0,0,0.06);">
                <div class="space-y-1">
                    <h3 class="section-title mb-0">Filter laporan</h3>
                    <p class="text-sm leading-relaxed m-0 max-w-3xl" style="color:#9E9790;">Tanggal, anak, dan aspek
                        bersifat opsional. Reset lewat &quot;Tampilkan semua&quot;.</p>
                </div>
                <form method="get" action="{{ route('orangtua.pencapaian.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-2 lg:col-span-3 min-w-0">
                        <label class="input-label" for="ortu-penc-aspek">Aspek</label>
                        <select id="ortu-penc-aspek" name="aspek" class="input-field w-full min-w-0">
                            <option value="">Semua aspek</option>
                            <option value="{{ \App\Support\FilterAspekPencapaian::UMUM }}"
                                @selected($filterAspekRaw === \App\Support\FilterAspekPencapaian::UMUM)>Umum / tanpa aspek
                            </option>
                            @foreach($aspekPilihan as $asp)
                                <option value="{{ $asp }}" @selected($filterAspekRaw === $asp)>{{ $asp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2 flex flex-col gap-2 min-w-0">
                        <span class="input-label opacity-0 text-[0.65rem] leading-none max-sm:hidden"
                            aria-hidden="true">&nbsp;</span>
                        <button type="submit" class="btn-primary w-full">Terapkan</button>
                        @if($filterAktif)
                            <a href="{{ route('orangtua.pencapaian.index') }}"
                                class="btn-secondary w-full text-center">Tampilkan semua</a>
                        @endif
                    </div>
                </form>
            </div>
            @if($filterAktif)
                <div class="px-6 py-3 text-sm space-y-1" style="background:#FAF6F0; color:#6B6560;">
                    @if($filterTanggalAktif)
                        <p> Rentang tanggal input: <strong
                                style="color:#2C2C2C;">{{ \Carbon\Carbon::parse($tanggalDari)->translatedFormat('d M Y') }}</strong>
                            – <strong
                                style="color:#2C2C2C;">{{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d M Y') }}</strong>
                        </p>
                    @endif
                    @if($filterAnakId)
                        <p>Anak: <strong
                                style="color:#2C2C2C;">{{ $anakList->firstWhere('id', $filterAnakId)?->name ?? '—' }}</strong>
                        </p>
                    @endif
                    @if($filterAspek)
                        <p>Aspek: <strong
                                style="color:#2C2C2C;">{{ $filterAspek === \App\Support\FilterAspekPencapaian::UMUM ? 'Umum / tanpa aspek' : $filterAspek }}</strong>
                        </p>
                    @endif
                </div>
            @endif
        </div>
        <details class="card mb-5 overflow-hidden">
            <summary class="px-5 py-4 cursor-pointer list-none font-semibold text-sm flex items-center gap-2"
                style="color:#1A6B6B;">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Penjelasan tingkat penilaian
                <span class="text-xs font-normal ml-auto" style="color:#9E9790;">Klik untuk membuka</span>
            </summary>
            <div class="px-5 pb-5 pt-0 border-t text-sm space-y-3 leading-relaxed"
                style="border-color:rgba(0,0,0,0.06); color:#5A5A5A;">
                <p class="pt-4">Nilai mengacu pada penilaian perkembangan anak usia dini (biasanya dicatat per indikator
                    matrikulasi).</p>
                <ul class="space-y-2 pl-1">
                    <li><span class="inline-block font-bold px-2 py-0.5 rounded text-xs mr-2 leading-snug"
                            style="background:#FAD7D2;">Belum Berkembang</span> — Anak belum menunjukkan perilaku sesuai
                        indikator yang diharapkan pada tahap ini.</li>
                    <li><span class="inline-block font-bold px-2 py-0.5 rounded text-xs mr-2 leading-snug"
                            style="background:#FDE9BC;">Mulai Berkembang</span> — Anak mulai menunjukkan kemampuan;
                        masih perlu bimbingan dan pengulangan.</li>
                    <li><span class="inline-block font-bold px-2 py-0.5 rounded text-xs mr-2 leading-snug"
                            style="background:#D0E8E8;">Berkembang Sesuai Harapan</span> — Anak sudah konsisten
                        menunjukkan perilaku sesuai indikator.</li>
                    <li><span class="inline-block font-bold px-2 py-0.5 rounded text-xs mr-2 leading-snug"
                            style="background:#C5E8C5;">Berkembang Sangat Baik</span> — Anak menunjukkan kemampuan di
                        atas harapan umur/tahap untuk indikator tersebut.</li>
                </ul>
            </div>
        </details>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Rapor per kegiatan &amp; aspek</h3>
                <p class="section-subtitle">Setiap kartu = satu kegiatan; di dalamnya nilai per indikator matrikulasi
                    beserta tujuan &amp; strategi jika tersedia.</p>
            </div>
            <div class="divide-y" style="border-color:rgba(0,0,0,0.06);">
                @forelse($groupedPencapaian as $bundleKey => $rows)
                    @php $first = $rows->first(); @endphp
                    <div class="px-6 py-5">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                            <div class="shrink-0">
                                @if($first->photo)
                                    <img src="{{ Storage::url($first->photo) }}"
                                        class="h-16 w-16 object-cover rounded-xl shadow-sm cursor-pointer"
                                        onclick="window.open(this.src)">
                                @else
                                    <div
                                        class="h-16 w-16 bg-gray-100 rounded-xl flex items-center justify-center text-gray-300">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <x-foto-profil :path="$first->anak->photo ?? null" :name="$first->anak->name ?? '?'"
                                        size="md" />
                                    <span class="font-bold" style="color:#2C2C2C;">{{ $first->anak->name ?? '-' }}</span>
                                    @if($first->anak && $first->anak->dob)
                                        <span class="text-[10px] font-bold text-[#1A6B6B]">({{ $first->anak->age }})</span>
                                    @endif
                                    <span class="text-xs"
                                        style="color:#9E9790;">{{ \Carbon\Carbon::parse($first->created_at)->translatedFormat('d M Y') }}</span>
                                </div>
                                @if($first->kegiatan)
                                    <div class="font-semibold text-sm mb-0.5" style="color:#1A6B6B;">
                                        {{ $first->kegiatan->title }}</div>
                                    <div class="text-xs mb-3 flex flex-wrap items-center gap-2" style="color:#9E9790;">
                                        <span>Kegiatan {{ \Carbon\Carbon::parse($first->kegiatan->date)->format('d M Y') }}
                                            ·</span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <x-foto-profil :path="$first->pengajar->photo ?? null" :name="$first->pengajar->name ?? 'Guru'" size="xs" />
                                            <span>{{ $first->pengajar->name ?? 'Guru' }}</span>
                                        </span>
                                    </div>
                                @endif
                                <div class="rounded-xl border overflow-hidden" style="border-color:rgba(0,0,0,0.08);">
                                    {{-- Desktop View --}}
                                    <div class="hidden sm:block overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr style="background:#F5F5F3;">
                                                    <th class="text-left px-3 py-2 text-xs font-semibold"
                                                        style="color:#5A5A5A;">Aspek / indikator</th>
                                                    <th class="text-left px-3 py-2 text-xs font-semibold w-24"
                                                        style="color:#5A5A5A;">Nilai</th>
                                                    <th class="text-left px-3 py-2 text-xs font-semibold"
                                                        style="color:#5A5A5A;">Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($rows->filter(fn($p) => \App\Support\FilterAspekPencapaian::rowMatches($filterAspek, $p))->sortBy(fn($p) => ($p->matrikulasi->aspek ?? '') . ($p->matrikulasi->indicator ?? '')) as $p)
                                                    <tr class="border-t" style="border-color:rgba(0,0,0,0.05);">
                                                        <td class="px-3 py-2 align-top">
                                                            @if($p->matrikulasi)
                                                                <span class="font-semibold text-xs block"
                                                                    style="color:#1A6B6B;">{{ $p->matrikulasi->aspek ?: 'Aspek' }}</span>
                                                                <span class="text-xs mt-0.5 block"
                                                                    style="color:#5A5A5A;">{{ $p->matrikulasi->indicator }}</span>
                                                                @if(filled($p->matrikulasi->description))
                                                                    <p class="text-xs mt-1.5" style="color:#6B6560;">
                                                                        {{ $p->matrikulasi->description }}</p>
                                                                @endif
                                                                @if(filled($p->matrikulasi->tujuan))
                                                                    <p class="text-xs mt-2 rounded-lg px-2 py-1.5"
                                                                        style="background:#F5F5F3; color:#4A4A4A;"><strong
                                                                            style="color:#1A6B6B;">Tujuan pembelajaran:</strong>
                                                                        {{ $p->matrikulasi->tujuan }}</p>
                                                                @endif
                                                                @if(filled($p->matrikulasi->strategi))
                                                                    <p class="text-xs mt-1.5 rounded-lg px-2 py-1.5"
                                                                        style="background:#F0FAFA; color:#4A4A4A;"><strong
                                                                            style="color:#1A6B6B;">Strategi:</strong>
                                                                        {{ $p->matrikulasi->strategi }}</p>
                                                                @endif
                                                            @else
                                                                <span class="text-xs italic" style="color:#9E9790;">Catatan lama</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 align-top">
                                                            <span
                                                                class="text-xs font-bold px-2 py-1 rounded inline-block leading-snug max-w-[12rem]"
                                                                style="background:{{ \App\Support\LabelSkorPencapaian::color($p->score) }};">{{ \App\Support\LabelSkorPencapaian::label($p->score) }}</span>
                                                        </td>
                                                        <td class="px-3 py-2 text-xs align-top" style="color:#6B6560;">
                                                            {{ $p->feedback ?: '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Mobile View --}}
                                    <div class="sm:hidden divide-y divide-gray-50">
                                        @foreach($rows->filter(fn($p) => \App\Support\FilterAspekPencapaian::rowMatches($filterAspek, $p))->sortBy(fn($p) => ($p->matrikulasi->aspek ?? '') . ($p->matrikulasi->indicator ?? '')) as $p)
                                            <div class="p-4 bg-white space-y-3">
                                                <div class="flex justify-between items-start gap-3">
                                                    <div class="min-w-0">
                                                        <span class="text-[10px] font-bold text-[#1A6B6B] uppercase tracking-wider block mb-0.5">{{ $p->matrikulasi->aspek ?: 'Aspek' }}</span>
                                                        <h4 class="text-sm font-bold text-gray-800 leading-snug">{{ $p->matrikulasi->indicator }}</h4>
                                                    </div>
                                                    <span class="shrink-0 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-tighter text-center" style="background:{{ \App\Support\LabelSkorPencapaian::color($p->score) }};">
                                                        {{ \App\Support\LabelSkorPencapaian::label($p->score) }}
                                                    </span>
                                                </div>

                                                @if($p->feedback)
                                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Catatan Guru</span>
                                                        <p class="text-xs text-gray-600 leading-relaxed italic">&quot;{{ $p->feedback }}&quot;</p>
                                                    </div>
                                                @endif

                                                @if($p->matrikulasi)
                                                    <div class="space-y-2">
                                                        @if(filled($p->matrikulasi->tujuan))
                                                            <div class="p-2.5 rounded-xl border border-gray-100 bg-[#F5F5F3]/50">
                                                                <span class="text-[9px] font-bold text-[#1A6B6B] uppercase tracking-widest block mb-1">Tujuan</span>
                                                                <p class="text-xs text-gray-600">{{ $p->matrikulasi->tujuan }}</p>
                                                            </div>
                                                        @endif
                                                        @if(filled($p->matrikulasi->strategi))
                                                            <div class="p-2.5 rounded-xl border border-teal-50 bg-[#F0FAFA]/50">
                                                                <span class="text-[9px] font-bold text-[#1A6B6B] uppercase tracking-widest block mb-1">Strategi</span>
                                                                <p class="text-xs text-gray-600">{{ $p->matrikulasi->strategi }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-16 text-center text-sm" style="color:#9E9790;">Belum ada laporan evaluasi.</div>
                @endforelse
            </div>
            @if($groupedPencapaian->hasPages())
                <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $groupedPencapaian->links() }}
            </div>@endif
        </div>
    </div>
</x-app-layout>