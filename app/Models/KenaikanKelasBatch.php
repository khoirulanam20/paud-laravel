<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KenaikanKelasBatch extends Model
{
    use BelongsToSekolah, LogsScopedActivity;

    protected $fillable = [
        'sekolah_id',
        'mode',
        'tanggal_acuan',
        'tanggal_efektif',
        'tahun_ajaran_asal',
        'tahun_ajaran_tujuan',
        'jumlah_naik',
        'jumlah_tinggal',
        'jumlah_lulus',
        'status',
        'dibuat_oleh',
        'dibatalkan_oleh',
        'dibatalkan_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_acuan' => 'date',
            'tanggal_efektif' => 'date',
            'dibatalkan_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(KenaikanKelasBatchItem::class, 'batch_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
