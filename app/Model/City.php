<?php

namespace App\Model;

use App\UtilityFunction;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public static function getCity(){
        $cities = City::OrderBy('name','asc')->get();
        return $cities;
    }
    public function getLocationByCity(){
        return $this->hasMany(Location::class,'city_id')->orderBy('name','asc');
    }

    public function getCountry()
    {
        return $this->belongsTo(Country::class,'country_id');
    }

    public static function getCityName($city_id){
        $city_name = '';

        if(!empty($city_id)){
            $city = City::where('id',$city_id)->first();

            $city_name = (UtilityFunction::getLocal() == "en") ? $city->name : $city->ar_name;
        }

        return $city_name;
    }
}
