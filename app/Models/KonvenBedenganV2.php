<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonvenBedenganV2 extends Model
{
    protected $table    = 'konven_bedengans';
    protected $fillable = ['zona_id', 'nomor', 'nama_display', 'jumlah_lubang_rencana'];

    public function zona(): BelongsTo
    {
        return $this->belongsTo(KonvenZonaV2::class, 'zona_id');
    }

    public function lubangTanams(): HasMany
    {
        return $this->hasMany(KonvenLubangTanamV2::class, 'bedengan_id');
    }
}
