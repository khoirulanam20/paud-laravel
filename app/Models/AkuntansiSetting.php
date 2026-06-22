<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkuntansiSetting extends Model
{
    protected $table = 'akuntansi_settings';

    protected $fillable = [
        'sekolah_id',
        'metode_pencatatan',
        'akun_kas_id',
        'akun_piutang_id',
        'akun_pendapatan_id',
        'akun_untuk_in',
        'akun_untuk_out',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function akunKas(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'akun_kas_id');
    }

    public function akunPiutang(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'akun_piutang_id');
    }

    public function akunPendapatan(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'akun_pendapatan_id');
    }

    public function akunUntukIn(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'akun_untuk_in');
    }

    public function akunUntukOut(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'akun_untuk_out');
    }

    public function isAccrual(): bool
    {
        return $this->metode_pencatatan === 'accrual';
    }

    public function isCash(): bool
    {
        return $this->metode_pencatatan === 'cash';
    }

    public static function forSekolah(int $sekolahId): self
    {
        $kas = Akun::where('sekolah_id', $sekolahId)->where('kode', '1-1000')->first();
        $pendapatanLain = Akun::where('sekolah_id', $sekolahId)->where('kode', '4-1200')->first();
        $bebanLain = Akun::where('sekolah_id', $sekolahId)->where('kode', '5-1600')->first();
        $piutang = Akun::where('sekolah_id', $sekolahId)->where('kode', '1-1200')->first();
        $pendapatanSpp = Akun::where('sekolah_id', $sekolahId)->where('kode', '4-1000')->first();

        return static::firstOrCreate(
            ['sekolah_id' => $sekolahId],
            [
                'metode_pencatatan' => 'cash',
                'akun_kas_id' => $kas?->id,
                'akun_piutang_id' => $piutang?->id,
                'akun_pendapatan_id' => $pendapatanSpp?->id,
                'akun_untuk_in' => $pendapatanLain?->id,
                'akun_untuk_out' => $bebanLain?->id,
            ],
        );
    }
}
