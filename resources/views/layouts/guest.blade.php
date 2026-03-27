<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PAUD Manager') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased flex min-h-screen items-center justify-center py-10 px-4" style="background-color: #F5F0E8;">
        <div class="w-full {{ $maxWidth ?? 'max-w-md' }}">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl mb-4" style="background-color: #1A6B6B; box-shadow: 4px 4px 14px rgba(26,107,107,0.4);">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold" style="color: #2C2C2C;">PAUD Manager</h1>
                <p class="text-sm mt-1" style="color: #9E9790;">Sistem Manajemen Daycare & PAUD</p>
            </div>
            <!-- Auth Form Box -->
            <div class="card p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
