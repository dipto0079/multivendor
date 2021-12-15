<?php

namespace App\Model;

use App\Http\Controllers\Enum\ProductTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Http\Controllers\Enum\DealStatusEnum;


class ProductCategory extends Model
{

    public function getParentCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_category_id');
    }

    public function getSubCategory()
    {
        return $this->hasMany(ProductCategory::class, 'parent_category_id')->OrderBy('name', 'asc');
    }

    public static function getServiceCategoryForMenu()
    {
        $categories = ProductCategory::whereNull('parent_category_id')
            ->where('show_in_public_menu', 1)
            ->where('product_category_type_id', 2)
            ->OrderBy('name', 'asc')
            ->get();
        return $categories;
    }

    public static function getProductCount($category, $product_type, $filter="", $store_name=null)
    {
        ProductCategory::$category_ids=[];
        $all_child_categories = ProductCategory::getAllChildCategory([$category]);
        $now = Carbon::now()->format('Y-m-d 00:00:00');

        if(isset($store_name))
            $products_count = Product::join('sellers', 'sellers.id', '=', 'products.seller_id')->where('sellers.store_name',$store_name)->wherein('products.category_id', $all_child_categories);
        else
            $products_count = Product::wherein('products.category_id', $all_child_categories);

        if($product_type == ProductTypeEnum::PRODUCT)
            $products_count = $products_count->where('products.quantity','>',0);
        elseif($product_type == ProductTypeEnum::SERVICE)
            $products_count = $products_count->join('subscriptions','subscriptions.seller_id','=','products.seller_id')->where('subscriptions.from_date', '<=', $now)->where('subscriptions.to_date', '>=', $now);

        if($filter=="deals") $products_count = $products_count->join('deals', 'products.id', 'deals.product_id')->where('from_date', '<=', $now)->where('to_date', '>=', $now)->where('deals.status', DealStatusEnum::APPROVED);


        return  $products_count->count();
    }

    public function subCategory()
    {
        return $this->hasMany(ProductCategory::class, 'parent_category_id');
    }

//    public static function rootParentCategory($category_id)
//    {
//        $parent = ProductCategory::find($category_id);
//        if (isset($parent->parent_category_id)) {
//            ProductCategory::rootParentCategory($parent->parent_category_id);
//        }
//        return $parent;
//    }


    public static $rootParent ="";
    public static function rootParentCategory($category_id)
    {
        $parent = ProductCategory::find($category_id);
        if (isset($parent->parent_category_id)) ProductCategory::rootParentCategory($parent->parent_category_id);
        else ProductCategory::$rootParent = $parent;
        return ProductCategory::$rootParent;
    }


    public static function getAllParentCategory($category_id, $category_ids)
    {
        array_push($category_ids, "" . $category_id);

        $parent = ProductCategory::find($category_id);
        if (isset($parent)) $parent = $parent->parent_category_id;

        if (is_null($parent)) return $category_ids;

        return ProductCategory::getAllParentCategory($parent, $category_ids);
    }


    public static $category_ids = [];

    public static function getAllChildCategory($categories)
    {
        foreach ($categories as $category) {
            array_push(ProductCategory::$category_ids, $category->id);
            if (count($category->subCategory) > 0)
                ProductCategory::getAllChildCategory($category->subCategory);
        }

        return ProductCategory::$category_ids;
    }

    public static function isCategoryExists($category_id, $category_ids)
    {
        if (in_array($category_id, $category_ids)) {
            return 1;
        } else {
            return 0;
        }
    }
}
