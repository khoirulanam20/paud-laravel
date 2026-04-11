<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSetting extends Model
{
    protected $fillable = [
        'lembaga_id',
        'ai_provider',
        'ai_api_key',
        'ai_model',
    ];

    protected $casts = [
        'ai_api_key' => 'encrypted',
    ];

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }
}
