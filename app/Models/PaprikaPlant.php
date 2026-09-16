<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaprikaPlant extends Model
{
    use HasFactory;

    protected $fillable = [
        'paprika_greenhouse_id',
        'code',
        'status',
        'planted_at',
        'harvested_at'
    ];

    protected $casts = [
        'planted_at' => 'datetime',
        'harvested_at' => 'datetime'
    ];

    public function greenhouse()
    {
        return $this->belongsTo(PaprikaGreenhouse::class, 'paprika_greenhouse_id');
    }
}
