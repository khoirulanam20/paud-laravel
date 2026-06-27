<?php

namespace App\Models;

use App\Models\Concerns\LogsScopedActivity;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, LogsScopedActivity, Notifiable;

    protected array $activityLogExcept = ['password', 'remember_token'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'lembaga_id',
        'sekolah_id',
        'kelas_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tour_completed' => 'array',
        ];
    }

    public function markTourCompleted(string $route): void
    {
        $completed = $this->tour_completed ?? [];
        $completed[$route] = now()->toIso8601String();
        $this->tour_completed = $completed;
        $this->save();
    }

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function pengajar()
    {
        return $this->hasOne(Pengajar::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function anaks()
    {
        return $this->hasMany(Anak::class, 'user_id');
    }

    public function kritikSarans()
    {
        return $this->hasMany(KritikSaran::class);
    }

    public function orangTuaChat()
    {
        return $this->hasOne(OrangTuaChat::class);
    }

    public function hasAnyMenuPermission(): bool
    {
        return $this->getAllPermissions()->contains(
            fn ($permission) => str_starts_with($permission->name, 'menu.')
        );
    }

    public function getSekolahIdAttribute($value): ?int
    {
        if ($this->hasRole('Lembaga')) {
            $active = session('active_sekolah_id');

            return $active ? (int) $active : null;
        }

        return $value !== null ? (int) $value : null;
    }

    public function canAccessAdminPanel(): bool
    {
        if ($this->hasRole('Orang Tua')) {
            return false;
        }

        if ($this->hasRole('Lembaga')) {
            return $this->sekolah_id !== null;
        }

        if (!$this->getAttributes()['sekolah_id'] ?? null) {
            return false;
        }

        if ($this->hasRole('Admin Sekolah')) {
            return true;
        }

        // Role kustom (mis. Bendahara) — bukan role operasional default
        $builtinRoles = ['Superadmin', 'Admin Sekolah', 'Wali Kelas', 'Pengajar', 'Lembaga', 'Orang Tua'];
        $hasCustomRole = $this->roles->contains(
            fn ($role) => !in_array($role->name, $builtinRoles, true)
        );

        return $hasCustomRole && $this->hasAnyMenuPermission();
    }

    public function firstAccessibleAdminRoute(?bool $chatOrangTuaEnabled = null): ?string
    {
        if ($this->sekolah_id && $chatOrangTuaEnabled === null) {
            $chatOrangTuaEnabled = app(\App\Services\AiTokenService::class)
                ->isChatOrangTuaEnabled((int) $this->sekolah_id);
        }

        foreach (config('admin-menu.menu_order', []) as $item) {
            if (($item['perm'] ?? '') === 'menu.chat-orangtua' && !$chatOrangTuaEnabled) {
                continue;
            }
            if ($this->can($item['perm'])) {
                return $item['route'];
            }
        }

        return null;
    }
}
