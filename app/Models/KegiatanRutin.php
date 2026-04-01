<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanRutin extends Model
{
    protected $fillable = [
        'sekolah_id',
        'kelas_id',
        'anak_id',
        'pengajar_id',
        'tanggal',
        'aspek',
        'kegiatan',
        'status_pencapaian',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class);
    }
}
