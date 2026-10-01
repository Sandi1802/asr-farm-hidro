<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonvenPosisiV2 extends Model
{
    protected $table    = 'konven_posisis';
    protected $fillable = ['lahan_id', 'nama', 'prefix_kode'];

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(KonvenLahanV2::class, 'lahan_id');
    }

    public function kodes(): HasMany
    {
        return $this->hasMany(KonvenKodeV2::class, 'posisi_id');
    }
}
