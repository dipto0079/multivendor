<?php

namespace App\Model;

use App\Http\Controllers\Enum\ShippingTypeEnum;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    // Get Shipping Rate
    public function getShippingRateByWeight()
    {
        return $this->hasMany(ShippingRate::class,'shipping_id');
    }

    // Get Shipping Rate By Order Price
    public function getShippingRateByOrderPrice()
    {
        return $this->hasMany(ShippingRate::class,'shipping_id');
    }

    // get Shipping Pickup
    public function getShippingPickup()
    {
        return $this->hasOne(ShippingStorePickUp::class,'shipping_id');
    }

    // get Country
    public function getCountry()
    {
        return $this->belongsTo(Country::class,'country_id');
    }

    // Get Cities
    public static function getCities($city_ids)
    {
        $city = '';

        if(!empty($city_ids)){
            $cities = explode(',',$city_ids);
            $first = 0;
            foreach($cities as $city_id){
                $city_info = City::where('id',$city_id)->first();
                if($first == 0){
                    $city = (\App\UtilityFunction::getLocal() == 'en') ? $city_info->name : $city_info->ar_name;
                    $first = 1;
                }else{
                    $city .= (\App\UtilityFunction::getLocal() == 'en') ? ',<br>'.$city_info->name : ','.$city_info->ar_name;
                }
            }
        }

        return $city;
    }
}
