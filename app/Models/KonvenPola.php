<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonvenPola extends Model
{
    protected $table = 'konven_pola';
    protected $fillable = ['zona_id', 'nama', 'urutan'];

    public function zona()
    {
        return $this->belongsTo(KonvenZona::class, 'zona_id');
    }

    public function bedeng()
    {
        return $this->hasMany(KonvenBedeng::class, 'pola_id')->orderBy('nomor');
    }
}
