<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="guest-site">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SIPP — Masuk & Pendaftaran</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        @vite(['resources/css/guest.css', 'resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="guest-body flex min-h-screen items-center justify-center py-10 px-4">
        <div class="w-full {{ $maxWidth ?? 'max-w-md' }}">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full mb-4" style="background: var(--guest-sage);">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h1 class="text-2xl guest-heading font-bold" style="color: var(--guest-text);">SIPP</h1>
                <p class="text-sm mt-1" style="color: var(--guest-text-muted);">Sistem Informasi Pengelolaan PAUD</p>
            </div>
            <div class="guest-card p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
