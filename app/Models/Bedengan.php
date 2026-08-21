<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bedengan extends Model
{
    use HasFactory;

    protected $table = 'bedengan';
    protected $fillable = ['lahan_id', 'nama_bedengan', 'pakai_mulsa', 'status'];

    public function lahan()
    {
        return $this->belongsTo(Lahan::class);
    }

    public function titik_tanam()
    {
        return $this->hasMany(TitikTanam::class);
    }
}
