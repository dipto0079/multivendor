<?php

namespace App\Model;

use App\Http\Controllers\Enum\CouponDiscountTypeEnum;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    //
    public static function getAmountAfterCouponApplied($coupon,$amount){
        $coupon_data = Coupon::where('id',$coupon)->first();

        if(isset($coupon_data)){
            if($coupon_data->discount_type == CouponDiscountTypeEnum::FIXED){
                $amount = $amount-$coupon_data->discount;
            }
            elseif($coupon_data->discount_type == CouponDiscountTypeEnum::PERCENTAGE){
                $amount =  $amount-($amount*($coupon_data->discount/100));
            }
        }

        if($amount <0) return 0;

        return $amount;
    }

    //
    public static function getCouponVal($id){
        $coupon = Coupon::find($id);

        if(isset($coupon)){
            return $coupon->coupon;
        }
        return '';
    }
}
