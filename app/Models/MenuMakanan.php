<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuMakanan extends Model
{
    use LogsScopedActivity;

    protected $fillable = [
        'sekolah_id',
        'date',
        'menu',
        'nutrition_info',
        'photo',
        'photo_kegiatan',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function votes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuMakananVote::class);
    }
}
