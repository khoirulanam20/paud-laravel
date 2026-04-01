<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiPengajar extends Model
{
    protected $fillable = [
        'sekolah_id',
        'pengajar_id',
        'tanggal',
        'hadir',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'hadir' => 'boolean',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class);
    }
}
