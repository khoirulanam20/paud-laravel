<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg
                    class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laporan Pencapaian Anak</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showImageModal: false, activeImage: '', activeDownloadUrl: null }">
        <div class="card mb-5">
            <div class="px-5 sm:px-6 py-5 border-b space-y-5" style="border-color:rgba(0,0,0,0.06);">
                <div class="space-y-1">
                    <h3 class="section-title mb-0">Filter laporan</h3>
                    <p class="text-sm leading-relaxed m-0 max-w-3xl" style="color:#9E9790;">Tanggal, anak, dan aspek
                        bersifat opsional. Reset lewat &quot;Tampilkan semua&quot;.</p>
                </div>
                <form data-tour="ortu-pencapaian-filter" method="get" action="{{ route('orangtua.pencapaian.index') }}"
                    class="filter-toolbar-inline">
                    @if($anakList->count() > 1)
                        <div class="filter-toolbar-field">
                            <label class="input-label" for="ortu-penc-anak">Anak</label>
                            <select id="ortu-penc-anak" name="filter_anak_id" class="input-field w-full h-11 min-w-0">
                                <option value="">Semua Anak</option>
                                @foreach($anakList as $anak)
                                    <option value="{{ $anak->id }}" @selected($filterAnakId === $anak->id)>{{ $anak->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="filter-toolbar-field">
                        <label class="input-label" for="ortu-penc-tanggal-dari">Dari</label>
                        <input id="ortu-penc-tanggal-dari" type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="input-field w-full h-11 min-w-0">
                    </div>
                    <div class="filter-toolbar-field">
                        <label class="input-label" for="ortu-penc-tanggal-sampai">Sampai</label>
                        <input id="ortu-penc-tanggal-sampai" type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="input-field w-full h-11 min-w-0">
                    </div>
                    <div class="filter-toolbar-field">
                        <label class="input-label" for="ortu-penc-aspek">Aspek</label>
                        <select id="ortu-penc-aspek" name="aspek" class="input-field w-full h-11 min-w-0">
                            <option value="">Semua aspek</option>
                            <option value="{{ \App\Support\FilterAspekPencapaian::UMUM }}"
                                @selected($filterAspekRaw === \App\Support\FilterAspekPencapaian::UMUM)>Umum / tanpa aspek
                            </option>
                            @foreach($aspekPilihan as $asp)
                                <option value="{{ $asp }}" @selected($filterAspekRaw === $asp)>{{ $asp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-toolbar-actions">
                        <button type="submit" class="btn-primary h-11 w-11 p-0 shrink-0" title="Terapkan" aria-label="Terapkan filter">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                        @if($filterAktif)
                            <a href="{{ route('orangtua.pencapaian.index') }}"
                                class="btn-secondary h-11 w-11 p-0 shrink-0 inline-flex items-center justify-center"
                                title="Tampilkan semua" aria-label="Tampilkan semua">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </a>
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
                    @forelse($skalaLegenda as $sk)
                    <li>
                        <span class="inline-block font-bold px-2 py-0.5 rounded text-xs mr-2 leading-snug"
                            style="background:{{ $sk->color }};">{{ $sk->label }}</span>
                        <span class="text-gray-500">({{ $sk->code }})</span>
                        — Penilaian capaian pada indikator matrikulasi.
                    </li>
                    @empty
                    <li class="text-gray-500">Belum ada skala capaian yang dikonfigurasi sekolah.</li>
                    @endforelse
                </ul>
            </div>
        </details>

        <div class="card overflow-hidden" data-tour="ortu-pencapaian-reports">
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
                                        @click="activeImage = '{{ Storage::url($first->photo) }}'; activeDownloadUrl = '{{ route('orangtua.pencapaian.photos.download-bundle', ['anak_id' => $first->anak_id, 'kegiatan_id' => $first->kegiatan_id]) }}'; showImageModal = true">
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
                                                                style="background:{{ \App\Support\LabelSkorPencapaian::color($p->score, $p->anak?->sekolah_id) }};">{{ \App\Support\LabelSkorPencapaian::label($p->score, $p->anak?->sekolah_id) }}</span>
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
                                                {{-- Aspek & Indikator --}}
                                                <div class="min-w-0">
                                                    <span class="text-[10px] font-bold text-[#1A6B6B] uppercase tracking-wider block mb-0.5">{{ $p->matrikulasi->aspek ?: 'Aspek' }}</span>
                                                    <h4 class="text-sm font-bold text-gray-800 leading-snug">{{ $p->matrikulasi->indicator }}</h4>
                                                </div>

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

                                                {{-- Skala Pencapaian --}}
                                                <div>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Skala Pencapaian</span>
                                                    <span class="text-[10px] font-bold px-2 py-1 rounded uppercase tracking-tighter inline-block leading-snug" style="background:{{ \App\Support\LabelSkorPencapaian::color($p->score, $p->anak?->sekolah_id) }};">
                                                        {{ \App\Support\LabelSkorPencapaian::label($p->score, $p->anak?->sekolah_id) }}
                                                    </span>
                                                </div>

                                                {{-- Catatan Guru --}}
                                                @if($p->feedback)
                                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Catatan Guru</span>
                                                        <p class="text-xs text-gray-600 leading-relaxed italic">&quot;{{ $p->feedback }}&quot;</p>
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
            <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <x-per-page-selector :paginator="$groupedPencapaian" />
                {{ $groupedPencapaian->links() }}
            </div>
        </div>

        <x-image-lightbox />
    </div>
</x-app-layout>
