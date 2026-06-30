<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajar extends Model
{
    use LogsScopedActivity;

    protected $fillable = [
        'user_id',
        'sekolah_id',
        'name',
        'jabatan',
        'education_history',
        'nik',
        'alamat',
        'phone',
        'pendidikan',
        'jenis_kelamin',
        'photo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_pengajar');
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }
}
