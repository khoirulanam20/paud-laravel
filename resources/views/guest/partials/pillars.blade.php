@props(['heading' => true])
@php use App\Support\GuestFeatures; @endphp
<section class="guest-section {{ $heading ? '' : 'pt-0' }}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        @if($heading)
        <div class="text-center mb-10 md:mb-14" data-guest-animate="fade-up">
            <span class="guest-badge mb-3">Nilai Utama</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Tiga Alasan Sekolah Memilih SIPP</h2>
            <p class="mt-3 text-[var(--guest-text-muted)] max-w-2xl mx-auto">Bukan sekadar banyak fitur — SIPP fokus menghubungkan orang tua, mempermudah kerja tim sekolah, dan mengotomasi pekerjaan rutin dengan AI.</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" data-guest-stagger>
            @foreach(GuestFeatures::pillars() as $pillar)
            <div class="guest-card flex flex-col h-full" data-guest-hover data-guest-stagger-item>
                <div class="guest-icon-wrap mb-4">
                    @include('guest.partials.icon', ['path' => $pillar['icon'], 'class' => 'h-6 w-6'])
                </div>
                <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--guest-teal);">{{ $pillar['tagline'] }}</p>
                <h3 class="text-lg font-extrabold">{{ $pillar['title'] }}</h3>
                <p class="mt-2 text-sm text-[var(--guest-text-muted)] leading-relaxed flex-1">{{ $pillar['desc'] }}</p>
                <ul class="mt-5 space-y-2 border-t pt-4" style="border-color: var(--guest-border);">
                    @foreach($pillar['highlights'] as $highlight)
                    <li class="flex gap-2 text-sm text-[var(--guest-text-muted)]">
                        <span class="shrink-0 mt-0.5 font-bold" style="color: var(--guest-teal);">&#10003;</span>
                        <span>{{ $highlight }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>
</section>
