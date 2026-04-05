<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Matrikulasi extends Model
{
    protected $fillable = [
        'sekolah_id',
        'indicator',
        'description',
        'aspek',
        'tujuan',
        'strategi',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kegiatans(): BelongsToMany
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_matrikulasi');
    }

    public function masterKegiatanRutins()
    {
        return $this->hasMany(MasterKegiatanRutin::class);
    }
}
