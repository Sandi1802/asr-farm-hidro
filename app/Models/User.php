<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'role',
        'role_agri',
        'username'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function isAgriAdmin(): bool
    {
        return in_array($this->role_agri, ['superadmin', 'admin', 'produksi', 'produksi_gh', 'it_admin']);
    }

    // ── Role helpers ──────────────────────────────────────────
    public function isItAdmin(): bool
    {
        return $this->role_agri === 'it_admin';
    }

    public function isAtasan(): bool
    {
        return $this->role_agri === 'atasan';
    }

    public function isProduksi(): bool
    {
        return in_array($this->role_agri, ['produksi', 'produksi_gh', 'produksi_konven']);
    }

    public function isKeuangan(): bool
    {
        return $this->role_agri === 'keuangan';
    }

    public function isPemasaran(): bool
    {
        return $this->role_agri === 'pemasaran';
    }

    public function isPacking(): bool
    {
        return $this->role_agri === 'packing';
    }

    public function roleLabel(): string
    {
        return match($this->role_agri) {
            'it_admin'        => 'IT Admin',
            'atasan'          => 'Atasan (Read Only)',
            'produksi'        => 'Produksi (Global)',
            'produksi_gh'     => 'Produksi GH',
            'produksi_konven' => 'Produksi Konvensional',
            'keuangan'        => 'Keuangan',
            'pemasaran'       => 'Pemasaran',
            'packing'         => 'Packing',
            default           => ucfirst(str_replace('_', ' ', $this->role_agri ?? 'staff')),
        };
    }

    public function roleBadgeColor(): string
    {
        return match($this->role_agri) {
            'it_admin'        => '#ef4444', // red
            'atasan'          => '#f59e0b', // amber
            'produksi'        => '#16a34a', // green
            'produksi_gh'     => '#22c55e', // green light
            'produksi_konven' => '#15803d', // green dark
            'keuangan'        => '#3b82f6', // blue
            'pemasaran'       => '#8b5cf6', // violet
            'packing'         => '#f97316', // orange
            default           => '#64748B', // gray
        };
    }
}
