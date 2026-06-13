@php
    use App\Support\IndonesianMonths;

    $months = IndonesianMonths::NAMES;
    $periodeLabel = IndonesianMonths::label($bulan, $tahun);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">
                Monev{{ $selectedAnak ? ' — ' . $selectedAnak->name : '' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden mb-6">
            <div class="px-5 sm:px-6 py-5 border-b space-y-5" style="background:#FAF6F0; border-color: rgba(0,0,0,0.06);">
                <div class="space-y-1">
                    <h3 class="text-xl font-bold" style="color:#2C2C2C;">Ringkasan Perkembangan</h3>
                    <p class="text-sm leading-relaxed m-0 max-w-3xl" style="color:#9E9790;">
                        Laporan monitoring matrikulasi dari sekolah untuk periode yang dipilih.
                    </p>
                </div>

                <form method="get" action="{{ route('orangtua.monev.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                    @if($anaks->count() > 1)
                        <div class="sm:col-span-2 lg:col-span-4 min-w-0">
                            <label class="input-label" for="ortu-monev-anak">Anak</label>
                            <select id="ortu-monev-anak" name="anak_id" class="input-field w-full">
                                @foreach($anaks as $anak)
                                    <option value="{{ $anak->id }}" @selected($selectedAnak?->id === $anak->id)>{{ $anak->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($selectedAnak)
                        <input type="hidden" name="anak_id" value="{{ $selectedAnak->id }}">
                    @endif
                    <div class="sm:col-span-1 lg:col-span-3 min-w-0">
                        <label class="input-label" for="ortu-monev-bulan">Bulan</label>
                        <select id="ortu-monev-bulan" name="bulan" class="input-field w-full">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" @selected($bulan == $num)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1 lg:col-span-2 min-w-0">
                        <label class="input-label" for="ortu-monev-tahun">Tahun</label>
                        <select id="ortu-monev-tahun" name="tahun" class="input-field w-full">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 flex flex-col gap-2 min-w-0">
                        <span class="input-label opacity-0 text-[0.65rem] leading-none max-sm:hidden" aria-hidden="true">&nbsp;</span>
                        <button type="submit" class="btn-primary w-full text-sm">Tampilkan</button>
                    </div>
                </form>
            </div>
            <div class="px-5 sm:px-6 py-3" style="border-color: rgba(0,0,0,0.06);">
                <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#E8F5F5; color:#1A6B6B;">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    {{ $periodeLabel }}
                </span>
            </div>
        </div>

        @if($anaks->isEmpty())
            <div class="card px-6 py-16 text-center text-sm" style="color:#9E9790;">
                Belum ada data anak aktif.
            </div>
        @elseif(!$summary)
            <div class="card px-6 py-16 text-center">
                <p class="text-sm font-medium" style="color:#2C2C2C;">Ringkasan belum tersedia</p>
                <p class="text-sm mt-2" style="color:#9E9790;">
                    Laporan Monev untuk <strong>{{ $selectedAnak->name }}</strong> pada {{ $periodeLabel }} belum dipublikasikan sekolah.
                </p>
            </div>
        @else
            @include('monev._show-content', [
                'anak' => $selectedAnak,
                'summary' => $summary,
                'pdfRoute' => route('orangtua.monev.export-pdf', ['anak' => $selectedAnak->id, 'tahun' => $tahun, 'bulan' => $bulan]),
                'showBackLink' => false,
            ])
        @endif
    </div>
</x-app-layout>
