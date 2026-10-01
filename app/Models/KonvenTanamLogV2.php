<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonvenTanamLogV2 extends Model
{
    protected $table    = 'konven_tanam_logs';
    protected $fillable = ['lubang_id', 'action_type', 'plant_name', 'details', 'created_by'];

    protected $casts = [
        'details' => 'array',
    ];

    public function lubang(): BelongsTo
    {
        return $this->belongsTo(KonvenLubangTanamV2::class, 'lubang_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
