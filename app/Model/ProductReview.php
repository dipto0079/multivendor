<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    //
    public function getBuyer(){
        return $this->belongsTo(Buyer::class,'buyer_id');
    }

    public function getProduct()
    {
      return $this->belongsTo(Product::class,'product_id');
    }

    public static function buyerShortName($buyer_name)
    {
      $shortName = '';

      if(!empty($buyer_name)){
        $buyer_name_exp = explode(' ',$buyer_name);

        foreach($buyer_name_exp as $sm){
          $shortName .= $sm[0];
        }
      }

      return $shortName;
    }
}
