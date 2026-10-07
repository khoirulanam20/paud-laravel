@php
    use App\Support\GuestBrand;
    $metaDesc = 'Kenali '.GuestBrand::NAME.' — platform PAUD yang menghubungkan orang tua dan sekolah, mempermudah operasional, didukung AI.';
@endphp
<x-guest-ascent :cms="$cms" title="Tentang" :metaDesc="$metaDesc" :canonical="route('guest.tentang')">
    @include('guest.ascent.partials.page-hero', [
        'title' => 'Tentang Kami',
        'breadcrumbLabel' => 'Tentang',
    ])
    @include('guest.ascent.partials.about', ['cms' => $cms, 'pageLayout' => true, 'showLearnMore' => false])
    @include('guest.ascent.partials.stats', ['cms' => $cms])
    @include('guest.ascent.partials.testimonials', ['cms' => $cms])
    @include('guest.ascent.partials.stay-cta', ['cms' => $cms])
</x-guest-ascent>
