<?php
$files = [
    'resources/views/pengajar/master-kegiatan-rutin/create.blade.php',
    'resources/views/pengajar/master-kegiatan-rutin/edit.blade.php',
    'resources/views/pengajar/master-kegiatan-rutin/index.blade.php',
    'resources/views/pengajar/master-kegiatan-rutin/show.blade.php',
    'resources/views/pengajar/kegiatan-rutin/index.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    $content = str_replace(
        "route('pengajar.", 
        "route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'", 
        $content
    );
    file_put_contents($file, $content);
    echo "Replaced in $file\n";
}
