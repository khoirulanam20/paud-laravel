<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengumuman extends Model
{
    use LogsScopedActivity;

    protected $table = 'pengumumans';

    protected $fillable = [
        'sekolah_id',
        'judul',
        'kategori',
        'isi',
        'gambar',
        'mulai_tayang',
        'selesai_tayang',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'mulai_tayang' => 'date',
            'selesai_tayang' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(PengumumanRead::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query
            ->where('is_active', true)
            ->whereDate('mulai_tayang', '<=', $today)
            ->whereDate('selesai_tayang', '>=', $today);
    }

    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }
}
