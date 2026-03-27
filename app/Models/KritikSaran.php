<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KritikSaran extends Model
{
    protected $fillable = [
        'sekolah_id',
        'user_id',
        'message',
        'status',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
