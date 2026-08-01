<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'event_time', 'event_type', 'color', 'user_id'
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
