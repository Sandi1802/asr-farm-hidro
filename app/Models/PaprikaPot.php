<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaprikaPot extends Model
{
    use HasFactory;

    protected $table = 'paprika_pots';
    protected $fillable = [
        'baris_id', 'nomor_pot', 'status', 'plant_name', 'planted_at', 'estimated_harvest_at'
    ];

    public function baris()
    {
        return $this->belongsTo(PaprikaBaris::class, 'baris_id');
    }

    public function logs()
    {
        return $this->hasMany(PaprikaLog::class, 'pot_id');
    }
}
