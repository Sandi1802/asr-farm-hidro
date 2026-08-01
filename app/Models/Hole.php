<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hole extends Model
{
    use HasFactory;

    protected $fillable = [
        'row_id', 'name', 'plant_name', 'status', 'planted_at', 'harvested_at'
    ];

    protected $casts = [
        'planted_at'   => 'datetime',
        'harvested_at' => 'datetime',
    ];

    public function row()
    {
        return $this->belongsTo(Row::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
