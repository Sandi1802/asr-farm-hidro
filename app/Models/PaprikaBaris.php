<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaprikaBaris extends Model
{
    use HasFactory;

    protected $table = 'paprika_baris';
    protected $fillable = ['gh_id', 'nama_baris'];

    public function gh()
    {
        return $this->belongsTo(PaprikaGh::class, 'gh_id');
    }

    public function pots()
    {
        return $this->hasMany(PaprikaPot::class, 'baris_id');
    }
}
