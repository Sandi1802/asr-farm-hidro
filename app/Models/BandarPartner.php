<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandarPartner extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type', 'phone', 'address'];

    public function transactions()
    {
        return $this->hasMany(BandarTransaction::class, 'partner_id');
    }
}
