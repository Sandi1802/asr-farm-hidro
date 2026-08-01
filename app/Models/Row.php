<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    use HasFactory;

    protected $fillable = ['rack_id', 'name', 'status'];

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function holes()
    {
        return $this->hasMany(Hole::class);
    }
}
