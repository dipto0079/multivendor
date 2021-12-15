<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerPayment extends Model
{
    //
    public function getPaymentSubOrder(){
        return $this->hasMany(SubOrder::class,'seller_payment_id');
    }

    public function getSeller(){
        return $this->belongsTo(Seller::class,'seller_id');
    }
}
