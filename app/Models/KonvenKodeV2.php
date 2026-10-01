<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonvenKodeV2 extends Model
{
    protected $table    = 'konven_kodes';
    protected $fillable = ['posisi_id', 'kode', 'nomor_urut'];

    public function posisi(): BelongsTo
    {
        return $this->belongsTo(KonvenPosisiV2::class, 'posisi_id');
    }

    public function zonas(): HasMany
    {
        return $this->hasMany(KonvenZonaV2::class, 'kode_id');
    }
}
