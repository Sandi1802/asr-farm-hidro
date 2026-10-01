<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonvenLahanV2 extends Model
{
    protected $table    = 'konven_lahans';
    protected $fillable = ['nama', 'catatan'];

    // Relasi lama – dipertahankan
    public function posisis(): HasMany
    {
        return $this->hasMany(KonvenPosisiV2::class, 'lahan_id');
    }

    // Relasi baru – Kode langsung di bawah Lahan
    public function kodes(): HasMany
    {
        return $this->hasMany(KonvenKodeV2::class, 'lahan_id');
    }
}

