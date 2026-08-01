<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Semai extends Model
{
    protected $fillable = [
        'plant_type_id', 'plant_name', 'quantity',
        'semai_date', 'estimated_transfer_date',
        'target_greenhouse_id', 'notes', 'status',
        'transferred_date', 'user_id',
    ];

    protected $casts = [
        'semai_date'              => 'date',
        'estimated_transfer_date' => 'date',
        'transferred_date'        => 'date',
    ];

    public function plantType()
    {
        return $this->belongsTo(PlantType::class);
    }

    public function targetGreenhouse()
    {
        return $this->belongsTo(Greenhouse::class, 'target_greenhouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Sisa hari sampai estimasi pindah (negatif = sudah lewat) */
    public function remainingDays(): int
    {
        if (!$this->estimated_transfer_date) return 0;
        return (int) now()->startOfDay()->diffInDays($this->estimated_transfer_date->startOfDay(), false);
    }

    /** Sudah siap dipindahkan? */
    public function isReadyToTransfer(): bool
    {
        return $this->status === 'aktif' && $this->remainingDays() <= 0;
    }

    /** Hari ke- sejak disemai */
    public function daysOld(): int
    {
        return (int) Carbon::parse($this->semai_date)->diffInDays(now());
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'aktif'        => 'Sedang Disemai',
            'sudah_pindah' => 'Sudah Pindah ke GH',
            'gagal'        => 'Gagal',
            default        => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'aktif'        => '#16a34a',
            'sudah_pindah' => '#2563eb',
            'gagal'        => '#dc2626',
            default        => '#64748b',
        };
    }
}
