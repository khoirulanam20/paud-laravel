<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonevManualTrigger extends Model
{
    protected $fillable = [
        'sekolah_id',
        'kelas_id',
        'tahun',
        'bulan',
        'triggered_by_user_id',
        'triggered_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'tahun' => 'integer',
        'bulan' => 'integer',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }
}
