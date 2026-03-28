<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembaga extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'pendiri',
        'organisasi',
        'no_akta',
        'no_pengesahan',
    ];
}
