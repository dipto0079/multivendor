<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Enum\CouponDiscountTypeEnum;
use App\Http\Controllers\Enum\DiscountTypeEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Model\Buyer;
use App\Model\CartItem;
use App\Model\Country;
use App\Model\Product;
use App\Model\Coupon;
use App\Model\Notification;
use App\Model\Order;
use App\Model\Question;
use App\Model\QuestionAnswer;
use App\Model\Seller;
use App\Model\SubOrder;
use App\Model\OrderItem;
use App\Model\PushNotification;
use App\Model\PushNotificationReceiver;
use App\User;
use App\Model\FavoriteProduct;
use App\Model\FavoriteStore;
use App\Model\Setting;
use App\Model\Shipping;
use App\Model\City;
use App\UtilityFunction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\BuyerStatusEnum;
use Cookie;
use Hash;
use Illuminate\Support\Facades\Crypt;
use DB;
use Mail;
use Image;
use Session;

class BuyerController extends Controller
{

    // My Dashboard page
    public function myDashboard()
    {
        return view('frontend/buyer/my-dashboard');
    }

    // wish List page
    public function wishList()
    {
        $wishLists = FavoriteProduct::where('buyer_id', Auth::user()->getBuyer->id)->orderBy('created_at', 'desc')->get();

        return view('frontend/buyer/wishlist')->with('wishLists', $wishLists);
    }

    // Favourite Store List page
    public function favouriteStore()
    {
        $stores = FavoriteStore::orderBy('created_at', 'desc')
            ->where('buyer_id', Auth::user()->getBuyer->id)
            ->get();

        return view('frontend/buyer/favourite-store')->with('stores', $stores);
    }

    // Remove Favorite Store List
    public function removeFavoriteStore($id)
    {
        try {
            FavoriteStore::where('buyer_id', Auth::user()->getBuyer->id)->where('id', $id)->delete();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.store_removed_from_list'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . $e->getMessage());
        }
    }

    // Remove Wish List
    public function removeWishList($id)
    {
        try {
            FavoriteProduct::where('id', $id)->delete();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.product_removed'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . $e->getMessage());
        }
    }

    public function editProfileBuyer()
    {
        $language = \App\UtilityFunction::getLocal();
        $order_name = "name";
        if ($language == "ar") $order_name = "ar_name";
        $countries = Country::join('cities','cities.country_id','=','countries.id')
            ->select('countries.*')
            ->groupby('countries.id')
            ->orderBy('countries.name', 'asc')
            ->get();

        return view('frontend/buyer/edit-profile')->with('countries', $countries);
    }

    public function editProfileBuyerSave(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if (isset($user)) {

            try {

                if ($request->skip == 1) {
                    $country = $request->country_id;

                    $cities = City::where('country_id', $country)->orderby('name', 'asc')->get();

                    $cities_html = '';
                    foreach ($cities as $city) {
                        $cities_html .= '<option value="' . $city->id . '">';
                        if (\App\UtilityFunction::getLocal() == "en") $cities_html .= $city->name;
                        else $cities_html .= $city->ar_name;
                        $cities_html .= '</option>';
                    }
                    return response()->json(['cities_html' => $cities_html]);
                } else {
                    $user->username = $request->full_name;
                    $user->phone = $request->phone;
                    $user->country_code = $request->country_code;

                    $path = env('USER_PHOTO_PATH');
                    $image = $request->file('profile_image');
                    if (isset($image)) {
                        $fileName = 'user_' . date('Y-m-d-g-i-a') . $image->getClientOriginalName();
                        $image->move($path . '/', $fileName);
                        $user->photo = $fileName;
                    }

                    $buyer = $user->getBuyer;
                    $buyer->country = $request->country;
                    $buyer->city = $request->city;
                    $buyer->state = $request->state;
                    $buyer->street = $request->street;
                    $buyer->zip = $request->zip;

                    $user->save();
                    $buyer->save();

                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.profile_updated_successfully'));
                }

            } catch (Exception $e) {
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.something_went_wrong_please_try_again'));
            }

            //return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }


        return view('frontend/buyer/edit-profile');
    }

    public function password()
    {
        if (!empty(Auth::user()->password)) return view('frontend/buyer/password');
        else return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.something_went_wrong_please_try_again'));
    }

    public function passwordSave(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if (isset($user)) {
            if ($request->password == $request->confirm_password) {
                if (Hash::check($request->current_password, $user->password)) {
                    try {
                        $user->password = bcrypt($request->password);
                        $user->save();
                        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.password_updated_successfully'));
                    } catch (\Exception $e) {
                        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
                    }
                }
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . trans('messages.error_message.your_current_password_is_wrong'));
            }
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . trans('messages.error_message.password_not_matched_with_the_confirmed_password'));
        }
    }

    // Notification List page
    public function notification()
    {
        $notifications = PushNotification::join('push_notification_receivers', 'push_notifications.id', '=', 'push_notification_receivers.push_notification_id')
            ->where('push_notification_receivers.receiver_type', PushNotificationRepeatTypeEnum::BUYER)
            ->where('push_notification_receivers.receiver_id', Auth::user()->id)->get();

        return view('frontend/buyer/notification')
            ->with('notifications', $notifications);
    }

    // Remove Notification
    public function removeNotification($id)
    {
        try {
            $notification = PushNotificationReceiver::where('receiver_id', Auth::user()->id)->where('id', $id)->first();
            $notification->delete();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // Remove Notification
    public function notificationClearAll()
    {
        try {
            $notification = PushNotificationReceiver::where('receiver_id', Auth::user()->id)->delete();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // Notification Read
    public function notificationRead($id)
    {
        try {
            $notification = PushNotificationReceiver::find(Crypt::decrypt($id));
            $notification->is_viewed = 1;
            $notification->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function isProfileInformationCompleted()
    {
        $user = Auth::user();
        $buyer = $user->getBuyer;

        if (empty($user->username)) return 0;
        if (empty($user->email)) return 0;
        if (empty($user->phone)) return 0;
        if (empty($buyer->country)) return 0;
        if (empty($buyer->city)) return 0;
        if (empty($buyer->state)) return 0;
        if (empty($buyer->street)) return 0;
        if (empty($buyer->zip)) return 0;

        return 1;
    }

    // Cart List Page
    public function buyerCartList()
    {
        //before payment u need to fill-up your personal information
        if (!$this->isProfileInformationCompleted()) {
            return redirect('/buyer/edit-profile')->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . trans('messages.error_message.you_must_fill_up_your_profile'));
        }

        $cart_items = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->get();

        return view('frontend/buyer/cart-list')->with('cart_items', $cart_items);
    }


    // Remove Cart List
    public function removeCartList($id)
    {
        try {
            $cart = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->where('id', $id)->first();
            $cart->delete();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // Update Cart List
    public function buyerUpdateCartList(Request $request)
    {
        try {
            $cart_quantity = $request->qty;
            $cart_id = $request->skip;

            $coupon_code = trim($request->coupon_code);
            $sub_total = 0;
            $quantity_message = [];

            foreach ($cart_quantity as $index => $value) {
                $id = Crypt::decrypt($cart_id[$index]);

                $cart_item = CartItem::where('id', $id)->where('buyer_id', Auth::user()->getBuyer->id)->first();
                $cart_item->getProduct->quantity;

                if ($cart_item->getProduct->quantity < $cart_item->quantity) {

                    $extra = $cart_item->quantity - $cart_item->getProduct->quantity;
                    $cart_item->quantity = $cart_item->getProduct->quantity;
                    $msg = 'Cart Quantity ' . $extra . ' More Than Product Quantity.';
                    $cart_id = $cart_item->id;

                    array_push($quantity_message, ['msg' => $msg, 'id' => $cart_id]);

                } else {
                    $cart_item->quantity = $value;
                }

                $cart_item->save();

                $sub_total = $sub_total + ($cart_item->getProduct->price * $cart_item->quantity);
            }

            $coupon = Coupon::where('coupon', $coupon_code)->first();

            if (!empty($coupon)) {
                $discount = $coupon->discount;
                $discount_type = $coupon->discount_type;

                if ($discount_type == CouponDiscountTypeEnum::FIXED) {
//                    $total = $sub_total - $discount;
                } elseif ($discount_type == CouponDiscountTypeEnum::PERCENTAGE) {
                    $discount = round(($sub_total * $discount) / 100);
//                    $total = $sub_total - $discount;
                }

//                $coupon_id = $coupon->id;
            }

            if (!empty($coupon)) {
                return redirect('/buyer/cart-list')
                    ->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Cart update successfully.');
            } else {
                return redirect('/buyer/cart-list')->with('quantity_message', $quantity_message)->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.cart_updated_successfully'));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // Shipping Calculation
    public function getShippingCalculation($coupon = null, $country_id = null, $city_id = null, $skip = 0)
    {
        $cart_total_price = 0;
        $cart_item_count = 0;
        $coupon_info = [];
        $status = 0;
        $shipping_status = 0;
        $status_array = [];
        $discount_txt = '';
        $seller_id = [];
        if (!empty($coupon)) {
            $coupon = Crypt::decrypt($coupon);
            $coupon_info = Coupon::where('id', $coupon)->first();
        }

        // Buyer Information
        // Ship to my address
        $buyer = Auth::user()->getBuyer;
        $country = $buyer->country;
        $city = $buyer->city;

        if (!empty($country_id) && !empty($city_id)) {
            $country = $country_id;
            $city = $city_id;
        }

        $cart_items = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->get();
        $cart_html = '';


        foreach ($cart_items as $cart_item) {

            $product_price = $cart_item->getProduct->price;
            $product_deal = $cart_item->getProduct->getProductDeals;

            if (isset($product_deal)) {
                $discount = $product_deal->discount;
                if ($product_deal->discount_type == DiscountTypeEnum::PERCENTAGE) $discount = $cart_item->getProduct->price * ($discount / 100);
                if ($discount < 0) $discount = 0;

                $product_price = $cart_item->getProduct->price - $discount;
            }

            $cart_total_price = $cart_total_price + ($product_price * $cart_item->quantity);
            $media = $cart_item->getProduct->getMedia;
            $cart_item_count = $cart_item_count + $cart_item->quantity;

            // Check is the product can ship the delivery location using city and country
            $status = $cart_item->getProceedStatus($country, $city);
            $status_array[] = $cart_item->getProceedStatus($country, $city);

            // new cart html section for which product can shipped and which is not.
            $cart_html .= '<tr';
            if ($status == false) $cart_html .= ' style="border-color: red;"';
            $cart_html .= '><td class="product-remove"><a href="javascript:;" data-id="' . Crypt::encrypt($cart_item->id) . '" class="item-del remove remove_from_wishlist">×</a></td>
                                <td class="product-thumbnail"><a href="javascript:;" data-id="' . Crypt::encrypt($cart_item->id) . '" class="item-del remove remove_from_wishlist visible-xs visible-sm pull-right">×</a>';
            if (!empty($media[0])) {
                $cart_html .= '<span class="img_popover" data-toggle="popover" data-html="true"
                                            data-trigger="focus" data-content="<img src=' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 350, 350, ['crop']) . '>">
                                        <img width="150" height="150" class="img_background" src="' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 150, 150, ['crop']) . '"
                                             alt="Product-1">
                                       </span>';
            } else {
                $cart_html .= '<img width="150" height="150" class="img_background"
                                             src="' . asset('/image/default.jpg') . '"
                                             data-src="' . Image::url(asset('/image/default.jpg'), 150, 150, ['crop']) . '"
                                             alt="Product-1">';
            }
            $cart_html .= '</td><td class="product-name">
                                                    <a style="font-size: 13px;" href="' . url('/product/details/' . $cart_item->getProduct->id) . '">';
            if (\App\UtilityFunction::getLocal() == "en") {
                $cart_html .= $cart_item->getProduct->name;
            } else {
                $cart_html .= $cart_item->getProduct->ar_name;
            }
            $cart_html .= '</a>';

            $cart_html .= '<div class="product-category"><span class="label label-custom label-pill label-success cart-category">';
            if (UtilityFunction::getLocal() == "en")
                $cart_html .= '<a href="' . url('/products/category/' . $cart_item->getProduct->getSeller->getCategory->id) . '">' . $cart_item->getProduct->getSeller->getCategory->name;
            else
                $cart_html .= $cart_item->getProduct->getSeller->getCategory->ar_name . '</a>';

            $cart_html .= '</span></div>';


//
//                                                    <div class="product-category">
//                                                        <span>' . trans('messages.buyer.category') . ' </span>';
//            if (\App\UtilityFunction::getLocal() == "en") {
//                $cart_html .= $cart_item->getProduct->getSeller->getCategory->name;
//            } else {
//                $cart_html .= $cart_item->getProduct->getSeller->getCategory->ar_name;
//            }
//            $cart_html .= '</div>';


            if ($status == false) $cart_html .= '<span class="label label-danger">' . trans('messages.error_message.this_product_cant_ship_to_your_location') . '</span>';
            $cart_html .= '</td>';
            $cart_html .= '<td style="width:100px" class="product-price"><span class="amount">';


            $cart_html .= '<div style="font-size: 13px;" class="product-item_price font-additional font-weight-normal customColor">' . env('CURRENCY_SYMBOL') . number_format($product_price, 2);


            if ($product_price < $cart_item->getProduct->price)
                $cart_html .= '<br><span style="font-size: 11px;">' . env('CURRENCY_SYMBOL') . number_format($cart_item->getProduct->price, 2) . '</span>';

            $cart_html .= '</div>';

            $cart_html .= '</span></td>';

            $cart_html .= '<td class="product-stock-status">
                                                    <div class="form-group wishlist-in-stock"
                                                         style="margin-bottom: 5px;">';
            if ($cart_item->getProduct->quantity > 0) {
                $cart_html .= '<input type="text" class="quantity_input qty" name="qty[]" onpaste="qtyNumber()" max="' . $cart_item->getProduct->quantity . '" data-bts-max="' . $cart_item->getProduct->quantity . '" value="' . $cart_item->quantity . '"
                                                                   data-price="' . $product_price . '">
                                                            <input type="hidden" name="skip[]"
                                                                   value="' . Crypt::encrypt($cart_item->id) . '">';
            } else {
                $cart_html .= '<strong style="color: red">' . trans('messages.buyer.product_not_available') . '</strong><div class="clearfix"></div>';
            }
            $cart_html .= '</div></td>';
            $cart_html .= '<td class="product-add-to-cart"><span class="amount">';
            if ($cart_item->getProduct->quantity <= 0) {
                $cart_html .= '0.00';
            } else $cart_html .= env('CURRENCY_SYMBOL') . number_format($product_price * $cart_item->quantity, 2);
            $cart_html .= '</span></td></tr>';


            if (!in_array($cart_item->getProduct->getSeller->id, $seller_id)) {
                $seller_id[] = $cart_item->getProduct->getSeller->id;
            }

            $product = $cart_item->getProduct;
            $products[] = ['product_id' => $product->id, 'seller_id' => $product->seller_id, 'price' => $product_price * $cart_item->quantity];

        }


        // Delivery Address
        $cart_total_price_after_coupon = Coupon::getAmountAfterCouponApplied($coupon, $cart_total_price);
        $tax_shipping_charge = CartItem::getTaxAmountBySellerID($seller_id, $products, $country, $city);

        $tax_amount = $tax_shipping_charge['tax'];
        $shipping_rate = $tax_shipping_charge['shipping_rate'];

        $total_payment = $cart_total_price_after_coupon + $tax_amount + $shipping_rate;


        // Coupon calculation section
        if (!empty($coupon)) {
            $discount = $coupon_info->discount;
            $discount_type = $coupon_info->discount_type;
            $info = '<span class="discount_info"><br>' . trans('messages.buyer.you_applied_the_coupon_code') . ': <span>' . $coupon_info->coupon . '</span></span>';

            if ($discount_type == CouponDiscountTypeEnum::FIXED) {
                $discount_txt = ' (' . $coupon_info->discount . ')' . $info;
            } elseif ($discount_type == CouponDiscountTypeEnum::PERCENTAGE) {
                $discount = round(($cart_total_price * $discount) / 100);
                $discount_txt = ' (' . $coupon_info->discount . '%)' . $info;
            }

            if ($skip == 1) $discount_txt = trans('messages.buyer.coupon');
        }

//        dd($tax_amount,$shipping_rate,$cart_total_price,$total_payment);


        $data_generate = '';
        $vat_percentage = 0;

        if (!in_array(false, $status_array)) {

            $vat_percentage = ($tax_amount * 100) / $cart_total_price;

            if ($skip == 0) $data_generate .= '<div class="cart_totals"><h2>' . trans('messages.buyer.cart_totals') . '</h2>';
            $data_generate .= '<table cellspacing="0"';
            if ($skip == 0) $data_generate .= 'class="checkout_table"';
            $data_generate .= '><tbody>';


            $data_generate .= '<tr class="order-total"><th>' . trans('messages.buyer.subtotal') . '</th><td style="width: 15%; text-align: right;"><strong>' . env('CURRENCY_SYMBOL') . number_format($cart_total_price, 2) . '</strong></td></tr>';
            $data_generate .= '<tr class="order-total"><th>' . trans('messages.vat') . ' (' . number_format($vat_percentage, 2) . '%)</th><td style="width: 15%; text-align: right;"><strong>';
            $data_generate .= env('CURRENCY_SYMBOL') . number_format($tax_amount, 2);
            $data_generate .= '</strong></td></tr><tr class="order-total"><th>' . trans('messages.shipping_charge') . '</th><td style="width: 15%; text-align: right;"><strong>';
            $data_generate .= env('CURRENCY_SYMBOL') . number_format($shipping_rate, 2);
            $data_generate .= '</strong></td></tr>';
            if (!empty($coupon)) $data_generate .= '<tr class="order-total"><th>' . $discount_txt . '</th><td style="width: 15%; text-align: right;"><strong>' . env('CURRENCY_SYMBOL') . number_format($discount, 2) . '</strong></td></tr>';
            if ($skip == 0) $data_generate .= '';
            $data_generate .= '<tr class="order-total large"><th>' . trans('messages.buyer.total') . '</th><td style="width: 15%; text-align: right;"><strong>' . env('CURRENCY_SYMBOL') . number_format($total_payment, 2) . '</strong></td></tr>';


            $data_generate .= '</tbody></table>';

            if ($skip == 0) {
                $data_generate .= '<div class="proceed-to-checkout">
                                        <button type="submit" name="check_out"
                                                class="btn btn-primary font-additional">' . trans('messages.buyer.proceed') . '</button>
                                    </div>
                                </div>';
            }
        } else {
            $shipping_status = 1;
            $data_generate .= '<span class="label label-danger" style="white-space: normal;">' . trans('messages.error_message.cart_list_proceed_checkout_error') . '</span>';
        }

        $data_html = ['data_generate' => $data_generate, 'cart_html' => $cart_html, 'shipping_status' => $shipping_status];

        return $data_html;
    }

    // Buyer Apply Coupon
    public function buyerApplyCoupon(Request $request)
    {
//        $coupon_id = '';
//        $total = '';
//        $discount = '';
//        $discount_txt = '';
//        $discount_type = '';
//        $success = false;

//        $sub_total = $request->sub_total;
//        $tax = $request->tax;
//        $shipping_charge = $request->shipping_charge;

        $country = 0;
        $city = 0;
        $data_generate = '';
        $cart_html = '';

        $coupon_code = trim($request->coupon_code);
        $buyer_shipping_address = $request->buyer_shipping_address;
        $coupon_info = Coupon::where('coupon', $coupon_code)->first();
        $coupon_id = Crypt::encrypt($coupon_info->id);

        if (!empty($_GET['country']) && !empty($_GET['city'])) {
            $country = $_GET['country'];
            $city = $_GET['city'];
        }

        if ($buyer_shipping_address == 'true' || (!empty($_GET['country']) && !empty($_GET['city']))) {
            $data_html = $this->getShippingCalculation($coupon_id, $country, $city);

            $data_generate = $data_html['data_generate'];
            $cart_html = $data_html['cart_html'];
        }

        return response()->json(['success' => true, 'data_generate' => $data_generate, 'cart_html' => $cart_html, 'coupon_id' => $coupon_id]);


//        if (!empty($coupon)) {
//            $discount = $coupon->discount;
//            $discount_type = $coupon->discount_type;
//            $info = '<span class="discount_info"><br>'.trans('messages.buyer.you_applied_the_coupon_code').': <span>' . $coupon->coupon . '</span></span>';
//
//
//            if ($discount_type == CouponDiscountTypeEnum::FIXED) {
//                $total = $sub_total - $discount;
//                $discount_txt = ' (' . $coupon->discount . ')' . $info;
//            } elseif ($discount_type == CouponDiscountTypeEnum::PERCENTAGE) {
//                $discount = round(($sub_total * $discount) / 100);
//                $total = $sub_total - $discount;
//                $discount_txt = ' (' . $coupon->discount . '%)' . $info;
//            }
//
//            if ($total < 0) $total = 0;
//            $coupon_id = Crypt::encrypt($coupon->id);
//            $success = true;
//
////            $setting = Setting::where('key','vat_percentage')->first();
////            $vat = $setting->value;
////            $vat_amount = ($total*$vat)/100;
//
//            $paid_amount = $total;
//
//            $data_generate = '';
//            $data_generate .= '<tr class="order-total"><th>'.trans('messages.buyer.total').'</th><td style="width: 170px; text-align: right;"><strong>'.env('CURRENCY_SYMBOL').number_format($sub_total,2).'</strong></td></tr>';
//            $data_generate .= '<tr class="order-total"><th>'.$discount_txt.'</th><td style="width: 170px; text-align: right;"><strong>'.env('CURRENCY_SYMBOL').number_format($discount,2).'</strong></td></tr>';
//            $data_generate .= '<tr class="order-total"><th>'.trans('messages.buyer.total').'</th><td style="width: 170px; text-align: right;"><strong>'.env('CURRENCY_SYMBOL').number_format($paid_amount,2).'</strong></td></tr>';
//
//        }
//
//        return response()->json(['success' => $success, 'coupon_id' => $coupon_id, 'data_generate' => $data_generate]);
    }

    // Shipping Calculation
    public function shippingCalculation()
    {
        try {
            $coupon = 0;
            $country = 0;
            $city = 0;

            if (!empty($_GET['coupon'])) {
                $coupon = $_GET['coupon'];
            }

            if (!empty($_GET['country']) && !empty($_GET['city'])) {
                $country = $_GET['country'];
                $city = $_GET['city'];
            }

            $data_html = $this->getShippingCalculation($coupon, $country, $city);

            $data_generate = $data_html['data_generate'];
            $cart_html = $data_html['cart_html'];

            return response()->json(['success' => true, 'data_generate' => $data_generate, 'cart_html' => $cart_html]);

        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // payment
    public function deliveryPayment(Request $request)
    {
        try {
            if ($request->skip == 1) {
                $country = $request->country_id;

                $cities = City::where('country_id', $country)->where('shipping_status', 1)->get();

                $cities_html = '';
                foreach ($cities as $city) {
                    $cities_html .= '<option value="' . $city->id . '">';
                    if (\App\UtilityFunction::getLocal() == "en") $cities_html .= $city->name;
                    else $cities_html .= $city->ar_name;
                    $cities_html .= '</option>';
                }
                return response()->json(['cities_html' => $cities_html]);
            } else {
                $coupon = '';
                if (!empty($request->coupon_id)) {
                    $coupon = $request->coupon_id;
                }

                $country = $request->country;
                $city = $request->city;
                $state = $request->state;
                $zip = $request->zip;
                $street = $request->street;

                if ($request->buyer_shipping_address == 'on' || $request->checkout != null) {
                    $country = Auth::user()->getBuyer->country;
                    $city = Auth::user()->getBuyer->city;
                    $state = Auth::user()->getBuyer->state;
                    $zip = Auth::user()->getBuyer->zip;
                    $street = Auth::user()->getBuyer->street;
                }

                $checked_d_s = $request->checked_d_s;
                $delivery_schedule_date = $request->delivery_schedule_date;
                $delivery_schedule_time = $request->delivery_schedule_time;

                Session::put('delivery_info',
                    ['country' => $country,
                        'city' => $city,
                        'state' => $state,
                        'zip' => $zip,
                        'street' => $street,
                        'checked_d_s' => $checked_d_s,
                        'delivery_schedule_date' => $delivery_schedule_date,
                        'delivery_schedule_time' => $delivery_schedule_time]);

                $cart_items = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->get();
                $data_html = $this->getShippingCalculation($coupon, $country, $city, 1);
                $cart_calculation = $data_html['data_generate'];
                $shipping_status = $data_html['shipping_status'];

                if (empty($cart_items)) {
                    return redirect('/buyer/cart-list');
                }

                return view('/frontend/buyer/delivery-payment')
                    ->with('cart_calculation', $cart_calculation)
                    ->with('shipping_status', $shipping_status)
                    ->with('coupon', $coupon);
            }

        } catch (\Exception $e) {
            return redirect('/buyer/cart-list');
        }
    }

    // Place Order
    public function placeOrder(Request $request)
    {


        try {
            if ($request->skip == 1) {
                $country = $request->country_id;

                $cities = City::where('country_id', $country)->where('shipping_status', 1)->get();

                $cities_html = '';
                foreach ($cities as $city) {
                    $cities_html .= '<option value="' . $city->id . '">';
                    if (\App\UtilityFunction::getLocal() == "en") $cities_html .= $city->name;
                    else $cities_html .= $city->ar_name;
                    $cities_html .= '</option>';
                }
                return response()->json(['cities_html' => $cities_html]);
            } else {
                $cart_items = CartItem::where('buyer_id', Auth::user()->getBuyer->id)->get();
                $total_price = 0;
                $coupon = '';
                $status = [];
                $seller_id = [];
                $products = [];

                try {
                    if (!empty($request->coupon)) $coupon = Crypt::decrypt($request->coupon);
                } catch (\Exception $e) {
                    return redirect('/buyer/cart-list')->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . trans('messages.error_message.invalid_coupon_code'));
                }

                $delivery_info = Session::get('delivery_info');
                $country = $delivery_info['country'];
                $city = $delivery_info['city'];

                foreach ($cart_items as $cart_item) {
                    $quantity = $cart_item->quantity;
                    if ($quantity > $cart_item->getProduct->quantity) $quantity = $cart_item->getProduct->quantity;

                    $product_price = $cart_item->getProduct->price;
                    $product_deal = $cart_item->getProduct->getProductDeals;

                    if (isset($product_deal)) {
                        $discount = $product_deal->discount;
                        if ($product_deal->discount_type == DiscountTypeEnum::PERCENTAGE) $discount = $cart_item->getProduct->price * ($discount / 100);
                        if ($discount < 0) $discount = 0;

                        $product_price = $product_price - $discount;
                    }

                    $total_price = $total_price + ($product_price * $quantity);

                    $status[] = $cart_item->getProceedStatus($country, $city);

                    if (!in_array($cart_item->getProduct->getSeller->id, $seller_id)) {
                        $seller_id[] = $cart_item->getProduct->getSeller->id;
                    }

                    $product = $cart_item->getProduct;
                    $products[] = ['product_id' => $product->id, 'seller_id' => $product->seller_id, 'price' => $product_price * $quantity];
                }

                $discount = $total_price - Coupon::getAmountAfterCouponApplied($coupon, $total_price);

                $tax_shipping_rate = CartItem::getTaxAmountBySellerID($seller_id, $products, $country, $city);

                $tax_amount = $tax_shipping_rate['tax'];
                $shipping_rate = $tax_shipping_rate['shipping_rate'];
                $seller_vat_shipping_charge = $tax_shipping_rate['sub_order_info'];


                if (!in_array(false, $status, true)) {
                    DB::beginTransaction();

                    //store in the order table
                    $order = new Order();
                    $order->buyer_id = Auth::user()->getBuyer->id;
                    $order->coupon = Coupon::getCouponVal($coupon);
                    $order->sub_total_price = $total_price;
                    $order->vat_amount = $tax_amount;
                    $order->shipping_rate = $shipping_rate;
                    $order->discount = $discount;
                    $order->delivery_country = $delivery_info['country'];
                    $order->delivery_city = $delivery_info['city'];
                    $order->delivery_state = $delivery_info['state'];
                    $order->delivery_zip = $delivery_info['zip'];
                    $order->delivery_street = $delivery_info['street'];
                    if (isset($delivery_info['checked_d_s'])) $order->delivery_schedule = date('Y-m-d', strtotime($delivery_info['delivery_schedule_date'])) . ' ' . $delivery_info['delivery_schedule_time'];
                    $order->save();
                    $last_order_id = $order->id;

                    //store in the sub order
                    foreach ($cart_items as $cart_item) {

                        $order_item = new OrderItem();

                        $order_item->product_price = $cart_item->getProduct->price;

                        $product_deal = $cart_item->getProduct->getProductDeals;
                        if (isset($product_deal)) {
                            $discount = $product_deal->discount;
                            if ($product_deal->discount_type == DiscountTypeEnum::PERCENTAGE) $discount = $cart_item->getProduct->price * ($discount / 100);
                            if ($discount < 0) $discount = 0;

                            $order_item->deal_price = $order_item->product_price - $discount;
                        }

                        if ($cart_item->getProduct->quantity < $cart_item->quantity) {
                            $order_item->quantity = $cart_item->getProduct->quantity;
                        } else {
                            $order_item->quantity = $cart_item->quantity;
                        }
                        $order_item->product_id = $cart_item->product_id;


                        if (isset($product_deal)) {
                            $order_item->discount = $discount;
                            $order_item->discount_rate = $product_deal->discount;
                            $order_item->discount_type = $product_deal->discount_type;
                        }


                        //find the sub order if not exist then create
                        $product = Product::find($cart_item->product_id);
                        $product->quantity = $product->quantity - $cart_item->quantity;
                        $product->save();

                        $subOrder = SubOrder::where('order_id', $last_order_id)->where('seller_id', $product->seller_id)->first();
                        if (!isset($subOrder)) {
                            for ($i = 0; $i < count($seller_vat_shipping_charge); $i++) {
                                if ($seller_vat_shipping_charge[$i]['seller_id'] == $product->seller_id) {


                                    $subOrder = new SubOrder();
                                    $subOrder->seller_id = $product->seller_id;
                                    $subOrder->shipping_cost = $seller_vat_shipping_charge[$i]['shipping_rate'];
                                    $subOrder->tax = $seller_vat_shipping_charge[$i]['vat'];
                                    $subOrder->order_id = $last_order_id;

                                    $subOrder->tax_rate = $seller_vat_shipping_charge[$i]['vat_rate'];
                                    $subOrder->shipping_type = $seller_vat_shipping_charge[$i]['shipping_type'];

                                    $seller = Seller::where('status', SellerStatusEnum::APPROVED)->where('id', $product->seller_id)->first();
                                    $subOrder->admin_commission = $seller->commission;


//                                    dd($subOrder);

                                    $subOrder->save();
                                }
                            }
                        }

                        $order_item->sub_order_id = $subOrder->id;

                        $order_item->save();


                        Session::forget('delivery_info');
                    }

                    CartItem::where('buyer_id', Auth::user()->getBuyer->id)->delete();


                    $notification = new PushNotification();
                    $notification->notification_by = Auth::user()->id;
                    $notification->description = 'Your order payment is successful.';
                    $notification->notification_repeat = PushNotificationRepeatEnum::ONCE;

                    $notification->save();

                    if (isset(Auth::user()->getBuyer->id)) {
                        $push_notification_receiver = new PushNotificationReceiver();
                        $push_notification_receiver->push_notification_id = $notification->id;
                        $push_notification_receiver->is_viewed = 0;
                        $push_notification_receiver->receiver_id = Auth::user()->id;
                        $push_notification_receiver->receiver_type = PushNotificationRepeatTypeEnum::BUYER;

                        $push_notification_receiver->save();
                    }


                    DB::commit();

                    $user = User::find(Auth::user()->id);
                    $data = ['username' => $user->username, 'message' => $notification->description];

                    Mail::send('emails.orderConfirmation', $data, function ($message) use ($user) {
                        $message->to($user->email, $user->username)
                            ->subject('New Notification');
                    });


                    return redirect('/buyer/purchased-success');
                } else {
                    return redirect('/buyer/cart-list')->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.seller_cant_ship_product_at_your_delivery_location'));
                }
            }

        } catch (\Exception $e) {

//            dd($e);
            return redirect('/buyer/cart-list')->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.something_went_wrong_please_try_again'));
        }
    }

    // Purchased Success
    public function purchasedSuccess()
    {
        return view('/frontend/buyer/purchased-success');
    }

    // Purchased Failed
    public function purchasedFailed()
    {
        return view('/frontend/buyer/purchased-failed');
    }

    // Order History page
    public function orderHistory()
    {
        $order_histories = Order::orderBy('created_at', 'desc')
            ->where('buyer_id', Auth::user()->getBuyer->id)->paginate(env('PAGINATION_SMALL'));

        return view('frontend/buyer/order-history')->with('order_histories', $order_histories);
    }

    // Order List Details
    public function orderHistoryDetails()
    {
        $data_generate = '';
        try {
            $id = Crypt::decrypt($_GET['order_id']);

            $order = Order::where('id', $id)->where('buyer_id', Auth::user()->getBuyer->id)->first();
            $vat_setting = Setting::where('key', 'vat_percentage')->first();
            $sub_orders = $order->getSubOrders;

            //dd($sub_order);
            $total = 0;
            $sub_total = 0;


            $data_generate .= '<table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="100">' . trans('messages.buyer.order_history.sub_order') . '</th>
                    <th>' . trans('messages.buyer.order_history.price') . '</th>
                    <th>' . trans('messages.buyer.order_history.store_name') . '</th>
                    <th>' . trans('messages.buyer.order_history.seller_name') . '</th>
                    <th>' . trans('messages.buyer.order_history.order_items') . '</th>
                    <th class="text-center"  width="90">' . trans('messages.buyer.order_history.details') . '</th>
                </thead>
                <tbody>';
            $i = 1;
            foreach ($sub_orders as $sub_order) {

                $get_sub_total_price = SubOrder::getSubOrderTotalPrice($sub_order->id);

                $total = $total + $get_sub_total_price;

                $data_generate .= '<tr>';
                $data_generate .= '<td>' . $i . '</td>';
                $data_generate .= '<td>' . env('CURRENCY_SYMBOL') . number_format($get_sub_total_price, 2) . '</td>';
                $data_generate .= '<td>' . $sub_order->getSeller->store_name . '</td>';
                $data_generate .= '<td>' . $sub_order->getSeller->getUser->username . '</td>';
                $data_generate .= '<td>' . $sub_order->getSubOrderItems->count() . ' item';
                if ($sub_order->getSubOrderItems->count() > 1) $data_generate .= 's';
                $data_generate .= '</td>';
                $data_generate .= '<td class="text-center"><a href="javascript:;" data-id="' . $i . '" class="details label label-success">' . trans('messages.buyer.order_history.details') . '</a></td>';
                $data_generate .= '</tr>';

                $order_items = $sub_order->getSubOrderItems;

                $data_generate .= '<tr style="display: none;" id="show_order_' . $i . '">';
                $data_generate .= '<td colspan="6">';
                $data_generate .= '<table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>' . trans('messages.buyer.order_history.image') . '</th>
                        <th>' . trans('messages.buyer.order_history.product') . '</th>
                        <th class="text-right">' . trans('messages.buyer.order_history.price') . '</th>
                        <th class="text-right">' . trans('messages.buyer.order_history.qty') . '</th>
                        <th class="text-right"  width="90">' . trans('messages.buyer.order_history.line_total') . '</th>
                    </thead>
                    <tbody>';

                foreach ($order_items as $order_item) {
                    $media = $order_item->getProduct->getMedia;
                    $data_generate .= '<tr>';
                    $data_generate .= '<td><img ';

                    if (!empty($media[0]->file_in_disk)) {
                        $data_generate .= 'src="' . Image::url(asset('uploads/media/' . $media[0]->file_in_disk), 80, 75, ['crop']) . '"';
                    } else {
                        $data_generate .= 'src="' . Image::url(asset('/image/no-media.jpg'), 80, 75, ['crop']) . '"';
                    }

                    $data_generate .= 'alt=""></td>';
                    $data_generate .= '<td><a href="' . url('/product/details/' . $order_item->getProduct->id) . '" target="_blank">' . $order_item->getProduct->name . '</a></td>';

                    $price = $order_item->product_price;

                    $data_generate .= '<td class="text-right">';
                    if (isset($order_item->discount)) {
                        $price = $order_item->deal_price;
                        $data_generate .= "<span>" . env('CURRENCY_SYMBOL') . number_format($price, 2) . "</span></br>";
                        $data_generate .= "<span style='color:red;font-size:12px; text-decoration: line-through;'>" . env('CURRENCY_SYMBOL') . number_format($order_item->product_price, 2) . "</span></br>";

                        if ($order_item->discount_type == DiscountTypeEnum::FIXED) $data_generate .= '<span  style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '</span>';
                        else if ($order_item->discount_type == DiscountTypeEnum::PERCENTAGE) $data_generate .= '<span style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '%</span>';

                    } else {
                        $data_generate .= env('CURRENCY_SYMBOL') . number_format($order_item->product_price, 2);
                    }
                    $data_generate .= '</td>';

                    $data_generate .= '<td class="text-right">' . $order_item->quantity . '</td>';
                    $data_generate .= '<td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($price * $order_item->quantity, 2) . '</td>';
                    $data_generate .= '</tr>';
                }
                $data_generate .= '<tr>
                        <td colspan="4" class="text-right"><strong>' . trans('messages.buyer.order_history.total') . '</strong></td>
                        <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($get_sub_total_price, 2) . '</td>
                    </tr>';

                $data_generate .= '</tbody></table></td></tr>';

                $i++;
            }

            $total = $order->sub_total_price;
            $paid_amount = $order->sub_total_price - $order->discount + $order->vat_amount + $order->shipping_rate;
            $vat_percentage = ($order->vat_amount / ($total - $order->discount)) * 100;

            $data_generate .= '<tr>
                    <td colspan="5" class="text-right"><strong>' . trans('messages.buyer.order_history.total') . '</strong></td>
                    <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($total, 2) . '</td>
                </tr>';

            $data_generate .= '<tr>
                    <td colspan="5" class="text-right"><strong>+' . trans('messages.vat') . '</strong></td>
                    <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order->vat_amount, 2) . '</td>
                </tr>';

            $data_generate .= '<tr>
                    <td colspan="5" class="text-right"><strong>+' . trans('messages.shipping_charge') . '</strong></td>
                    <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order->shipping_rate, 2) . '</td>
                </tr>';

            if (!empty($order->discount)) {
                $data_generate .= '<tr>
                      <td colspan="5" class="text-right"><strong>-' . trans('messages.discount') . '</strong></td>
                      <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order->discount, 2) . '</td>
                  </tr>';
            }

            $data_generate .= '<tr style="border-top: 2px solid #ddd;">
                    <td colspan="5" class="text-right"><strong>' . trans('messages.paid_amount') . '</strong></td>
                    <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($paid_amount, 2) . '</td>
                </tr>';

            $data_generate .= '</tbody>';

            //dd($data_generate);


            return response()->json(['success' => true, 'data_generate' => $data_generate]);
        } catch (\Exception $e) {
            $data_generate .= 'Something went wrong. Please try again.';
            return response()->json(['success' => false, 'data_generate' => $data_generate]);
        }

    }

    // Ask Questions
    public function buyerQuestion()
    {
        $questions = Question::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return view('/frontend/buyer/question')->with('questions', $questions);
    }

    public function buyerQuestionSave(Request $request)
    {

        $question = new Question();
        $question->title = $request->question;
        $question->user_id = Auth::user()->id;
        $question->save();

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));

    }

    public function buyerQuestionDetails($question_id)
    {
        $question = Question::find($question_id);

        return view('frontend/buyer/question-details')
            ->with('question', $question);
    }

    public function buyerQuestionReplySave(Request $request)
    {
        if (!empty(Auth::user()) && Auth::user()->user_type == UserTypeEnum::USER) {
            $answer = new QuestionAnswer();
            $answer->answer = $request->answer;
            $answer->user_id = Auth::user()->id;
            $answer->question_id = Crypt::decrypt($request->q_skip);
            $answer->save();
        }
        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
    }

    public function buyerQuestionDelete($id)
    {
        if (((int)$id) > 0) {
            $question = Question::find($id);
            if (isset($question) && isset($question->id)) {
                QuestionAnswer::where('question_id', $id)->delete();
                $question->delete();
            }
        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Invalid Deal');

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Deal Deleted Successfully');
    }
}
