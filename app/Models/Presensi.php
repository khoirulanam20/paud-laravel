<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $fillable = [
        'sekolah_id',
        'kelas_id',
        'anak_id',
        'tanggal',
        'hadir',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'hadir' => 'boolean',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }
}
