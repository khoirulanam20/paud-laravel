@php use App\Support\GuestFeatures; @endphp
<section class="guest-section guest-section-alt">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10 md:mb-14" data-guest-animate="fade-up">
            <span class="guest-badge mb-3">Cara Mulai</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Live dalam 3 Langkah</h2>
            <p class="mt-3 text-[var(--guest-text-muted)]">Dari setup sekolah hingga orang tua terhubung — tanpa proses rumit.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5" data-guest-stagger>
            @foreach(GuestFeatures::onboardingSteps() as $step)
            <div class="guest-card relative" data-guest-hover data-guest-stagger-item>
                <span class="text-4xl font-extrabold opacity-15 absolute top-4 right-4">{{ $step['step'] }}</span>
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--guest-teal);">Langkah {{ $step['step'] }}</p>
                <h4 class="font-bold text-lg">{{ $step['title'] }}</h4>
                <p class="mt-2 text-sm text-[var(--guest-text-muted)] leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
