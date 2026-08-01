<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory;

    protected $fillable = [
        'greenhouse_id', 'name', 'ppm_level', 'ph_level', 'ppm_ph_updated_at', 'status', 'last_drained_at'
    ];

    protected $casts = [
        'ppm_ph_updated_at' => 'datetime',
        'last_drained_at' => 'datetime',
    ];

    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }

    public function rows()
    {
        return $this->hasMany(Row::class);
    }

    public function holes()
    {
        return $this->hasManyThrough(Hole::class, Row::class);
    }
}
