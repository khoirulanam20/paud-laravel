<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    protected $fillable = [
        'sekolah_id',
        'pengajar_id',
        'date',
        'title',
        'description',
        'photo',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class);
    }

    public function matrikulasis(): BelongsToMany
    {
        return $this->belongsToMany(Matrikulasi::class, 'kegiatan_matrikulasi');
    }

    public function pencapaians(): HasMany
    {
        return $this->hasMany(Pencapaian::class);
    }
}
