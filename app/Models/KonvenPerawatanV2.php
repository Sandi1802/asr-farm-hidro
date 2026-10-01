<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonvenPerawatanV2 extends Model
{
    use HasFactory;

    protected $table = 'konven_perawatans';

    protected $fillable = [
        'bedengan_id',
        'jenis',
        'nama_bahan',
        'dosis',
        'tanggal',
        'keterangan',
        'created_by'
    ];

    public function bedengan()
    {
        return $this->belongsTo(KonvenBedenganV2::class, 'bedengan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
