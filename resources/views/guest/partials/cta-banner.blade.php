@props([
    'title' => 'Siap Digitalisasi PAUD Anda?',
    'subtitle' => 'Hubungi tim kami untuk demo platform SIPP dan konsultasi kebutuhan lembaga Anda.',
    'primaryLabel' => 'Hubungi untuk Demo',
    'primaryRoute' => 'guest.kontak',
    'secondaryLabel' => 'Masuk',
    'secondaryRoute' => 'login',
])
<section class="guest-section">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="rounded-3xl p-8 sm:p-12 text-center text-white" style="background: linear-gradient(135deg, var(--guest-teal) 0%, var(--guest-teal-dark) 100%);" data-guest-animate="fade-up">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $title }}</h2>
            <p class="mt-3 text-base sm:text-lg opacity-90 max-w-xl mx-auto">{{ $subtitle }}</p>
            <div class="mt-8 flex flex-col sm:flex-row flex-wrap justify-center gap-3">
                <a href="{{ route($primaryRoute) }}" class="guest-btn w-full sm:w-auto cursor-pointer" style="background:#fff; color: var(--guest-teal);">{{ $primaryLabel }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="guest-btn guest-btn-secondary w-full sm:w-auto cursor-pointer" style="border-color:#fff; color:#fff; background:transparent;">Buka Dashboard</a>
                @else
                    <a href="{{ route($secondaryRoute) }}" class="guest-btn guest-btn-secondary w-full sm:w-auto cursor-pointer" style="border-color:#fff; color:#fff; background:transparent;">{{ $secondaryLabel }}</a>
                @endauth
            </div>
        </div>
    </div>
</section>
