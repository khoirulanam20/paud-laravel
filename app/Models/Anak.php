<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anak extends Model
{
    protected $fillable = [
        'user_id',
        'sekolah_id',
        'kelas_id',
        'name',
        'dob',
        'parent_name',
        'photo',
        'status',
        'catatan_ortu',
        'catatan_admin',
        'nik',
        'alamat',
        'jenis_kelamin',
        'nik_bapak',
        'nama_bapak',
        'nik_ibu',
        'nama_ibu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
    public function kesehatans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Kesehatan::class);
    }
}
