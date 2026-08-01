<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandarProduct extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'unit', 'stock'];

    public function transactions()
    {
        return $this->hasMany(BandarTransaction::class, 'product_id');
    }
}
