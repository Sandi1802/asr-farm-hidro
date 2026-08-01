<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'quantity', 'unit', 'description', 'image'];

    public function logs()
    {
        return $this->hasMany(InventoryLog::class)->orderBy('created_at', 'desc');
    }
}
