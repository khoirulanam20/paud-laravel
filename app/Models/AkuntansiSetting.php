<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\LogsScopedActivity;
use App\Support\StandardCoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkuntansiSetting extends Model
{
    use BelongsToSekolah, LogsScopedActivity;

    protected $table = 'akuntansi_settings';

    protected $fillable = [
        'sekolah_id',
        'metode_pencatatan',
        'akun_kas_id',
        'akun_piutang_id',
        'akun_pendapatan_id',
        'akun_untuk_in',
        'akun_untuk_out',
        'jenis_akun_aset',
    ];

    protected $casts = [
        'jenis_akun_aset' => 'array',
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

    /** Jenis akun yang tampil di kolom Akun Aset saat catat transaksi. Kosong = aset. */
    public function jenisUntukAkunAset(): array
    {
        $list = array_values(array_filter(array_map(
            fn ($v) => trim((string) $v),
            $this->jenis_akun_aset ?? []
        )));

        return $list !== [] ? $list : ['aset'];
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
        $kas = Akun::where('sekolah_id', $sekolahId)->where('kode', StandardCoa::DEFAULT_KAS)->first();
        $pendapatanLain = Akun::where('sekolah_id', $sekolahId)->where('kode', StandardCoa::DEFAULT_COUNTER_IN)->first();
        $bebanDefault = Akun::where('sekolah_id', $sekolahId)->where('kode', StandardCoa::DEFAULT_COUNTER_OUT)->first();
        $piutang = Akun::where('sekolah_id', $sekolahId)->where('kode', StandardCoa::DEFAULT_PIUTANG)->first();
        $pendapatanSpp = Akun::where('sekolah_id', $sekolahId)->where('kode', StandardCoa::DEFAULT_PENDAPATAN)->first();

        $attrs = array_filter([
            'metode_pencatatan' => 'cash',
            'akun_kas_id' => $kas?->id,
            'akun_piutang_id' => $piutang?->id,
            'akun_pendapatan_id' => $pendapatanSpp?->id,
            'akun_untuk_in' => $pendapatanLain?->id,
            'akun_untuk_out' => $bebanDefault?->id,
        ], fn ($v) => $v !== null);

        $setting = static::firstOrCreate(
            ['sekolah_id' => $sekolahId],
            $attrs + ['metode_pencatatan' => 'cash'],
        );

        if ($kas && $setting->akun_kas_id !== $kas->id) {
            $setting->update(array_filter([
                'akun_kas_id' => $kas->id,
                'akun_piutang_id' => $piutang?->id,
                'akun_pendapatan_id' => $pendapatanSpp?->id,
                'akun_untuk_in' => $pendapatanLain?->id,
                'akun_untuk_out' => $bebanDefault?->id,
            ], fn ($v) => $v !== null));
        }

        return $setting->fresh();
    }
}
