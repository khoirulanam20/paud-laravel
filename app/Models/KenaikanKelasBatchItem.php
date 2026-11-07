<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KenaikanKelasBatchItem extends Model
{
    use LogsScopedActivity;

    protected $fillable = [
        'batch_id',
        'anak_id',
        'kelas_asal_id',
        'kelas_tujuan_id',
        'aksi',
        'usia_bulan',
        'catatan',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(KenaikanKelasBatch::class, 'batch_id');
    }

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    public function kelasAsal(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_asal_id');
    }

    public function kelasTujuan(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_tujuan_id');
    }
}
