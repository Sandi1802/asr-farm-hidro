<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class KonvenZonaV2 extends Model
{
    protected $table    = 'konven_zonas';
    protected $fillable = ['kode_id', 'nama'];

    public function kode(): BelongsTo
    {
        return $this->belongsTo(KonvenKodeV2::class, 'kode_id');
    }

    public function bedengans(): HasMany
    {
        return $this->hasMany(KonvenBedenganV2::class, 'zona_id');
    }

    public function lubangTanams(): HasManyThrough
    {
        return $this->hasManyThrough(KonvenLubangTanamV2::class, KonvenBedenganV2::class, 'zona_id', 'bedengan_id');
    }
}
