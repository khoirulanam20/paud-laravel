<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['sekolah_id', 'name', 'description'];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function anaks()
    {
        return $this->hasMany(Anak::class);
    }

    public function pengajars()
    {
        return $this->belongsToMany(Pengajar::class, 'kelas_pengajar');
    }
}
