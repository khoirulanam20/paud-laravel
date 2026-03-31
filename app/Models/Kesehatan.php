<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kesehatan extends Model
{
    protected $fillable = [
        'anak_id',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'gigi',
        'telinga',
        'kuku',
        'alergi',
        'tanggal_pemeriksaan',
    ];

    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }
}
