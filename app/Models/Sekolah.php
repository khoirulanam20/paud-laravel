<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $fillable = [
        'lembaga_id',
        'name',
        'address',
        'phone',
        'nisn',
        'location_coordinate',
        'photo',
    ];

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function aiPersonas(): HasMany
    {
        return $this->hasMany(SekolahAiPersona::class);
    }
}
