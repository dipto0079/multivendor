<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FavoriteProduct extends Model
{
    //
    public function getProduct(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
