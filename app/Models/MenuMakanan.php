<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuMakanan extends Model
{
    protected $fillable = [
        'sekolah_id',
        'date',
        'menu',
        'nutrition_info',
        'photo',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }
}
