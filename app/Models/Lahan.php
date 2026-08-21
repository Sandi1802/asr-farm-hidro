<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lahan extends Model
{
    use HasFactory;

    protected $table = 'lahan';
    protected $fillable = ['nama_lahan', 'status', 'deskripsi'];

    public function bedengan()
    {
        return $this->hasMany(Bedengan::class);
    }
}
