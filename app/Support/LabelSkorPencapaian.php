<?php

namespace App\Support;

final class LabelSkorPencapaian
{
    public const CODES = ['BB', 'MB', 'BSH', 'BSB'];

    private const LABELS = [
        'BB' => 'Belum Berkembang',
        'MB' => 'Mulai Berkembang',
        'BSH' => 'Berkembang Sesuai Harapan',
        'BSB' => 'Berkembang Sangat Baik',
    ];

    private const COLORS = [
        'BB' => '#FAD7D2',
        'MB' => '#FDE9BC',
        'BSH' => '#D0E8E8',
        'BSB' => '#C5E8C5',
    ];

    public static function label(?string $code): string
    {
        if ($code === null || $code === '') {
            return '—';
        }

        return self::LABELS[$code] ?? $code;
    }

    public static function color(?string $code): string
    {
        if ($code === null || $code === '') {
            return '#eee';
        }

        return self::COLORS[$code] ?? '#eee';
    }

    /** @return array<string, string> kode => warna latar */
    public static function colorsByCode(): array
    {
        return self::COLORS;
    }
}
