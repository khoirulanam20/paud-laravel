<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKegiatanRutin extends Model
{
    protected $fillable = [
        'sekolah_id',
        'pengajar_id',
        'matrikulasi_id',
        'nama_kegiatan',
        'aspek',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class);
    }

    public function matrikulasi()
    {
        return $this->belongsTo(Matrikulasi::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_master_kegiatan_rutin');
    }
}
