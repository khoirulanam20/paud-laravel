<?php

namespace App\Support;

use App\Models\MonevSummary;

final class MonevSummaryPresenter
{
    /**
     * @return array<int, array{key: string, title: string, icon: string, content: string}>
     */
    public static function sections(MonevSummary $summary): array
    {
        $text = trim($summary->ringkasan);

        if ($text === 'Belum ada data pencapaian matrikulasi pada periode ini.') {
            return [[
                'key' => 'empty',
                'title' => 'Belum Ada Data',
                'icon' => 'info',
                'content' => $text,
            ]];
        }

        $markers = [
            'gambaran' => ['GAMBARAN_UMUM', 'Gambaran Umum', 'chart'],
            'kekuatan' => ['KEKUATAN', 'Kekuatan yang Menonjol', 'star'],
            'perhatian' => ['PERHATIAN', 'Area Perlu Perhatian', 'alert'],
            'rekomendasi' => ['REKOMENDASI', 'Rekomendasi Bulan Depan', 'lightbulb'],
        ];

        $parsed = [];

        foreach ($markers as $key => [$tag, $title, $icon]) {
            $pattern = '/\[' . preg_quote($tag, '/') . '\]\s*(.*?)(?=\[(?:GAMBARAN_UMUM|KEKUATAN|PERHATIAN|REKOMENDASI)\]|$)/s';
            if (preg_match($pattern, $text, $matches)) {
                $content = trim($matches[1]);
                if ($content !== '') {
                    $parsed[] = compact('key', 'title', 'icon') + ['content' => $content];
                }
            }
        }

        if ($parsed !== []) {
            return $parsed;
        }

        $paragraphs = preg_split("/\n\s*\n/", $text) ?: [];
        $paragraphs = array_values(array_filter(array_map('trim', $paragraphs)));

        $fallbackTitles = [
            ['Gambaran Umum', 'chart'],
            ['Kekuatan yang Menonjol', 'star'],
            ['Area Perlu Perhatian', 'alert'],
            ['Rekomendasi Bulan Depan', 'lightbulb'],
        ];

        foreach ($paragraphs as $i => $paragraph) {
            [$title, $icon] = $fallbackTitles[$i] ?? ['Ringkasan', 'chart'];
            $parsed[] = [
                'key' => 'section_' . $i,
                'title' => $title,
                'icon' => $icon,
                'content' => $paragraph,
            ];
        }

        return $parsed ?: [[
            'key' => 'full',
            'title' => 'Ringkasan',
            'icon' => 'chart',
            'content' => $text,
        ]];
    }

    /**
     * @return array<string, int>
     */
    public static function scoreDistribution(MonevSummary $summary): array
    {
        return $summary->data_snapshot['distribusi_skor'] ?? [];
    }

    /**
     * @return array<string, array{jumlah: int, skor: array<string, int>}>
     */
    public static function perAspek(MonevSummary $summary): array
    {
        return $summary->data_snapshot['per_aspek'] ?? [];
    }

    /**
     * @return array<string>
     */
    public static function feedbackSamples(MonevSummary $summary): array
    {
        return $summary->data_snapshot['cuplikan_feedback'] ?? [];
    }

    public static function totalEntri(MonevSummary $summary): int
    {
        return (int) ($summary->data_snapshot['total_entri'] ?? 0);
    }
}
