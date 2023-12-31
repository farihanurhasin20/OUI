<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'total_price',
        'order_amount',
    ];


    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

