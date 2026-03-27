<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anak extends Model
{
    protected $fillable = [
        'user_id',
        'sekolah_id',
        'name',
        'dob',
        'parent_name',
        'photo',
        'status',
        'catatan_ortu',
        'catatan_admin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }
}
