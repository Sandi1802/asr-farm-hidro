<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaprikaFertilization extends Model
{
    use HasFactory;

    protected $fillable = [
        'paprika_greenhouse_id',
        'fertilizer_name',
        'dose',
        'date',
        'worker_name'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function greenhouse()
    {
        return $this->belongsTo(PaprikaGreenhouse::class, 'paprika_greenhouse_id');
    }
}
