<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getSubOrder()
    {
        return $this->belongsTo(SubOrder::class, 'sub_order_id');
    }
}
