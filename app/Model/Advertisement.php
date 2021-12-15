<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    public static function getAdvertisement($position, $item = 0)
    {
        if (!empty($position)) {
            $advertisements = Advertisement::where('end', '>', date('Y-m-d'));
            if (!empty($item)) {
                $advertisements = $advertisements->take($item)->latest();
            }
            $advertisements = $advertisements->get();

            $filterADs = array();
            foreach ($advertisements as $ad) {
                $position_arr = explode(',', $ad->position);
                if (in_array($position, $position_arr)) {
                    array_push($filterADs, $ad);
                }
            }
            $advertisements = $filterADs;



            return $advertisements;


        }


    }
}
