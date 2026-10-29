<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

final class AdminBreadcrumb
{
    /** @var array<string, string> */
    private const ROUTE_LABELS = [
        'dashboard' => 'Dashboard',
        'superadmin.dashboard' => 'Dashboard',
        'profile.edit' => 'Profil',
        'admin.settings' => 'Pengaturan',
        'admin.jam-kerja.index' => 'Pengaturan Presensi Guru',
        'admin.presensi.rekap' => 'Rekap Presensi',
        'admin.laporan-keuangan.index' => 'Laporan Keuangan',
        'admin.laporan.arus-kas' => 'Arus Kas',
        'admin.laporan.buku-besar' => 'Buku Besar',
        'admin.laporan.laba-rugi' => 'Laba Rugi',
        'admin.laporan.neraca' => 'Neraca',
        'admin.gaji-guru.komponen.index' => 'Komponen Gaji',
        'admin.monev-guru.kriteria.index' => 'Kriteria Monev Guru',
        'admin.monev-guru.evaluasi.index' => 'Evaluasi Monev Guru',
        'admin.monev-guru.evaluasi.form' => 'Form Evaluasi',
        'admin.akuntansi-setting.index' => 'Setting Akuntansi',
        'admin.activity-log.index' => 'Log Aktivitas',
        'lembaga.activity-log.index' => 'Log Aktivitas',
        'superadmin.activity-log.index' => 'Log Aktivitas',
        'orangtua.anak.create' => 'Tambah Anak',
        'pengajar.kegiatan-rutin.index' => 'Kegiatan Rutin',
    ];

    /** @var array<string, string> */
    private const ROUTE_SUBTITLES = [
        'admin.jam-kerja.index' => 'Hari kerja, jam masuk/pulang, radius lokasi, dan hari libur.',
    ];

    /**
     * @return list<array{label: string, url: string|null}>
     */
    public static function resolve(array $roleNavItems, string $homeRoute, Request $request, ?string $titleOverride = null, ?string $subtitle = null): array
    {
        $routeName = Route::currentRouteName();
        if (! $routeName) {
            return [];
        }

        $homeUrl = Route::has($homeRoute) ? route($homeRoute) : null;
        $items = [['label' => 'Dashboard', 'url' => $homeUrl]];

        if (in_array($routeName, ['dashboard', 'superadmin.dashboard'], true)) {
            $items[0]['url'] = null;

            return self::applyOverride($items, $titleOverride);
        }

        $match = self::matchNavItem($roleNavItems, $routeName);
        $parentLabel = $match['label'] ?? self::ROUTE_LABELS[$routeName] ?? self::humanizeRoute($routeName);
        $parentUrl = ($match && Route::has($match['route'])) ? route($match['route']) : null;

        $suffix = self::suffixForRoute($routeName);
        if ($suffix && ! str_ends_with($routeName, '.index')) {
            if ($parentUrl && ($match === null || $routeName !== $match['route'])) {
                $items[] = ['label' => $parentLabel, 'url' => $parentUrl];
            }
            $items[] = ['label' => $titleOverride ?? $suffix, 'url' => null];
        } else {
            $items[] = ['label' => $titleOverride ?? $parentLabel, 'url' => null];
        }

        return self::applyOverride($items, $titleOverride, $subtitle);
    }

    /**
     * @param  list<array{label: string, url: string|null}>  $items
     * @return list<array{label: string, url: string|null, subtitle?: string|null}>
     */
    private static function applyOverride(array $items, ?string $titleOverride, ?string $subtitle = null): array
    {
        if ($titleOverride && count($items) > 0) {
            $items[array_key_last($items)]['label'] = $titleOverride;
        }
        $routeName = Route::currentRouteName();
        $autoSubtitle = $routeName ? (self::ROUTE_SUBTITLES[$routeName] ?? null) : null;
        $resolvedSubtitle = $subtitle ?? $autoSubtitle;
        if ($resolvedSubtitle !== null && $resolvedSubtitle !== '' && count($items) > 0) {
            $items[array_key_last($items)]['subtitle'] = $resolvedSubtitle;
        }

        return $items;
    }

    /**
     * @return array{route: string, label: string}|null
     */
    private static function matchNavItem(array $roleNavItems, string $routeName): ?array
    {
        $best = null;
        $bestLen = -1;

        foreach (self::flattenNav($roleNavItems) as $item) {
            $patterns = is_array($item['pattern'] ?? null) ? $item['pattern'] : [($item['pattern'] ?? $item['route'] ?? '')];
            foreach ($patterns as $pattern) {
                if ($pattern === '') {
                    continue;
                }
                if (! request()->routeIs($pattern)) {
                    continue;
                }
                $len = strlen($pattern);
                if ($len > $bestLen) {
                    $bestLen = $len;
                    $best = ['route' => $item['route'], 'label' => $item['label']];
                }
            }
        }

        return $best;
    }

    /** @return list<array{route: string, label: string, pattern?: string|list<string>}> */
    private static function flattenNav(array $roleNavItems): array
    {
        $flat = [];
        foreach ($roleNavItems as $nav) {
            if (! empty($nav['collapsible']) && isset($nav['sections'])) {
                foreach ($nav['sections'] as $section) {
                    foreach ($section['items'] as $item) {
                        $flat[] = $item;
                    }
                }
            } elseif (isset($nav['items'])) {
                foreach ($nav['items'] as $item) {
                    $flat[] = $item;
                }
            } elseif (isset($nav['route'], $nav['label'])) {
                $flat[] = $nav;
            }
        }

        if (Route::has('dashboard')) {
            $flat[] = ['route' => 'dashboard', 'label' => 'Dashboard', 'pattern' => 'dashboard'];
        }
        if (Route::has('superadmin.dashboard')) {
            $flat[] = ['route' => 'superadmin.dashboard', 'label' => 'Dashboard', 'pattern' => 'superadmin.dashboard'];
        }

        return $flat;
    }

    private static function suffixForRoute(string $routeName): ?string
    {
        return match (true) {
            str_ends_with($routeName, '.create') => 'Tambah',
            str_ends_with($routeName, '.edit') => 'Ubah',
            str_ends_with($routeName, '.show') => 'Detail',
            default => null,
        };
    }

    private static function humanizeRoute(string $routeName): string
    {
        if (isset(self::ROUTE_LABELS[$routeName])) {
            return self::ROUTE_LABELS[$routeName];
        }

        $tail = str_replace(['.', '-', '_'], ' ', Str::afterLast($routeName, '.'));

        return Str::title($tail);
    }
}
