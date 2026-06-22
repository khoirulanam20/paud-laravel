<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Cashflow extends Model
{
    protected $fillable = [
        'sekolah_id',
        'akun_id',
        'jurnal_id',
        'type',
        'amount',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(Akun::class);
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
