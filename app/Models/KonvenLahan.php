<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonvenLahan extends Model
{
    protected $table = 'konven_lahan';
    protected $fillable = ['nama'];

    public function zona()
    {
        return $this->hasMany(KonvenZona::class, 'lahan_id');
    }
}
