<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaprikaGreenhouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity'];

    public function plants()
    {
        return $this->hasMany(PaprikaPlant::class);
    }

    public function fertilizations()
    {
        return $this->hasMany(PaprikaFertilization::class);
    }

    public function sprayings()
    {
        return $this->hasMany(PaprikaSpraying::class);
    }
}
