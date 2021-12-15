<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\OrderTypeEnum;
use App\Http\Controllers\Enum\ProductStatusEnum;
use App\Http\Controllers\Enum\ProductTypeEnum;
use App\Http\Controllers\Enum\RoleEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Model\Buyer;
use App\Model\CartItem;
use App\Model\City;
use App\Model\Deal;
use App\Model\DealImage;
use App\Model\DealStore;
use App\Model\DealType;
use App\Model\FavoriteProduct;
use App\Model\FavouriteDeal;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\PaymentHistory;
use App\Model\Product;
use App\Model\ProductCategory;
use App\Model\ProductDeal;
use App\Model\ProductReview;
use App\Model\ProductType;
use App\Model\Purchases;
use App\Model\Question;
use App\Model\QuestionAnswer;
use App\Model\Store;
use App\Model\SubOrder;
use App\Model\UserComment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;
use File;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    // Category Product List
    public function categoryProductList(Request $request)
    {
        $category_id = $request->category_id;

        $categories = ProductCategory::orderBy('name', 'asc')->where('id', $category_id)->get();

        if (isset($category_id)) {
            $products = Product::orderBy('name')->whereIn('category_id', $categories)->get();
//            \Log::info($products);
        }

        if (!empty($products)) return response()->json(['error' => false, 'products' => $products]);
        else return response()->json(['error' => true]);
    }

    // Product Details
//    public function productDetails(Request $request)
//    {
//        $product_id = $request->product_id;
//
//        if (isset($product_id)) {
//            $product = Product::find($product_id);
//        }
//
//        if (!empty($product)) return response()->json(['error' => false, 'product' => $product]);
//        else return response()->json(['error' => true]);
//    }


    public function getCurrentPrice($discount, $discount_type, $old_price)
    {
        $price = '';
        if ($discount_type == 1) {
            $price = $old_price - $discount;
        } elseif ($discount_type == 2) {
            $price = $old_price - (($old_price * $discount) / 100);
        } else {
            $price = $old_price;
        }
        return $price;
    }

    public function randomString($number)
    {
        $alphabet = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $number; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $random_alpha = implode($pass);

        return $random_alpha;
    }

    // all json for product save page
    public function getAllInfoToSaveProduct(Request $request)
    {
        $merchant_id = $request->merchant_id;

        if (!empty($merchant_id)) {
            $user = User::find($request->user_id);

            $stores_by_merchantId = DB::table('stores')->where('stores.merchant_id', $merchant_id)->get();
            $city_name = [];
            foreach ($stores_by_merchantId as $srore) {
                $srore->available = Store::getDealByStore($srore->id, $merchant_id);
                array_push($city_name, $srore->city_name);
            }

            $cities = City::OrderBy('name', 'asc')->whereIn('name', $city_name)->get();
            foreach ($cities as $city) {
                $city->available = 1;
            }
//            dd($city_name,$cities);
            $product_types = DealType::orderBy('name', 'asc')->get();
            $product_type_filter_list = DealType::whereNull('deal_type_id')->orderBy('name', 'asc')->get();
            if ($user->role_id == RoleEnum::SALES_MAN) {
                return response()->json(['error' => false, 'product_type_filter_list' => $stores_by_merchantId, 'product_types' => $stores_by_merchantId, 'branches' => $cities, 'cities' => $cities]);
            }
            return response()->json(['error' => false, 'product_type_filter_list' => $product_type_filter_list, 'product_types' => $product_types, 'branches' => $stores_by_merchantId, 'cities' => $cities]);
        }

        return response()->json(['error' => true, 'error_msg' => 'Invalid Input']);
    }

    public function ProductTypeList()
    {
        $product_types = [];
        $query = DealType::whereNull('deal_type_id')
            ->orderBy('name', 'asc')->get();

        foreach ($query as $q) {
            if ($q->getDealCountByDealType($q->id) > 0) {
                array_push($product_types, $q);
                $path = 'uploads/dealtype/';
                $q->image_for_app = url($path . $q->image_for_app);
                $q->image = url($path . $q->image);
                $q->product_count = $q->getDealCountByDealType($q->id);
                $q->featured_product_count = DealType::getfeaturedProductOffer($q->id);
            }
        }

        if (isset($product_types[0]))
            return response()->json(['error' => false, 'product_types' => $product_types]);

        return response()->json(['error' => true, 'product_types' => 'No product type exists']);
    }


//toreId = obj.getString("seller_id");//
//productId = obj.getString("product_id");//
//storeName = obj.getString("store_name");//
//productName = obj.getString("product_name");//
//productDescription = obj.getString("product_description");//
//storeAddress = obj.getString("store_address");//
//dealURL = obj.getString("product_url_online");//
//purchaseAmount = obj.getString("total_purchase");
//tottalusercount = obj.getJSONObject("rating_info").getString("total_user_count");
//avaragerating = obj.getJSONObject("rating_info").getString("average_rating");
//is_favorite = obj.getString("is_favorite");
//review_availability=obj.getString("review_availability");
//newPrice = obj.getString("price");//
//productImage = obj.getString("product_image");//
//JSONArray array1 = obj.getJSONArray("all_images");//
//allImagesOfSingleProduct.add(object.getString("image"));//




    public function ProductByType(Request $request)
    {
//        try {
//
//            $product_type_id = $request->product_type_id;
//            $user_id = $request->user_id;
//            $child_deal_type_ids = '';
//            if (isset($product_type_id)) $child_deal_type_ids = DealType::getAllChildCategory([DealType::find($product_type_id)]);
//
//            $products = Deal::leftjoin('deal_stores', 'deal_stores.deal_id', '=', 'deals.id')
//                ->leftjoin('stores', 'stores.id', '=', 'deal_stores.store_id')
//                ->leftjoin('order_items', 'deals.id', '=', 'order_items.deal_id')
//                ->leftjoin('merchants', 'merchants.id', '=', 'deals.merchant_id')
//                ->select(
//                    'deals.id'
//                    , 'deals.name as productName'
//                    , 'deals.merchant_id as merchant_id'
//                    , 'deals.description as productDescription'
//                    , 'deals.discount'
//                    , 'deals.discount_type'
//                    , 'deals.deal_type_id'
//                    , 'deals.purchase_type as ibrahim'
//                    , 'deals.deal_url_online'
//                    , 'deals.deal_site_name'
//                    , 'stores.id as storeId'
//                    , 'stores.name as storeName'
//                    , 'stores.address as storeAddress'
//                    , 'stores.latitude as location_latitude'
//                    , 'stores.longitude as location_longitude'
//                    , 'deals.original_price as oldPrice'
//                    , 'deals.is_featured'
//                    , 'deals.id as dealCode'
//                    , 'deals.start as start'
//                    , 'deals.end as expire'
//                    , DB::raw('count(order_items.deal_id) as total_purchase')
//                )
//                ->where('deals.end', '>=', date('Y-m-d'))
//                ->where('deals.status', 1)
//                ->orderBy('deals.created_at', 'desc')
//                ->groupBy('deals.id');
//            if (isset($child_deal_type_ids[0])) {
//                $products = $products->whereIn('deals.deal_type_id', $child_deal_type_ids);
//            }
//
//            $search_text = $request->deal_search;
//            $deal_search_strings = explode(' ', $search_text);
//
//            \Log::info($deal_search_strings);
//
//            if (isset($deal_search_strings[0])) {
//                foreach ($deal_search_strings as $deal_search_string) {
//                    $products = $products->Where(function ($q) use ($deal_search_string) {
//                        $q->where('deals.name', 'LIKE', '%' . $deal_search_string . '%')
//                            ->orwhere('deals.description', 'LIKE', '%' . $deal_search_string . '%')
//                            ->orwhere('stores.name', 'LIKE', '%' . $deal_search_string . '%')
//                            ->orwhere('stores.city_name', 'LIKE', '%' . $deal_search_string . '%')
//                            ->orwhere('merchants.name', 'LIKE', '%' . $deal_search_string . '%')
//                            ->orwhere('merchants.address', 'LIKE', '%' . $deal_search_string . '%');
//                    });
//                }
//            }
//
//            $products = $products->groupBy('deals.id')->groupBy('stores.id')
////                ->get();
//                ->paginate(env('PAGINATION_SMALL'));
//
//            foreach ($products as $product) {
//
//                $product->productId = $product->id;
//                $path = 'uploads/merchant/' . $product->merchant_id . '/deal/';
//                $product->all_images = $product->getAllImages;
//                $product->productImage = (isset($product->getAllImages[0])) ? url($path . $product->getAllImages[0]->image) : '';
//                if (isset($product->all_images[0])) {
//                    foreach ($product->all_images as $all_image) {
//                        $all_image->image = url($path . $all_image->image);
//                    }
//                }
//
//                $product->store_count = Store::getStoreByDeal($product->id);
//                $product->is_favorite = FavouriteDeal::verifyUserSavedDealAPI($product->id, $user_id);
//                $product->productDescription = strip_tags($product->productDescription);
//                $product->dealCode = 1;
//                $product->rating_info = Deal::getDealRating($product->id);
//                $product->sell_count = $product->getDealSellCount->count();
//                $product->deal_count = Deal::getDealCount($product->ibrahim)->count();
//                $product->review_availability = Deal::reviewAvailability($product->id, $user_id);
//
//                $product->newPrice = $this->getCurrentPrice($product->discount, $product->discount_type, $product->oldPrice);
//
//
//            }
////            dd($products);
//            if (isset($products[0])) {
//                return response()->json(['error' => false, 'products' => $products]);
//            }
//            return response()->json(['error' => false, 'products' => 'No product exists in the product type.']);
//
//        } catch (\Exception $e) {
//            return response()->json(['error' => true, 'err_msg' => $e]);
//        }

        try {
            $product_type_id = $request->product_type_id;
            $user_id = $request->user_id;

            \Log::info($product_type_id ." ".$user_id);

            $buyer_id = 0;
            $buyer = Buyer::where('user_id',$user_id)->first();
            if(isset($buyer)) $buyer_id = $buyer->id;
            $child_deal_type_ids = '';
            if (isset($product_type_id)) $child_deal_type_ids = ProductCategory::getAllChildCategory([ProductCategory::find($product_type_id)]);

            $products = Product::leftjoin('deals', 'deals.product_id', '=', 'products.id')
                ->leftjoin('sellers', 'products.seller_id', '=', 'sellers.id')
                ->where('products.status', ProductStatusEnum::SHOWN)
                ->select(
                    'products.id'
                    ,'products.id as product_id'
                    ,'products.name as product_name'
                    ,'products.description'
                    ,'products.price as oldPrice'
                    ,'products.is_featured'
                    ,'products.seller_id'
                    ,'deals.discount'
                    ,'deals.discount_type'
                    ,'sellers.store_name'
                    ,'sellers.business_address'
                )
                ->orderBy('products.created_at', 'desc');

            if (isset($child_deal_type_ids[0])) {
                $products = $products->whereIn('products.category_id', $child_deal_type_ids);
            }

            $search_text = $request->product_search;
            $product_search_strings = explode(' ', $search_text);

            if (isset($product_search_strings[0])) {
                foreach ($product_search_strings as $product_search_string) {
                    $products = $products->Where(function ($q) use ($product_search_string) {
                        $q->where('products.name', 'LIKE', '%' . $product_search_string . '%')
                            ->orWhere('products.description', 'LIKE', '%' . $product_search_string . '%');
                    });
                }
            }

            $products = $products->paginate(env('PAGINATION_SMALL'));
//            $products = $products->get();

//            dd($products);


            foreach($products as $product){
                $path = 'uploads/media/';
                $product->all_images = $product->getMedia;
                $product->product_image = (isset($product->all_images[0])) ? url($path . $product->all_images[0]->file_in_disk) : '';

                if (isset($product->all_images[0])) {
                    foreach ($product->all_images as $all_image) {
                        $all_image->image = url($path . $all_image->file_in_disk);
                    }
                }
                $product->product_url_online = url('/product/details/'.$product->product_id);

                $product->store_name = $product->getSeller->store_name;
                $product->store_address = $product->getSeller->business_address;

                $product->total_purchase = Product::getTotalPurchase($product->product_id);

                $product->is_favorite = Product::getUserFavoriteProduct($product->product_id, $buyer_id);

                $product->product_description = strip_tags($product->description);
                $product->rating_info = Product::getProductRating($product->id);
                $product->review_availability = 1;

                $product->newPrice = $this->getCurrentPrice($product->discount, $product->discount_type, $product->oldPrice);
            }

            if (isset($products[0])) {
                return response()->json(['error' => false, 'products' => $products]);
            }
            return response()->json(['error' => false, 'products' => 'No product exists in the product type.']);

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'err_msg' => $e]);
        }

    }

    // ProductDetails
    public function ProductDetails(Request $request){
        try {
            $product_id = $request->product_id;
            $user_id = $request->user_id;
            $buyer_id = 0;
            $buyer = Buyer::where('user_id',$user_id)->first();
            if(isset($buyer)) $buyer_id = $buyer->id;

            $products = Product::where('products.status', ProductStatusEnum::SHOWN)
                ->select(
                    'products.id'
                    ,'products.id as product_id'
                    ,'products.name as product_name'
                    ,'products.description'
                    ,'products.price'
                    ,'products.is_featured'
                    ,'products.seller_id'
                )
                ->orderBy('products.created_at', 'desc')
                ->where('products.id',$product_id)
            ;

            $search_text = $request->product_search;
            $product_search_strings = explode(' ', $search_text);

            if (isset($product_search_strings[0])) {
                foreach ($product_search_strings as $product_search_string) {
                    $products = $products->Where(function ($q) use ($product_search_string) {
                        $q->where('products.name', 'LIKE', '%' . $product_search_string . '%')
                            ->orWhere('products.description', 'LIKE', '%' . $product_search_string . '%');
                    });
                }
            }

            $products = $products->paginate(env('PAGINATION_SMALL'));


            foreach($products as $product){
                $path = 'uploads/media/';
                $product->all_images = $product->getMedia;
                $product->product_image = (isset($product->all_images[0])) ? url($path . $product->all_images[0]->file_in_disk) : '';

                if (isset($product->all_images[0])) {
                    foreach ($product->all_images as $all_image) {
                        $all_image->image = url($path . $all_image->file_in_disk);
                    }
                }
                $product->product_url_online = url('/product/details/'.$product->product_id);

                $product->store_name = $product->getSeller->store_name;
                $product->store_address = $product->getSeller->business_address;

                $product->total_purchase = Product::getTotalPurchase($product->product_id);

                $product->is_favorite = Product::getUserFavoriteProduct($product->product_id, $buyer_id);

                $product->product_description = strip_tags($product->description);
                $product->rating_info = Product::getProductRating($product->id);
                $product->review_availability = 1;
            }

            if (isset($products[0])) {
                return response()->json(['error' => false, 'products' => $products]);
            }
            return response()->json(['error' => false, 'products' => 'No product exists in the product type.']);

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'err_msg' => $e]);
        }

    }

    public function ProductBySeller(Request $request)
    {
        try {
            $seller_id = $request->store_id;
            $user_id = $request->user_id;
            $search_text = '';

            $buyer = Buyer::where('user_id',$user_id)->first();

//            dd($buyer);

            $products = Product::where('products.status', ProductStatusEnum::SHOWN)
                ->select(
                    'products.id'
                    ,'products.id as product_id'
                    ,'products.name as product_name'
                    ,'products.description'
                    ,'products.price'
                    ,'products.is_featured'
                    ,'products.seller_id'
                )
                ->orderBy('products.created_at', 'desc');

            if (isset($seller_id)) {
                $products = $products->where('products.seller_id', $seller_id);
            }

            if(!empty($request->product_search)) {
                $search_text = $request->product_search;
                $product_search_strings = explode(' ', $search_text);
            }


            if (isset($product_search_strings[0])) {
                foreach ($product_search_strings as $product_search_string) {
                    $products = $products->Where(function ($q) use ($product_search_string) {
                        $q->where('products.name', 'LIKE', '%' . $product_search_string . '%')
                            ->orWhere('products.description', 'LIKE', '%' . $product_search_string . '%');
                    });
                }
            }

            $products = $products->paginate(env('PAGINATION_SMALL'));

            foreach($products as $product){
                $path = 'uploads/media/';
                $product->all_images = $product->getMedia;
                $product->product_image = (isset($product->all_images[0])) ? url($path . $product->all_images[0]->file_in_disk) : '';

                if (isset($product->all_images[0])) {
                    foreach ($product->all_images as $all_image) {
                        $all_image->image = url($path . $all_image->file_in_disk);
                    }
                }
                $product->product_url_online = url('/product/details/'.$product->product_id);

                $product->store_name = $product->getSeller->store_name;
                $product->store_address = $product->getSeller->business_address;

                $product->total_purchase = Product::getTotalPurchase($product->product_id);

                $product->is_favorite = Product::getUserFavoriteProduct($product->product_id, $buyer->id);
                $product->product_description = strip_tags($product->description);
                $product->rating_info = Product::getProductRating($product->product_id);
                $product->review_availability = 1;
            }

            if (isset($products[0])) {
                return response()->json(['error' => false, 'products' => $products]);
            }
            return response()->json(['error' => false, 'products' => 'No product exists in the product type.']);

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'err_msg' => $e]);
        }
    }

    public function ProductStoreInfo(Request $request)
    {
        try {
            if ($request->product_id) {
                $product = Product::join('sellers','sellers.id','=','products.seller_id')
                    ->join('users','users.id','=','sellers.user_id')
                    ->join('favorite_stores','sellers.id','=','favorite_stores.seller_id')
                ->select(
                    'sellers.store_name'
                    ,'sellers.about_me'
                    ,'sellers.company_name'
                    ,'sellers.business_address as address'
                    ,'users.username as seller_name'
                    ,DB::raw('count(favorite_stores.id) as favorite')
                )
                ->where('products.id',$request->product_id)->first();

                return response()->json(['error'=>false,'store'=>$product]);
            }else{
                return response()->json(['error'=>true]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'err_msg' => $e]);
        }
    }

    public function ProductReviewList(Request $request)
    {
        try {
            if (!empty($request->product_id)) {
                $reviews = ProductReview::join('buyers', 'buyers.id', '=', 'product_reviews.buyer_id')
                    ->join('users','users.id','=','buyers.user_id')
                    ->select(
                        'product_reviews.id'
                        , 'product_reviews.review_rating as rate'
                        , 'product_reviews.review_comment as comment'
                        , 'product_reviews.created_at as post_date'
                        , 'users.username'
                        , 'users.photo'
                        , 'users.created_at as join_date'
                    )
                    ->where('product_reviews.product_id', $request->product_id)
                    ->take(env('TAKE_TOP'))
                    ->get();

                if (isset($reviews[0])) {
                    foreach ($reviews as $review) {
                        !empty($review->photo) ? $review->photo = url('uploads/user/' . $review->photo) : $review->photo = '';
                        $review->post_date = Carbon::createFromFormat('Y-m-d H:i:s', $review->post_date)->diffForHumans();
                        $review->join_date = date('j F, Y', strtotime($review->join_date));
                        $review->comment = strip_tags($review->comment);
                    }

                    return response()->json(['error' => false, 'reviews' => $reviews]);
                }
                return response()->json(['error' => true, 'message' => 'No Data Exists']);
            }else{
                return response()->json(['error' => true, 'message' => 'No Data Exists']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'err_msg' => $e]);
        }
    }

    public function productWriteReview(Request $request)
    {
        try {
            if (!empty($request->user_id) && !empty($request->product_id) && !empty($request->review_text) && !empty($request->rating)) {

                $user_id = $request->user_id;
                $buyer = Buyer::where('user_id',$user_id)->first();

                $review = ProductReview::where('product_id', $request->product_id)->where('buyer_id', $buyer->id)->first();
                if (isset($review)) {
                    $user_comment = ProductReview::find($review->id);
                } else {
                    $user_comment = new ProductReview();
                }
                $user_comment->buyer_id = $buyer->id;
                $user_comment->product_id = $request->product_id;
                $user_comment->review_rating = $request->rating;
                $user_comment->review_comment = $request->review_text;
                $user_comment->save();
                return response()->json(['error' => false]);
            }
            return response()->json(['error' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'err_msg' => $e]);
        }
    }

    public function userAskQuestionSave(Request $request)
    {
        try {
            if (!empty($request->question) && !empty($request->user_id)) {
                $question = new Question();
                $question->title = $request->question;
                $question->user_id = $request->user_id;
                $question->save();

                return response()->json(['error' => false, 'msg' => 'successfully Saved']);
            }
            return response()->json(['error' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'err_msg' => $e]);
        }
    }

    public function userQuestionList(Request $request)
    {
        try {
            if (!empty($request->user_id) && User::where('id', $request->user_id)->where('user_type', UserTypeEnum::USER)->exists()) {

                $search = '';

                if(!empty($request->search_text)) $search = $request->search_text;

                $questions = Question::join('users','users.id','=','questions.user_id')
                    ->select('questions.id as question_id','questions.title','questions.created_at','questions.user_id','users.photo')
                    ->where('user_id',$request->user_id);

                if(!empty($search)){
                    $questions = $questions->where(function($q)use ($search) {
                        $q->where('questions.title','LIKE','%'.$search.'%');
                    });
                }

                $questions = $questions->get();

                foreach($questions as $question){
                    $question->user_image = url(env('USER_PHOTO_PATH').$question->photo);
                    $question->title = strip_tags($question->title);
                    $question->post_date = Carbon::createFromFormat('Y-m-d H:i:s', $question->created_at)->diffForHumans();
                }

                return response()->json(['error' => false, 'questions' => $questions]);
            }
            return response()->json(['error' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'err_msg' => $e]);
        }
    }

    public function userQuestionDetails(Request $request)
    {

        try {
            if (!empty($request->question_id)) {

                $question = Question::find($request->question_id);
                $question_answers = QuestionAnswer::join('users', 'users.id', '=', 'question_answers.user_id')
                    ->select('question_answers.*', 'users.username', 'users.user_type', 'users.photo', 'users.id')
                    ->where('question_answers.question_id', $request->question_id)
//                    ->where('question_answers.user_id', $request->user_id)
                    ->orderBy('question_answers.created_at', 'desc')->get();

                foreach ($question_answers as $qs) {
                    $path = env('USER_PHOTO_PATH');

                    $qs->photo = url($path . $qs->photo);
                    $qs->post_date = Carbon::createFromFormat('Y-m-d H:i:s', $qs->created_at)->diffForHumans();
                }

                return response()->json(['error' => false, 'question_answers' => $question_answers, 'question' => $question]);
            }
            return response()->json(['error' => false]);

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'err_msg' => $e]);
        }
    }

    public function questionReply(Request $request)
    {

        if (!empty($request->user_id) && User::where('id', $request->user_id)->where('user_type', UserTypeEnum::USER)->exists()) {
            if (!empty($request->answer)) {
                $answer = new QuestionAnswer();
                $answer->answer = $request->answer;
                $answer->user_id = $request->user_id;
                $answer->question_id = $request->question_id;
                $answer->save();

                $question = Question::find($request->question_id);
                $question_answers = QuestionAnswer::join('users', 'users.id', '=', 'question_answers.user_id')
                    ->select('question_answers.*', 'users.username', 'users.user_type', 'users.photo', 'users.id')
                    ->where('question_answers.question_id', $request->question_id)->orderBy('question_answers.created_at', 'desc')->get();
                foreach ($question_answers as $qs) {
                    $path = env('USER_PHOTO_PATH');

                    $qs->photo = url($path . $qs->photo);
                    $qs->post_date = Carbon::createFromFormat('Y-m-d H:i:s', $qs->created_at)->diffForHumans();
                }
                return response()->json(['error' => false, 'question_answers' => $question_answers, 'question' => $question]);
            }
        }
        return response()->json(['error' => false]);
    }

    public function ProductAddToFavorite(Request $request)
    {
        if (!empty($request->user_id) && !empty($request->product_id)) {
            $user_id = $request->user_id;
            $buyer = Buyer::where('user_id',$user_id)->first();
            if (!FavoriteProduct::where('product_id', $request->product_id)->where('buyer_id', $buyer->id)->exists()) {
                $make_favourite = new FavoriteProduct();
                $make_favourite->product_id = $request->product_id;
                $make_favourite->buyer_id = $buyer->id;
                $make_favourite->save();
                return response()->json(['error' => false]);
            }
        }
        return response()->json(['error' => true]);
    }

    public function merchantProductList(Request $request)
    {
        try {

            if (!empty($request->merchant_id) && !empty($request->user_id) && User::where('id', $request->user_id)->where('merchant_id', $request->merchant_id)->where('user_type', UserTypeEnum::MERCHANT)->exists()) {
                $category_id = '';
                $store_id = '';
                $city_name = '';
                $user = User::find($request->user_id);

                if ($user->role_id == RoleEnum::SALES_MAN) {

                    $city = City::find($request->branch_id);
                    if (isset($city)) $city_name = $city->name;
                    $store_id = $request->producttype_id;
                } else {
                    $category_id = $request->producttype_id;
                    $store_id = $request->branch_id;
                }


                $product_list = Deal::leftjoin('deal_stores', 'deal_stores.deal_id', '=', 'deals.id')
                    ->join('stores', 'stores.id', '=', 'deal_stores.store_id')
                    ->leftjoin('order_items', 'order_items.deal_id', '=', 'deals.id')
                    ->join('users', 'users.merchant_id', '=', 'deals.merchant_id')
                    ->select(
                        'deals.id'
                        , 'deals.name'
                        , 'deals.description'
                        , 'deals.attach_file'
                        , 'stores.name as store_name'
                        , 'deals.start'
                        , 'deals.end'
                        , 'deals.deal_type_id'
                        , 'deals.original_price as price'
                        , 'deals.discount'
                        , 'stores.address'
                        , 'stores.city_name'
                        , 'order_items.price'
                        , 'order_items.quantity'
                        , 'stores.id as store_id'
                        , DB::raw('count(order_items.deal_id) as sold')
//                    , DB::raw('sum(order_items.deal_id) as sold')
                    )
                    ->where('deals.merchant_id', $request->merchant_id)
//                    ->where('users.id', $request->user_id)
                    ->where('deals.status', 1)
                    ->groupBy('deals.id')
                    ->orderBy('deals.created_at', 'desc');
                if (!empty($category_id) && $category_id != 0) {
                    $all_child_deal_type_id = DealType::getAllChildCategory([DealType::find($category_id)]);
                    $product_list = $product_list->whereIn('deals.deal_type_id', $all_child_deal_type_id);
                }
                if (!empty($store_id) && $store_id != 0) {
                    $product_list = $product_list->where('deal_stores.store_id', $store_id);
                }

                if (!empty($city_name)) {
                    $product_list = $product_list->where('stores.city_name', strtolower($city_name));
                }

                $search_text = $request->deal_search;
                $deal_search_strings = explode(' ', $search_text);
                if (isset($deal_search_strings[0])) {
                    foreach ($deal_search_strings as $deal_search_string) {
                        $product_list = $product_list->Where(function ($q) use ($deal_search_string) {
                            $q->where('deals.name', 'LIKE', '%' . $deal_search_string . '%')
                                ->orWhere('deals.description', 'LIKE', '%' . $deal_search_string . '%');
                        });
                    }
                }


                $product_list = $product_list->paginate(env('PAGINATION_SMALL'));
//                dd(strtolower($city_name),$product_list);
                foreach ($product_list as $pls) {
                    $image_name = '';
                    if (count($pls->getAllImages) > 0) $image_name = $pls->getAllImages[0]->image;
                    $pls->image = asset('uploads/merchant/' . $request->merchant_id . '/deal/' . $image_name);
                    $pls->description = strip_tags($pls->description);
                    $pls->total_sold_amount = $pls->deal_price * $pls->quantity;
                    $pls->average_sold_amount = (!empty($pls->deal_price * $pls->quantity)) ? $pls->total_sold_amount / $pls->quantity : 0;
                }

                if (isset($product_list[0]))
                    return response()->json(['error' => false, 'product_list' => $product_list]);

            } else {
                return response()->json(['error' => false, 'product_types' => 'No Merchant Exists']);
            }


            return response()->json(['error' => false, 'product_types' => 'No product exists']);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'message' => $e]);
        }
    }

    public function productNearBy(Request $request)
    {

        $user_id = $request->user_id;
        $product_list = Deal::leftjoin('deal_stores', 'deal_stores.deal_id', '=', 'deals.id')
            ->leftjoin('stores', 'stores.id', '=', 'deal_stores.store_id')
            ->leftjoin('order_items', 'deals.id', '=', 'order_items.deal_id')
            ->select(
                'deals.id'
                , 'deals.name as productName'
                , 'deals.merchant_id as merchant_id'
                , 'deals.description as productDescription'
                , 'deals.discount'
                , 'deals.discount_type'
                , 'deals.deal_type_id'
                , 'deals.purchase_type as ibrahim'
                , 'deals.deal_url_online'
                , 'deals.deal_site_name'
                , 'stores.id as storeId'
                , 'stores.name as storeName'
                , 'stores.address as storeAddress'
                , 'stores.latitude as location_latitude'
                , 'stores.longitude as location_longitude'
                , 'deals.original_price as oldPrice'
                , 'deals.is_featured'
                , 'deals.id as dealCode'
                , 'deals.start as start'
                , 'deals.end as expire'
                , DB::raw('count(order_items.deal_id) as total_purchase')
//                , DB::raw('( 3959 * acos( cos( radians(' . $request->latitude . ') ) * cos( radians( stores.latitude ) ) * cos( radians( stores.longitude ) - radians(' . $request->longitude . ') ) + sin( radians(' . $request->latitude . ') ) * sin( radians(stores.latitude) ) ) ) AS distance')
            )
            ->where('deals.end', '>=', date('Y-m-d'))
            ->where('deals.status', 1)
            ->where(DB::raw('( 3959 * acos( cos( radians(' . $request->latitude . ') ) * cos( radians( stores.latitude ) ) * cos( radians( stores.longitude ) - radians(' . $request->longitude . ') ) + sin( radians(' . $request->latitude . ') ) * sin( radians(stores.latitude) ) ) )'), '<=',env("NEARBY_DISTANCE"))
            ->groupBy('deals.id')->groupBy('stores.id')
            ->paginate(env('PAGINATION_SMALL'));


        foreach ($product_list as $product) {

                $product->productId = $product->id;
                $path = 'uploads/merchant/' . $product->merchant_id . '/deal/';
                $product->all_images = $product->getAllImages;
                $product->productImage = (isset($product->getAllImages[0])) ? url($path . $product->getAllImages[0]->image) : '';
                if (isset($product->all_images[0])) {
                    foreach ($product->all_images as $all_image) {
                        $all_image->image = url($path . $all_image->image);
                    }
                }

                $product->store_count = Store::getStoreByDeal($product->id);
                $product->is_favorite = FavouriteDeal::verifyUserSavedDealAPI($product->id, $user_id);
                $product->productDescription = strip_tags($product->productDescription);
                $product->dealCode = 1;
                $product->rating_info = Deal::getDealRating($product->id);
                $product->sell_count = $product->getDealSellCount->count();
                $product->deal_count = Deal::getDealCount($product->ibrahim)->count();
                $product->review_availability = Deal::reviewAvailability($product->id,$user_id);

                $product->newPrice = $this->getCurrentPrice($product->discount, $product->discount_type, $product->oldPrice);

        }

        if (isset($product_list[0]))
            return response()->json(['error' => false, 'products' => $product_list]);

        return response()->json(['error' => true, 'product_types' => 'No product exists']);
    }

    public function productPopularity(Request $request)
    {
        if (isset($request->deal_id)) {
            $deal = Deal::find($request->deal_id);
            $deal->popularity = $deal->popularity + 1;
            $deal->save();
            return response()->json(['error' => false]);
        }
    }

    public function getProductDetailsByProductId(Request $request)
    {
        if (!empty($request->product_id)) {
            $product_details = DB::table('products')
                ->leftjoin('stores', 'stores.id', '=', 'products.store_id')
                ->leftjoin('product_deals', 'product_deals.product_id', '=', 'products.id')
                ->select(
                    'products.id'
                    , 'products.name'
                    , 'products.description'
                    , 'products.image'
                    , 'products.attach_file'
                    , 'stores.name as store_name'
                    , 'product_deals.start'
                    , 'product_deals.end'
                    , 'product_deals.price'
                    , 'product_deals.deal_price'
                    , 'product_deals.discount'
                    , 'stores.name as store_name'
                    , 'stores.address'
                )
                ->where('products.id', $request->product_id)
                ->first();
            if (isset($product_details))
                return response()->json(['error' => false, 'product_details' => $product_details]);
            else
                return response()->json(['error' => true, 'error_msg' => 'Product Not Found']);
        }
        return response()->json(['error' => true, 'error_msg' => 'Invalid Input']);
    }

    public function addNewProduct(Request $request)
    {

        if (!empty($request->branch_id) && !empty($request->product_name)) {

            DB::beginTransaction();

            $deal = new Deal();
            $deal->name = $request->product_name;
            $deal->description = $request->description;

            $path = public_path() . '/uploads/merchant/' . $request->merchant_id . '/deal';
            if (!file_exists($path)) {
                File::makeDirectory($path, $mode = 0777, true, true);
            }


            if (!empty($request->attach_file)) {
                $filename = 'image_' . date('d-m-y') . '_' . time() . '.pdf';
                $path = public_path() . '/uploads/merchant' . '/' . $request->merchant_id . '/deal/image_' . date('d-m-y') . '_' . time() . '.pdf';
                $file = $request->attach_file;
                $deal->attach_file = $filename;
                file_put_contents($path, base64_decode($file));
            }


            $deal->deal_type_id = $request->product_type_id;
            $deal->merchant_id = $request->merchant_id;
            $deal->start = $request->offer_start;
            $deal->end = $request->offer_end;
            $deal->original_price = $request->price_usd;
            $deal->discount = !empty($request->price_discount) ? $request->price_discount : null;
            if (!empty($request->discount_type)) $deal->discount_type = $request->discount_type;
            $deal->save();


            if (!empty($request->image_data)) {
                $image_name = 'image_' . date('d-m-y') . '_' . time() . '.png';
                $path = public_path() . '/uploads/merchant' . '/' . $request->merchant_id . '/deal/image_' . date('d-m-y') . '_' . time() . '.png';
                $image = $request->image_data;
                file_put_contents($path, base64_decode($image));
                $deal_image = new  DealImage();
                $deal_image->deal_id = $deal->id;
                $deal_image->image = $image_name;
                $deal_image->save();
            }

            if (isset($deal->id)) {
                DealStore::where('deal_id', $deal->id)->delete();
                $stores = explode(' ', $request->branch_id);
                if (isset($stores[0])) {
                    foreach ($stores as $store_id) {
                        $deal_store = new DealStore();
                        $deal_store->deal_id = $deal->id;
                        $deal_store->store_id = $store_id;
                        $deal_store->save();
                    }
                } else {
                    $deal_store = new DealStore();
                    $deal_store->deal_id = $deal->id;
                    $deal_store->store_id = 0;
                    $deal_store->save();
                }
            }


            \Log::info($deal);

            DB::commit();

            return response()->json(['error' => false, 'error_msg' => 'successfully Added']);
        } else {
            return response()->json(['error' => true, 'error_msg' => 'Invalid Input']);
        }
    }

    public function addNewProductType(Request $request)
    {
        if (!empty($request->productName)) {

            $product_type = new ProductType();
            $product_type->name = $request->productName;
            $product_type->save();

            return response()->json(['error' => false, 'product_type' => $product_type]);
        } else {
            return response()->json(['error' => true, 'error_msg' => 'Invalid Input']);
        }
    }

    public function ProductQrcode(Request $request)
    {
        $deal_id = $request->product_id;
        $store_id = $request->store_id;

        $customer_id = $request->customer_id;
        $qrcode_create = '';

        $product = DB::table('deals')
            ->join('deal_stores', 'deal_stores.deal_id', '=', 'deals.id')
            ->join('stores', 'stores.id', '=', 'deal_stores.store_id')
            ->join('users', 'users.merchant_id', '=', 'stores.merchant_id')
            ->select(
                'deals.id as dealId'
                , 'stores.id as storeId'
                , 'deals.merchant_id as merchantId'
                , 'deals.original_price as oldPrice'

            )
            ->where('deals.id', $deal_id)
            ->where('stores.id', $store_id)
            ->first();
        if (isset($product)) $product->dealCode = 1;

        if (isset($product)) {

            $qrcode_create = base64_encode($product->merchantId . ':' . $product->dealId . ':' . $customer_id);
            $qrcode_create_main = $product->merchantId . ':' . $product->dealId . ':' . $customer_id;

            return response()->json(['error' => false, 'qrcode' => $qrcode_create, 'qrcode_create_main' => $qrcode_create_main]);
        }

        return response()->json(['error' => true, 'qrcode' => 'No product exists in this information.']);
    }

    public function ProductQrcodeDecrypt(Request $request)
    {
        $crypt_qrcode = $request->scan_content;

        if (!empty($crypt_qrcode)) {
            $decrypt_qrcode = base64_decode($crypt_qrcode);
            return response()->json(['error' => false, 'qrcode_dt' => $decrypt_qrcode]);
        }

        return response()->json(['error' => true]);
    }

    public function qrCodeConfirm(Request $request)
    {
        $product_id = $request->product_id;
        $customer_id = $request->customer_id;
        $merchant_id = $request->merchant_id;


        $product = Deal::select(
            'deals.id as productId'
            , 'deals.name as product_name'
            , 'deals.original_price as old_price'
            , 'deals.discount_type'
            , 'deals.discount'
        )
            ->where('deals.id', $product_id)
            ->where('deals.merchant_id', $merchant_id)
            ->first();

        $customer = User::where('id', $customer_id)->select('username as customer_name')->first();

        if (isset($product)) {
            $product->new_price = $this->getCurrentPrice($product->discount, $product->discount_type, $product->old_price);
//            DB::beginTransaction();
//            $order = new Order();
//            $order->buyer_id = $customer_id;
//            if(!empty($coupon_code))$order->coupon = $coupon_code->coupon;
//            $order->sub_total_price = $this->getCurrentPrice($product->discount,$product->discount_type,$product->old_price);
//            if(!empty($customer->address))$order->delivery_street = $customer->address;
//            if(!empty($customer->city))$order->delivery_city = $customer->city;
//            if(!empty($customer->country))$order->delivery_country = $customer->country;
//            $order->save();
//
//                $order_item = new OrderItem();
//                $order_item->quantity = 1;
//                $order_item->deal_id = $product_id;

//                $deal = Deal::find($product_id);
//                if(isset($deal)) {
//                    $deal->current_price = $this->getCurrentPrice($deal->discount,$deal->discount_type,$deal->original_price);
//                }
//                //Save Sub_orders
//                $sub_order = SubOrder::where('order_id',$order->id)->where('merchant_id',$deal->merchant_id)->first();
//                if(!isset($sub_order))
//                {
//                    $sub_order = new SubOrder();
//                    $sub_order->merchant_id = $deal->merchant_id;
//                    $sub_order->order_id = $order->id;
//                    $sub_order->save();
//                }

//                $order_item->price = $deal->current_price;
//                $order_item->sub_order_id = $sub_order->id;
//                $order_item->save();

//
//            $payment_history = new PaymentHistory();
//            $payment_history->order_id =$order->id;
//            $payment_history->payment_type = 'cash';
//            $payment_history->save();

//            DB::commit();
            return response()->json(['error' => false, 'product' => $product, 'customer' => $customer]);
        }

        return response()->json(['error' => true, 'products' => 'No product exists in this information.']);
    }

    public function redeemConfirm(Request $request)
    {
        try {
            $product_id = $request->product_id;
            $customer_id = $request->customer_id;
            $merchant_id = $request->merchant_id;
            $seller_id = $request->user_id;
            $order_type = $request->order_type;


            $product = Deal::select(
                'deals.id as productId'
                , 'deals.name as product_name'
                , 'deals.original_price as old_price'
                , 'deals.discount_type'
                , 'deals.discount'
            )
                ->where('deals.id', $product_id)
                ->where('deals.merchant_id', $merchant_id)
                ->first();

            $customer = User::where('id', $customer_id)->select('username as customer_name')->first();

            if (isset($product)) {
                $product->new_price = $this->getCurrentPrice($product->discount, $product->discount_type, $product->old_price);
                DB::beginTransaction();
                $order = new Order();
                $order->buyer_id = $customer_id;
                $order->order_type = $order_type;
                $order->order_type = OrderTypeEnum::REDEEM;
                if (!empty($coupon_code)) $order->coupon = $coupon_code->coupon;
                $order->sub_total_price = $this->getCurrentPrice($product->discount, $product->discount_type, $product->old_price);
                if (!empty($customer->address)) $order->delivery_street = $customer->address;
                if (!empty($customer->city)) $order->delivery_city = $customer->city;
                if (!empty($customer->country)) $order->delivery_country = $customer->country;
                $order->save();

                $order_item = new OrderItem();
                $order_item->quantity = 1;
                $order_item->deal_id = $product_id;

                $deal = Deal::find($product_id);
                if (isset($deal)) {
                    $deal->current_price = $this->getCurrentPrice($deal->discount, $deal->discount_type, $deal->original_price);
                }
                //Save Sub_orders
                $sub_order = SubOrder::where('order_id', $order->id)->where('merchant_id', $deal->merchant_id)->first();
                if (!isset($sub_order)) {
                    $sub_order = new SubOrder();
                    $sub_order->merchant_id = $deal->merchant_id;
                    $sub_order->seller_id = $seller_id;
                    $sub_order->order_id = $order->id;
                    $sub_order->save();
                }

                $order_item->price = $deal->current_price;
                $order_item->sub_order_id = $sub_order->id;
                $order_item->save();


                $payment_history = new PaymentHistory();
                $payment_history->order_id = $order->id;
                $payment_history->payment_type = 'cash';
                $payment_history->save();

                DB::commit();
                return response()->json(['error' => false, 'message' => 'Successfully Redeemed']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'message' => $e]);
        }
    }


    public function productPurchase(Request $request)
    {
        try {

            if (!empty($request->user_id) && User::where('id', $request->user_id)->where('user_type', UserTypeEnum::USER)->exists()) {
                $user_id = $request->user_id;
                $auth = true;
            }
            if ($auth == true) {
                $total_price = '';
                $cart_items = CartItem::where('buyer_id', $user_id)->get();
                foreach ($cart_items as $cart_item) {
                    $deal = $cart_item->getDeal;
                    $cart_item->newPrice = $this->getCurrentPrice($deal->discount, $deal->discount_type, $deal->oldPrice);
                    $total_price = $total_price + ($cart_item->newPrice * $cart_item->quantity);
                }

                $coupon_id = $request->coupon_id;
                if (isset($coupon_id))
                    $coupon_code = Coupon::find(Crypt::decrypt($coupon_id));


                DB::beginTransaction();
                if (isset($cart_items[0])) {

                    $order = new Order();
                    $order->buyer_id = $user_id;
                    $order->order_type = OrderTypeEnum::REDEEM;
                    if (!empty($coupon_code)) $order->coupon = $coupon_code->coupon;
                    $order->sub_total_price = $total_price;
                    if (!empty($request->address)) $order->delivery_street = $request->address;
                    if (!empty($request->city)) $order->delivery_city = $request->city;
                    if (!empty($request->country)) $order->delivery_country = $request->country;
                    $order->save();


                    for ($i = 0; $i < count($cart_items); $i++) {

                        $order_item = new OrderItem();
                        $order_item->quantity = $cart_items[$i]->quantity;
                        $order_item->deal_id = $cart_items[$i]->deal_id;

                        $deal = Deal::find($order_item->deal_id);
                        if (isset($deal)) {
                            $deal->current_price = $this->getCurrentPrice($deal->discount, $deal->discount_type, $deal->original_price);
                        }
                        //Save Sub_orders
                        $sub_order = SubOrder::where('order_id', $order->id)->where('merchant_id', $deal->merchant_id)->first();
                        if (!isset($sub_order)) {
                            $sub_order = new SubOrder();
                            $sub_order->merchant_id = $deal->merchant_id;
                            $sub_order->order_id = $order->id;
                            $sub_order->save();
                        }

                        $order_item->price = $deal->current_price;
                        $order_item->sub_order_id = $sub_order->id;
                        $order_item->save();
                    }

                    $payment_history = new PaymentHistory();
                    $payment_history->order_id = $order->id;
                    $payment_history->payment_type = $request->payment_method;
                    $payment_history->save();

                }


                CartItem::where('buyer_id', $user_id)->delete();


                DB::commit();
                return response()->json(['error' => false]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => false]);
        }

    }


//    public function productPurchase(Request $request){
//        $deal_id = $request->product_id;
//        $sold_by = $request->user_id;
//        $purchased_by = $request->customer_id;
//        $merchant_id = $request->merchant_id;
//
//
//        if(!empty($deal_id) && !empty($merchant_id) && !empty($sold_by) && !empty($purchased_by)){
//            $product= Deal::find($deal_id);
//            $purchase = new Purchases();
//            $purchase->purchased_date = date('Y-m-d H:i:s');
//            $purchase->deal_id = $deal_id;
//            $purchase->purchased_by = $purchased_by;
//            $purchase->merchant_id = $merchant_id;
//            $purchase->sold_by = $sold_by;
//            $purchase->original_price = $product->original_price;
//            $purchase->discount = $product->discount;
//            $purchase->discount_type = $product->discount_type;
//            $purchase->transaction_id = $this->randomString(5).$deal_id.$this->randomString(5);
//            $purchase->save();
//
//            return response()->json(['error'=>false,'transaction'=>$purchase->transaction_id]);
//        }else{
//            return response()->json(['error'=>true,'error_msg'=>'Invalid Input']);
//        }
//    }
    public function merchantVoucherList(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $user_id = $request->user_id;
        $date_time = $request->position;
        if (!empty($merchant_id)) {
            $product_vouchers = Order::join('sub_orders', 'sub_orders.order_id', '=', 'orders.id')
                ->join('order_items', 'order_items.sub_order_id', '=', 'sub_orders.id')
                ->join('deals', 'deals.id', '=', 'order_items.deal_id')
                ->join('payment_histories', 'payment_histories.order_id', '=', 'orders.id')
                ->select(
                    'orders.id as order_id'
                    , 'orders.created_at'
                    , 'deals.id as deal_id'
                    , 'deals.name as deal_name'
                    , 'deals.discount'
                    , 'deals.discount_type'
                    , 'deals.original_price as bill_total'
                    , 'payment_histories.id as transaction_id'
                )
                ->groupBy('order_items.id');
            if ($date_time == 1) $product_vouchers = $product_vouchers->whereDate('payment_histories.created_at', date('Y-m-d'));
            elseif ($date_time == 2) $product_vouchers = $product_vouchers->whereDate('payment_histories.created_at', '>', date('Y-m-d', strtotime('-7 days')));
            elseif ($date_time == 3) $product_vouchers = $product_vouchers->whereMonth('payment_histories.created_at', date('m'));
            $product_vouchers = $product_vouchers->OrderBy('orders.created_at', 'desc')
                ->get();


            if (isset($product_vouchers)) {
                foreach ($product_vouchers as $product_voucher) {
                    $product_voucher->order_date = date('j F, Y', strtotime($product_voucher->created_at));
                    if ($product_voucher->discount_type == 1) {
                        $product_voucher->voucher_value = $product_voucher->discount_type;
                    } elseif ($product_voucher->discount_type == 2) {
                        $product_voucher->voucher_value = ($product_voucher->discount * $product_voucher->bill_total) / 100;
                    }
                    $product_voucher->customer_bill_total = $this->getCurrentPrice($product_voucher->discount, $product_voucher->discount_type, $product_voucher->bill_total);
                }
            }
//            dd($product_vouchers);
            if (!empty($product_vouchers[0])) {
                return response()->json(['error' => false, 'product_vouchers' => $product_vouchers]);
            } else {
                return response()->json(['error' => false, 'error_msg' => 'None']);
            }

        } else {
            return response()->json(['error' => true, 'error_msg' => 'Invalid Input']);
        }
    }





    public function userTrackOrder(Request $request){
        $user_id = $request->user_id;
        $buyer = Buyer::where('user_id',$user_id)->first();
        $sort_by_date = $request->sort_by_date;
        $date_time= $request->sort_by_day;

        $track_order = Product::join('order_items','products.id','=','order_items.product_id')
            ->join('sub_orders','sub_orders.id','=','order_items.sub_order_id')
            ->join('orders','orders.id','=','sub_orders.order_id')
            ->select(
                'orders.created_at'
                ,'sub_orders.status'
                , 'products.id'
                , 'products.name as product_name'
            )
            ->where('orders.status',OrderStatusEnum::ACCEPTED)
            ->where('orders.buyer_id',$buyer->id);

        if ($sort_by_date == 0) $track_order = $track_order->orderBy('orders.created_at', 'desc');
        elseif ($sort_by_date == 1) $track_order = $track_order->orderBy('products.name', 'asc');

        if ($date_time == 1) $track_order = $track_order->whereDate('orders.created_at', date('Y-m-d'));
        elseif ($date_time == 2) $track_order = $track_order->whereMonth('orders.created_at', date('m'));
        else $track_order = $track_order->OrderBy('orders.created_at', 'desc');

        $track_order = $track_order->get();

        foreach($track_order as $track){
            $track->order_date = date('j F, Y', strtotime($track->created_at));
            if($track->status == OrderStatusEnum::PENDING) $track->status = 'Pending';
            if($track->status == OrderStatusEnum::ACCEPTED) $track->status = 'Approved';
            if($track->status == OrderStatusEnum::DELIVERED) $track->status = 'Delivered';
            $track->item_heading = $track->product_name;


            $track->product_info = Product::join('order_items','products.id','=','order_items.product_id')
                ->join('sellers','sellers.id','=','products.seller_id')
                ->select(
                    'products.id as product_id'
                    ,'products.name as product_name'
                    ,'products.description as product_description'
                    ,'products.price'
                    ,'products.is_featured'
                    ,'products.seller_id'
                    ,'sellers.business_address as store_address'
                    ,'sellers.store_name'
                    ,'sellers.id as seller_id'
                )
                ->where('products.id',$track->id)
                ->first();

            if(isset($track->product_info)){
                $path = 'uploads/media/';
                $track->product_info->all_images = $track->getMedia;
                $track->product_info->product_image = (isset($track->product_info->all_images[0])) ? url($path . $track->product_info->all_images[0]->file_in_disk) : '';
                if (isset($track->product_info->all_images[0])) {
                    foreach ($track->product_info->all_images as $all_image) {
                        $all_image->image = url($path . $all_image->file_in_disk);
                    }
                }

                $track->product_info->is_favorite = Product::getUserFavoriteProduct($track->id, $buyer->id);

                $track->product_info->rating_info = $track::getProductRating($track->id);
                $track->product_info->review_availability = 1;
                $track->product_info->product_url_online = url('product/details/'.$track->id);
                $track->product_info->total_purchase = Product::getTotalPurchase($track->product_info->product_id);
            }
        }

        //dd($track_order);

        return response()->json(['error' => false, 'track_order' => $track_order]);
    }

    // Public User Transaction
    public function userTransaction(Request $request){
        $user_id = $request->user_id;
        $buyer = Buyer::where('user_id',$user_id)->first();
        $sort_by_date = $request->sort_by_date;
        $date_time= $request->sort_by_day;
        try {
            if (!empty($user_id) && User::where('id',$user_id)->where('user_type',UserTypeEnum::USER)->exists()) {
                $transactions = Order::join('sub_orders','sub_orders.order_id','=','orders.id')
                    ->join('order_items','order_items.sub_order_id','=','sub_orders.id')
                    ->join('products','products.id','=','order_items.product_id')
                    ->where('orders.buyer_id',$buyer->id)
                    ->select(
                        'orders.id as transation_id'
                        ,'sub_orders.order_id'
                        ,'orders.created_at'
                        ,'orders.delivery_street'
                        ,'orders.id'
                        ,'order_items.product_id'
                        ,'orders.sub_total_price as trasaction_amount'
                    );

                if($sort_by_date==0)$transactions=$transactions->orderBy('orders.created_at','desc');
                elseif($sort_by_date==1)$transactions=$transactions->orderBy('products.name','asc');

                if($date_time==1)$transactions=$transactions->whereDate('orders.created_at','=',date('Y-m-d'));

                if($date_time==2)$transactions=$transactions->whereMonth('orders.created_at',date('m'));
                else $transactions=$transactions->OrderBy('orders.created_at','desc');

                $transactions=$transactions->groupBy('orders.id')->get();

                if(isset($transactions[0])){
                    foreach($transactions as $transaction){
                        $transaction->payment_type = '1';
                        $transaction->transaction_method = 'Credit Card';
                        $transaction->transaction_order_date = date('j F, Y',strtotime($transaction->created_at));
                        $transaction->order_items = Order::getOrderItemsByOrderId($transaction->id);
                    }
                }


                return response()->json(['error' => false, 'transactions' => $transactions]);
            }
            return response()->json(['error' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    // Public User Transaction
    public function userTransaction1(Request $request){
        $user_id = $request->user_id;
        $sort_by_date = $request->sort_by_date;
        $date_time= $request->sort_by_day;
        try {
            if (!empty($user_id) && User::where('id',$user_id)->where('user_type',UserTypeEnum::USER)->exists()) {

                $deal_types = DealType::whereNull('deal_type_id')->OrderBy('name')->get();
//                $date_time = $request->date_time;


                $transactions = PaymentHistory::join('orders','orders.id','=','payment_histories.order_id')
                    ->join('sub_orders','sub_orders.order_id','=','orders.id')
                    ->join('order_items','order_items.sub_order_id','=','sub_orders.id')
                    ->join('deals','deals.id','=','order_items.deal_id')
                    ->where('orders.buyer_id',$user_id)
                    ->select(
                        'payment_histories.id as transation_id'
                        ,'payment_histories.order_id'
                        ,'payment_histories.payment_type as transaction_method'
                        ,'payment_histories.created_at'
                        ,'orders.delivery_street'
                        ,'orders.id'
                        ,'order_items.deal_id'
                        ,'orders.sub_total_price as trasaction_amount'
                    );


                if($sort_by_date==0)$transactions=$transactions->orderBy('payment_histories.created_at','desc');
                elseif($sort_by_date==1)$transactions=$transactions->orderBy('deals.name','asc');

//              if($date_time==0)$transactions=$transactions->whereDate('payment_histories.created_at',date('Y-m-d'));
                if($date_time==1)$transactions=$transactions->whereDate('payment_histories.created_at','=',date('Y-m-d'));

                if($date_time==2)$transactions=$transactions->whereMonth('payment_histories.created_at',date('m'));
                else $transactions=$transactions->OrderBy('payment_histories.created_at','desc');

                $transactions=$transactions->groupBy('orders.id')->get();

                if(isset($transactions[0])){
                    foreach($transactions as $transaction){
                        $transaction->transaction_order_date = date('j F, Y',strtotime($transaction->created_at));
                        $transaction->order_items = PaymentHistory::getOrderItemsByOrderId($transaction->id);
                    }
                }


                return response()->json(['error' => false, 'transactions' => $transactions]);

            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error_msg', $e->getMessage());
        }
    }

    public function userTrackOrder1(Request $request)
    {
        $user_id = $request->user_id;
        $sort_by_date = $request->sort_by_date;
        $date_time= $request->sort_by_day;

        $track_order = Deal::join('order_items', 'order_items.deal_id', '=', 'deals.id')
            ->join('sub_orders', 'order_items.sub_order_id', '=', 'sub_orders.id')
            ->join('orders', 'sub_orders.order_id', '=', 'orders.id')
            ->where('orders.buyer_id', $user_id)
            ->select(
                'orders.created_at'
                ,'orders.order_type'
                , 'products.id'

            );

        if($sort_by_date==0)$track_order=$track_order->orderBy('payment_histories.created_at','desc');
        elseif($sort_by_date==1)$track_order=$track_order->orderBy('deals.name','asc');

//              if($date_time==0)$transactions=$transactions->whereDate('payment_histories.created_at',date('Y-m-d'));
        if($date_time==1)$track_order=$track_order->whereDate('payment_histories.created_at','=',date('Y-m-d'));

        if($date_time==2)$track_order=$track_order->whereMonth('payment_histories.created_at',date('m'));
        else $track_order=$track_order->OrderBy('payment_histories.created_at','desc');

        $track_order = $track_order->get();

        if (isset($track_order[0])) {
            foreach ($track_order as $track) {
                $track->order_date = date('j F, Y', strtotime($track->created_at));
                if($track->order_type == OrderTypeEnum::ONLINE_ORDER)$track->status = 'Online Purchase';
                elseif($track->order_type == OrderTypeEnum::REDEEM)$track->status = 'Store Purchase';
//                else $track->status = 'Store Purchase';

                $track-> product_info = Deal::leftjoin('deal_stores', 'deal_stores.deal_id', '=', 'deals.id')
                    ->leftjoin('stores', 'stores.id', '=', 'deal_stores.store_id')
                    ->leftjoin('order_items', 'deals.id', '=', 'order_items.deal_id')
                    ->select(
                        'deals.id'
                        , 'deals.name as productName'
                        , 'deals.merchant_id as merchant_id'
                        , 'deals.description as productDescription'
                        , 'deals.discount'
                        , 'deals.discount_type'
                        , 'deals.deal_type_id'
                        , 'deals.purchase_type as ibrahim'
                        , 'deals.deal_url_online'
                        , 'deals.deal_site_name'
                        , 'stores.id as storeId'
                        , 'stores.name as storeName'
                        , 'stores.address as storeAddress'
                        , 'stores.latitude as location_latitude'
                        , 'stores.longitude as location_longitude'
                        , 'deals.original_price as oldPrice'
                        , 'deals.is_featured'
                        , 'deals.id as dealCode'
                        , 'deals.start as start'
                        , 'deals.end as expire'
                        , DB::raw('count(order_items.deal_id) as total_purchase')
                    )
//                    ->where('deals.end', '>=', date('Y-m-d'))
                    ->where('deals.status', 1)
                    ->where('deals.id', $track->id)
//                    ->orderBy('deals.created_at', 'desc')
                    ->groupBy('deals.id');


                $track-> product_info = $track-> product_info->groupBy('deals.id')->groupBy('stores.id')->first();
//                    dd($track-> product_info);
                if(isset($track-> product_info)){
                    $track-> product_info->review_availability = Deal::reviewAvailability($track-> product_info->id,$user_id);
                    $track->product_info->productId = $track-> product_info->id;
                    $path = 'uploads/merchant/' . $track-> product_info->merchant_id . '/deal/';
                    $track->product_info->all_images = $track-> product_info->getAllImages;
                    $track->product_info->productImage = (isset($track-> product_info->getAllImages[0])) ? url($path . $track-> product_info->getAllImages[0]->image) : '';
                    if (isset($track->product_info->all_images[0])) {
                        foreach ($track-> product_info->all_images as $all_image) {
                            $all_image->image = url($path . $all_image->image);
                        }
                    }

                    $track-> product_info->store_count = Store::getStoreByDeal($track-> product_info->id);
                    $track-> product_info->is_favorite = FavouriteDeal::verifyUserSavedDealAPI($track-> product_info->id, $user_id);
                    $track-> product_info->productDescription = strip_tags($track-> product_info->productDescription);
                    $track-> product_info->dealCode = 1;
                    $track-> product_info->rating_info = Deal::getDealRating($track-> product_info->id);
                    $track-> product_info->sell_count = $track-> product_info->getDealSellCount->count();
                    $track-> product_info->deal_count = Deal::getDealCount($track-> product_info->ibrahim)->count();

                    $track-> product_info->newPrice = $this->getCurrentPrice($track-> product_info->discount, $track-> product_info->discount_type, $track-> product_info->oldPrice);

                }else{
                    $track-> product_info='';
                }

            }
        }

        return response()->json(['error' => false, 'track_order' => $track_order]);
    }




}
