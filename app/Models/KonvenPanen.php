<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonvenPanen extends Model
{
    protected $table = 'konven_panen';
    protected $fillable = ['tanam_id', 'tanggal_panen', 'jumlah_hasil', 'satuan', 'kualitas', 'catatan'];

    protected $casts = [
        'tanggal_panen' => 'date',
    ];

    public function tanam()
    {
        return $this->belongsTo(KonvenTanam::class, 'tanam_id');
    }
}
