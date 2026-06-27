<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SumberDana extends Model
{
    use LogsScopedActivity;

    protected $fillable = [
        'sekolah_id',
        'kode',
        'nama',
        'urutan',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function anggarans(): HasMany
    {
        return $this->hasMany(RkasLineAnggaran::class);
    }

    public function scopeAktif($q)
    {
        $q->where('is_aktif', true);
    }
}
