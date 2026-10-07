@php
    use App\Support\GuestSeo;
    $pageSeo = GuestSeo::forInnerPage(
        $cms,
        'tentang',
        'Tentang Kami',
        route('guest.tentang'),
        $cms['seo_tentang_description'] ?? '',
    );
@endphp
<x-guest-ascent :cms="$cms" title="Tentang" :metaDesc="$pageSeo['description']" :canonical="route('guest.tentang')">
    @include('guest.ascent.partials.page-hero', [
        'title' => $pageSeo['h1'],
        'breadcrumbLabel' => 'Tentang',
    ])
    @include('guest.ascent.partials.about', ['cms' => $cms, 'pageLayout' => true, 'showLearnMore' => false])
    @include('guest.ascent.partials.stats', ['cms' => $cms])
    @include('guest.ascent.partials.testimonials', ['cms' => $cms])
    @include('guest.ascent.partials.stay-cta', ['cms' => $cms])
</x-guest-ascent>
