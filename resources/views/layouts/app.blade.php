@php
    $user = auth()->user();
    // Badge: pending enrollments for Admin Sekolah
    $pendingCount = 0;
    if ($user && $user->hasRole('Admin Sekolah') && $user->sekolah_id) {
        $pendingCount = \App\Models\Anak::where('sekolah_id', $user->sekolah_id)->where('status', 'pending')->count();
    }
    $roleNavItems = [];
    if ($user && $user->hasRole('Lembaga')) {
        $roleNavItems = array_merge($roleNavItems, [
            ['route' => 'lembaga.sekolah.index', 'label' => 'Sekolah', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'pattern' => 'lembaga.sekolah.*'],
            ['route' => 'lembaga.admin-sekolah.index', 'label' => 'Admin Sekolah', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'pattern' => 'lembaga.admin-sekolah.*'],
            ['route' => 'lembaga.kritik-saran.index', 'label' => 'Kritik & Saran', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'pattern' => 'lembaga.kritik-saran.*'],
        ]);
    }
    if ($user && $user->hasRole('Admin Sekolah')) {
        $roleNavItems = array_merge($roleNavItems, [
            ['group' => 'Manajemen Siswa', 'items' => [
                ['route' => 'admin.anak.index', 'label' => 'Siswa Sekolah', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'pattern' => 'admin.anak.*'],
                ['route' => 'admin.pendaftaran.index', 'label' => 'Pendaftaran', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7l3 3 3-3', 'pattern' => 'admin.pendaftaran.*', 'badge' => $pendingCount],
                ['route' => 'admin.kelas.index', 'label' => 'Kelola Kelas', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'pattern' => 'admin.kelas.*'],
                ['route' => 'admin.presensi.index', 'label' => 'Rekap Presensi', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'pattern' => 'admin.presensi.*'],
                ['route' => 'admin.kesehatan.index', 'label' => 'Kesehatan Siswa', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'pattern' => 'admin.kesehatan.*'],
            ]],
            ['group' => 'Kurikulum', 'items' => [
                ['route' => 'admin.matrikulasi.index', 'label' => 'Matrikulasi', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'pattern' => 'admin.matrikulasi.*'],
                ['route' => 'admin.kegiatan.index', 'label' => 'Jurnal (lihat)', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'pattern' => 'admin.kegiatan.*'],
            ]],
            ['group' => 'Lembaga & Guru', 'items' => [
                ['route' => 'admin.pengajar.index', 'label' => 'Data Pengajar', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'pattern' => 'admin.pengajar.*'],
                ['route' => 'admin.presensi-guru.index', 'label' => 'Presensi Guru', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'pattern' => 'admin.presensi-guru.*'],
                ['route' => 'admin.sarana.index', 'label' => 'Sarana', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'pattern' => 'admin.sarana.*'],
                ['route' => 'admin.menu-makanan.index', 'label' => 'Menu Makanan', 'icon' => 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z', 'pattern' => 'admin.menu-makanan.*'],
            ]],
            ['group' => 'Keuangan & Masukan', 'items' => [
                ['route' => 'admin.cashflow.index', 'label' => 'Cashflow', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402-2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'admin.cashflow.*'],
                ['route' => 'admin.kritik-saran.index', 'label' => 'Kritik & Saran', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'pattern' => 'admin.kritik-saran.*'],
            ]],
        ]);
    }
    if ($user && $user->hasRole('Admin Kelas')) {
        $roleNavItems = array_merge($roleNavItems, [
            ['route' => 'adminkelas.presensi.index', 'label' => 'Presensi Kelasku', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'pattern' => 'adminkelas.presensi.*'],
            ['route' => 'adminkelas.kesehatan.index', 'label' => 'Kesehatan Kelasku', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'pattern' => 'adminkelas.kesehatan.*'],
        ]);
    }
    if ($user && $user->hasRole('Pengajar')) {
        $pengajarNav = [];
        $pengajarNav = array_merge($pengajarNav, [
            ['route' => 'pengajar.kegiatan-rutin.index', 'label' => 'Kegiatan Rutin', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'pattern' => 'pengajar.kegiatan-rutin.*'],
            ['route' => 'pengajar.kegiatan.index', 'label' => 'Jurnal Kegiatan', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'pattern' => 'pengajar.kegiatan.*'],
            ['route' => 'pengajar.matrikulasi.index', 'label' => 'Matrikulasi', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'pattern' => 'pengajar.matrikulasi.*'],
            ['route' => 'pengajar.pencapaian.index', 'label' => 'Pencapaian Siswa', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'pengajar.pencapaian.*'],
        ]);
        $roleNavItems = array_merge($roleNavItems, $pengajarNav);
    }
    if ($user && $user->hasRole('Orang Tua')) {
        $roleNavItems = array_merge($roleNavItems, [
            ['route' => 'orangtua.presensi.index', 'label' => 'Rekap Kehadiran', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'pattern' => 'orangtua.presensi.*'],
            ['route' => 'orangtua.kegiatan.index', 'label' => 'Kegiatan Anak', 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'orangtua.kegiatan.*'],
            ['route' => 'orangtua.pencapaian.index', 'label' => 'Laporan Pencapaian', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'pattern' => 'orangtua.pencapaian.*'],
            ['route' => 'orangtua.menu-makanan.index', 'label' => 'Menu Makanan', 'icon' => 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z', 'pattern' => 'orangtua.menu-makanan.*'],
            ['route' => 'orangtua.kritik-saran.index', 'label' => 'Saran & Kritik', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'pattern' => 'orangtua.kritik-saran.*'],
            ['route' => 'orangtua.kesehatan.index', 'label' => 'Kesehatan Anak', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'pattern' => 'orangtua.kesehatan.*'],
        ]);
    }
    $roleLabel = 'Pengguna Khusus';
    if ($user && $user->hasRole('Lembaga')) $roleLabel = 'Yayasan';
    elseif ($user && $user->hasRole('Admin Sekolah')) $roleLabel = 'Admin Sekolah';
    elseif ($user && $user->hasRole('Admin Kelas') && !$user->hasRole('Admin Sekolah')) $roleLabel = 'Wali Kelas';
    elseif ($user && $user->hasRole('Pengajar')) $roleLabel = 'Guru';
    elseif ($user && $user->hasRole('Orang Tua')) $roleLabel = 'Wali Murid';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PAUD Manager') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-[#2C2C2C] bg-[#F5F0E8]">
        <div x-data="{ sidebarOpen: false, moreMenuOpen: false }" class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col overflow-hidden relative">
                <!-- Topbar -->
                @include('layouts.topbar')

                <!-- Main Content Area -->
                <main class="flex-1 overflow-y-auto w-full pb-20 lg:pb-0">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="page-header sticky top-0 z-20 bg-[#F5F0E8]/90 backdrop-blur-sm border-b border-black/5">
                            <div class="max-w-7xl mx-auto py-2 px-4 md:py-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <div class="min-h-[calc(100vh-64px)] w-full">
                        {{ $slot }}
                    </div>
                </main>
                
                <!-- Bottom Navbar (Mobile only) -->
                @include('layouts.bottom-nav')
            </div>
        </div>
        <script>
            window.compressImage = function(file, maxWidth = 1280, quality = 0.8) {
                return new Promise((resolve, reject) => {
                    if (!file || !file.type.startsWith('image/')) return resolve(file);
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = (e) => {
                        const img = new Image();
                        img.src = e.target.result;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;
                            if (width > height) {
                                if (width > maxWidth) {
                                    height *= maxWidth / width;
                                    width = maxWidth;
                                }
                            } else {
                                if (height > maxWidth) {
                                    width *= maxWidth / height;
                                    height = maxWidth;
                                }
                            }
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            canvas.toBlob((blob) => {
                                const fileName = file.name.replace(/\.[^/.]+$/, '') + '.jpg';
                                const compressedFile = new File([blob], fileName, { type: 'image/jpeg' });
                                resolve(compressedFile);
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = reject;
                    };
                    reader.onerror = reject;
                });
            };
        </script>
    </body>
</html>
