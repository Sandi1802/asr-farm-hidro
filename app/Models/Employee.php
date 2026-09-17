<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'name',
        'position',
        'department',
        'email',
        'phone',
        'status',
        'avatar',
    ];

    // Relasi ke User berdasarkan email yang sama
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
