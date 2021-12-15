<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
//    public static function getActiveCategory($category_url,$is_sub){
//        $category_level = ['first'=>'','second'=>'','third'=>''];
//        $sub_category='';
//        $category='';
//        if(!empty($category_url)){
//            $category_url = str_replace('_', ' ', $category_url);
//            $category_url = str_replace('amp', '&', $category_url);
////            if($category_url==$is_sub) {
//                $category = ProductCategory::where('name', $category_url)->orWhere('ar_name', $category_url)->first();
//                if (!empty($category)) $category_level['first'] = 1;
////            }
//        }
//        if(!empty($category) && $category->product_category_level_no>1){
//            $sub_category = ProductCategory::where('parent_category_id',$category->parent_category_id)->first();
//            if(!empty($sub_category))$category_level['second']=2;
//        }
////        dd($is_sub,$category_level);
//        return $category_level;
//    }


}
