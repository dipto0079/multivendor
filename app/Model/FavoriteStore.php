<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FavoriteStore extends Model
{
    //
    public function getSeller(){
        return $this->belongsTo(Seller::class,'seller_id');
    }

    public static function isExistInTheList($seller_id, $favorite_stores)
    {
        foreach ($favorite_stores as $fs) {
            if ($fs->seller_id == $seller_id)
                return 1;
        }
        return 0;
    }
}
