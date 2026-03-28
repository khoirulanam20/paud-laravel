<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sarana extends Model
{
    protected $fillable = [
        'sekolah_id',
        'name',
        'condition',
        'quantity',
        'lokasi',
        'jenis',
        'photo',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }
}
