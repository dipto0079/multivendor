<?php

namespace App\Model;

use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\ProductTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Http\Controllers\Enum\DealStatusEnum;
use App\Http\Controllers\Enum\DiscountTypeEnum;
use App\Http\Controllers\Enum\ProductStatusEnum;
use DB;

class Product extends Model
{
    public function getSeller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function getMedia()
    {
        return $this->hasMany(Media::class, 'product_id');
    }

    public function getCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getReview()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function getProductDeals()
    {
        $now = Carbon::now()->format('Y-m-d 00:00:00');

        return $this->hasOne(Deal::class, 'product_id')->where('from_date', '<=', $now)
            ->where('to_date', '>=', $now)
            ->where('status', DealStatusEnum::APPROVED);
    }

    // get wish list
    public function getWishList()
    {
        return $this->hasMany(FavoriteProduct::class, 'product_id');
    }

    // get featured product
    public function getFeaturedProduct()
    {
        return $this->hasOne(FeaturedProductSubscription::class, 'product_id');
    }

    //get deal
    public function getDeal()
    {
        $now = Carbon::now()->format('Y-m-d 00:00:00');

        $deal = Deal::where('product_id', $this->id)
            ->where('from_date', '<=', $now)
            ->where('to_date', '>=', $now)
            ->where('status', DealStatusEnum::APPROVED)
            ->first();

        return $deal;
    }

    // reviewAvailability
    static function reviewAvailability($product_id,$buyer_id){
        $exists = 0;
        try{
            if(!empty($product_id) && !empty($buyer_id)){
                $exists = ProductReview::where('product_id',$product_id)->where('buyer_id',$buyer_id)->exists();
            }
            return $exists;
        }catch (\Exception $e){
            return $exists;
        }
    }

    public static function getProductRating($product_id){
        $rate_count=0;
        $total_user_count=0;
        $final_rating=0;
        $average_rating=0;
        $ratings = ProductReview::where('product_id',$product_id)
            ->select(
                'product_reviews.review_rating',
                DB::raw('(product_reviews.review_rating*count(product_reviews.review_rating)) as rate_count'),
                DB::raw('(count(product_reviews.review_rating)) as user_count')
            )
            ->groupBy('product_reviews.review_rating')
            ->get();

        if(isset($ratings[0])){
            foreach($ratings as $rating){
                $rate_count = $rate_count+$rating->rate_count;
                $total_user_count = $total_user_count+$rating->user_count;
            }
            $average_rating =$rate_count/$total_user_count;
            $final_rating = ($rate_count/$total_user_count)*20;

        }
        return ['final_rating'=>$final_rating,'total_user_count'=>$total_user_count,'average_rating'=>$average_rating];
    }

    public static function EnglishToArabic($str)
    {
        $western_arabic = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $eastern_arabic = array('?', '?', '?', '?', '?', '?', '?', '?', '?', '?');

        $str = str_replace($western_arabic, $eastern_arabic, $str);
        return $str;
    }

    //get Deal Discount Amount
    public function getDealDiscountHTMLArray()
    {
        $currency_symbol = env('CURRENCY_SYMBOL');

        $discountArray = array();

        $deal = $this->getDeal();

        if (isset($deal)) {
            $discount = $deal->discount;

            if ($deal->discount_type == DiscountTypeEnum::PERCENTAGE) $discount = $this->price * ($discount / 100);

            if ($discount < 0) $discount = 0;

            $discountArray[0] = $currency_symbol . number_format($this->price - $discount, 2) . '<span>' . $currency_symbol . number_format($this->price, 2) . '</span>';

            if ($deal->discount_type == DiscountTypeEnum::PERCENTAGE) $discountArray[1] = '<span class="product-item_sale color-main font-additional customBgColor circle">-' . $deal->discount . '%</span>';
            else $discountArray[1] = '<span class="product-item_new color-main font-additional text-uppercase circle">-' . $deal->discount . '</span>';

            return $discountArray;
        }

        $discountArray[0] = $currency_symbol . number_format($this->price, 2);
        $discountArray[1] = "";


        return $discountArray;
    }

    //get similar products
    public function getSimilarProducts()
    {
        $similar_products = Product::where('category_id', $this->category_id)->where('products.status',ProductStatusEnum::SHOWN)->where('quantity', '>', 0)->where('id', '<>', $this->id)->get()->take(10);
        //dd($similar_products,$this->id);
        return $similar_products;
    }

    //get the featured products that are related to the current product category
    public static function getSimilarFeaturedProducts($category)
    {
        if (isset($category)) {
            $allChild = ProductCategory::getAllChildCategory([$category]);
            if ($category->product_category_type_id == ProductTypeEnum::PRODUCT) {
                $featured_products = Product::wherein('products.category_id', $allChild)->where('products.status',ProductStatusEnum::SHOWN)->where('quantity', '>', 0);
            } elseif ($category->product_category_type_id == ProductTypeEnum::SERVICE) {
                $now = Carbon::now()->format('Y-m-d 00:00:00');
                $featured_products = Product::wherein('products.category_id', $allChild)->where('products.status',ProductStatusEnum::SHOWN)->join('subscriptions', 'subscriptions.seller_id', '=', 'products.seller_id')->where('subscriptions.from_date', '<=', $now)->where('subscriptions.to_date', '>=', $now);
            }

            $featured_products = $featured_products->select('products.*')->where('products.is_featured', 1)->inRandomOrder()->get()->take(10);

            return $featured_products;
        }
        return null;
    }

// get new arrival product list
    public
    function getNewArrivalProducts()
    {
        $rootParent = ProductCategory::rootParentCategory($this->category_id);
        $allChild = ProductCategory::getAllChildCategory([$rootParent]);

        if ($rootParent->product_category_type_id == ProductTypeEnum::PRODUCT) {
            $new_arrivals = Product::wherein('products.category_id', $allChild)->where('products.status',ProductStatusEnum::SHOWN)->where('quantity', '>', 0);
        } elseif ($rootParent->product_category_type_id == ProductTypeEnum::SERVICE) {
            $now = Carbon::now()->format('Y-m-d 00:00:00');
            $new_arrivals = Product::wherein('products.category_id', $allChild)->join('subscriptions', 'subscriptions.seller_id', '=', 'products.seller_id')->where('products.status',ProductStatusEnum::SHOWN)->where('subscriptions.from_date', '<=', $now)->where('subscriptions.to_date', '>=', $now);
        }

        $new_arrivals = $new_arrivals->select('products.*')->where('products.id', '<>', $this->id)->orderby('products.created_at', 'desc')->get()->take(10);

//        $new_arrivals = Product::wherein('category_id', $allChild)->where('quantity', '>', 0)->where('id', '<>', $this->id)->orderby('created_at', 'desc')->get()->take(10);

        return $new_arrivals;
    }

//top deal in categories
    public static function topDealInCategories()
    {
        $all_root_categories = ProductCategory::wherenull('parent_category_id')->get();

        foreach ($all_root_categories as $rc) {
            ProductCategory::$category_ids = [];
            $allChild = ProductCategory::getAllChildCategory([$rc]);

            $now = Carbon::now()->format('Y-m-d 00:00:00');
            $rc->deal_count = Deal::join('products', 'products.id', 'deals.product_id')
                ->where('from_date', '<=', $now)
                ->where('to_date', '>=', $now)
                ->where('deals.status', DealStatusEnum::APPROVED)
                ->where('products.status', ProductStatusEnum::SHOWN)
                ->where('products.quantity', '>', 0)
                ->wherein('products.category_id', $allChild)
                ->count();

        }

        $sorted = $all_root_categories->sortByDesc('deal_count');
        $sorted = $sorted->take(10);
        return $sorted;
    }

//deal of the day products
    public
    static function dealOfTheDayProducts()
    {
        $now = Carbon::now()->format('Y-m-d 00:00:00');

        $deal_of_the_day_products = Deal::join('products', 'products.id', 'deals.product_id')
            ->select('products.id'
                , 'products.price'
                , 'products.name'
                , 'products.ar_name'
                , 'deals.title as deal_title'
                , 'deals.discount_type'
                , 'deals.discount'
            )
            ->where('from_date', '<=', $now)
            ->where('to_date', '>=', $now)
            ->where('products.quantity', '>', 0)
            ->where('deals.status', DealStatusEnum::APPROVED)
            ->where('products.status', ProductStatusEnum::SHOWN)
            ->orderbyraw("CASE WHEN deals.discount_type = 2 THEN (products.price * (deals.discount/100)) ELSE deals.discount END desc")
            ->get()->take(10);
        return $deal_of_the_day_products;
    }

//trending offers
    public
    static function trendingOffers()
    {
        $now = Carbon::now()->format('Y-m-d 00:00:00');

        $trending_offers = Deal::join('products', 'products.id', 'deals.product_id')
            ->select('products.id'
                , 'products.price'
                , 'products.name'
                , 'products.ar_name'
                , 'deals.title as deal_title'
                , 'deals.discount_type'
                , 'deals.discount'
            )
            ->where('from_date', '<=', $now)
            ->where('to_date', '>=', $now)
            ->where('products.status',ProductStatusEnum::SHOWN)
            ->where('products.quantity', '>', 0)
            ->where('deals.status', DealStatusEnum::APPROVED)
            ->orderby('products.created_at', 'desc')
            ->get()->take(10);

        return $trending_offers;
    }

//editors choice deals
    public
    static function editorsChoice()
    {
        $now = Carbon::now()->format('Y-m-d 00:00:00');

        $editors_choice = Deal::join('products', 'products.id', 'deals.product_id')
            ->select('products.id'
                , 'products.price'
                , 'products.name'
                , 'products.ar_name'
                , 'deals.title as deal_title'
                , 'deals.discount_type'
                , 'deals.discount'
            )
            ->where('from_date', '<=', $now)
            ->where('to_date', '>=', $now)
            ->where('products.quantity', '>', 0)
            ->where('products.status',ProductStatusEnum::SHOWN)
            ->where('deals.status', DealStatusEnum::APPROVED)
            ->where('products.is_editors_choice', 1)
            ->orderby('products.created_at', 'desc')
            ->get()->take(10);

        return $editors_choice;
    }


// Top seller
    public
    static function topSeller()
    {
        $top_seller = Seller::join('sub_orders', 'sellers.id', '=', 'sub_orders.seller_id')
            ->join('order_items', 'order_items.sub_order_id', 'sub_orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(DB::raw('count(products.id) as order_items_count'), 'sellers.user_id', 'sellers.category_id', 'sellers.id', 'sellers.store_name', 'sellers.business_type')
            ->where('sub_orders.status', OrderStatusEnum::DELIVERED)
            ->groupBy('sub_orders.seller_id')
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get()
            ;

        return $top_seller;
    }

    // get product total purchase
    static function getTotalPurchase($product_id){
        $total_purchase = 0;
        try{
            $sub_order = SubOrder::join('order_items', 'order_items.sub_order_id', 'sub_orders.id')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->select(DB::raw('count(products.id) as order_items_count'), 'sellers.category_id', 'sellers.id', 'sellers.store_name', 'sellers.business_type')
                ->where('products.id',$product_id)
                ->first();

            $total_purchase = $sub_order->order_items_count;

            return $total_purchase;
        }catch (\Exception $e){
            return $total_purchase;
        }
    }

    // get User Favorite Product
    public static function getUserFavoriteProduct($product_id, $buyer_id=null){
        $count = 0;
        try{
            if(!empty($product_id) && !empty($buyer_id)){
                $count = FavoriteProduct::where('product_id',$product_id)->where('buyer_id',$buyer_id)->exists();
                if($count == true) $count = 1;
                else $count = 0;
            }
            return $count;
        }catch (\Exception $e){
            return $count;
        }
    }



}























//    public static function getBestSellerProducts($top)
//    {
//
//    }

//    public function getAllCategoryByProduct(){
//        return $this->hasMany(SelectedProductCategory::class,'product_id');
//    }


//    //get Deal Discount Amount
//    public function getDealDiscount()
//    {
//        $deal = $this->getDeal();
//
//        if(isset($deal))
//        {
//            $discount = $deal->discount;
//
//            if ($deal->discount_type == DiscountTypeEnum::PERCENTAGE) $discount = $this->price - ($this->price * ($discount / 100));
//
//            if ($discount < 0) $discount = 0;
//
//            return number_format($discount,2);
//        }
//
//        return 0;
//    }
