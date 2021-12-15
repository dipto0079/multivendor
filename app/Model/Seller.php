<?php

namespace App\Model;

use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\ProductStatusEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\PaymentStatusEnum;
use App\UtilityFunction;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Seller extends Model
{
    //
    public function getUser()
    {
        return $this->belongsTo('App\User', 'user_id')->where('user_type', UserTypeEnum::SELLER);
    }

    // Get Category
    public function getCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Get Stores
    public function getStores()
    {
        return $this->hasMany(Store::class, 'seller_id');
    }

    // Get Product
    public function getProducts()
    {
        return $this->hasMany(Product::class, 'seller_id')->where('status', ProductStatusEnum::SHOWN);
    }

    public static function getSellerByUserID($uid)
    {
        $seller = Seller::where('user_id', $uid)->first();
        return $seller;
    }

    // get review
    public function getReview()
    {
        return $this->hasMany(StoreReview::class, 'seller_id');
    }

    // Delivered Count
    public function getDeliveredCount()
    {
        return $this->hasMany(SubOrder::class, 'seller_id')->where('status', OrderStatusEnum::DELIVERED);
    }

    // Favorite Store
    public function getFavoriteStore()
    {
        $buyer_id = '';
        if (!empty(Auth::user()) && Auth::user()->user_type == UserTypeEnum::USER) $buyer_id = Auth::user()->getBuyer->id;
        return $this->hasMany(FavoriteStore::class, 'seller_id')->where('buyer_id', $buyer_id);
    }

    // Get Country
    public function getCountry()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    // Seller Commission
    public function getSellerCommission()
    {
        return $this->hasMany(SellerPayment::class, 'seller_id');
    }

    // Static functions
    // Total earning
    public function getTotalEarning()
    {
        return $this->hasMany(SellerPayment::class, 'seller_id')->where('status', PaymentStatusEnum::COMPLETED);
    }

    // Get Shipping
    public function getShipping()
    {
        return $this->hasMany(Shipping::class, 'seller_id');
    }

    // total service media
    public static function getTotalServiceMedia($seller_id)
    {
        $total_media = [];

        if (!empty($seller_id)) {
            $products = Product::where('seller_id', $seller_id)->get();

            foreach ($products as $product) {
                $media = $product->getMedia()->orderBy('created_at')->get();
                $total_media[] = $media;
            }
            $total_media = array_collapse($total_media);
        }


        return $total_media;
    }

    public static function isStringExist($city_ids, $search_cities)
    {
        if (!isset($search_cities)) return false;
//        $city_ids = explode(',', $city_ids);
//        $search_cities = explode(',', $search_cities);
        foreach ($search_cities as $c) {
            if (in_array($c, $city_ids)) {
                return false;
            }
        }
        return true;
    }


    // Get Shipping Eligible
    public static function createEligibleDeliveryCityList($country_id, $edit_shipping=null)
    {

        $cities_html = "";
        $current_cities=[];

        $all_city = Shipping::where('seller_id', Auth::user()->getSeller->id)->where('country_id', $country_id)->where('city_ids', "")->exists();

        if ($all_city) return $cities_html;
        else {
            $shippings = Shipping::where('seller_id', Auth::user()->getSeller->id)->where('country_id', $country_id);
            if (isset($edit_shipping)) {
                $shippings = $shippings->where('id', '!=', $edit_shipping->id);
                $current_cities = explode(',', $edit_shipping->city_ids);
            }
            $shippings = $shippings->get();

            $already_shipping_cities = [];
            foreach ($shippings as $shipping) {
                $city_ids = explode(',', $shipping->city_ids);
                foreach ($city_ids as $c) {
                    if (in_array($c, $already_shipping_cities) == false) array_push($already_shipping_cities, $c);
                }
            }

            $all_city_by_country = City::where('shipping_status', 1)->where('country_id', $country_id)->get();
            foreach ($all_city_by_country as $c) {
                if (in_array($c->id, $already_shipping_cities) == false) {
                    $cities_html .= '<option value="' . $c->id . '"';

                    if(!empty($current_cities[0]) && in_array($c->id,$current_cities)) {
                        $cities_html .=' selected ';
                    }

                    $cities_html .= '>';

                    if (UtilityFunction::getLocal() == "en") $cities_html .= $c->name;
                    else $cities_html .= $c->ar_name;
                    $cities_html .= '</option>';
                }
            }

            return $cities_html;
        }
    }


    // Get Shipping Eligible
    public static function isCitiesShippingEligible($country_id, $cities)
    {


        $eligibleCityArray = [];

        $all_city = Shipping::where('seller_id', Auth::user()->getSeller->id)->where('country_id', $country_id)->where('city_ids', "")->exists();

        if ($all_city) return $eligibleCityArray;
        else {
            $shippings = Shipping::where('seller_id', Auth::user()->getSeller->id)->where('country_id', $country_id)->get();

            $already_shipping_cities = [];

            foreach ($shippings as $shipping) {
                $city_ids = explode(',', $shipping->city_ids);

                foreach ($city_ids as $c) {
                    if (in_array($c, $already_shipping_cities) == false)
                        array_push($already_shipping_cities, $c);
                }
            }

            $all_city_by_country = City::where('shipping_status', 1)->where('country_id', $country_id)->get();
            foreach ($all_city_by_country as $c) {
                if (in_array($c->id, $already_shipping_cities) == false) {
                    array_push($eligibleCityArray, $c->id);
                }
            }

            if (is_null($cities)) {
                $cities = Shipping::where('seller_id', Auth::user()->getSeller->id)->where('country_id', $country_id)->select('id')->get();
                foreach ($cities as $city) {
                    if (in_array($city->id, $eligibleCityArray) == false) {
                        return false;
                    }
                }
            } else {
                foreach ($cities as $city_id) {
                    if (in_array($city_id, $eligibleCityArray) == false) {
                        return false;
                    }
                }
            }

            return true;
        }
    }
}
