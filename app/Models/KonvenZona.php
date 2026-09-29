<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonvenZona extends Model
{
    protected $table = 'konven_zona';
    protected $fillable = ['lahan_id', 'nama'];

    public function lahan()
    {
        return $this->belongsTo(KonvenLahan::class, 'lahan_id');
    }

    public function pola()
    {
        return $this->hasMany(KonvenPola::class, 'zona_id')->orderBy('urutan');
    }
    public function bedeng()
    {
        return $this->hasManyThrough(KonvenBedeng::class, KonvenPola::class, 'zona_id', 'pola_id');
    }
}

