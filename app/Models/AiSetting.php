<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }

    protected function aiApiKey(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if ($value === null || $value === '') {
                    return null;
                }

                try {
                    return decrypt($value, false);
                } catch (DecryptException) {
                    return null;
                }
            },
            set: function (?string $value) {
                if ($value === null || trim($value) === '') {
                    return null;
                }

                return encrypt($value, false);
            },
        );
    }

    /** Kolom DB berisi nilai (mungkin tidak bisa didekripsi). */
    public function hasStoredApiKey(): bool
    {
        return filled($this->attributes['ai_api_key'] ?? null);
    }

    /** API key berhasil didekripsi dan siap dipakai. */
    public function hasValidApiKey(): bool
    {
        return filled($this->ai_api_key);
    }

    /** Data ada di DB tetapi gagal didekripsi (mis. APP_KEY berubah). */
    public function apiKeyNeedsReentry(): bool
    {
        if (! $this->hasStoredApiKey()) {
            return false;
        }

        $raw = $this->attributes['ai_api_key'];

        try {
            decrypt($raw, false);

            return false;
        } catch (DecryptException) {
            return true;
        }
    }
}
