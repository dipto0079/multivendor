<?php

namespace App\Model;

use App\UtilityFunction;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
    public static function getCountryName($country_id){
        $country_name = '';

        if(!empty($country_id)){
            $country = Country::where('id',$country_id)->orderBy('name','asc')->first();

            $country_name = (UtilityFunction::getLocal() == "en") ? $country->name : $country->ar_name;
        }

        return $country_name;
    }
}
