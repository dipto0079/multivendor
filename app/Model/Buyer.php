<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Enum\UserTypeEnum;

class Buyer extends Model
{
    public function getUser()
    {
        return $this->belongsTo('App\User', 'user_id')->where('user_type', UserTypeEnum::USER);
    }

    function getBuyerByUserID($uid)
    {
        $buyer = Buyer::where('user_id',$uid)->first();
        return $buyer;
    }
    public function getCountryName(){
        return $this->belongsTo(Country::class,'country');
    }

    // review
    public function getReview(){
        return $this->hasOne(ProductReview::class,'buyer_id');
    }

    // store review
    public function getStoreReview(){
        return $this->hasOne(StoreReview::class,'buyer_id');
    }

    // Get District
    function getDistrict(){
        return $this->belongsTo(City::class,'city');
    }
}
