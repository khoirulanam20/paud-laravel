<?php

namespace App\Models;

use App\Models\Concerns\FiltersByKelasRiwayat;
use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pencapaian extends Model
{
    use FiltersByKelasRiwayat, LogsScopedActivity;

    protected $guarded = [];

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class);
    }

    /** Kelas pencapaian = kelas kegiatannya (tetap walau siswa dipindah/dinaikkan). */
    public function scopeDiKelas(\Illuminate\Database\Eloquent\Builder $query, int|array $kelasIds): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereHas('kegiatan', fn ($q) => $q->whereIn('kelas_id', (array) $kelasIds));
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function matrikulasi(): BelongsTo
    {
        return $this->belongsTo(Matrikulasi::class);
    }
}
