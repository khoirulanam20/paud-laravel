<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiayaBulananSiswa extends Model
{
    protected $fillable = [
        'sekolah_id',
        'anak_id',
        'biaya_bulanan_sekolah_id',
        'biaya_harian',
    ];

    protected function casts(): array
    {
        return [
            'biaya_harian' => 'decimal:2',
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

    public function biayaBulananSekolah(): BelongsTo
    {
        return $this->belongsTo(BiayaBulananSekolah::class);
    }

    public function getBiayaHarianFormatted(): string
    {
        return 'Rp ' . number_format($this->biaya_harian, 0, ',', '.');
    }
}
