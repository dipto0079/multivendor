<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    public static function isSameDealExist($current_deal, $product_id, $from, $to)
    {
        $deals = Deal::where('product_id', $product_id)
            ->whereDate('to_date', '>=', $from)
            ->whereDate('from_date', '<=', $to)
            ->where('id','<>',$current_deal->id)
            ->get();
        return count($deals);
    }

    public function getProduct(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
