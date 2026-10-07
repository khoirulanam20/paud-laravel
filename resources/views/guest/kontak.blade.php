@php
    use App\Support\GuestBrand;
    $metaDesc = 'Hubungi tim '.GuestBrand::NAME.' untuk demo, penawaran, dan konsultasi implementasi platform PAUD Anda.';
@endphp
<x-guest-ascent :cms="$cms" title="Kontak" :metaDesc="$metaDesc" :canonical="route('guest.kontak')">
    @include('guest.ascent.partials.page-hero', [
        'title' => 'Hubungi Kami',
        'breadcrumbLabel' => 'Kontak',
    ])
    @include('guest.ascent.partials.contact', ['cms' => $cms])
    @include('guest.ascent.partials.stay-cta', ['cms' => $cms])
</x-guest-ascent>
