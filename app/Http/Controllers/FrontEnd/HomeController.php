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
use App\Model\Deal;
use App\Model\Seller;
use App\Model\Buyer;
use App\Model\Country;
use App\Model\ProductReview;
use App\Model\StoreReview;
use App\Model\Setting;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;
use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\ProductStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Hash;
use Cookie;
use Crypt;
use App;
use Session;
use App\Http\Controllers\Enum\ProductTypeEnum;


class HomeController extends Controller
{

    public static function switchLanguage()
    {
        \App\UtilityFunction::switchLocal();
        return redirect()->back();
    }

    public function userLogout()
    {
        Auth::logout();

        return redirect('/login');
    }

    public function index()
    {
        return view('frontend/index');
    }

    public function products()
    {
        $product_categories = ProductCategory::where('show_in_public_menu', 1)
            ->where('product_category_type_id', ProductTypeEnum::PRODUCT)
            ->OrderBy('name', 'asc')
            ->get();

        $products = Product::leftjoin('sellers', 'sellers.id', '=', 'products.seller_id')
            ->where('sellers.business_type', ProductTypeEnum::PRODUCT)->OrderBy('products.created_at', 'desc')
            ->select(
                'products.id'
                , 'products.name'
                , 'products.ar_name'
                , 'products.is_featured'
                , 'products.price'
            )
            ->where('products.status', ProductStatusEnum::SHOWN)
            ->where('products.quantity', '>', 0)
            ->get();

        $featured_products = $products->where('is_featured', 1)->take(12);
        $recent_products = $products
            // ->where('created_at',date('Y-m-d',strtotime('-5 days', strtotime(date('Y-m-d')))))
            ->take(12);

        $top_seller_products = Product::join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('sub_orders', 'sub_orders.id', '=', 'order_items.sub_order_id')
            ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->select('products.*', DB::raw('count(orders.id) as order_count'))
            ->orderBy('order_count', 'desc')
            ->where('products.status', ProductStatusEnum::SHOWN)
            ->where('sub_orders.status', OrderStatusEnum::DELIVERED)
            ->orwhere('sub_orders.status', OrderStatusEnum::FINALIZED)
            ->groupBy('products.id')
            ->take(12)
            ->get();

        $popular_stores = Seller::join('sub_orders', 'sellers.id', '=', 'sub_orders.seller_id')
            ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->select('sellers.*', DB::raw('count(orders.id) as order_count'))
            ->where('business_type', ProductTypeEnum::PRODUCT)
            ->where('sub_orders.status', OrderStatusEnum::DELIVERED)
            ->orwhere('sub_orders.status', OrderStatusEnum::FINALIZED)
            ->where('sellers.status', SellerStatusEnum::ARCHIVE)
            ->groupBy('sellers.id')
            ->get();

        //dd($popular_stores);

        return view('frontend/products')
            ->with('popular_stores', $popular_stores)
            ->with('recent_products', $recent_products)
            ->with('featured_products', $featured_products)
            ->with('top_seller_products', $top_seller_products)
            ->with('product_categories', $product_categories);
    }

    public function services()
    {
        $service_categories = ProductCategory::where('show_in_public_menu', 1)
            ->where('product_category_type_id', ProductTypeEnum::SERVICE)
            ->OrderBy('name', 'asc')
            ->get();


        return view('frontend/services')->with('service_categories', $service_categories);
    }


    public function serviceProviderDetails($service_id)
    {
        $product = Product::find($service_id);
        return view('frontend/details')->with('product', $product);
    }

    public function sellerDetails($store_name)
    {
        $seller = Seller::where('store_name', $store_name)->first();

        return view('frontend/service-provider-details')->with('seller', $seller);
    }

    public function storeList()
    {
        $cities = City::orderBy('name', 'asc')->get();

        $favorite_stores = [];
        if (isset(Auth::user()->getBuyer))
            $favorite_stores = FavoriteStore::select('seller_id')->where('buyer_id', Auth::user()->getBuyer->id)->get();


        $letter = 'A';
        $filter_search = '';
        $filter_category = '';
        $store_category = ProductTypeEnum::PRODUCT;

        if (!empty($_GET['filter_store'])) $letter = $_GET['filter_store'];
        if (!empty($_GET['filter_search'])) {
            $filter_search = $_GET['filter_search'];
            $letter = strtoupper($filter_search[0]);
        }
        if (!empty($_GET['filter_category'])) $filter_category = $_GET['filter_category'];
        if (!empty($_GET['store_category'])) $store_category = $_GET['store_category'];

        $categories = ProductCategory::orderBy('name', 'asc')->where('parent_category_id', null)->where('product_category_type_id', $store_category)->get();

        $sellers = Seller::join('product_categories', 'product_categories.id', '=', 'sellers.category_id')
            ->select('sellers.*')
            ->orderBy('sellers.store_name', 'asc')
            ->orderBy('product_categories.name', 'asc')
            ->where('sellers.status', SellerStatusEnum::APPROVED)
            ->where('business_type', $store_category);

        if (!empty($letter) && empty($filter_search) && empty($filter_category)) $sellers = $sellers->where('store_name', 'LIKE', $letter . '%');
        if (!empty($filter_search)) $sellers = $sellers->where('store_name', 'LIKE', '%' . $filter_search . '%');
        if (!empty($filter_category)) $sellers = $sellers->where('sellers.category_id', $filter_category);
        $sellers = $sellers->get();

        return view('frontend/stores')
            ->with('letter', $letter)
            ->with('cities', $cities)
            ->with('categories', $categories)
            ->with('favorite_stores', $favorite_stores)
            ->with('sellers', $sellers);
    }


    public function addToFavoriteStore()
    {
        $seller_id = '';
        $exists = 0;
        if (!empty($_GET['seller_id'])) $seller_id = base64_decode($_GET['seller_id']);

        if (Auth::user() != null && Auth::user()->user_type == UserTypeEnum::USER) {
            $exists_favorite_store = FavoriteStore::where('buyer_id', Auth::user()->getBuyer->id)->where('seller_id', $seller_id)->exists();

            if ($exists_favorite_store == false) {
                $favorite_store = new FavoriteStore();
                $favorite_store->seller_id = $seller_id;
                $favorite_store->buyer_id = Auth::user()->getBuyer->id;
                $favorite_store->save();
                $exists = 1;
            }
            return response()->json(['success' => true, 'exists' => $exists]);
        } else {
            $courrent_url = url()->previous();
            Session::put(['session_url' => $courrent_url]);

            return response()->json(['success' => false, 'exists' => $exists]);
        }
    }

    public function addToWishList()
    {
        $product_id = '';
        $exists = 0;
        if (!empty($_GET['product'])) $product_id = Crypt::decrypt(($_GET['product']));

        if (Auth::user() != null && Auth::user()->user_type == UserTypeEnum::USER) {
            $exists_favorite_product = FavoriteProduct::where('buyer_id', Auth::user()->getBuyer->id)->where('product_id', $product_id)->exists();

            if ($exists_favorite_product == false) {
                $favorite_product = new FavoriteProduct();
                $favorite_product->product_id = $product_id;
                $favorite_product->buyer_id = Auth::user()->getBuyer->id;
                $favorite_product->save();
                $exists = 1;
            }
            return response()->json(['success' => true, 'exists' => $exists]);
        } else {
            $courrent_url = url()->previous();
            Session::put(['session_url' => $courrent_url]);

            return response()->json(['success' => false, 'exists' => $exists]);
        }
    }

    public function deals()
    {

//        $deal_of_the_day = Deal::where()->orderby('discount')->take(10);

//        $deals = Deal::join('products','products.id','deals.product_id')
//            ->join('product_categories','product_categories.id','products.category_id')
//            ->select(
//                'deals.id',
//                'deals.title',
//                'product_categories.name',
//                'product_categories.ar_name'
////                DB::raw('count(category_id) as deals')
//            )
//            ->get();

//        dd($deals);


//        $topDealInCategories =


        return view('frontend/deals');
    }

    public function dealAll()
    {
        return view('frontend/deal-all');
    }

    public function details($product_id)
    {
        $product = Product::find($product_id);


        return view('frontend/details')->with('product', $product);
    }

    public function serviceDetails($product_id)
    {
        $product = Product::find($product_id);

        return view('frontend/service_details')->with('product', $product);
    }


    public function cart()
    {
        return view('frontend/cart');
    }

    public function addToCart(Request $request)
    {
        try {
            $product_id = 0;
            $quantity = 0;
            $low_quantity = 0;
            $cart_item_count = 0;
            $cart_items_generate = '';
            $sub_total = 0;
            $name = '';
            $auth = false;

            if (!empty($_GET['product_id'])) $product_id = Crypt::decrypt($_GET['product_id']);
            if (!empty($_GET['quantity'])) $quantity = $_GET['quantity'];

            if (Auth::user() != null && Auth::user()->user_type == UserTypeEnum::USER) {
                $auth = true;
            }


            if (!empty($product_id) && $auth == true) {

                $cart_item_exists = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->where('product_id', $product_id)->first();


                if (isset($cart_item_exists)) {
                    $cart_item_exists = CartItem::find($cart_item_exists->id);
                } else {
                    $cart_item_exists = new CartItem();
                    $cart_item_exists->buyer_id = Auth::user()->getBuyer->id;
                    $cart_item_exists->product_id = $product_id;
                }

                if (!empty($quantity)) $cart_item_exists->quantity = $cart_item_exists->quantity + $quantity;
                else $cart_item_exists->quantity = $cart_item_exists->quantity + 1;

                $product = Product::where('id', $product_id)->first();


                if ($product->quantity >= $cart_item_exists->quantity) {
                    $cart_item_exists->save();
                } else {
                    $low_quantity = 1;
                }

                if (\App\UtilityFunction::getLocal() == "en")
                    $name = $cart_item_exists->getProduct->name;
                else $name = $cart_item_exists->getProduct->ar_name;

                $cart_items = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->get();

                $product_price = $cart_item_exists->getProduct->price;

                if (!empty($cart_items[0])) {
                    foreach ($cart_items as $cart_item) {
                        $media = $cart_item->getProduct->getMedia;
                        $cart_items_generate .= '<li>
                            <div class="header-cart_product_list_item clearfix">
                                <a class="item-preview" href="' . url('details/') . '">
                                <img';
                        if (!empty($media[0])) $cart_items_generate .= 'src="' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 70, 70, ['crop']) . '"';
                        else $cart_items_generate .= 'src="' . Image::url(asset('images/default.jpg'), 70, 70, ['crop']) . '"';

                        $cart_items_generate .= 'alt="Product"></a>
                                <h4><a class="font-additional font-weight-normal hover-focus-color" href="' . url('/product/details/' . $cart_item->getProduct->id) . '">' . $cart_item->getProduct->name . '</a></h4>
                                    <span class="item-cat font-main font-weight-normal"><a class="hover-focus-color" href="#">' . $cart_item->getProduct->getSeller->getCategory->name . '</a></span>';

                        $cart_items_generate .= '<span  class="item-price font-additional font-weight-normal customColor"><div style="font-size: 13px; text-align: right" class="product-item_price font-additional font-weight-normal customColor">';


                        $product_deal = $cart_item->getProduct->getProductDeals;
                        if (isset($product_deal)) {
                            $discount = $product_deal->discount;
                            if ($product_deal->discount_type == \App\Http\Controllers\Enum\DiscountTypeEnum::PERCENTAGE) $discount = $cart_item->getProduct->price * ($discount / 100);
                            if ($discount < 0) $discount = 0;

                            $product_price = $cart_item->getProduct->price - $discount;
                        }

                        if ($product_price < $cart_item->getProduct->price)
                            $cart_items_generate .= '<span style="font-size: 11px;">' . env('CURRENCY_SYMBOL') . number_format($cart_item->getProduct->price, 2) . '</span>';

                        $cart_items_generate .= env('CURRENCY_SYMBOL') . number_format($product_price, 2) . 'X' . $cart_item->quantity;

                        $cart_items_generate .= '</div> <a class="item-del hover-focus-color" href="javascript:;" data-id="' . Crypt::encrypt($cart_item->id) . '"><i class="fa fa-trash-o"></i></a>';

                        if ($cart_item->getProduct->quantity <= 0)
                            $cart_items_generate .= ' < span class="" style = "color: red; cursor: auto; font-weight: 700;  float: left;" >' . lang('messages.buyer.product_not_available') . '</span >';

                        $cart_items_generate .= '</span>';

                        $cart_items_generate .= '</div>
                        </li>';

                        $sub_total = $sub_total + ($product_price * $cart_item->quantity);
                        $cart_item_count = $cart_item_count + $cart_item->quantity;
                    }

                }


                return response()->json([
                    'success' => $auth,
                    'cart_items_generate' => $cart_items_generate,
                    'cart_item_count' => $cart_item_count,
                    'sub_total' => $sub_total,
                    'cart_item_name' => $name,
                    'low_quantity' => $low_quantity,
                ]);

            } else {

                $courrent_url = url()->previous();
                Session::put(['session_url' => $courrent_url]);
                return response()->json(['success' => $auth]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function removeCartItems($id = '')
    {
        $id = 0;
        $sub_total = 0;
        $cart_item_count = 0;
        $coupon = 0;
        $country = 0;
        $city = 0;

        if (!empty($_GET['item_id'])) $id = Crypt::decrypt($_GET['item_id']);

        if (!empty($_GET['country']) && !empty($_GET['city'])) {
            $country = $_GET['country'];
            $city = $_GET['city'];
        }

        $cart_item = CartItem::where('id', $id);

        if ($cart_item->exists() == true) {
            $data_html = app('App\Http\Controllers\FrontEnd\BuyerController')->getShippingCalculation($coupon, $country, $city);

            $cart_item->delete();

            $cart_items = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->get();

            foreach ($cart_items as $cart_i) {
                $sub_total = $sub_total + ($cart_i->getProduct->price * $cart_i->quantity);
                $cart_item_count = $cart_item_count + $cart_i->quantity;
            }


            $data_generate = $data_html['data_generate'];
            $cart_html = $data_html['cart_html'];

            return response()->json(['success' => true, 'cart_item_count' => $cart_item_count, 'sub_total' => $sub_total, 'data_generate' => $data_generate, 'cart_html' => $cart_html]);
        }

        return response()->json(['success' => false]);
    }


    // Email Exists Checking
    public function emailExistsChecking(Request $request)
    {
        $email = '';
        if (!empty($_GET['email'])) $email = $_GET['email'];

        $email_exist = User::where('email', $email)->where('user_type', UserTypeEnum::SELLER)->exists();

        return response()->json(['exists' => $email_exist]);
    }

    // Seller Email Exists Checking
    public function sellerEmailExistsChecking(Request $request)
    {
        $email = '';
        if (!empty($_GET['email'])) $email = $_GET['email'];

        $email_exist = Seller::where('business_email', $email)->exists();

        return response()->json(['exists' => $email_exist]);
    }

    // Store Exists Checking
    public function storeExistsChecking(Request $request)
    {
        $store = '';
        if (!empty($_GET['store'])) $store = $_GET['store'];

        $exists = Seller::where('store_name', $store)->exists();

        return response()->json(['store_exists' => $exists]);
    }


    public function forgetPassword()
    {

        return view('/frontend/auth/forget-password');
    }

    public function passwordEmail(Request $request)
    {

        $email = $request->email;
        $type = $request->type;

        $user = User::where('email', trim($email))->where('user_type', $type)->first();

        if (isset($user)) {

            $name = $user->username;


            DB::table('password_resets')->insert(
                ['email' => trim($email), 'token' => base64_encode(User::generateStrongPassword(8)), 'created_at' => date('Y-m-d H:i:s')]
            );

            $token = DB::table('password_resets')->where('email', $email)->first();

            if ($type == UserTypeEnum::SELLER && $user->getSeller->status == SellerStatusEnum::APPROVED) {
                $seller_status = $user->getSeller()->first();

                $data = [
                    'name' => $name,
                    'status' => $seller_status->status,
                    'type' => UserTypeEnum::SELLER,
                    'token' => $token->token,
                ];

                Mail::send('emails.forgetPassword', $data, function ($message) use ($user) {
                    $message->to($user->email)->subject('Password Change Link');
                });
            } else {
                return redirect()->back()->with('message', trans('messages.error_message.your_cradential_not_approved'));
            }

            if ($type == UserTypeEnum::USER) {
                $data = [
                    'name' => $name,
                    'status' => 0,
                    'type' => UserTypeEnum::USER,
                    'token' => $token->token,
                ];
                Mail::send('emails.forgetPassword', $data, function ($message) use ($user) {
                    $message->to($user->email)->subject('Password Change Link');
                });
            }

            return redirect()->back()->with('success_message', trans('messages.error_message.sent_link_to_email'));
        } else {
            return redirect()->back()->with('message', trans('messages.error_message.email_not_exist'));
        }
    }

    public function passwordReset($token)
    {
        $email = '';

        $token_ex = explode('::', $token);

        $token_email = DB::table('password_resets')->where('token', $token_ex[0])->first();

        if (!empty($token_email)) {
            $email = $token_email->email;

            DB::table('password_resets')->where('email', $email)->delete();
        } else {
            if ($token_ex[1] == UserTypeEnum::SELLER) {
                return redirect('/seller/login')->with('message', trans('messages.error_message.link_no_longer_available'));
            } elseif ($token_ex[1] == UserTypeEnum::USER) {
                return redirect('/buyer/login')->with('message', trans('messages.error_message.link_no_longer_available'));
            }

        }

        return view('/frontend/auth/password-reset')->with('email', $email)->with('user_type', $token_ex[1]);
    }

    public function passwordChange(Request $request)
    {
        $decode = explode('::', base64_decode($request->type));
        $email = $decode[0];
        $user_type = $decode[1];

        $user = User::where('email', $email)->where('user_type', $user_type)->first();

        if (!empty($user)) {
            $user->password = bcrypt($request->password);
            $user->save();

            if ($user->user_type == UserTypeEnum::SELLER) {
                return redirect('/seller/login')->with('success_message', trans('messages.error_message.password_changed_successfully'));
            } elseif ($user->user_type == UserTypeEnum::USER) {
                return redirect('/buyer/login')->with('success_message', trans('messages.error_message.password_changed_successfully'));
            }

        } else {
            return redirect()->back()->with('email', $email)->with('message', trans('messages.error_message.something_wrong'));
        }
    }

    public function emailSubscribe(Request $request)
    {
        $email = NewsLetterSubscriber::where('email', trim($request->email))->exists();

        $data_g = 0;
        if ($email == false) {
            $email_subscribe = new NewsLetterSubscriber();
            $email_subscribe->email = $request->email;
            $email_subscribe->save();

            $email_s = $request->email;
            $data = [];
            Mail::send('emails.emailSubscription', $data, function ($message) use ($email_s) {
                $message->to($email_s)->subject('Email Subscription');
            });

            $data_g = 1;
        }

        return response()->json(['success' => true, 'data_g' => $data_g]);
    }

    public function aboutUs()
    {
        $item = StaticPage::where('key_word', StaticPageEnum::ABOUT_US)->first();

        return view('/frontend/staticPage/page')->with('item', $item);
    }

    public function press()
    {
        $item = StaticPage::where('key_word', StaticPageEnum::PRESS)->first();

        return view('/frontend/staticPage/page')->with('item', $item);
    }

    public function support()
    {
        $item = StaticPage::where('key_word', StaticPageEnum::SUPPORT)->first();

        return view('/frontend/staticPage/page')->with('item', $item);
    }

    public function privacyPolicy()
    {
        $item = StaticPage::where('key_word', StaticPageEnum::PRIVACY_POLICY)->first();

        return view('/frontend/staticPage/page')->with('item', $item);
    }

    public function termAndCondition()
    {
        $item = StaticPage::where('key_word', StaticPageEnum::TERM_CONDITION)->first();

        return view('/frontend/staticPage/page')->with('item', $item);
    }

    // Product Review
    public function productReview(Request $request)
    {
        try {
            if (Auth::user() != null) {
                if (empty(ProductReview::where('product_id', Crypt::decrypt($request->product_id))->where('buyer_id', Auth::user()->getBuyer->id)->exists)) {
                    $review = new ProductReview();
                    $review->product_id = Crypt::decrypt($request->product_id);
                    $review->buyer_id = Auth::user()->getBuyer->id;
                    $review->review_rating = $request->rate;
                    $review->review_comment = $request->comment;
                    $review->save();
                    return redirect()->back()->with('message', 'Review Save Successfully.');
                } else {
                    return redirect()->back()->with('error_message', 'You already review this product.');
                }
            }
            return redirect('/buyer/login')->with('message', 'Please login to give review.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_message', 'Something wrong, Please try again.');
        }
    }

    // Store Review
    public function storeReview(Request $request)
    {
        try {
            if (Auth::user() != null && Auth::user()->user_type == UserTypeEnum::USER) {
                if (empty(StoreReview::where('seller_id', Crypt::decrypt($request->store_id))->where('buyer_id', Auth::user()->getBuyer->id)->exists())) {
                    $review = new StoreReview();
                    $review->seller_id = Crypt::decrypt($request->store_id);
                    $review->buyer_id = Auth::user()->getBuyer->id;
                    $review->review_rating = $request->rate;
                    $review->review_comment = $request->comment;
                    $review->save();
                    return redirect()->back()->with('message', 'Review Save Successfully.');
                } else {
                    return redirect()->back()->with('error_message', 'You already review this Store.');
                }
            }
            return redirect('/buyer/login')->with('message', 'Please login to give review.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_message', 'Something wrong, Please try again.');
        }
    }

    // Seller Media
    public function sellerMedia()
    {
        $media = '';
        $media_html = '';
        $count = '';
        $seller_id = Crypt::decrypt($_GET['seller_id']);

        if (!empty($seller_id)) {
            $media = Seller::getTotalServiceMedia($seller_id);

            $media_html = '';
            $media_array = '';
            $media_array .= '[';

            foreach ($media as $m) {
                $media_html .= '<a data-title-id="title-' . $m->id . '" ';
                if (isset($media)) $media_html .= 'href="' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $m->file_in_disk), 1024, 680, ['crop']) . '"';
                else $media_html .= 'href="' . Image::url(asset("/images/no-media.jpg"), 1024, 680, ['crop']) . '"';
                $media_html .= ' class="fancybox" rel="gallery">';
                $media_html .= '<img class="img_background" ';
                if (isset($m)) $media_html .= 'src="' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $m->file_in_disk), 250, 150, ['crop']) . '"';
                else $media_html .= 'src="' . Image::url(asset("/images/no-media.jpg"), 1024, 680, ['crop']) . '"';
                $media_html .= ' alt=""></a>';


                $media_array .= "{";
                $media_array .= ' href : ';
                $media_array .= "'" . asset(env('MEDIA_PHOTO_PATH') . $m->file_in_disk) . "'";
                $media_array .= "}";
                $count++;
                if ($count != count($media)) $media_array .= ',';
            }
            $media_array .= ']';
        }

        return response()->json($media_html);
//        return response()->json(['html'=>$media_html,'json_format'=>$media_array]);
    }

    // contactUs
    public function contactUs()
    {
        return view('/frontend/contact');
    }

    public function contactUsSave(Request $request)
    {
        try {
            $admin_email = Setting::where('key', 'admin_email')->first();

            $data = [
                'email_info' => $request->all()
            ];
            Mail::send('emails.contactEmail', $data, function ($message) use ($admin_email) {
                $message->to($admin_email->value, 'Contact Form')->subject('Contact Form Email');
            });

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.email_send_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.something_went_wrong_please_try_again'));
        }
    }

    // requestQuotation
    public function requestQuotation(Request $request)
    {
        try {
            $seller = Seller::where('id', Crypt::decrypt($request->skip))->first();
            $seller_email = $seller->getUser->email;
            $seller_name = $seller->getUser->username;
            $service_product = $seller->getProducts->where('id', $request->service_product)->first();
            $auth_info = '';
            if (!empty(Auth::user())) $auth_info = Auth::user();

            $request_message = $request->phone_email;

            $data = [
                'seller' => $seller,
                'quotation_info' => $request->all(),
                'auth_info' => $auth_info,
                'service_product' => $service_product];

            Mail::send('emails.requestQuotationEmail', $data, function ($message) use ($seller_email) {
                $message->to($seller_email, 'Quotation Request')->subject('Quotation Request Email');
            });

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.email_send_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.something_went_wrong_please_try_again'));
        }
    }

    // sendEnquiry
    public function sendEnquiry(Request $request)
    {
        try {
            if ($request->skip == 1) {
                $seller = Seller::where('id', Crypt::decrypt($request->id))->first();
                $seller_services = $seller->getProducts;
                $service_id = $request->service_id;

                $data_generate = '';
                $data_generate .= '<div class="form-group">
            <label for="" class="required">' . trans('messages.seller.details.your_phone_email') . '</label>
            <input type="text" class="form-control" name="phone_email"';
                if (!empty(Auth::user())) $data_generate .= 'value="' . Auth::user()->email . '"';
                $data_generate .= 'required>
          </div>
          <div class="form-group">
            <label for="" class="required">' . trans('messages.seller.details.request_for_the_service') . '</label>
            <select class="form-control" name="service_product" required>
              <option value="">' . trans('messages.select') . '</option>';
                if (!empty($seller_services[0])) {
                    foreach ($seller_services as $seller_service) {
                        $data_generate .= '<option value="' . $seller_service->id . '"';
                        if ($service_id == $seller_service->id) $data_generate .= ' selected ';
                        $data_generate .= '>';
                        if (\App\UtilityFunction::getLocal() == 'en') $data_generate .= $seller_service->name;
                        else $data_generate .= $seller_service->ar_name;
                        $data_generate .= '</option>';
                    }
                }

                $data_generate .= '</select>
          </div>
          <div class="form-group">
            <label for="">' . trans('messages.message') . '</label>
            <textarea name="quotation_message" class="form-control" rows="6" required></textarea>
          </div>
          <div class="form-group">
            <div id="g-recaptcha" data-sitekey="' . env('GOOGLE_RECAPTCHA_KEY') . '" style="display: inline-block;"></div>
            <button type="submit" class="btn btn-primary pull-right">' . trans('messages.send') . '</button>
          </div><input type="hidden" name="seller_id" value="' . Crypt::encrypt($seller->id) . '">';

                return response()->json(['data_generate' => $data_generate]);
            } else {
                $seller = Seller::where('id', Crypt::decrypt($request->seller_id))->first();
                $seller_email = $seller->getUser->email;
                $seller_name = $seller->getUser->username;
                $service_product = $seller->getProducts->where('id', $request->service_product)->first();
                $auth_info = '';
                if (!empty(Auth::user())) $auth_info = Auth::user();

                $request_message = $request->phone_email;

                $data = [
                    'seller' => $seller,
                    'quotation_info' => $request->all(),
                    'auth_info' => $auth_info,
                    'service_product' => $service_product];
            }

            Mail::send('emails.enquiryEmail', $data, function ($message) use ($seller_email) {
                $message->to($seller_email, 'Quotation Request')->subject('Quotation Request Email');
            });

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.email_send_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.something_went_wrong_please_try_again'));
        }
    }
}
