@php
    use App\Support\GuestAscent;
    use App\Support\GuestBrand;
    $brand = GuestBrand::name();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $brand }} — Masuk &amp; Pendaftaran</title>
    <link rel="shortcut icon" href="{{ GuestAscent::asset('images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ GuestAscent::asset('icons/style.css') }}">
    <link rel="stylesheet" href="{{ GuestAscent::asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ GuestAscent::asset('css/output.css') }}">
    <style>
        .auth-ascent .input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.375rem;
            color: #2C2C2C;
        }
        .auth-ascent .input-field,
        .auth-ascent select.input-field,
        .auth-ascent textarea.input-field {
            width: 100%;
            border-radius: 10px;
            border: 2px solid #F2F2F2;
            padding: 0.875rem 1.25rem;
            font-size: 0.875rem;
            color: #686868;
            outline: none;
            background: #fff;
        }
        .auth-ascent .input-field:focus,
        .auth-ascent select.input-field:focus,
        .auth-ascent textarea.input-field:focus {
            border-color: color-mix(in srgb, var(--primary) 45%, #F2F2F2);
        }
        .auth-ascent .auth-field-wrap {
            position: relative;
            width: 100%;
        }
        .auth-ascent .auth-field-wrap .input-field {
            padding-right: 2.75rem;
        }
        .auth-ascent .auth-field-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #9ca3af;
            font-size: 0.875rem;
            line-height: 1;
        }
        .auth-ascent .auth-callout {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            padding: 0.875rem 1rem;
            border-radius: 10px;
            border: 2px solid #F2F2F2;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .auth-ascent .auth-callout__icon {
            flex-shrink: 0;
            width: 1.125rem;
            text-align: center;
            margin-top: 0.125rem;
        }
        .auth-ascent .auth-callout--warm {
            background: var(--warm, #FFF8F0);
            color: #6b7280;
        }
        .auth-ascent .auth-callout--warm .auth-callout__icon {
            color: var(--secondary-foreground, #0A6375);
        }
        .auth-ascent .auth-callout--muted {
            background: #fff;
            color: #6b7280;
        }
        .auth-ascent .auth-callout--danger {
            background: color-mix(in srgb, var(--destructive, #e11) 12%, #fff);
            color: #7a2e2e;
        }
        .auth-ascent .auth-callout--row {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
        @media (min-width: 640px) {
            .auth-ascent .auth-callout--row {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .auth-ascent .auth-callout-link {
            font-weight: 600;
            color: var(--green-foreground, #2d6a4f);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .auth-ascent .auth-callout-link:hover {
            text-decoration: underline;
        }
        .auth-ascent .alert-success {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            border-radius: 10px;
            font-size: 0.875rem;
            background: #E9FFB6;
            color: #385469;
            border: 2px solid #F2F2F2;
        }
        .auth-ascent .text-input-error {
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 0.25rem;
        }
        .auth-ascent .auth-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        @media (min-width: 1024px) {
            .auth-ascent .auth-topbar {
                margin-bottom: 3rem;
            }
        }
        .auth-ascent .auth-layout-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .auth-ascent .auth-layout-grid {
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
            }
            .auth-ascent .auth-aside-sticky {
                position: sticky;
                top: 1.5rem;
            }
        }
        .auth-ascent .auth-aside-sticky {
            display: none;
        }
        @media (min-width: 1024px) {
            .auth-ascent .auth-aside-sticky {
                display: block;
            }
        }
        .auth-ascent .auth-aside-visual img {
            display: block;
            width: 100%;
            max-width: 22rem;
            margin: 0 auto;
            height: auto;
        }
        .auth-ascent .auth-form-col {
            width: 100%;
            max-width: 28rem;
            margin-left: auto;
            margin-right: auto;
        }
        .auth-ascent .auth-form-col--wide {
            max-width: 36rem;
        }
        @media (min-width: 1024px) {
            .auth-ascent .auth-form-col {
                margin-right: 0;
            }
        }
    </style>
</head>
<body class="auth-ascent bg-warm min-h-screen" data-ascent-assets="{{ asset('ascent/assets') }}">
    <div class="container py-6 lg:py-10">
        <div class="auth-topbar">
            <a href="{{ route('guest.beranda') }}" class="inline-flex items-center gap-2 font-semibold text-lg font-jost hover:text-primary-foreground transition-colors">
                <i class="fa-solid fa-arrow-left text-sm" aria-hidden="true"></i>
                {{ $brand }}
            </a>
            <a href="{{ route('guest.kontak') }}" class="hidden sm:inline-flex items-center gap-2 border border-gray-200 rounded-md px-4 py-2 text-sm btn hover:text-cream-foreground !py-3 !px-5">
                <i class="fa-solid fa-headset" aria-hidden="true"></i> Bantuan
            </a>
        </div>

        <div class="auth-layout-grid">
            <aside class="auth-aside-sticky" aria-hidden="true">
                <div class="auth-aside-visual">
                    <img src="{{ GuestAscent::asset('images/contact/contact-1.png') }}" alt="" loading="lazy" width="464" height="539">
                </div>
            </aside>

            <div class="auth-form-col {{ ($maxWidth ?? 'max-w-md') === 'max-w-xl' ? 'auth-form-col--wide' : '' }}">
                <div class="bg-background shadow-[0px_5px_60px_0px_rgba(0,0,0,0.05)] rounded-[10px] lg:p-10 p-6 md:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
