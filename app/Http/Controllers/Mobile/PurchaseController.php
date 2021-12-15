<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Enum\CouponDiscountTypeEnum;
use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\OrderTypeEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Model\Buyer;
use App\Model\CartItem;
use App\Model\City;
use App\Model\Country;
use App\Model\Coupon;
use App\Model\Deal;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\PaymentHistory;
use App\Model\Product;
use App\Model\PushNotification;
use App\Model\PushNotificationReceiver;
use App\Model\SubOrder;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Frontend\HomeController;
use Mail;

class PurchaseController extends Controller
{
    // Cart Info

    public function addToCartMobile(Request $request)
    {
        try{
            $product_id = $request->product_id;
            $user_id = $request->user_id;
            $buyer = Buyer::where('user_id',$user_id)->first();


            $name = '';
            $cart_item_count_total='';


            if (!empty($product_id) && !empty($user_id) ) {

                $cart_item_exists = CartItem::where('buyer_id', $buyer->id)->where('product_id', $product_id)->first();




//                    if(!empty($cart_item_exists) && $cart_item_exists->quantity>=env('CART_ITEM')) {
//
//                        return response()->json(['error' => false, 'message' => 'Can\'t Add More Than '.env('CART_ITEM').' Items']);
//                    }else {

                        if (!empty($cart_item_exists)) {
                            $cart = CartItem::find($cart_item_exists->id);
                            $cart->quantity = $cart->quantity + 1;
                            $cart->save();

                            $name = $cart->getProduct->name;
                        } else {
                            $cart = new CartItem();
                            $cart->buyer_id = $buyer->id;
                            $cart->product_id = $product_id;
                            $cart->quantity = 1;
                            $cart->save();

                            $name = $cart->getProduct->name;
                        }

                        $cart_items = CartItem::where('buyer_id', $buyer->id)->get();
                        $cart_item_count_total = $cart_items->sum('quantity');

                        return response()->json(['error' => false, 'message'=>'','cart_item_count' => $cart_item_count_total, 'cart_item_name' => $name]);
//                    }
            }

        }catch(\Exception $e){
            return response()->json(['error'=>true]);
        }
    }

    public function cartItemByUser(Request $request){
        try{
            if(!empty($request->user_id)){
                $cart_item_count = 0;
                $total_price = 0;
                $buyer = Buyer::where('user_id',$request->user_id)->first();

                $cart_items = DB::table('cart_items')->join('products','products.id','=','cart_items.product_id')
                    ->select(
                        'cart_items.*'
                        ,'products.name'
                        ,'products.price'
                        )
                    ->where('buyer_id',$buyer->id)->get();

                $cart_item_count = $cart_items->sum('quantity');

                if(isset($cart_items[0])){
                    foreach($cart_items as $item){
                        $item->price = CartItem::getProductInfo($item->product_id)->price;

                        $item->total_price_per_item = $item->price*$item->quantity ;
                        $total_price += $item->price*$item->quantity ;

                        $item->store_name = CartItem::getSellerInfo($item->product_id)->store_name;
                        $item->rating_info = Product::getProductRating($item->product_id);

                        $item->sell_count = CartItem::getSellCount($item->product_id)->sell_count;
                    }
                }

                //  dd($cart_items);

                return response()->json(['error'=>false,'total_price'=>$total_price,'cart_item_count'=>$cart_item_count,'cart_items'=>$cart_items]);
            }
            return response()->json(['error'=>false]);
        }catch(\Exception $e){
            return response()->json(['error'=>false]);
        }
    }

//    function cartItemByUser(Request $request){
//        try{
//            $cart_item_count = 0;
//            if(!empty($request->user_id)) {
//                $buyer = Buyer::where('user_id', $request->user_id)->first();
//                $cart_items = CartItem::where('buyer_id',$buyer->id)->get();
//                $cart_item_count = $cart_items->sum('quantity');
//
//                return response()->json(['error'=>false,'cart_item_count'=>$cart_item_count]);
//            }
//        }catch (\Exception $e){
//            return response()->json(['error'=>false]);
//        }
//    }

    public function removeCartItems(Request $request)
    {
        try {
            if (!empty($request->cart_item_id)) {
                $cart_item = CartItem::where('id', $request->cart_item_id);
                $cart_item->delete();
                return response()->json(['error'=>false]);
            }
            return response()->json(['error'=>true]);
        } catch (\Exception $e) {
            return response()->json(['error'=>true]);
        }
    }
    public function buyerApplyCoupon(Request $request)
    {
        try {
            $coupon_id = '';
            $total = '';
            $discount = '';
            $discount_txt = '';
            $discount_type = '';
            $coupon_code = trim($request->coupon_code);
            $sub_total = $request->sub_total;

            $coupon = Coupon::where('coupon', $coupon_code)->first();

            if (!empty($coupon)) {
                $discount = $coupon->discount;
                $discount_type = $coupon->discount_type;
                $info = 'You applied the coupon code: ' . $coupon->coupon;


                if ($discount_type == CouponDiscountTypeEnum::FIXED) {
                    $total = $sub_total - $discount;
                    $discount_txt = ' (' . $coupon->discount . ')' . $info;
                } elseif ($discount_type == CouponDiscountTypeEnum::PERCENTAGE) {
                    $discount = round(($sub_total * $discount) / 100);
                    $total = $sub_total - $discount;
                    $discount_txt = ' (' . $coupon->discount . '%)' . $info;
                }

                if ($total < 0) $total = 0;
//                $coupon_id = Crypt::encrypt($coupon->id);
                $coupon_id = $coupon->id;
//                $coupon_id = Crypt::encrypt($coupon->id);
            }

            return response()->json(['error' => false, 'coupon_id' => $coupon_id, 'total' => $total, 'sub_total' => $sub_total, 'discount' => $discount, 'discount_txt' => $discount_txt]);

        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }
    public function cartUpdate(Request $request){
        try{
            if (!empty($request->cart)) {
                for($i=0;$i<count($request->cart);$i++){
                    $cart_item = CartItem::find($request->cart[$i]);
                    $cart_item->quantity = $request->unit[$i];
                    $cart_item->save();
                }
                return response()->json(['error'=>false]);
            }else{
                return response()->json(['error'=>true]);
            }
        }catch(\Exception $e){
            return response()->json(['error'=>true]);
        }
    }

    public function cartPaymentConfirm(Request $request){

        try {

            $auth = false;
            if (!empty($request->user_id) && User::where('id',$request->user_id)->where('user_type',UserTypeEnum::USER)->exists()) {
                $user_id = $request->user_id;
                $auth = true;
            }


            if ($auth == true) {
                $buyer = Buyer::where('user_id',$user_id)->first();
                $cart_items = CartItem::where('buyer_id', $buyer->id)->get();

                $total_price = 0;
                $coupon = '';
                $tax = 0;
                $total_tax_amount = '';
                $status = [];
                $seller_id = [];
                $first = 0;
                $products = [];

                $coupon = $request->coupon_id;

                $country = $buyer->country;
                $city = $buyer->city;

                if($request->flag == 'false'){
                    $country_name = $request->country;
                    $country_info= Country::where('name',$country_name)->select('id')->first();
                    $country = $country_info->id;

                    $city_name = $request->city;
                    $city_info= City::where('name',$city_name)->select('id')->first();
                    $city = $city_info->id;
                }


                foreach ($cart_items as $cart_item) {
                    $total_price = $total_price + ($cart_item->getProduct->price * $cart_item->quantity);

                    $status[] = $cart_item->getProceedStatus($country,$city);

                    if(!in_array($cart_item->getProduct->getSeller->id,$seller_id)){
                        $seller_id[] = $cart_item->getProduct->getSeller->id;
                    }

                    $product = $cart_item->getProduct;
                    $products[] = ['product_id'=>$product->id,'seller_id'=>$product->seller_id,'price'=>$product->price*$cart_item->quantity];
                }

                $discount = $total_price - Coupon::getAmountAfterCouponApplied($coupon, $total_price);

                $shipping_rate = CartItem::getTaxAmountBySellerID($seller_id,$products,$country,$city);

                $tax_amount = $shipping_rate['tax'];
                $shipping_rate = $shipping_rate['shipping_rate'];


                if(!in_array(false,$status,true)){
                    \Log::info('succ');
                    DB::beginTransaction();
                    //store in the order table
                    $order = new Order();
                    $order->buyer_id = $buyer->id;
                    $order->coupon = Coupon::getCouponVal($coupon);
                    $order->sub_total_price = $total_price;
                    $order->vat_amount = $tax_amount;
                    $order->shipping_rate = $shipping_rate;
                    $order->discount = $discount;
                    $order->delivery_street = $request->street;
                    $order->delivery_city = $request->city;
                    $order->delivery_state = $request->state;
                    $order->delivery_zip = $request->zipcode;
                    $order->delivery_country = $request->country;
                    if ($request->checked_d_s == true) $order->delivery_schedule = date('Y-m-d', strtotime($request->delivery_schedule_date)) . ' ' . $request->delivery_schedule_time;
                    $order->save();
                    $last_order_id = $order->id;

                    //store in the sub order
                    foreach ($cart_items as $cart_item) {
                        $order_item = new OrderItem();
                        $order_item->price = $cart_item->getProduct->price;
                        if ($cart_item->getProduct->quantity < $cart_item->quantity) {
                            $order_item->quantity = $cart_item->getProduct->quantity;
                        } else {
                            $order_item->quantity = $cart_item->quantity;
                        }
                        $order_item->product_id = $cart_item->product_id;

                        //find the sub order if not exist then create
                        $product = Product::find($cart_item->product_id);
                        $product->quantity = $product->quantity - $cart_item->quantity;
                        $product->save();

                        $subOrder = SubOrder::where('order_id', $last_order_id)->where('seller_id', $product->seller_id)->first();
                        if (!isset($subOrder)) {
                            $subOrder = new SubOrder();
                            $subOrder->seller_id = $product->seller_id;
                            $subOrder->order_id = $last_order_id;
                            $subOrder->save();
                        }

                        $order_item->sub_order_id = $subOrder->id;

                        $order_item->save();
                    }

                    CartItem::where('buyer_id', $buyer->id)->delete();


                    $notification = new PushNotification();
                    $notification->notification_by = $user_id;
                    $notification->description = 'Your order payment is successful.';
                    $notification->notification_repeat = PushNotificationRepeatEnum::ONCE;

                    $notification->save();

                    if (isset($buyer->id)) {
                        $push_notification_receiver = new PushNotificationReceiver();
                        $push_notification_receiver->push_notification_id = $notification->id;
                        $push_notification_receiver->is_viewed = 0;
                        $push_notification_receiver->receiver_id = $user_id;
                        $push_notification_receiver->receiver_type = PushNotificationRepeatTypeEnum::BUYER;

                        $push_notification_receiver->save();
                    }


                    DB::commit();

                    $user = User::find($user_id);
                    $data = ['username' => $user->username, 'message' => $notification->description];

                    Mail::send('emails.orderConfirmation', $data, function ($message) use ($user) {
                        $message->to($user->email, $user->username)
                            ->subject('New Notification');
                    });


                    return response()->json(['error'=>false]);
                }
                else {
                    return response()->json(['error'=>true,'error_msg'=>'Seller Can\'t Ship This Destination.']);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>true]);
        }

    }

    // shipToBuyerAddress
    public function shippingTaxCharge(Request $request){
        try{
            if(!empty($request->user_id)){
                $user = User::find($request->user_id);
                $buyer = $user->getBuyer;

                $cart_items = CartItem::where('buyer_id', $buyer->id)->get();
                $cart_total_price = '';
                $seller_id = [];
                $coupon = $request->coupon_id;

                foreach ($cart_items as $cart_item) {

                    $cart_total_price = $cart_total_price + ($cart_item->getProduct->price * $cart_item->quantity);

                    if(!in_array($cart_item->getProduct->getSeller->id,$seller_id)){
                        $seller_id[] = $cart_item->getProduct->getSeller->id;
                    }

                    $product = $cart_item->getProduct;
                    $products[] = ['product_id'=>$product->id,'seller_id'=>$product->seller_id,'price'=>$product->price*$cart_item->quantity];
                }

                $country = $buyer->country;
                $city = $buyer->city;

                if(!empty($request->country)) {
                    $country_name = $request->country;
                    $country_info= Country::where('name',$country_name)->select('id')->first();
                    $country = $country_info->id;
                }
                if(!empty($request->city)) {
                    $city_name = $request->city;
                    $city_info= City::where('name',$city_name)->select('id')->first();
                    $city = $city_info->id;
                }

                $cart_total_price = Coupon::getAmountAfterCouponApplied($coupon, $cart_total_price);
                $tax_shipping_charge = CartItem::getTaxAmountBySellerID($seller_id,$products,$country,$city);



                $tax_amount = $tax_shipping_charge['tax'];
                $shipping_rate = $tax_shipping_charge['shipping_rate'];

                $total_payment = $cart_total_price + $tax_amount + $shipping_rate;

               // dd($total_payment,$shipping_rate,$tax_amount,$cart_total_price);

                return response()->json(['error'=>false,'total_invoice'=>$cart_total_price,'shipping_charge'=>$shipping_rate,'vat'=>$tax_amount,'total_payment'=>$total_payment]);
            }
            return response()->json(['error'=>false]);
        } catch (\Exception $e) {
            return response()->json(['error'=>false]);
        }
    }



//    public function cart(Request $request)
//    {
//        try{
//            $cart_items=[];
//            $total_cart_price='';
//            $cart_items = CartItem::join('deals','deals.id','=','cart_items.deal_id')
//                ->select(
//                    'cart_items.*'
//                    ,'deals.original_price'
//                    ,'deals.discount'
//                    ,'deals.discount_type'
//                )
//                ->where('buyer_id', $request->user_id)->get();
//            foreach($cart_items as $item){
//                $new_price = app('App\Http\Controllers\Mobile\ProductController')->getCurrentPrice($item->discount,$item->discount_type,$item->original_price);
//                $ratting = Deal::getDealRating($item->deal_id);
////                $item->sell_count = $item->getDeal->getDealSellCount->count();
//                $item->rating_count = $ratting['final_rating'];
//                $item->rating_count = $ratting['final_rating'];
//                $item->price = $new_price;
//                $item->total = $new_price*$item->quantity;
//                $total_cart_price += $new_price*$item->quantity;
//            }
//
//            return response()->json(['error'=>false,'cart_items'=>$cart_items,'total_cart_price'=>$total_cart_price]);
//        }catch(\Exception $e){
//            return response()->json(['error'=>true,'err_msg'=>$e]);
//        }
//
//    }
//
//
//
//
//
//
//
//    public function cartProceed(Request $request)
//    {
//        try {
//            if (Auth::user() != null && Auth::user()->user_type == UserTypeEnum::USER) {
//                $auth = true;
//            }
//
//            if ($auth == true) {
//                $cart_items = CartItem::where('buyer_id', Auth::user()->id)->get();
//                $user = Auth::user();
//                $citis = City::OrderBy('name', 'asc')->get();
//                $countries = Country::OrderBy('name', 'asc')->get();
//
//                Session::put('coupon_id',$request->coupon_id);
//
//                return view('frontend/user/cart-proceed')
//                    ->with('countries', $countries)
//                    ->with('citis', $citis)
//                    ->with('cart_items', $cart_items)
//                    ->with('user', $user);
//            }
//        } catch (\Exception $e) {
//            return redirect()->back()->with('error_msg', $e->getMessage());
//        }
//    }
//    public function cartProceedSave(Request $request)
//    {
//        try {
//            if (Auth::user() != null && Auth::user()->user_type == UserTypeEnum::USER) {
//                $auth = true;
//            }
//
//            if ($auth == true) {
//                DB::beginTransaction();
//                $coupon='';
//                if(!empty($request->coupon_id)) $coupon = Coupon::find(Crypt::decrypt($request->coupon_id));
//
//                $order = new Order();
//                $order->buyer_id = Auth::user()->id;
//                if(!empty($coupon->coupon))$order->coupon = $coupon->coupon;
//                $order->sub_total_price = $request->total_sub_price;
//                $order->delivery_street = $request->address;
//                $order->delivery_city = $request->city;
//                $order->delivery_country = $request->country;
//                $order->save();
//
//                if(!empty($request->cart)){
//                    for($i=0;$i<count($request->deal_id);$i++){
//
//                        $order_item = new OrderItem();
//                        $order_item->quantity = $request->unit[$i];
//                        $order_item->deal_id = $request->deal_id[$i];
//
//
//                        $deal = Deal::find($order_item->deal_id);
//                        if(isset($deal)) {
//                            if ($deal->discount_type == 1) $deal->current_price = $deal->original_price - $deal->discount;
//                            elseif ($deal->discount_type == 2) $deal->current_price = $deal->original_price - ($deal->original_price * $deal->discount) / 100;
//                            else $deal->current_price = $deal->original_price;
//                        }
//
//                        //Save Sub_orders
//                        $sub_order = SubOrder::where('order_id',$order->id)->where('merchant_id',$deal->merchant_id)->first();
//                        if(!isset($sub_order))
//                        {
//                            $sub_order = new SubOrder();
//                            $sub_order->merchant_id = $deal->merchant_id;
//                            $sub_order->order_id = $order->id;
//                            $sub_order->save();
//                        }
//
//
//                        $order_item->price = $deal->current_price;
//                        $order_item->sub_order_id = $sub_order->id;
//                        $order_item->save();
//                    }
//
//                    CartItem::where('buyer_id',Auth::user()->id)->delete();
//
//                }
//                DB::commit();
//                Session::forget(['coupon_id']);
//                Session::put(['order_id'=>Crypt::encrypt($order->id),'total_price'=>$request->total_price]);
//
//                return redirect('payment');
//            }
//        } catch (\Exception $e) { //dd($e);
//            return redirect()->back()->with('error_msg', $e->getMessage());
//        }
//    }
//    public function cartPayment(){
//        return view('frontend/user/payment');
//    }
//
////    public function cartPaymentSave(Request $request){
////
////        return redirect('payment-confirm')
////            ->with('order_id',$request->order_id)
////            ->with('payment_method',Crypt::encrypt($request->payment_method))
////            ->with('total_price',$request->total_price);
////    }
//    public function cartPaymentConfirmm(){
//        $cart_items = CartItem::where('buyer_id', Auth::user()->id)->get();
//        $total_price ='';
//        foreach($cart_items as $cart_item){
//            if($cart_item->getDeal->discount_type==1){
//                $discount = $cart_item->getDeal->original_price-$cart_item->getDeal->discount;
//            } elseif($cart_item->getDeal->discount_type==2){
//                $discount = ($cart_item->getDeal->original_price*$cart_item->getDeal->discount)/100;
//
//            }
//            $total_price = $total_price+($cart_item->getDeal->original_price-$discount)*$cart_item->quantity;
//        }
//
//        return view('frontend/user/payment-confirm')
//            ->with('total_price',$total_price)
//            ->with('cart_items',$cart_items);
//
//    }
//
//    public function cartpurchasedSuccess(){
//        return view('frontend/user/purchased-success');
//    }

}
