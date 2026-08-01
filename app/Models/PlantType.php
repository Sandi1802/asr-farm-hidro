<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantType extends Model
{
    protected $fillable = [
        'name', 'growth_days', 'description', 'color',
        'semai_days', 'tanam_days', 'remaja_days', 'dewasa_days',
        'semai_ppm', 'semai_ph', 'tanam_ppm', 'tanam_ph',
        'remaja_ppm', 'remaja_ph', 'dewasa_ppm', 'dewasa_ph',
    ];

    /**
     * Holes that use this plant type (matched by plant_name = name).
     */
    public function holes()
    {
        return $this->hasMany(Hole::class, 'plant_name', 'name');
    }
}
