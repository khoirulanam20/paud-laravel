<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Akun extends Model
{
    protected $fillable = [
        'sekolah_id',
        'kode',
        'nama',
        'jenis',
        'kategori_arus_kas',
        'saldo_normal',
        'induk_id',
        'is_aktif',
        'deskripsi',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function induk(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'induk_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(Akun::class, 'induk_id');
    }

    public function cashflows(): HasMany
    {
        return $this->hasMany(Cashflow::class);
    }

    public function jurnalLines(): HasMany
    {
        return $this->hasMany(JurnalLine::class);
    }

    public function scopeAktif($q)
    {
        $q->where('is_aktif', true);
    }

    public function scopeByJenis($q, string $jenis)
    {
        $q->where('jenis', $jenis);
    }
}
