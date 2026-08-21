<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BibitKonvensional extends Model
{
    use HasFactory;

    protected $table = 'bibit_konvensional';
    protected $fillable = ['nama_bibit', 'estimasi_panen_hari', 'deskripsi'];
}
