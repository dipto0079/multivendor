<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\StaticPageEnum;
use App\Model\NewsLetterSubscriber;
use App\Model\ProductCategory;
use App\Model\Product;
use App\Model\FavoriteStore;
use App\Model\FavoriteProduct;
use App\Model\City;
use App\Model\StaticPage;
use App\Model\CartItem;
use Folklore\Image\Facades\Image;
use Illuminate\Http\Request;
use App\User;
use App\Model\Seller;
use App\Model\Buyer;
use App\Model\Country;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;
use App\Http\Controllers\Enum\DealStatusEnum;
use App\Http\Controllers\Enum\ProductStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Hash;
use Cookie;
use Crypt;
use App;
use App\Http\Controllers\Enum\ProductTypeEnum;
use App\UtilityFunction;
use Carbon\Carbon;


class ProductListController extends Controller
{
    public function buildCategoryMenu($cid, $first_child_cid, $second_child_cid, $product_type, $categories, $store_name = null)
    {
        $filter = '';
        $queryString = '';
        $category_menu = '';

        if (isset($store_name)) $url = url('/store/' . $store_name);
        else {
            if ($product_type == ProductTypeEnum::PRODUCT) $url = url('/products/category');
            else $url = url('/service/category');
        }

        if (isset($_GET['filter'])) $filter = $_GET['filter'];

        if (isset($cid) && isset($first_child_cid) && isset($second_child_cid)) {
            $category_menu .= '<ul><li><a href="' . $url . $queryString . '"><i class="fa fa-angle-left"></i> ' . trans('messages.all_categories') . '</a></li></ul>';
            $category_menu .= '<ul class="ul_step_1"><li><a style="font-weight: normal; color: #ff8300" href="' . $url . '/' . $cid . $queryString . '"><i class="fa fa-angle-left"></i> ';
            $c = ProductCategory::find($cid);

            if (UtilityFunction::getLocal() == "en") $category_menu .= $c->name;
            else $category_menu .= $c->ar_name;

            $c_count = ProductCategory::getProductCount($c, $product_type, $filter, $store_name);

            if ($c_count > 0) $category_menu .= ' <span>(' . $c_count . ')</span>';

            $category_menu .= '</span></li></ul></a>';
            $category_menu .= '<ul class="ul_step_2"><li><a style="font-weight: normal; color: #ff8300" href="' . $url . '/' . $cid . '/' . $first_child_cid . $queryString . '"><i class="fa fa-angle-left"></i> ';
            $fc = ProductCategory::find($first_child_cid);
            if (UtilityFunction::getLocal() == "en") $category_menu .= $fc->name;
            else $category_menu .= $fc->ar_name;
            $category_count = ProductCategory::getProductCount($fc, $product_type, $filter, $store_name);
            if ($category_count > 0) $category_menu .= ' <span>(' . $category_count . ')</span>';
            $category_menu .= '</span></li></ul></a>';

            $first_sub_categories = ProductCategory::where('parent_category_id', $first_child_cid)->OrderBy('name', 'asc')->get();
            $category_menu .= '<ul class="ul_step_3">';

            foreach ($first_sub_categories as $first_sub_category) {
                $first_sub_category_count = ProductCategory::getProductCount($first_sub_category, $product_type, $filter, $store_name);
                if ($first_sub_category_count > 0) {
                    $category_menu .= '<li ';
                    if (isset($second_child_cid) && $first_sub_category->id == $second_child_cid) $category_menu .= ' class="selected"';

                    $category_menu .= '>';
                    $category_menu .= '<a href="' . $url . '/' . $cid . '/' . $first_child_cid . '/' . $first_sub_category->id . $queryString . '">';

                    if (UtilityFunction::getLocal() == "en") $category_menu .= $first_sub_category->name;
                    else $category_menu .= $first_sub_category->ar_name;

                    $category_menu .= ' <span>(' . $first_sub_category_count . ') </span>';
                    $category_menu .= '</span></a></li>';
                }
            }
            $category_menu .= '</ul>';

        } elseif (isset($cid) && isset($first_child_cid)) {
            $category_menu .= '<ul><li><a href="' . $url . $queryString . '"><i class="fa fa-angle-left"></i> ' . trans('messages.all_categories') . '</a></li></ul>';
            $category_menu .= '<ul class="ul_step_1"><li><a style="font-weight: normal; color: #ff8300" href="' . $url . '/' . $cid . $queryString . '"><i class="fa fa-angle-left"></i> ';
            $c = ProductCategory::find($cid);

            if (UtilityFunction::getLocal() == "en") $category_menu .= $c->name;
            else $category_menu .= $c->ar_name;

            $c_count = ProductCategory::getProductCount($c, $product_type, $filter, $store_name);

            if ($c_count > 0) $category_menu .= ' <span>(' . $c_count . ')</span>';

            $category_menu .= '</span></li></ul></a>';

            foreach ($categories as $category) {
                $category_menu .= '<ul class="ul_step_2"><li><a style="font-weight: bold" href="' . $url . '/' . $cid . '/' . $first_child_cid . $queryString . '">';

                if (UtilityFunction::getLocal() == "en") $category_menu .= $category->name;
                else $category_menu .= $category->ar_name;

                $category_count = ProductCategory::getProductCount($category, $product_type, $filter, $store_name);
                if ($category_count > 0) $category_menu .= ' <span>(' . $category_count . ')</span>';
                $category_menu .= '</span></li></ul></a>';

                //display all first level subcategories
                $first_sub_categories = ProductCategory::where('parent_category_id', $category->id)->OrderBy('name', 'asc')->get();
                $category_menu .= '<ul class="ul_step_3">';

                foreach ($first_sub_categories as $first_sub_category) {
                    $first_sub_category_count = ProductCategory::getProductCount($first_sub_category, $product_type, $filter, $store_name);
                    if ($first_sub_category_count > 0) {
                        $category_menu .= '<li ';
                        if (isset($second_child_cid) && $first_sub_category->id == $second_child_cid) $category_menu .= ' class="selected"';

                        $category_menu .= '>';
                        $category_menu .= '<a href="' . $url . '/' . $cid . '/' . $category->id . '/' . $first_sub_category->id . $queryString . '">';

                        if (UtilityFunction::getLocal() == "en") $category_menu .= $first_sub_category->name;
                        else $category_menu .= $first_sub_category->ar_name;

                        $category_menu .= ' <span>(' . $first_sub_category_count . ') </span>';
                        $category_menu .= '</span></a></li>';
                    }
                }
                $category_menu .= '</ul>';
            }
        } elseif (isset($cid)) {
            $category_menu .= '<ul><li><a href="' . $url . $queryString . '"><i class="fa fa-angle-left"></i> ' . trans('messages.all_categories') . '</a></li></ul>';
            foreach ($categories as $category) {
                $category_menu .= '<ul class="ul_step_1"><li><a href="' . $url . '/' . $category->id . $queryString . '">';

                if (UtilityFunction::getLocal() == "en") $category_menu .= $category->name;
                else $category_menu .= $category->ar_name;

                $category_count = ProductCategory::getProductCount($category, $product_type, $filter, $store_name);
                if ($category_count > 0) $category_menu .= ' <span>(' . $category_count . ')</span>';
                $category_menu .= '</span></li></ul></a>';
                $first_sub_categories = ProductCategory::where('parent_category_id', $category->id)->OrderBy('name', 'asc')->get();

                $category_menu .= '<ul class="ul_step_2">';
                foreach ($first_sub_categories as $first_sub_category) {
                    $first_sub_category_count = ProductCategory::getProductCount($first_sub_category, $product_type, $filter, $store_name);
                    if ($first_sub_category_count > 0) {
                        $category_menu .= '<li>';
                        $category_menu .= '<a href="' . $url . '/' . $category->id . '/' . $first_sub_category->id . $queryString . '">';

                        if (UtilityFunction::getLocal() == "en") $category_menu .= $first_sub_category->name;
                        else $category_menu .= $first_sub_category->ar_name;

                        $category_menu .= ' <span>(' . $first_sub_category_count . ') </span>';
                        $category_menu .= '</span></a></li>';
                    }
                }
                $category_menu .= '</ul>';
            }
        } else {
            foreach ($categories as $category) {
                $category_count = ProductCategory::getProductCount($category, $product_type, $filter, $store_name);
                if ($category_count > 0) {
                    $category_menu .= '<a class="cata-box" href="' . $url . '/' . $category->id . $queryString . '"><span><img src="' . asset('uploads/category/' . $category->image) . '" alt=""/>';
                    if (UtilityFunction::getLocal() == "en")
                        $category_menu .= $category->name;
                    else $category_menu .= $category->ar_name;

                    $category_menu .= ' (' . $category_count . ')';
                    $category_menu .= '</span></a>';
                }
            }
        }
        return $category_menu;
    }

    public function serviceProvider($cid = null, $first_child_cid = null, $second_child_cid = null, $store_name = null, $returnType = null,$mex_price_value = null, $min_price_value = null, $search = null)
    {
        $pagination = 10;
        $min_price = '';
        $max_price = '';
        $filter = '';
        $categories = null;
        $ret = null;
        $sorting = 'new';
        //rating
        //p_asc
        //p_desc


        $now = Carbon::now()->format('Y-m-d 00:00:00');
        if (isset($second_child_cid)) $categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::SERVICE)->OrderBy('name', 'asc')->where('id', $second_child_cid)->get();
        else if (isset($first_child_cid)) $categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::SERVICE)->OrderBy('name', 'asc')->where('id', $first_child_cid)->get();
        elseif (isset($cid)) $categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::SERVICE)->OrderBy('name', 'asc')->where('id', $cid)->get();
        else $categories = ProductCategory::whereNull('parent_category_id')->where('product_category_type_id', ProductTypeEnum::SERVICE)->get();

        //build the category menu
        $category_menu = $this->buildCategoryMenu($cid, $first_child_cid, $second_child_cid, ProductTypeEnum::SERVICE, $categories, $store_name);

        if (isset($second_child_cid)) $all_child_categories = ProductCategory::getAllChildCategory([ProductCategory::find($second_child_cid)]);
        elseif (isset($first_child_cid)) $all_child_categories = ProductCategory::getAllChildCategory([ProductCategory::find($first_child_cid)]);
        elseif (isset($cid)) $all_child_categories = ProductCategory::getAllChildCategory([ProductCategory::find($cid)]);
        else $all_child_categories = ProductCategory::getAllChildCategory($categories);


        if (isset($_GET['filter'])) $filter = $_GET['filter'];
        if (isset($_GET['sorting'])) $sorting = $_GET['sorting'];

        if (isset($_GET['min-price'])) $min_price = $_GET['min-price'];
        if (isset($_GET['max-price'])) $max_price = $_GET['max-price'];

        if (isset($min_price_value)) $min_price = $min_price_value;
        if (isset($mex_price_value)) $max_price = $mex_price_value;

        //in case of store filter
        if (isset($store_name)) $products = Product::join('sellers', 'sellers.id', '=', 'products.seller_id')->select('products.*')->where('products.status',ProductStatusEnum::SHOWN)->where('sellers.store_name', $store_name)->wherein('products.category_id', $all_child_categories)->join('subscriptions', 'subscriptions.seller_id', '=', 'products.seller_id')->where('subscriptions.from_date', '<=', $now)->where('subscriptions.to_date', '>=', $now);
        else $products = Product::wherein('products.category_id', $all_child_categories)->join('subscriptions', 'subscriptions.seller_id', '=', 'products.seller_id')->select('products.*')->where('products.status',ProductStatusEnum::SHOWN)->where('subscriptions.from_date', '<=', $now)->where('subscriptions.to_date', '>=', $now);

        //this is for the price range slider
        $price[0] = $products->min('products.price');
        $price[1] = $products->max('products.price');

        if (!empty($min_price) && !empty($max_price)) $products = $products->whereBetween('products.price', [$min_price, $max_price]);

        //sorting
        if ($sorting == 'p_asc') $products = $products->orderby('products.price', 'asc');
        elseif ($sorting == 'p_desc') $products = $products->orderby('products.price', 'desc');
        elseif ($sorting == 'rating') {
            $products = $products->leftjoin('product_reviews', 'products.id', '=', 'product_reviews.product_id')
                ->select('products.*', DB::raw('AVG(product_reviews.review_rating) as ratings_average'))->groupby('products.id')
                ->orderby('ratings_average', 'desc');
        }
        elseif ($sorting == 'new') $products = $products->OrderBy('products.created_at', 'desc');

        if(isset($search)) {
            $search = explode(' ',$search);
            foreach($search as $s){
                $products = $products->where(function($q)use ($s) {
                    $q->where('products.name', 'LIKE', '%'. $s .'%')
                        ->orWhere('products.ar_name', 'LIKE', '%'. $s .'%')
                        ->orWhere('products.description', 'LIKE', '%'. $s .'%')
                        ->orWhere('products.ar_description', 'LIKE', '%'. $s .'%')
                    ;
                });
            }
        }

        if (isset($filter) && $filter == 'featured') $products = $products->where('products.is_featured', 1);
        elseif (isset($filter) && $filter == 'deals') {
            $queryString = "?filter=deals";
            $now = Carbon::now()->format('Y-m-d 00:00:00');
            $products = $products->join('deals', 'products.id', 'deals.product_id')
                ->select('products.id', 'products.name', 'products.ar_name', 'products.category_id')
                ->where('from_date', '<=', $now)
                ->where('to_date', '>=', $now)
                ->where('deals.status', DealStatusEnum::APPROVED)
            ;
        } elseif (isset($filter) && $filter == 'new-products') $products = $products->OrderBy('products.created_at', 'desc');
        elseif (isset($filter) && $filter == 'best-seller') $products = $products->OrderBy('products.created_at', 'desc');

        if (!isset($_GET['min-price'])) $min_price =  $price[0];
        if (!isset($_GET['max-price'])) $max_price =  $price[1];

        $products = $products->paginate($pagination);

        if (isset($returnType)) {//storeDetails() need this
            $ret[0] = $category_menu;
            $ret[1] = $products;
            $ret[2] = $sorting;
            $ret[3] = $price[0];
            $ret[4] = $price[1];

            return $ret;
        }


        return view('frontend/service-category')
            ->with('categories', $categories)
            ->with('category_id', $cid)
            ->with('first_child_cid', $first_child_cid)
            ->with('second_child_cid', $second_child_cid)
            ->with('category_menu', $category_menu)
            ->with('p_min', $price[0])
            ->with('p_max', $price[1])
            ->with('search_min_price', $min_price)
            ->with('search_max_price', $max_price)

            ->with('sorting', $sorting)
            ->with('products', $products);
    }

    public function productCategories($cid = null, $first_child_cid = null, $second_child_cid = null, $store_name = null, $returnType = null,$mex_price_value = null, $min_price_value = null, $search = null)
    {
        $pagination = 5;
        $min_price = '';
        $max_price = '';
        $filter = '';
        $categories = null;
        $sorting = 'new';

        if (isset($second_child_cid)) $categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::PRODUCT)->OrderBy('name', 'asc')->where('id', $second_child_cid)->get();
        elseif (isset($first_child_cid)) $categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::PRODUCT)->OrderBy('name', 'asc')->where('id', $first_child_cid)->get();
        elseif (isset($cid)) $categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::PRODUCT)->OrderBy('name', 'asc')->where('id', $cid)->get();
        else $categories = ProductCategory::whereNull('parent_category_id')->where('product_category_type_id', ProductTypeEnum::PRODUCT)->get();

        //build the category menu
        $category_menu = $this->buildCategoryMenu($cid, $first_child_cid, $second_child_cid, ProductTypeEnum::PRODUCT, $categories, $store_name);

        if (isset($second_child_cid)) $all_child_categories = ProductCategory::getAllChildCategory([ProductCategory::find($second_child_cid)]);
        elseif (isset($first_child_cid)) $all_child_categories = ProductCategory::getAllChildCategory([ProductCategory::find($first_child_cid)]);
        elseif (isset($cid)) $all_child_categories = ProductCategory::getAllChildCategory([ProductCategory::find($cid)]);
        else $all_child_categories = ProductCategory::getAllChildCategory($categories);

        //in case of store filter
        if (isset($store_name)) $products = Product::join('sellers', 'sellers.id', '=', 'products.seller_id')->select('products.*')->where('products.status',ProductStatusEnum::SHOWN)->where('sellers.store_name', $store_name)->wherein('products.category_id', $all_child_categories)->where('products.quantity', '>', 0);
        else $products = Product::wherein('products.category_id', $all_child_categories)->where('products.status',ProductStatusEnum::SHOWN)->where('products.quantity', '>', 0)->select('products.*');


        if (isset($_GET['filter'])) $filter = $_GET['filter'];
        if (isset($_GET['sorting'])) $sorting = $_GET['sorting'];

        //this is for the price range slider
        $price[0] = $products->min('products.price');
        $price[1] = $products->max('products.price');

        if (isset($_GET['min-price'])) $min_price = $_GET['min-price'];
        if (isset($_GET['max-price'])) $max_price = $_GET['max-price'];

        if (isset($min_price_value)) $min_price = $min_price_value;
        if (isset($mex_price_value)) $max_price = $mex_price_value;


        if (!empty($min_price) && !empty($max_price)) $products = $products->whereBetween('products.price', [$min_price, $max_price]);

        //sorting
        if ($sorting == 'p_asc') $products = $products->orderby('products.price', 'asc');
        elseif ($sorting == 'p_desc') $products = $products->orderby('products.price', 'desc');
        elseif ($sorting == 'rating') {
            $products = $products->leftjoin('product_reviews', 'products.id', '=', 'product_reviews.product_id')
                ->select('products.*', DB::raw('AVG(product_reviews.review_rating) as ratings_average'))->groupby('products.id')
                ->orderby('ratings_average', 'desc');
        }
        elseif ($sorting == 'new') $products = $products->OrderBy('products.created_at', 'desc');

        if(isset($search)) {
            $search = explode(' ',$search);
            foreach($search as $s){
                $products = $products->where(function($q)use ($s) {
                    $q->where('products.name', 'LIKE', '%'. $s .'%')
                        ->orWhere('products.ar_name', 'LIKE', '%'. $s .'%')
                        ->orWhere('products.description', 'LIKE', '%'. $s .'%')
                        ->orWhere('products.ar_description', 'LIKE', '%'. $s .'%')
                    ;
                });
            }
        }

        if (isset($filter) && $filter == 'featured') $products = $products->where('products.is_featured', 1);
        elseif (isset($filter) && $filter == 'deals') {
            $queryString = "?filter=deals";
            $now = Carbon::now()->format('Y-m-d 00:00:00');
            $products = $products->join('deals', 'products.id', 'deals.product_id')
                ->select('products.id', 'products.name', 'products.ar_name', 'products.category_id')
                ->where('from_date', '<=', $now)
                ->where('to_date', '>=', $now)
                ->where('deals.status', DealStatusEnum::APPROVED)
            ;
        }
        elseif (isset($filter) && $filter == 'new-products') $products = $products->OrderBy('products.created_at', 'desc');
        elseif (isset($filter) && $filter == 'best-seller') $products = $products->OrderBy('products.created_at', 'desc');

        if (!isset($_GET['min-price'])) $min_price =  $price[0];
        if (!isset($_GET['max-price'])) $max_price =  $price[1];

        $products = $products->paginate($pagination);

        if (isset($returnType)) {
            $ret[0] = $category_menu;
            $ret[1] = $products;
            $ret[2] = $sorting;
            $ret[3] = $price[0];
            $ret[4] = $price[1];

            return $ret;
        }

        //dd($min_price,$max_price);

        return view('frontend/category')
            ->with('categories', $categories)
            ->with('category_id', $cid)
            ->with('first_child_cid', $first_child_cid)
            ->with('second_child_cid', $second_child_cid)
            ->with('category_menu', $category_menu)
            ->with('p_min', $price[0])
            ->with('p_max', $price[1])
            ->with('search_min_price', $min_price)
            ->with('search_max_price', $max_price)
            ->with('sorting', $sorting)
            ->with('products', $products);
    }

    public function storeDetails($store_name, $cid = null, $first_child_cid = null, $second_child_cid = null)
    {
        $search = '';
        $categories = null;
        $seller_business_type = "";
        $ret = null;
        $min_price = '';
        $max_price = '';

        if (isset($_GET['search'])) $search = $_GET['search'];
        if (isset($_GET['min-price'])) $min_price = $_GET['min-price'];
        if (isset($_GET['max-price'])) $max_price = $_GET['max-price'];

        $seller_category = Seller::where('sellers.store_name', $store_name)->first();
        $seller_business_type = $seller_category->business_type;

        if (isset($seller_category)) {
            if ($seller_category->business_type == ProductTypeEnum::PRODUCT) $ret = $this->productCategories($cid, $first_child_cid, $second_child_cid, $store_name, 1, $max_price, $min_price, $search);
            elseif ($seller_category->business_type == ProductTypeEnum::SERVICE) $ret = $this->serviceProvider($cid, $first_child_cid, $second_child_cid, $store_name, 1, $max_price, $min_price, $search);
        }

        if (!isset($id)) $id = $seller_category->category_id;

        $price[0] = $ret[3];
        $price[1] = $ret[4];

        if(empty($min_price)) $min_price = $price[0];
        if(empty($max_price)) $max_price = $price[1];

        return view('frontend/store-details')
            ->with('seller_category', $seller_category)
            ->with('categories', $categories)
            ->with('search', $search)
            ->with('category_id', $id)
            ->with('seller_business_type', $seller_business_type)
            ->with('store_name', $store_name)
            ->with('category_menu', $ret[0])
            ->with('products', $ret[1])
            ->with('sorting', $ret[2])
            ->with('p_min', $price[0])
            ->with('p_max', $price[1])
            ->with('search_min_price', $min_price)
            ->with('search_max_price', $max_price);
    }

    //
    public function goSearch(Request $request){
        $search = '';
        $categories = null;
        $seller_business_type = "";
        $product_min_price = '';
        $product_max_price = '';
        $product_category_menu = '';
        $service_category_menu = '';
        $product_price = [];

        $service_min_price = '';
        $service_max_price = '';
        $service_price = [];

        if (isset($request->search)) $search = $request->search;
        if (isset($_GET['product-min-price'])) $product_min_price = $_GET['product-min-price'];
        if (isset($_GET['product-max-price'])) $product_max_price = $_GET['product-max-price'];
        if (isset($_GET['service-min-price'])) $service_min_price = $_GET['service-min-price'];
        if (isset($_GET['service-max-price'])) $service_max_price = $_GET['service-max-price'];

        $product_search = null;
        $service_search = null;

        $product_search = $this->productCategories(null, null, null, null, 1, $product_max_price, $product_min_price, $search);

        if(isset($product_search[0])){
            $product_price[0] = $product_search[3];
            $product_price[1] = $product_search[4];

            if(empty($product_min_price)) $product_min_price = $product_price[0];
            if(empty($product_max_price)) $product_max_price = $product_price[1];

            $product_category_menu = $product_search[0];
        }

        $service_search = $this->serviceProvider(null, null, null, null, 1, $service_max_price, $service_min_price, $search);

        if(isset($service_search[0])){
            $service_price[0] = $service_search[3];
            $service_price[1] = $service_search[4];

            if(empty($service_min_price)) $service_min_price = $service_price[0];
            if(empty($service_max_price)) $service_max_price = $service_price[1];

            $service_category_menu = $service_search[0];
        }

        return view('frontend/search')
            ->with('categories', $categories)
            ->with('search', $search)
            ->with('seller_business_type', $seller_business_type)
            ->with('products', $product_search[1])
            ->with('product_sorting', $product_search[2])
            ->with('product_p_min', $product_price[0])
            ->with('product_p_max', $product_price[1])
            ->with('product_search_min_price', $product_min_price)
            ->with('product_search_max_price', $product_max_price)
            ->with('product_category_menu', $product_category_menu)

            ->with('services', $service_search[1])
            ->with('service_sorting', $service_search[2])
            ->with('service_p_min', $service_price[0])
            ->with('service_p_max', $service_price[1])
            ->with('service_search_min_price', $service_min_price)
            ->with('service_search_max_price', $service_max_price)
            ->with('service_category_menu', $service_category_menu)
            ;
    }
}


//    public static function getProductListAjaxResponse($products)
//    {
//        $data_generate = "";
//        $pagination_data_generate = "";
//
//        if (isset($products[0])) {
//            foreach ($products as $product) {
//                $data_generate .= '<li class="wow fadeInUp" data-wow-delay="0.3s">';
//                $data_generate .= '<div class="product-list_item row">';
//                $data_generate .= '<div class="product-list_item_img col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">';
//
//                $data_generate .= '<ul class="category-images">';
//                $data_generate .= '<li class="grid"><figure class="effect-bubba wow fadeInRight" data-wow-delay="0.3s">';
//                $media = $product->getMedia;
//                $data_generate .= '<img ';
//                if (isset($media[0])) $data_generate .= 'src="' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 270, 285, ['crop']) . '"';
//                else $data_generate .= 'src="' . Image::url(asset('/images/no-media.jpg'), 270, 285, ['crop']) . '"';
//                $data_generate .= 'alt="Category">';
//                $data_generate .= '<figcaption><div class="category-images_content"><h2 class="font-third font-weight-light text-uppercase color-main">Architect - Mecca</h2><p class="font-additional font-weight-bold text-uppercase color-main line-text line-text_white">6 reviews</p></div><a href="' . url('/service/provider/details/' . $product->id) . '">View more</a></figcaption>';
//                $data_generate .= '</figure></li>';
//                $data_generate .= '</ul>';
//
//                $data_generate .= '</div>';
//                $data_generate .= '<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 clearfix">';
//                $data_generate .= '<div class="product-list-info">';
//                $data_generate .= '<div class="product-list_item_title">';
//                $data_generate .= '<a style="color:#333" href="' . url('products/details/') . '/' . $product->id . '"><h3 class="font-additional font-weight-bold text-uppercase">' . str_limit($product->name, 135) . '</h3></a>';
//                $data_generate .= '<ul class="rating">';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star color-additional" aria-hidden="true"></span></li>';
//                $data_generate .= '</ul>';
//                $data_generate .= '</div>';
//                $data_generate .= '<div class="product-item_price font-additional font-weight-normal customColor">' . $product->price . ' <span>$265.00</span></div>';
//                $data_generate .= '<div class="product-list_item_desc font-main font-weight-normal color-third">' . str_limit($product->description, 150) . '</div>';
//                $data_generate .= '<a href="javascript:;" data-id="' . Crypt::encrypt($product->id) . '" class="add_to_cart btn button-additional font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">';
//                $data_generate .= '<span class="icon-basket" aria-hidden="true"></span>@lang("messages.add_to_cart")</a>';
//                $data_generate .= '<a href="javascript:;" data-id="' . Crypt::encrypt($product->id) . '" class="add_to_wish_list btn button-border font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-border hover-focus-bg before-bg">';
//                $data_generate .= '<span class="icon-heart" aria-hidden="true"></span>';
//                $data_generate .= '</a>';
//                $data_generate .= '</div>';
//                $data_generate .= '</div>';
//                $data_generate .= '</div>';
//                $data_generate .= '</li>';
//            }
//        }
//
//        $append_url = '';
//
//        if ($products->lastPage() > 1) {
//            $pagination_data_generate .= '<div class="pagination-container wow fadeInUp" data-wow-delay="0.3s">';
//            $pagination_data_generate .= '<div class="pagination-info font-additional">';
//
//            $from = ($products->currentPage() - 1) * ($products->perPage()) + 1;
//            $to = $from + $products->count() - 1;
//            $pagination_data_generate .= 'Items ' . $from . ' to ' . $to . ' of ' . $products->total() . ' totals';
//
//            $pagination_data_generate .= '</div>';
//            $pagination_data_generate .= '<ul class="pagination-list">';
//            $pagination_data_generate .= '<li><a class="prev hover-focus-color"  href="' . $products->url(1) . $append_url . '">PREVIOUS</a></li>';
//            for ($i = 1; $i <= $products->lastPage(); $i++) {
//                $pagination_data_generate .= '<li>';
//                $pagination_data_generate .= '<a class="page ';
//                if ($products->currentPage() == $i) $pagination_data_generate .= ' current customBgColor';
//                else $pagination_data_generate .= ' hover-focus-color';
//                $pagination_data_generate .= '" href="' . $products->url($i) . $append_url . '">' . $i . '</a>';
//                $pagination_data_generate .= '</li>';
//            }
//            $pagination_data_generate .= '<li><a class="next hover-focus-color" href="' . $products->url($products->currentPage() + 1) . $append_url . '">NEXT</a></li>';
//            $pagination_data_generate .= '</ul>';
//            $pagination_data_generate .= '</div>';
//        }
//
//        return response()->json(['success' => true, 'data_generate' => $data_generate, 'pagination_data_generate' => $pagination_data_generate]);
//    }
//
//    public static function getServiceListAjaxResponse($products)
//    {
//        $data_generate = "";
//        $pagination_data_generate = "";
//
//        if (isset($products[0])) {
//            foreach ($products as $product) {
//                $data_generate .= '<li class="wow fadeInUp" data-wow-delay="0.3s">';
//                $data_generate .= '<div class="product-list_item row">';
//                $data_generate .= '<div class="product-list_item_img col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">';
//
//                $data_generate .= '<ul class="category-images">';
//                $data_generate .= '<li class="grid"><figure class="effect-bubba wow fadeInRight" data-wow-delay="0.3s">';
//                $media = $product->getMedia;
//                $data_generate .= '<img ';
//                if (isset($media[0])) $data_generate .= 'src="' . Image::url(asset('uploads/media/' . $media[0]->file_in_disk), 270, 285, ['crop']) . '"';
//                else $data_generate .= 'src="' .  Image::url(asset('image/no-media.jpg'), 270, 285, ['crop']) . '"';
//                $data_generate .= 'alt="Category">';
//                $data_generate .= '<figcaption><div class="category-images_content"><h2 class="font-third font-weight-light text-uppercase color-main">Architect - Mecca</h2><p class="font-additional font-weight-bold text-uppercase color-main line-text line-text_white">6 reviews</p></div><a href="' . url('/service/provider/details/' . $product->id) . '">View more</a></figcaption>';
//                $data_generate .= '</figure></li>';
//                $data_generate .= '</ul>';
//
//                $data_generate .= '</div>';
//                $data_generate .= '<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 clearfix">';
//                $data_generate .= '<div class="product-list-info">';
//                $data_generate .= '<div class="product-list_item_title">';
//                $data_generate .= '<a style="color:#333" href="' . url('products/details/') . '/' . $product->id . '"><h3 class="font-additional font-weight-bold text-uppercase">' . str_limit($product->name, 135) . '</h3></a>';
//                $data_generate .= '<ul class="rating">';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star customColor" aria-hidden="true"></span></li>';
//                $data_generate .= '<li><span class="icon-star color-additional" aria-hidden="true"></span></li>';
//                $data_generate .= '</ul>';
//                $data_generate .= '</div>';
//                $data_generate .= '<div class="product-item_price font-additional font-weight-normal customColor">' . $product->price . ' <span>$265.00</span></div>';
//                $data_generate .= '<div class="product-list_item_desc font-main font-weight-normal color-third">' . str_limit($product->description, 150) . '</div>';
//                $data_generate .= '<a href="javascript:;" data-id="' . Crypt::encrypt($product->id) . '" class="add_to_cart btn button-additional font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">';
//                $data_generate .= '<span class="icon-basket" aria-hidden="true"></span>@lang("messages.add_to_cart")</a>';
//                $data_generate .= '<a href="javascript:;" data-id="' . Crypt::encrypt($product->id) . '" class="add_to_wish_list btn button-border font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-border hover-focus-bg before-bg">';
//                $data_generate .= '<span class="icon-heart" aria-hidden="true"></span>';
//                $data_generate .= '</a>';
//                $data_generate .= '</div>';
//                $data_generate .= '</div>';
//                $data_generate .= '</div>';
//                $data_generate .= '</li>';
//            }
//        }
//
//        $append_url = '';
//
//        if ($products->lastPage() > 1) {
//            $pagination_data_generate .= '<div class="pagination-container wow fadeInUp" data-wow-delay="0.3s">';
//            $pagination_data_generate .= '<div class="pagination-info font-additional">';
//
//            $from = ($products->currentPage() - 1) * ($products->perPage()) + 1;
//            $to = $from + $products->count() - 1;
//            $pagination_data_generate .= 'Items ' . $from . ' to ' . $to . ' of ' . $products->total() . ' totals';
//
//            $pagination_data_generate .= '</div>';
//            $pagination_data_generate .= '<ul class="pagination-list">';
//            $pagination_data_generate .= '<li><a class="prev hover-focus-color"  href="' . $products->url(1) . $append_url . '">PREVIOUS</a></li>';
//            for ($i = 1; $i <= $products->lastPage(); $i++) {
//                $pagination_data_generate .= '<li>';
//                $pagination_data_generate .= '<a class="page ';
//                if ($products->currentPage() == $i) $pagination_data_generate .= ' current customBgColor';
//                else $pagination_data_generate .= ' hover-focus-color';
//                $pagination_data_generate .= '" href="' . $products->url($i) . $append_url . '">' . $i . '</a>';
//                $pagination_data_generate .= '</li>';
//            }
//            $pagination_data_generate .= '<li><a class="next hover-focus-color" href="' . $products->url($products->currentPage() + 1) . $append_url . '">NEXT</a></li>';
//            $pagination_data_generate .= '</ul>';
//            $pagination_data_generate .= '</div>';
//        }
//
//        return response()->json(['success' => true, 'data_generate' => $data_generate, 'pagination_data_generate' => $pagination_data_generate]);
//    }
