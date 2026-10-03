<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaprikaLog extends Model
{
    use HasFactory;

    protected $table = 'paprika_logs';
    protected $fillable = ['pot_id', 'action_type', 'details'];

    public function pot()
    {
        return $this->belongsTo(PaprikaPot::class, 'pot_id');
    }
}
