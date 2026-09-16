<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'shift', 'task_name', 'status', 'notes', 'is_pr', 
        'created_by', 'completed_by', 'completed_at'
    ];

    protected $casts = [
        'date' => 'date',
        'is_pr' => 'boolean',
        'completed_at' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
