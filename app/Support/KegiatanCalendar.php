<?php

namespace App\Support;

use App\Models\Kegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanCalendar
{
    /**
     * @return array{0: int, 1: int}
     */
    public static function resolveYearMonth(Request $request): array
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        if ($month < 1 || $month > 12) {
            $month = (int) now()->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) now()->year;
        }

        return [$year, $month];
    }

    /**
     * Rentang tanggal untuk memuat event (bulan tengah ±1 bulan).
     *
     * @return array{0: string, 1: string} Y-m-d
     */
    public static function dateRangeForCalendar(int $year, int $month): array
    {
        $center = Carbon::create($year, $month, 1);
        $start = $center->copy()->subMonth()->startOfMonth();
        $end = $center->copy()->addMonth()->endOfMonth();

        return [$start->toDateString(), $end->toDateString()];
    }

    public static function formatDate(Kegiatan $k): string
    {
        $d = $k->date;

        return $d instanceof Carbon ? $d->format('Y-m-d') : Carbon::parse($d)->format('Y-m-d');
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPengajarEvent(Kegiatan $k): array
    {
        $k->loadMissing(['pencapaians.anak', 'pencapaians.matrikulasi', 'matrikulasis']);

        $detail = [
            'id' => $k->id,
            'title' => $k->title,
            'date' => self::formatDate($k),
            'description' => $k->description,
            'pencapaians' => $k->pencapaians->map(function ($p) {
                return [
                    'id' => $p->id,
                    'score' => $p->score,
                    'feedback' => $p->feedback,
                    'aspek' => $p->matrikulasi->aspek ?? null,
                    'indicator' => $p->matrikulasi->indicator ?? null,
                    'anak' => ['name' => $p->anak->name ?? '-'],
                ];
            })->values()->all(),
        ];

        return [
            'id' => (string) $k->id,
            'title' => $k->title,
            'start' => self::formatDate($k),
            'allDay' => true,
            'extendedProps' => [
                'mode' => 'pengajar',
                'delete_url' => route('pengajar.kegiatan.destroy', $k),
                'detail' => $detail,
                'edit' => [
                    'id' => $k->id,
                    'date' => self::formatDate($k),
                    'title' => $k->title,
                    'description' => $k->description,
                    'matrikulasi_ids' => $k->matrikulasis->pluck('id')->all(),
                ],
            ],
        ];
    }

    /**
     * @param  list<int>|null  $pencapaianSubset  Batasi baris pencapaian ke id ini
     * @param  list<int>|null  $limitAnakIds  Jika subset null, filter pencapaian ke anak_id dalam daftar
     * @return array<string, mixed>
     */
    public static function toReadonlyEvent(Kegiatan $k, ?array $pencapaianSubset = null, ?array $limitAnakIds = null): array
    {
        $k->loadMissing(['pengajar', 'pencapaians.anak', 'pencapaians.matrikulasi']);

        $pencapaians = $k->pencapaians;
        if ($pencapaianSubset !== null) {
            $pencapaians = $pencapaians->whereIn('id', $pencapaianSubset);
        } elseif ($limitAnakIds !== null && $limitAnakIds !== []) {
            $pencapaians = $pencapaians->whereIn('anak_id', $limitAnakIds);
        }

        $detail = [
            'id' => $k->id,
            'title' => $k->title,
            'date' => self::formatDate($k),
            'description' => $k->description,
            'photo_url' => $k->photo ? Storage::url($k->photo) : null,
            'pengajar_name' => $k->pengajar->name ?? '-',
            'pencapaians' => $pencapaians->map(function ($p) {
                return [
                    'id' => $p->id,
                    'score' => $p->score,
                    'feedback' => $p->feedback,
                    'aspek' => $p->matrikulasi->aspek ?? null,
                    'indicator' => $p->matrikulasi->indicator ?? null,
                    'anak_name' => $p->anak->name ?? '-',
                ];
            })->values()->all(),
        ];

        return [
            'id' => (string) $k->id,
            'title' => $k->title,
            'start' => self::formatDate($k),
            'allDay' => true,
            'extendedProps' => [
                'mode' => 'readonly',
                'detail' => $detail,
            ],
        ];
    }
}
