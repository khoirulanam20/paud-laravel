<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
use Illuminate\Database\Eloquent\Model;

class Lembaga extends Model
{
    use LogsScopedActivity;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'pendiri',
        'organisasi',
        'no_akta',
        'no_pengesahan',
    ];

    public function sekolahs()
    {
        return $this->hasMany(Sekolah::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
