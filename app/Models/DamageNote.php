<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageNote extends Model
{
    protected $fillable = [
        'hole_id', 'user_id', 'plant_name', 'damage_type',
        'description', 'severity', 'location', 'damaged_at',
        'action_taken', 'status',
    ];

    protected $casts = [
        'damaged_at' => 'datetime',
    ];

    public function hole()
    {
        return $this->belongsTo(Hole::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function severityBadgeColor(): string
    {
        return match ($this->severity) {
            'ringan' => '#16a34a',
            'sedang' => '#d97706',
            'berat'  => '#dc2626',
            default  => '#6b7280',
        };
    }

    public function severityBg(): string
    {
        return match ($this->severity) {
            'ringan' => '#dcfce7',
            'sedang' => '#fef3c7',
            'berat'  => '#fee2e2',
            default  => '#f3f4f6',
        };
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'open'     => '#dc2626',
            'handling' => '#d97706',
            'resolved' => '#16a34a',
            default    => '#6b7280',
        };
    }

    public function statusBg(): string
    {
        return match ($this->status) {
            'open'     => '#fee2e2',
            'handling' => '#fef3c7',
            'resolved' => '#dcfce7',
            default    => '#f3f4f6',
        };
    }
}
