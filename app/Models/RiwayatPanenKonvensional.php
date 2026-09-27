<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPanenKonvensional extends Model
{
    use HasFactory;

    protected $table = 'riwayat_panen_konvensional';
    
    protected $fillable = [
        'titik_tanam_id',
        'jenis_panen',
        'jumlah_kg',
        'catatan'
    ];

    public function titikTanam()
    {
        return $this->belongsTo(TitikTanam::class, 'titik_tanam_id');
    }
}
