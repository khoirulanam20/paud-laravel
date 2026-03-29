<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

final class PresensiPeriodeFilter
{
    /**
     * @return array{from: string, to: string, periode: string, tahun: int, bulan: int, minggu: string|null, label: string}
     */
    public static function resolve(Request $request): array
    {
        $periode = $request->input('periode', 'bulan');
        if (! in_array($periode, ['bulan', 'minggu'], true)) {
            $periode = 'bulan';
        }

        if ($periode === 'minggu') {
            $defaultWeek = sprintf('%d-W%02d', now()->isoWeekYear(), now()->isoWeek());
            $weekInput = (string) $request->input('week', $defaultWeek);
            $from = null;
            if (preg_match('/^(\d{4})-W(\d{1,2})$/', $weekInput, $m)) {
                $isoY = (int) $m[1];
                $isoW = (int) $m[2];
                if ($isoW >= 1 && $isoW <= 53) {
                    $from = Carbon::now()->setISODate($isoY, $isoW)->startOfWeek();
                }
            }
            if ($from === null) {
                $weekInput = $defaultWeek;
                preg_match('/^(\d{4})-W(\d{1,2})$/', $weekInput, $m);
                $from = Carbon::now()->setISODate((int) $m[1], (int) $m[2])->startOfWeek();
            }
            $to = (clone $from)->endOfWeek();

            return [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'periode' => 'minggu',
                'tahun' => (int) $from->year,
                'bulan' => (int) $from->month,
                'minggu' => $weekInput,
                'label' => $from->translatedFormat('d M').' – '.$to->translatedFormat('d M Y'),
            ];
        }

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        if ($month < 1 || $month > 12) {
            $month = (int) now()->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) now()->year;
        }
        $from = Carbon::create($year, $month, 1)->startOfMonth();
        $to = (clone $from)->endOfMonth();

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'periode' => 'bulan',
            'tahun' => $year,
            'bulan' => $month,
            'minggu' => null,
            'label' => $from->translatedFormat('F Y'),
        ];
    }
}
