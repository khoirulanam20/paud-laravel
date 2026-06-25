<x-guest-public :cms="$cms" title="Beranda">
    @include('guest.partials.hero', ['cms' => $cms])
    @include('guest.partials.about-snippet', ['cms' => $cms])
    @include('guest.partials.services-grid', ['cms' => $cms, 'title' => 'Layanan SIPP', 'subtitle' => 'Empat pilar yang mendukung operasional PAUD Anda setiap hari.'])
    @include('guest.partials.reviews')
    @include('guest.partials.onboarding')

   


    @include('guest.partials.cta-banner')
</x-guest-public>
