@php
    use App\Support\GuestSeo;
    $pageSeo = GuestSeo::forInnerPage(
        $cms,
        'kontak',
        'Hubungi Kami',
        route('guest.kontak'),
        $cms['seo_kontak_description'] ?? '',
    );
@endphp
<x-guest-ascent :cms="$cms" title="Kontak" :metaDesc="$pageSeo['description']" :canonical="route('guest.kontak')">
    @include('guest.ascent.partials.page-hero', [
        'title' => $pageSeo['h1'],
        'breadcrumbLabel' => 'Kontak',
    ])
    @include('guest.ascent.partials.contact', ['cms' => $cms])
    @include('guest.ascent.partials.stay-cta', ['cms' => $cms])
</x-guest-ascent>
