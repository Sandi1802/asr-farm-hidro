<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaprikaGh extends Model
{
    use HasFactory;

    protected $table = 'paprika_ghs';
    protected $fillable = ['nama_gh', 'keterangan'];

    public function baris()
    {
        return $this->hasMany(PaprikaBaris::class, 'gh_id');
    }
}
