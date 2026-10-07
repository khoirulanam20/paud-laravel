@props(['cms' => [], 'title' => 'Beranda', 'metaDesc' => '', 'canonical' => null])
@php
    use App\Support\GuestAscent;
    use App\Support\GuestBrand;
    use App\Support\GuestSeo;
    $brand = GuestBrand::name();
    $isHome = request()->routeIs('guest.beranda');
    if ($isHome) {
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
    } else {
        $homeSeo = GuestSeo::forHome($cms);
        $pageTitle = $title;
        $description = trim($metaDesc) !== ''
            ? $metaDesc
            : $brand.' — Sistem Informasi PAUD terpadu untuk lembaga, admin sekolah, pengajar, dan orang tua.';
        $seo = GuestSeo::forPage(
            $pageTitle,
            $description,
            $canonical ?? url()->current(),
            $homeSeo['og_image_url'],
        );
        $jsonLd = null;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <x-guest-seo-head
        :cms="$cms"
        :page-title="$seo['title']"
        :meta-description="$seo['description']"
        :canonical="$seo['canonical']"
        :og-image-url="$seo['og_image_url']"
        :json-ld="$jsonLd"
    />
    <link rel="shortcut icon" href="{{ GuestAscent::asset('images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ GuestAscent::asset('icons/style.css') }}">
    <link rel="stylesheet" href="{{ GuestAscent::asset('css/animate.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ GuestAscent::asset('css/output.css') }}">
    <style>
        .guest-daftar-menu {
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
        /* ponytail: batas ukuran upload CMS per section layout Ascent */
        .guest-hero-side {
            width: 100%;
            max-width: 9.5rem;
        }
        .guest-cms-img--hero-side {
            display: block;
            width: 100%;
            height: auto;
            max-height: 10.5rem;
            object-fit: contain;
        }
        @media (min-width: 1280px) {
            .guest-hero-side {
                max-width: 11rem;
            }
            .guest-cms-img--hero-side {
                max-height: 12rem;
            }
        }
        .guest-cms-img--about {
            width: 100%;
            max-width: 36rem;
            aspect-ratio: 4 / 3;
            max-height: 440px;
            object-fit: cover;
        }
        .guest-cms-img--about-collage {
            width: 100%;
            max-width: 17.5rem;
            aspect-ratio: 3 / 4;
            max-height: 360px;
            object-fit: cover;
        }
        .guest-cms-img--faq {
            width: 100%;
            max-width: 528px;
            aspect-ratio: 4 / 3;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .guest-cms-img--blog-thumb {
            width: 100%;
            aspect-ratio: 4 / 3;
            max-height: 10rem;
            object-fit: cover;
            border-radius: 0.375rem;
        }
        .guest-cms-img--blog-feature {
            width: 100%;
            aspect-ratio: 16 / 10;
            max-height: 22rem;
            object-fit: cover;
            border-radius: 0.375rem;
        }
        .guest-cms-img--newsletter {
            display: block;
            width: auto;
            max-width: 100%;
            max-height: 340px;
            margin-inline: auto;
            object-fit: contain;
        }
        .guest-cms-img--gallery-slide {
            width: 100%;
            height: 16rem;
            max-height: 20rem;
            object-fit: cover;
            border-radius: 10px;
        }
        @media (min-width: 640px) {
            .guest-cms-img--gallery-slide {
                height: 20rem;
            }
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
