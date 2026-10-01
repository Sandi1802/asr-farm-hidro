<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonvenLahanV2 extends Model
{
    protected $table    = 'konven_lahans';
    protected $fillable = ['nama', 'catatan'];

    public function posisis(): HasMany
    {
        return $this->hasMany(KonvenPosisiV2::class, 'lahan_id');
    }
}
