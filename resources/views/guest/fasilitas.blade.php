@php
    use App\Support\GuestBrand;
    $metaDesc = 'Fitur lengkap '.GuestBrand::NAME.' — portal orang tua, operasional sekolah, keuangan PSAK, presensi, dan asisten AI.';
@endphp
<x-guest-ascent :cms="$cms" title="Fitur" :metaDesc="$metaDesc" :canonical="route('guest.fasilitas')">
    @include('guest.ascent.partials.page-hero', [
        'title' => 'Fitur',
        'breadcrumbLabel' => 'Fitur',
    ])
    @include('guest.ascent.partials.services-page', ['cms' => $cms])
    @include('guest.ascent.partials.stay-cta', ['cms' => $cms])
</x-guest-ascent>
