<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiPengajar extends Model
{
    use LogsScopedActivity;

    protected $fillable = [
        'sekolah_id',
        'pengajar_id',
        'tanggal',
        'hadir',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'hadir' => 'boolean',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class);
    }
}
