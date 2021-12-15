<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreReview extends Model
{
    //
    public function getBuyer(){
        return $this->belongsTo(Buyer::class,'buyer_id');
    }
}
