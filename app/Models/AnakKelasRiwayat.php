<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnakKelasRiwayat extends Model
{
    use BelongsToSekolah, LogsScopedActivity;

    protected $table = 'anak_kelas_riwayats';

    protected $fillable = [
        'sekolah_id',
        'anak_id',
        'kelas_id',
        'kelas_nama',
        'tahun_ajaran',
        'semester',
        'mulai',
        'selesai',
        'alasan',
        'alasan_selesai',
        'batch_id',
        'dibuat_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'date',
            'selesai' => 'date',
        ];
    }

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(KenaikanKelasBatch::class, 'batch_id');
    }
}
