<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Greenhouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'description', 'last_sprayed_at'];

    protected $casts = [
        'last_sprayed_at' => 'datetime',
    ];

    public function racks()
    {
        return $this->hasMany(Rack::class);
    }

    public function rows()
    {
        return $this->hasManyThrough(Row::class, Rack::class);
    }
}
