@php
    use App\Support\GuestSeo;
    $pageSeo = GuestSeo::forInnerPage(
        $cms,
        'fasilitas',
        'Fitur',
        route('guest.fasilitas'),
        $cms['seo_fasilitas_description'] ?? '',
    );
@endphp
<x-guest-ascent :cms="$cms" title="Fitur" :metaDesc="$pageSeo['description']" :canonical="route('guest.fasilitas')">
    @include('guest.ascent.partials.page-hero', [
        'title' => $pageSeo['h1'],
        'breadcrumbLabel' => 'Fitur',
    ])
    @include('guest.ascent.partials.services-page', ['cms' => $cms])
    @include('guest.ascent.partials.stay-cta', ['cms' => $cms])
</x-guest-ascent>
