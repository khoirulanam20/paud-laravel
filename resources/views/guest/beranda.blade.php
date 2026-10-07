<x-guest-ascent :cms="$cms" title="Beranda">
    @include('guest.ascent.partials.banner', ['cms' => $cms])
    @include('guest.ascent.partials.stats', ['cms' => $cms])
    @include('guest.ascent.partials.about', ['cms' => $cms])
    @include('guest.ascent.partials.programs', ['cms' => $cms])
    @include('guest.ascent.partials.services', ['cms' => $cms])
    @include('guest.ascent.partials.faq', ['cms' => $cms])
    @include('guest.ascent.partials.student-age', ['cms' => $cms])
    @include('guest.ascent.partials.testimonials', ['cms' => $cms])
    @include('guest.ascent.partials.blog', ['cms' => $cms])
    @include('guest.ascent.partials.newsletter', ['cms' => $cms])
</x-guest-ascent>
