@props(['cms' => [], 'title' => 'Beranda'])
@php
    use App\Support\GuestAscent;
    use App\Support\GuestBrand;
    use App\Support\GuestSeo;
    $brand = GuestBrand::name();
    $seo = GuestSeo::forHome($cms);
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                'name' => $brand,
                'url' => url('/'),
                'logo' => GuestAscent::asset('images/logo/logo.png'),
                'description' => $cms['footer_text'] ?? '',
                'email' => $cms['kontak_email'] ?? null,
                'telephone' => $cms['kontak_telepon'] ?? null,
                'address' => isset($cms['kontak_alamat']) ? ['@type' => 'PostalAddress', 'streetAddress' => $cms['kontak_alamat']] : null,
            ],
            [
                '@type' => 'WebSite',
                'name' => $brand,
                'url' => url('/'),
                'inLanguage' => 'id-ID',
            ],
            [
                '@type' => 'SoftwareApplication',
                'name' => $brand,
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'description' => $seo['description'],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'IDR',
                    'description' => 'Hubungi kami untuk informasi paket',
                ],
            ],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <x-guest-seo-head :cms="$cms" :page-title="$seo['title']" :canonical="url('/')" :json-ld="$jsonLd" />
    <link rel="shortcut icon" href="{{ GuestAscent::asset('images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ GuestAscent::asset('icons/style.css') }}">
    <link rel="stylesheet" href="{{ GuestAscent::asset('css/animate.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ GuestAscent::asset('css/output.css') }}">
    <style>
        .guest-daftar-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(4px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
            pointer-events: none;
        }
        .guest-daftar-dropdown.is-open .guest-daftar-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }
        .guest-daftar-dropdown.is-open .guest-daftar-chevron {
            transform: rotate(180deg);
        }
    </style>
</head>
<body data-ascent-assets="{{ asset('ascent/assets') }}">
    @include('guest.ascent.partials.header', ['cms' => $cms])
    <main>{{ $slot }}</main>
    @include('guest.ascent.partials.footer', ['cms' => $cms])
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @vite(['resources/js/guest-ascent.js'])
</body>
</html>
