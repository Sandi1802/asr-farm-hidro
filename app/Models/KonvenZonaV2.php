<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
