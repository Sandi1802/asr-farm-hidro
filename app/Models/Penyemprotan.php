<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyemprotan extends Model
{
    use HasFactory;

    protected $table = 'penyemprotan';
    protected $fillable = ['lahan_id', 'bedengan_id', 'nama_obat', 'dosis', 'alasan', 'tanggal', 'nama_pekerja'];

    public function lahan()
    {
        return $this->belongsTo(Lahan::class);
    }

    public function bedengan()
    {
        return $this->belongsTo(Bedengan::class);
    }
}
