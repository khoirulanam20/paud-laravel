<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Akun extends Model
{
    use LogsScopedActivity;

    protected $fillable = [
        'sekolah_id',
        'tipe',
        'kode',
        'nama',
        'snp',
        'komponen',
        'uraian',
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

    public function scopeSistem($q)
    {
        $q->where('tipe', 'sistem');
    }

    public function scopeRkas($q)
    {
        $q->where('tipe', 'rkas');
    }

    public function isSistem(): bool
    {
        return $this->tipe === 'sistem';
    }

    public function getLabelAttribute(): string
    {
        return "{$this->kode} — ".($this->uraian ?? $this->nama);
    }
}
