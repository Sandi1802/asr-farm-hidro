<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonvenLubangTanamV2 extends Model
{
    protected $table = 'konven_lubang_tanams';
    protected $fillable = [
        'bedengan_id',
        'nomor_lubang',
        'status',
        'plant_name',
        'planted_at',
        'estimated_harvest_at',
        'harvested_at',
        'catatan',
    ];

    protected $casts = [
        'planted_at'           => 'date',
        'estimated_harvest_at' => 'date',
        'harvested_at'         => 'date',
    ];

    public function bedengan(): BelongsTo
    {
        return $this->belongsTo(KonvenBedenganV2::class, 'bedengan_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(KonvenTanamLogV2::class, 'lubang_id');
    }
}
