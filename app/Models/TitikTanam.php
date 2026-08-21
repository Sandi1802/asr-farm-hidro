<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitikTanam extends Model
{
    use HasFactory;

    protected $table = 'titik_tanam';
    protected $fillable = ['bedengan_id', 'nama_titik', 'nama_tanaman', 'status', 'tanggal_tanam', 'tanggal_panen'];

    protected $casts = [
        'tanggal_tanam' => 'datetime',
        'tanggal_panen' => 'datetime',
    ];

    public function bedengan()
    {
        return $this->belongsTo(Bedengan::class);
    }
}
