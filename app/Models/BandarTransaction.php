<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandarTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'partner_id', 'type', 'quantity', 'price', 'date', 'notes'];

    public function product()
    {
        return $this->belongsTo(BandarProduct::class, 'product_id');
    }

    public function partner()
    {
        return $this->belongsTo(BandarPartner::class, 'partner_id');
    }
}
