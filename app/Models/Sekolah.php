<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sekolah extends Model
{
    protected $fillable = [
        'lembaga_id',
        'name',
        'address',
        'phone',
        'nisn',
        'location_coordinate',
        'photo',
    ];

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function aiPersonas(): HasMany
    {
        return $this->hasMany(SekolahAiPersona::class);
    }

    public function aiChatDataAccess(): HasOne
    {
        return $this->hasOne(SekolahAiChatDataAccess::class);
    }

    public function aiToken(): HasOne
    {
        return $this->hasOne(SekolahAiToken::class);
    }

    public function aiSettings(): HasOne
    {
        return $this->hasOne(SekolahAiSetting::class);
    }

    public function aiTokenTransactions(): HasMany
    {
        return $this->hasMany(SekolahAiTokenTransaction::class);
    }

    public function akuns(): HasMany
    {
        return $this->hasMany(Akun::class);
    }

    public function jurnals(): HasMany
    {
        return $this->hasMany(Jurnal::class);
    }

    public function akuntansiSetting(): HasOne
    {
        return $this->hasOne(AkuntansiSetting::class);
    }
}
