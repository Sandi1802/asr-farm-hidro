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
        'email',
        'password',
        'role',
        'role_agri',
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

    // ── Role helpers ──────────────────────────────────────────
    public function isAgriAdmin(): bool
    {
        return $this->role_agri === 'admin';
    }

    public function isAgriPegawai(): bool
    {
        return $this->role_agri === 'pegawai';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function roleLabel(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'Super Admin',
            'viewer'      => 'Staff',
            default       => ucfirst($this->role ?? 'staff'),
        };
    }

    public function roleBadgeColor(): string
    {
        return match($this->role) {
            'super_admin' => '#16a34a',   // asr-green
            'admin'       => '#16a34a',   // asr-green
            'viewer'      => '#64748B',   // gray
            default       => '#64748B',
        };
    }
}
