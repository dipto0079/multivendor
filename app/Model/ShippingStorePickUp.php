<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShippingStorePickUp extends Model
{
    //
    // get Country
    public function getCountry()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
}
