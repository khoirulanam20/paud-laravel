@props(['badge' => '', 'title', 'subtitle' => ''])
<section class="guest-section border-b" style="border-color: var(--guest-border); background: linear-gradient(180deg, #fff 0%, var(--guest-bg) 100%);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
        @if($badge)
            <span class="guest-badge mb-4" data-guest-animate="fade-up">{{ $badge }}</span>
        @endif
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-[var(--guest-text)]" data-guest-animate="fade-up">
            {{ $title }}
        </h1>
        @if($subtitle)
            <p class="mt-4 text-base sm:text-lg max-w-2xl mx-auto text-[var(--guest-text-muted)]" data-guest-animate="fade-up">
                {{ $subtitle }}
            </p>
        @endif
    </div>
</section>
