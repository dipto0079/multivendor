<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Enum\CouponDiscountTypeEnum;
use App\Http\Controllers\Enum\DealStatusEnum;
use App\Http\Controllers\Enum\DiscountTypeEnum;
use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\ProductTypeEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\SettingsEnum;
use App\Http\Controllers\Enum\ProductStatusEnum;
use App\Http\Controllers\Enum\ShippingTypeEnum;
use App\Model\CartItem;
use App\Model\Country;
use App\Model\City;
use App\Model\Coupon;
use App\Model\Deal;
use App\Model\Media;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\Product;
use App\Model\ProductCategory;
use App\Model\PushNotification;
use App\Model\PushNotificationReceiver;
use App\Model\Question;
use App\Model\QuestionAnswer;
use App\Model\Seller;
use App\Model\SellerPayment;
use App\Model\SubOrder;
use App\Model\Setting;
use App\Model\FeaturedProductSubscription;
use App\Model\Subscription;
use App\Model\Shipping;
use App\Model\ShippingStorePickUp;
use App\Model\ShippingRate;
use App\User;
use App\Model\FavoriteProduct;
use App\Model\FavoriteStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Enum\MessageTypeEnum;
use Cookie;
use Hash;
use Crypt;
use Image;
use Mail;
use DB;
use Carbon\Carbon;

class SellerController extends Controller
{
    public function editProfileSeller()
    {
        $language = \App\UtilityFunction::getLocal();
        $order_name = "name";
        if ($language == "ar") $order_name = "ar_name";
        $countries = Country::join('cities','cities.country_id','=','countries.id')
            ->select('countries.*')
            ->groupby('countries.id')
            ->orderBy('countries.name', 'asc')
            ->get();

        return view('frontend/seller/edit-profile')->with('countries', $countries);
    }



    public function editProfileSellerSave(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (isset($user)) {

            try {

                if ($request->skip == 1) {
                    $email = '';
                    $email_type = '';
                    $email_exist=null;

                    if (!empty($_GET['email'])) $email = $_GET['email'];

                    if (!empty($_GET['email_type'])) $email_type = $_GET['email_type'];

                    if ($email_type == 'business') {
                        $email_exist = Seller::where('business_email', $email)
                            ->where('business_email', '!=', Auth::user()->getSeller->business_email)->exists();
                    } elseif ($email_type == 'personal') {
                        $email_exist = User::where('email', $email)->where('user_type', UserTypeEnum::SELLER)
                            ->where('email', '!=', Auth::user()->email)->exists();
                    }


                    $country = $request->country_id;

                    $cities = City::where('country_id', $country)->orderby('name','asc')->get();

                    $cities_html = '';
                    foreach ($cities as $city) {
                        $cities_html .= '<option value="' . $city->id . '">';
                        if (\App\UtilityFunction::getLocal() == "en") $cities_html .= $city->name;
                        else $cities_html .= $city->ar_name;
                        $cities_html .= '</option>';
                    }
//                    return response()->json(['cities_html' => $cities_html]);

                    return response()->json(['exists' => $email_exist, 'cities_html' => $cities_html]);
                } else {

                    DB::beginTransaction();

                    $user->username = $request->full_name;
                    $user->country_code = $request->country_code;
                    $user->phone = $request->phone;

                    $path = env('USER_PHOTO_PATH');
                    $image = $request->file('profile_image');
                    if (isset($image)) {
                        $fileName = 'user_' . date('Y-m-d-g-i-a') . $image->getClientOriginalName();
                        $image->move($path . '/', $fileName);
                        $user->photo = $fileName;
                    }

                    $seller = $user->getSeller;
                    $seller->company_name = $request->company_name;
                    //$seller->business_email = $request->business_email;
                    $seller->business_address = $request->business_address;
                    $seller->website = $request->business_website;
                    $seller->about_me = $request->about_me;
                    $seller->country = $request->country;
                    $seller->city = $request->city;
                    $seller->state = $request->state;
                    $seller->street = $request->street;
                    $seller->zip = $request->zip;



                    $user->save();
                    $seller->save();
                    DB::commit();

                }

                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . trans('messages.error_message.profile_updated_successfully'));
            } catch (\Exception $e) {
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
            }
        }

        return view('frontend/seller/edit-profile');
    }


    // Order List page
    public function orderList()
    {
        $sub_order_list = SubOrder::join('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->select('sub_orders.*','orders.status as order_status')
            ->where('sub_orders.seller_id', Auth::user()->getSeller->id)
            ->where('sub_orders.status', OrderStatusEnum::PENDING)
//            ->where('orders.status', OrderStatusEnum::ACCEPTED)
            ->get();

        return view('frontend/seller/order-list')->with('sub_order_list', $sub_order_list);
    }

    // Order List Details
    public function orderListDetails()
    {
        $admin_commission_percentage = '';
        $shipping_type='';
        $tax_rate='';

        $sub_order_id = Crypt::decrypt($_GET['order_id']);
        $sub_order = SubOrder::where('id', $sub_order_id)->where('seller_id', Auth::user()->getSeller->id)->first();
        $order = $sub_order->getOrder;
        $order_items = $sub_order->getSubOrderItems;
        $buyer_information = $order->getBuyer;

        $details = \App\Model\SubOrder::getSubOrderSummaryById($sub_order->id);
        $order_total = $details['sub_total'];
        $admin_commission = $details['admin_commission'];

        $admin_commission_percentage = " (".$sub_order->admin_commission."%)";

        if ($sub_order->shipping_type == ShippingTypeEnum::FREE_SHIPPING) {
            $shipping_type = " (Free)";
        } elseif ($sub_order->shipping_type == ShippingTypeEnum::FLAT_RATE) {
            $shipping_type = " (Flat)";
        } elseif ($sub_order->shipping_type == ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY) {
            $shipping_type = " (Store Pickup)";
        } elseif ($sub_order->shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
            $shipping_type = " (Rate by Price)";
        } elseif ($sub_order->shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {
            $shipping_type = " (Rate by Weight)";
        }

        $tax_rate = " (".$sub_order->tax_rate."%)";
        $tax= $sub_order->tax;
        $shipping_cost = $sub_order->shipping_cost;

        $paid_amount = $order_total + $tax + $shipping_cost - $admin_commission;

        $data_generate = '';
        $data_generate .= '

                 <section class="tabs-section">
				    <ul class="nav nav-tabs" role="tablist">
							<li class="nav-item active">
								<a class="nav-link" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
									<span class="nav-link-in">' . trans('messages.seller.menu.order_list') . '</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
									<span class="nav-link-in">' . trans('messages.seller.buyer_and_delivery_details') . '</span>
								</a>
							</li>
						</ul>';

        $data_generate .= '<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1">';

        $data_generate .= '<table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>' . trans('messages.seller.image') . '</th>
                    <th>' . trans('messages.seller.product_name') . '</th>
                    <th class="text-right">' . trans('messages.seller.price') . '</th>
                    <th class="text-right">' . trans('messages.seller.qty') . '</th>
                    <th class="text-right"  width="90">' . trans('messages.seller.line_total') . '</th>
                </thead>
                <tbody>';

        foreach ($order_items as $order_item) {
            $media = $order_item->getProduct->getMedia;

            $data_generate .= '<tr>';

            if (isset($media[0]))
                $data_generate .= '<td><img src="' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 80, 75, ['crop']) . '"></td>';
            else
                $data_generate .= '<td><img src="' . asset('/image/noimage_80x75.jpg') . '"></td>';

            $data_generate .= '<td>' . $order_item->getProduct->name . '</td>';
//            $data_generate .= '<td><a target="_blank" href="' . url('admin/product/seller/' . $order_item->getProduct->getSeller->id . '/product/list') . '">' . $order_item->getProduct->getSeller->store_name . '</a></td>';

            $data_generate .= '<td class="text-right">';

            $price = $order_item->product_price;

            if (isset($order_item->discount)) {
                $price = $order_item->deal_price;
                $data_generate .= "<span>" . env('CURRENCY_SYMBOL') . number_format($price, 2) . "</span></br>";
                $data_generate .= "<span style='color:red;font-size:14px; text-decoration: line-through;'>" . env('CURRENCY_SYMBOL') . number_format($order_item->product_price, 2) . "</span></br>";

                if ($order_item->discount_type == DiscountTypeEnum::FIXED) $data_generate .= '<span  style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '</span>';
                else if ($order_item->discount_type == DiscountTypeEnum::PERCENTAGE) $data_generate .= '<span style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '%</span>';

            } else {
                $data_generate .= env('CURRENCY_SYMBOL') . number_format($price, 2);
            }
            $data_generate .= '</td>';

            $data_generate .= '<td class="text-center">' . $order_item->quantity . '</td>';

            $data_generate .= '<td class="text-right">';
            $data_generate .= env('CURRENCY_SYMBOL') . number_format($price * $order_item->quantity, 2);
            $data_generate .= '</td>';

            $data_generate .= '</tr>';
        }

        $data_generate .= '<tr>
                <td colspan="4" class="text-right"><strong>' . trans('messages.seller.sub_total') . '</strong></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order_total, 2) . '</td>
            </tr>';

        $data_generate .= '<tr>
                <td colspan="4" class="text-right"><span style="background-color: #fdad2a;" class="label label-custom label-pill label-warning">+ ' . trans('messages.seller.tax') . $tax_rate . '</span></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($sub_order->tax, 2) . '</td>
            </tr>';
        $data_generate .= '<tr>
                <td colspan="4" class="text-right"><span style="background-color: #fdad2a;" class="label label-custom label-pill label-warning">+ ' . trans('messages.seller.shipping_rate')  . $shipping_type . '</td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($sub_order->shipping_cost, 2) . '</td>
            </tr>';
        $data_generate .= '<tr>
                <td colspan="4" class="text-right"><span style="background-color: #46c35f;" class="label label-custom label-pill label-success">- ' . trans('messages.seller.admin_commission')  . $admin_commission_percentage. '</span></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($admin_commission, 2) . '</td>
            </tr>';
        $data_generate .= '<tr>
                <td colspan="4" class="text-right"><strong>' . trans('messages.seller.total') . '</strong></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($paid_amount, 2) . '</td>
            </tr>';


        $data_generate .= '</tbody></table>';

        $data_generate .= '</div>';

        $data_generate .= '<div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2">';

        $data_generate .= '<div class="media">
              <div class="media-left">
                <a href="#">
                  <img class="media-object" width="100" height="100" ';
        if (!empty($buyer_information->getUser->photo) && stripos($buyer_information->getUser->photo, 'https://') !== false) {
            $data_generate .= 'src="' . $buyer_information->getUser->photo . '"';
        } elseif (!empty($buyer_information->getUser->photo)) {
            $data_generate .= 'src="' . Image::url(asset(env('USER_PHOTO_PATH') . $buyer_information->getUser->photo), 200, 200, ['crop']) . '"';
        } else {
            $data_generate .= 'src="' . asset('image/default_author.png') . '"';
        }
        $data_generate .= 'alt="">
                </a>
              </div>
              <div class="media-body">
                <h4 class="media-heading">' . $buyer_information->getUser->username . '</h4>
                <span><strong>' . trans('messages.seller.edit_profile.email') . ': </strong> ' . $buyer_information->getUser->email . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.phone') . ': </strong> ' . $buyer_information->getUser->phone . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.street') . ': </strong> ' . $buyer_information->street . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.city') . ': </strong> ';
        if (isset($buyer_information->getDistrict)) $data_generate .= $buyer_information->getDistrict->name;
        $data_generate .= '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.zip') . ': </strong> ' . $buyer_information->zip . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.state') . ': </strong> ' . $buyer_information->state . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.country') . ': </strong> ';
        if (!empty($buyer_information->getCountryName))
            $data_generate .= $buyer_information->getCountryName->name;
        $data_generate .= '</span><div class="clearfix"></div></div><br><hr>';
        $data_generate .= '<h4 class="media-heading">' . trans('messages.seller.Delivery_details') . '</h4>
                <span><strong>' . trans('messages.seller.edit_profile.street') . ': </strong> ' . $order->delivery_street . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.city') . ': </strong> ';
        if (isset($order->getOrderCity)) $data_generate .= $order->getOrderCity->name;
        $data_generate .= '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.state') . ': </strong> ' . $order->delivery_state . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.zip') . ': </strong> ' . $order->delivery_zip . '</span><br>
                <span><strong>' . trans('messages.seller.edit_profile.country') . ': </strong> ' . $order->getCountryName->name . '</span>
              </div>
            </div>';
        $data_generate .= '</div></div></section>';


        return response()->json(['success' => true, 'data_generate' => $data_generate]);






//        $data_generate = '';
        try {
//            $id = Crypt::decrypt($_GET['order_id']);

//            $a = new OrderController();
//            $data_generate = $a->orderDetails($id, Auth::user()->getSeller->id);





            $sub_order = SubOrder::where('id', $id)->where('seller_id', Auth::user()->getSeller->id)->first();

            $order = $sub_order->getOrder;
            $order_items = OrderItem::where('sub_order_id', $sub_order->id)->get();
            $buyer_information = $order->getBuyer;



            $sub_total = 0;

            $data_generate .= '

                 <section class="tabs-section">
				    <ul class="nav nav-tabs" role="tablist">
							<li class="nav-item active">
								<a class="nav-link" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
									<span class="nav-link-in">' . trans('messages.seller.menu.order_list') . '</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
									<span class="nav-link-in">' . trans('messages.seller.buyer_and_delivery_details') . '</span>
								</a>
							</li>
						</ul>';

            $data_generate .= '<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1">';

            $data_generate .= '<table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>' . trans('messages.seller.image') . '</th>
                    <th>' . trans('messages.seller.product_name') . '</th>
                    <th class="text-right">' . trans('messages.seller.price') . '</th>
                    <th class="text-right">' . trans('messages.seller.qty') . '</th>
                    <th class="text-right"  width="90">' . trans('messages.seller.line_total') . '</th>
                </thead>
                <tbody>';

            foreach ($order_items as $order_item) {
                $sub_total = $sub_total + ($order_item->deal_price * $order_item->quantity);


                $media = $order_item->getProduct->getMedia;
                $data_generate .= '<tr>';
                $data_generate .= '<td><img ';
                if (isset($media[0])) {
                    $data_generate .= 'src="' . Image::url(asset('uploads/media/' . $media[0]->file_in_disk), 80, 75, ['crop']) . '"';
                } else {
                    $data_generate .= 'src="' . Image::url(asset('/image/no-media.jpg'), 80, 75, ['crop']) . '"';
                }
                $data_generate .= 'alt="Product-1"></td>';
                $data_generate .= '<td><a target="_blank" href="' . url('/product/details/' . $order_item->getProduct->id) . '">' . $order_item->getProduct->name . '</a></td>';

                $data_generate .= '<td class="text-right">';
                if (isset($order_item->discount)) {
                    $data_generate .= "<span>" . env('CURRENCY_SYMBOL') . number_format($order_item->deal_price, 2) . "</span></br>";
                    $data_generate .= "<span style='color:red;font-size:14px; text-decoration: line-through;'>" . env('CURRENCY_SYMBOL') . number_format($order_item->product_price, 2) . "</span></br>";

                    if ($order_item->discount_type == DiscountTypeEnum::FIXED) $data_generate .= '<span  style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '</span>';
                    else if ($order_item->discount_type == DiscountTypeEnum::PERCENTAGE) $data_generate .= '<span style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '%</span>';

                } else {
                    $data_generate .= env('CURRENCY_SYMBOL') . number_format($order_item->deal_price, 2);
                }
                $data_generate .= '</td>';

                $data_generate .= '<td class="text-right">' . $order_item->quantity . '</td>';
                $data_generate .= '<td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order_item->deal_price * $order_item->quantity, 2) . '</td>';
                $data_generate .= '</tr>';
            }

            $shipping_type = "";
            if ($sub_order->shipping_type == ShippingTypeEnum::FREE_SHIPPING) {
                $shipping_type = "Free";
            } elseif ($sub_order->shipping_type == ShippingTypeEnum::FLAT_RATE) {
                $shipping_type = "Flat";
            } elseif ($sub_order->shipping_type == ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY) {
                $shipping_type = "Store Pickup";
            } elseif ($sub_order->shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
                $shipping_type = "Rate by Price";
            } elseif ($sub_order->shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {
                $shipping_type = "Rate by Weight";
            }

            $admin_commission_amount = ($sub_total * $sub_order->admin_commission) / 100;
            $total = $sub_total + $sub_order->tax + $sub_order->shipping_cost-$admin_commission_amount;

            $data_generate .= '<tr>
                <td colspan="4" class="text-right"><strong>' . trans('messages.seller.sub_total') . '</strong></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($sub_total, 2) . '</td>
            </tr>';

            $data_generate .= '<tr>
                <td colspan="4" class="text-right"><span class="label label-custom label-pill label-warning">+ ' . trans('messages.seller.tax') . ' (' . $sub_order->tax_rate . '%)</span></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($sub_order->tax, 2) . '</td>
            </tr>';
            $data_generate .= '<tr>
                <td colspan="4" class="text-right"><span class="label label-custom label-pill label-warning">+ ' . trans('messages.seller.shipping_rate') . ' (' . $shipping_type . ')</td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($sub_order->shipping_cost, 2) . '</td>
            </tr>';
            $data_generate .= '<tr>
                <td colspan="4" class="text-right"><span class="label label-custom label-pill label-success">- ' . trans('messages.seller.admin_commission') . ' (' . $sub_order->admin_commission . '%)</span></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($admin_commission_amount, 2) . '</td>
            </tr>';
            $data_generate .= '<tr>
                <td colspan="4" class="text-right"><strong>' . trans('messages.seller.total') . '</strong></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($total, 2) . '</td>
            </tr>';


            $data_generate .= '</tbody></table>';

            $data_generate .= '</div>';

            $data_generate .= '<div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2">';

            $data_generate .= '<div class="media">
              <div class="media-left">
                <a href="#">
                  <img class="media-object" width="100" height="100" ';
            if (!empty($buyer_information->getUser->photo) && stripos($buyer_information->getUser->photo, 'https://') !== false) {
                $data_generate .= 'src="' . $buyer_information->getUser->photo . '"';
            } elseif (!empty($buyer_information->getUser->photo)) {
                $data_generate .= 'src="' . Image::url(asset(env('USER_PHOTO_PATH') . $buyer_information->getUser->photo), 200, 200, ['crop']) . '"';
            } else {
                $data_generate .= 'src="' . asset('image/default_author.png') . '"';
            }
            $data_generate .= 'alt="">
                </a>
              </div>
              <div class="media-body">
                <h4 class="media-heading">' . $buyer_information->getUser->username . '</h4>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.email') . ': </strong> ' . $buyer_information->getUser->email . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.phone') . ': </strong> ' . $buyer_information->getUser->phone . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.street') . ': </strong> ' . $buyer_information->street . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.city') . ': </strong> ';
            if (isset($buyer_information->getDistrict)) $data_generate .= $buyer_information->getDistrict->name;
            $data_generate .= '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.zip') . ': </strong> ' . $buyer_information->zip . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.state') . ': </strong> ' . $buyer_information->state . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.country') . ': </strong> ';
            if (!empty($buyer_information->getCountryName))
                $data_generate .= $buyer_information->getCountryName->name;
            $data_generate .= '</span><div class="clearfix"></div></div><br><hr>';
            $data_generate .= '<h4 class="media-heading">' . trans('messages.seller.Delivery_details') . '</h4>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.street') . ': </strong> ' . $order->delivery_street . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.city') . ': </strong> ';
            if (isset($order->getOrderCity)) $data_generate .= $order->getOrderCity->name;
            $data_generate .= '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.state') . ': </strong> ' . $order->delivery_state . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.zip') . ': </strong> ' . $order->delivery_zip . '</span><br>
                <span><strong style="float: left;">' . trans('messages.seller.edit_profile.country') . ': </strong> ' . $order->getCountryName->name . '</span>
              </div>
            </div>';
            $data_generate .= '</div></div></section>';


            return response()->json(['success' => true, 'data_generate' => $data_generate]);
        } catch (\Exception $e) {

            // dd($e);
            $data_generate .= 'Something went wrong. Please try again.';
            return response()->json(['success' => false, 'data_generate' => $data_generate]);
        }

    }

    // Order Status Change
    public function orderStatusChange(Request $request)
    {
        try {

            $sub_order = SubOrder::find(Crypt::decrypt($request->order_id));
            $sub_order->status = Crypt::decrypt($request->change_status);
            $sub_order->save();

            $buyser_info = $sub_order->getOrder->getBuyer->getUser;
            $buyer_email = $buyser_info->email;
            $name = $buyser_info->username;

            if ($sub_order->status == OrderStatusEnum::REJECTED) {
                $order_items = $sub_order->getSubOrderItems;

                foreach ($order_items as $order_item) {
                    $storedProduct = Product::find($order_item->product_id);
                    $storedProduct->quantity = $storedProduct->quantity + $order_item->quantity; //restore the quantity
                    $storedProduct->save();
                }
            }

            if (!empty($buyer_email)) {
                $data = [
                    'name' => $name,
                    'order_number' => $sub_order->order_id,
                    'status' => $sub_order->status,
                ];

                Mail::send('emails.orderStatusEmail', $data, function ($message) use ($buyer_email) {
                    $message->to($buyer_email)->subject('Order Status');
                });
            }

            return redirect()->back()->with('message', 'Order status changed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_message', 'Something went wrong.');
        }
    }

    // Order History
    public function orderHistory()
    {
        $sub_order_list = SubOrder::join('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->select('sub_orders.*', 'orders.created_at as order_date')
            ->where('sub_orders.seller_id', Auth::user()->getSeller->id)
            ->where('sub_orders.status', '!=', OrderStatusEnum::PENDING)
            ->orderBy('orders.created_at', 'desc')
            ->paginate(env('PAGINATION_SMALL'));

        return view('frontend/seller/order-history')->with('sub_order_list', $sub_order_list);
    }

    // Notification List page
    public function notification()
    {
        $notifications = PushNotification::join('push_notification_receivers', 'push_notifications.id', '=', 'push_notification_receivers.push_notification_id')
            ->where('push_notification_receivers.receiver_type', PushNotificationRepeatTypeEnum::SELLER)
            ->where('push_notification_receivers.receiver_id', Auth::user()->id)->get();

        return view('frontend/seller/notification')
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
            PushNotificationReceiver::where('receiver_id', Auth::user()->id)->delete();
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

    // Product List
    public function productList()
    {
        $pagination = env('PAGINATION_SMALL');
        $products = '';

        $membership_plan = Subscription::where('seller_id', Auth::user()->getSeller->id)->exists();
        $disabled = false;
        if (Auth::user()->getSeller->business_type == ProductTypeEnum::SERVICE && $membership_plan == false) {
            $disabled = true;
        }

        if ($disabled == false) {
            $products = Product::orderBy('created_at', 'desc')->where('status', ProductStatusEnum::SHOWN)
                ->where('seller_id', Auth::user()->getSeller->id)->paginate($pagination);
        }

        return view('frontend/seller/products')->with('products', $products);
    }


    // Product List
    public function serviceList()
    {
        $pagination = env('PAGINATION_SMALL');
        $products = '';

        $membership_plan = Subscription::where('seller_id', Auth::user()->getSeller->id)->exists();
        $disabled = false;
        if (Auth::user()->getSeller->business_type == ProductTypeEnum::SERVICE && $membership_plan == false) {
            $disabled = true;
        }

        if ($disabled == false) {
            $products = Product::orderBy('created_at', 'desc')->where('status', ProductStatusEnum::SHOWN)
                ->where('seller_id', Auth::user()->getSeller->id)->paginate($pagination);
        }

        return view('frontend/seller/services')->with('products', $products);
    }

    // Product Add
    public function productSave(Request $request)
    {
        try {
            $product_selected_categories = [];
            $product = new Product();
            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                $seller = Seller::find(Crypt::decrypt($_GET['seller_id']));

                $categories = ProductCategory::OrderBy('name', 'asc')
                    ->where('parent_category_id', $seller->category_id)
                    ->where('product_category_type_id', Auth::user()->getSeller->business_type)
                    ->get();

                if (isset($_GET['edit_id'])) {
                    $product = Product::where('id', $_GET['edit_id'])->first();
                }

                $now = Carbon::now()->format('Y-m-d 00:00:00');
                $membership_plan = Subscription::where('seller_id', Auth::user()->getSeller->id)->where('to_date', '>=', $now)->exists();

                $shipping_type = Shipping::join('sellers', 'sellers.id', '=', 'shippings.seller_id')
                    ->where('seller_id', Auth::user()->getSeller->id)
                    ->where('business_type', ProductTypeEnum::PRODUCT)
                    ->exists();


                $product_type_disabled = true;
                $shipping_type_disabled = true;


                if (Auth::user()->getSeller->business_type == ProductTypeEnum::SERVICE && $membership_plan == true) {
                    $product_type_disabled = false;
                }

                if (Auth::user()->getSeller->business_type == ProductTypeEnum::SERVICE && $shipping_type == false) {
                    $shipping_type_disabled = false;
                }

                if (Auth::user()->getSeller->business_type == ProductTypeEnum::PRODUCT && $membership_plan == false) {
                    $product_type_disabled = false;
                }

                if (Auth::user()->getSeller->business_type == ProductTypeEnum::PRODUCT && $shipping_type == true) {
                    $shipping_type_disabled = false;
                }

                $data_generate = '';

                if ($product_type_disabled == false) {



                    if ($shipping_type_disabled == false) {
                        $data_generate .= '<div class="form-group"><div class="row" style="padding: 10px">';

                        $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                            <label class="required">' . trans('messages.seller.product.product_title') . '(English)</label>
                                            <input required="" type="text" class="form-control" name="name" value="' . $product->name . '">';
                        $data_generate .= '</div></div>';

                        $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                            <label class="">' . trans('messages.seller.product.product_title') . '(Arabic)</label>
                                            <input required="" type="text" class="form-control" name="ar_name" value="' . $product->ar_name . '">';
                        $data_generate .= '</div></div><div class="clearfix"></div>';

                        $data_generate .= '<div class="col-sm-4"><div class="form-group">
                                            <label class="">' . trans('messages.seller.product.price') . '</label>
                                            <input required="" type="number" class="form-control numaric" step="any" min="0" name="price" value="' . $product->price . '">';
                        $data_generate .= '</div></div>';

                        if (Auth::user()->getSeller->business_type == ProductTypeEnum::PRODUCT) {
                            $data_generate .= '<div class="col-sm-4"><div class="form-group">
                                            <label class="">' . trans('messages.seller.product.quantity') . '</label>
                                            <input required="" type="text" class="form-control numaric quantity_input" name="quantity" value="' . $product->quantity . '">';
                            $data_generate .= '</div></div>';

                            $data_generate .= '<div class="col-sm-4"><div class="form-group">
                                            <label class="">' . trans('messages.seller.product.unit') . '</label>
                                            <input type="number" class="form-control numaric" name="unit" value="' . $product->unit . '">';
                            $data_generate .= '</div></div>';
                        }


                        $data_generate .= '<div class="col-sm-12" id="product_type"><div class="form-group">
                                    <label class="">' . trans('messages.seller.product.product_category') . '</label>
                                    <select name="product_type_id" id="product_type_id" class="form-control category_type" required>';
                        $data_generate .= '<option value="">' . trans('messages.seller.product.select_category') . '</option>';
                        foreach ($categories as $category) {

                            if ($category->product_category_type_id == ProductTypeEnum::PRODUCT) {
                                $data_generate .= '<optgroup label="';
                                if (\App\UtilityFunction::getLocal() == "en") $data_generate .= $category->name; else  $data_generate .= $category->ar_name;
                                $data_generate .= '">';
                            } elseif ($category->product_category_type_id == ProductTypeEnum::SERVICE) {
                                $data_generate .= '<option value="' . $category->id . '"';
                                if ($product->category_id == $category->id) $data_generate .= ' selected ';
                                $data_generate .= '>';
                                if (\App\UtilityFunction::getLocal() == "en") $data_generate .= $category->name; else  $data_generate .= $category->ar_name;
                            }

                            $second_categories = $category->getSubCategory;
                            foreach ($second_categories as $second_category) {
                                $data_generate .= '<option value="' . $second_category->id . '"';
                                if ($product->category_id == $second_category->id) $data_generate .= ' selected ';
                                $data_generate .= '>';
                                if (\App\UtilityFunction::getLocal() == "en") $data_generate .= $second_category->name; else  $data_generate .= $second_category->ar_name;
                                $data_generate .= '</option>';
                            }

                            if ($category->product_category_type_id == ProductTypeEnum::PRODUCT) {
                                $data_generate .= '</optgroup>';
                            } elseif ($category->product_category_type_id == ProductTypeEnum::SERVICE) {
                                $data_generate .= '</option>';
                            }

                        }

                        $data_generate .= '</select></div></div>';


                        $data_generate .= '<div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="">' . trans('messages.seller.product.description') . '(English)</label>
                                            <textarea name="description" maxlength="512" class="form-control" rows="6">' . $product->description . '</textarea>
                                            <p class="charsRemaining"></p>
                                        </div>
                                    </div>';
                        $data_generate .= '<div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="">' . trans('messages.seller.product.description') . '(Arabic)</label>
                                            <textarea name="ar_description" maxlength="512" class="form-control" rows="6">' . $product->ar_description . '</textarea>
                                            <p class="charsRemaining"></p>
                                        </div>
                                    </div>';


                        $data_generate .= '<div class="clearfix"></div>';


                        if (isset($_GET['add_id'])) $data_generate .= '<input type="hidden" name="seller_id" value="' . $_GET['seller_id'] . '">';
                        if (!isset($_GET['add_id']))
                            $data_generate .= '<input type="hidden" id="edit_id" class="form-control" name="edit_id" value="' . Crypt::encrypt($product->id) . '">';
                    } else {
                        $data_generate .= '<h3 class="text-danger text-center" style="margin: 70px 0;">' . trans('messages.error_message.shipping_information_not_set') . '</h3>';
                    }

                } else {
                    $data_generate .= '<h3 class="text-danger text-center" style="margin: 70px 0;">' . trans('messages.error_message.you_have_no_membership_plan') . '</h3>';
                }

                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $product_categories = [];
                $encrypted_id = $request->input('edit_id');
                if (!empty($request->seller_id)) $seller_id = Crypt::decrypt($request->seller_id);
                if (isset($encrypted_id)) {
                    try {
                        $product = Product::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }
//                dd($request);
                if (isset($product)) {
                    DB::beginTransaction();

                    $product->name = $request->name;
                    $product->ar_name = $request->ar_name;
                    $product->price = $request->price;
                    if (Auth::user()->getSeller->business_type == ProductTypeEnum::PRODUCT) {
                        $product->quantity = $request->quantity;
                        $product->unit = $request->unit;
                    }
                    $product->category_id = $request->product_type_id;
                    if (!empty($request->seller_id))
                        $product->seller_id = $seller_id;
                    $product->description = $request->description;
                    $product->ar_description = $request->ar_description;
                    $product->save();

                    DB::commit();
                }

                if (isset($encrypted_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }

    }

    public function productMediaSave(Request $request)
    {
        try {
            $product_id = $request->product_id;
            $product_media_image = [];
            if (isset($_GET['edit_id'])) {
                $product_media_image = Media::where('product_id', $_GET['product_id'])->get();
                $data_generate = '';
                if (isset($product_media_image[0])) {
                    $data_generate .= '<input type="hidden" name="skip" id="skip" value="1">';
                    foreach ($product_media_image as $product_image) {
                        $data_generate .= '<div class="gallery-col media_' . $product_image->id . '">
                                        <article class="gallery-item">
                                            <img class="gallery-picture" src="' . asset(env('MEDIA_PHOTO_PATH') . $product_image->file_in_disk) . '" alt="" height="158">

                                           <a data-id="' . $product_image->id . '" href="javascript:;"
                                                            class="delete_image remove remove_from_wishlist">×</a>


                                          <p class="gallery-item-title">' . $product_image->title . '</p>
                                        </article>
                                        <input class="form-control" type="text" style="margin-top: 5px;" name="image_title[]" value="' . $product_image->title . '">
                                        <input type="hidden" value="' . $product_image->id . '">
                                        <input type="hidden" name="media_id[]" value="' . $product_image->id . '">
                                    </div><!--.gallery-col-->';
                    }

                } else {
                    $data_generate .= '<span class="text-center text-danger">' . trans('messages.seller.product.no_media_file_exist') . '</span><input type="hidden" name="skip" id="skip" value="0">';
                }
                return response()->json(['success' => true, 'data_generate' => $data_generate]);
            }

            if ($request->skip == 0) {
                // File upload
                $images = $request->file('photo');
                $path = 'uploads/media';

                if (!file_exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                if (isset($images)) {
                    foreach ($images as $image) {
                        $media = new Media();
                        $media->title = $image->getClientOriginalName();
                        $media->file_name = $image->getClientOriginalName();
                        if (isset($media->file_in_disk) && $media->file_in_disk != "" && file_exists($path . '/' . $media->file_in_disk)) {
                            unlink($path . '/' . $media->file_in_disk);
                        }
                        $fileName = time() . $image->getClientOriginalName();
                        $image->move($path . '/', $fileName);
                        $media->file_in_disk = $fileName;
                        $media->product_id = $product_id;
                        $media->save();
                    }
                    $data_generate = '';
                    if (isset($_GET['add_id'])) {
                        $product_media_image = Media::where('product_id', $product_id)->get();

                        if (isset($product_media_image[0])) {
                            $data_generate .= '<input type="hidden" name="skip" id="skip" value="1">';
                            foreach ($product_media_image as $product_image) {
                                $data_generate .= '<div class="gallery-col media_' . $product_image->id . '">
                                        <article class="gallery-item">
                                            <img class="gallery-picture" src="' . asset(env('MEDIA_PHOTO_PATH') . $product_image->file_in_disk) . '" alt="" height="158">

                                           <a data-id="' . $product_image->id . '" type="button"
                                                            class="delete_image remove remove_from_wishlist">×</a>


                                          <p class="gallery-item-title">' . $product_image->title . '</p>
                                        </article>
                                        <input class="form-control" type="text" style="margin-top: 5px;" name="image_title[]" value="' . $product_image->title . '">
                                        <input type="hidden" value="' . $product_image->id . '">
                                        <input type="hidden" name="media_id[]" value="' . $product_image->id . '">
                                    </div><!--.gallery-col-->';
                            }
                        }
                    }
                    return response()->json(['success' => true, 'data_generate' => $data_generate, 'product_id' => $product_id, 'media_count' => $product_media_image->count()]);
                }
            } else {
                if (!empty($request->media_id)) {
                    for ($i = 0; $i < count($request->media_id); $i++) {
                        $media_image = Media::find($request->media_id[$i]);
                        $media_image->title = $request->image_title[$i];
                        $media_image->save();
                    }
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                } else {
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'No Media Uploaded.');
                }
            }
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'No Media Uploaded.');
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'message', MessageTypeEnum::ERROR . $e->getMessage()]);
        }

    }

    public function productMediaDelete()
    {
        try {
            if (isset($_GET['media_image_id'])) $id = $_GET['media_image_id'];
            if (((int)$id) > 0) {

                $media = Media::find($id);
                $product_id = $media->product_id;
                $path = 'uploads/media';
                if (isset($media->file_in_disk) && $media->file_in_disk != "" && file_exists($path . '/' . $media->file_in_disk)) {
                    unlink($path . '/' . $media->file_in_disk);
                }
                $media->delete();

                $product_media_image = Media::where('product_id', $product_id)->get();
            } else
                return response()->json(['success' => true, 'message' => 'Invalid Media']);

            return response()->json(['success' => true, 'message' => env('MSG_DELETED_SUCCESSFULLY'), 'product_id' => $product_id, 'media_count' => $product_media_image->count()]);
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'message', MessageTypeEnum::ERROR . $e->getMessage()]);
        }
    }

    public function productQuantity(Request $request)
    {
        try {
            $product = Product::find($request->product);
            $product->quantity = $request->quantity;
            $product->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // productDealList
    public function productDealList($id)
    {
        $product = Product::find($id);
        $deals = $product->getProductDeals()
            ->where('delete_status', ProductStatusEnum::SHOWN)
            ->where('status', DealStatusEnum::APPROVED)->get();

        return view('/frontend/seller/product-deals')
            ->with('deals', $deals)
            ->with('product', $product);
    }

    public function productDealSave(Request $request)
    {
        try {
            $deal = new Deal();
            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {

                if (isset($_GET['edit_id']))
                    $deal = Deal::where('id', Crypt::decrypt($_GET['edit_id']))->first();

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';


                $data_generate .= '<div class="col-sm-12"><div class="form-group">
                                    <label class="required">' . trans('messages.seller.product.deal_page.deal_title') . '</label>
                                    <input autocomplete="off" required="" type="text" class="form-control" name="deal_title" value="' . $deal->title . '">';
                $data_generate .= '</div></div>';


                $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                    <label class="required">' . trans('messages.seller.product.deal_page.discount') . '</label>
                                    <input  autocomplete="off"  required="" type="number" class="form-control" name="discount" value="' . $deal->discount . '">';
                $data_generate .= '</div></div>';


                $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                    <label class="">' . trans('messages.seller.product.deal_page.discount_type') . '</label>
                                    <select required="" name="discount_type" class="form-control" >';
                $data_generate .= '<option';
                if ($deal->discount_type == DiscountTypeEnum::FIXED) $data_generate .= ' selected ';
                $data_generate .= ' value="' . DiscountTypeEnum::FIXED . '">' . trans('messages.seller.product.deal_page.fixed') . '</option>';
                $data_generate .= '<option';
                if ($deal->discount_type == DiscountTypeEnum::PERCENTAGE) $data_generate .= ' selected ';
                $data_generate .= ' value="' . DiscountTypeEnum::PERCENTAGE . '">' . trans('messages.seller.product.deal_page.percentage') . '</option>';
                $data_generate .= '</select></div></div><div class="clearfix"></div>';

                $data_generate .= '<div class="col-md-6">
                        <div class="form-group">
                        <label for="">' . trans('messages.seller.product.deal_page.valid_for') . '</label>
							<div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control" name="start"';
                if (!empty($deal->from_date)) {
                    $data_generate .= 'value="' . date('d-m-Y', strtotime($deal->from_date)) . '"';
                } else {
                    $data_generate .= 'value="' . date('d-m-Y') . '"';
                }
                $data_generate .= ';/>
                                <span class="input-group-addon">to</span>
                                <input type="text" class="input-sm form-control" name="end"';
                if (!empty($deal->from_date)) {
                    $data_generate .= 'value="' . date('d-m-Y', strtotime($deal->to_date)) . '"';
                } else {
                    $data_generate .= 'value="' . date('d-m-Y', strtotime("+1 month")) . '"';
                }
                $data_generate .= ';/>
                            </div></div>
						</div>';

                $data_generate .= '<div class="col-sm-12">
                                <div class="form-group">
                                    <label class="">' . trans('messages.seller.product.deal_page.deal_details') . '</label>
                                    <textarea name="description" class="form-control" rows="6">' . $deal->description . '</textarea>
                                </div>
                            </div>';

                if (isset($_GET['add_id'])) $data_generate .= '<input type="hidden" name="product_id" value="' . $_GET['product_id'] . '">';
                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" id="edit_id" class="form-control" name="edit_id" value="' . Crypt::encrypt($deal->id) . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $encrypted_id = $request->input('edit_id');

                if (isset($encrypted_id)) {
                    try {
                        $deal = Deal::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                $from_date = '';
                $to_date = '';
                if (!empty($request->start)) $from_date = date('Y-m-d H:i:s', strtotime($request->start . ' 00:00:00'));
                if (!empty($request->end)) $to_date = date('Y-m-d H:i:s', strtotime($request->end . ' 23:59:59'));

                if (Deal::isSameDealExist($deal, $request->product_id, $from_date, $to_date) <= 0) {
                    if (isset($deal)) {
                        $deal->title = $request->deal_title;
                        $deal->description = $request->description;
                        if (!isset($encrypted_id)) $deal->product_id = Crypt::decrypt($request->product_id);
                        $deal->discount = $request->discount;
                        $deal->discount_type = $request->discount_type;

                        $deal->from_date = $from_date;
                        $deal->to_date = $to_date;
                        $deal->save();
                    }
                } else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . "Another deal is running of this product within this time period.");


                if (isset($encrypted_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function productDelete($id)
    {
        try {
            if (((int)$id) > 0) {

                $product = Product::find($id);
                // $media = $product->getMedia;
                // $deals = $product->getProductDeals;
                // $wishList = $product->getWishList;
                // $path = env('MEDIA_PHOTO_PATH');
                //
                // //dd($wishList);
                //
                // DB::beginTransaction();
                // if(!empty($media[0])){
                //
                //   foreach ($media as $m) {
                //     if (isset($m->file_in_disk) && $m->file_in_disk != "" && file_exists($path . '/' . $m->file_in_disk)) {
                //         unlink($path . '/' . $m->file_in_disk);
                //     }
                //     $m->delete();
                //   }
                // }
                //
                // if(!empty($deals[0])) $product->getProductDeals()->delete();
                // if(!empty($wishList[0])) $product->getWishList()->delete();

                $product->status = ProductStatusEnum::ARCHIVE;
                $product->save();

                DB::commit();
            } else
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // Ask Questions
    public function sellerQuestion()
    {
        $questions = Question::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return view('/frontend/seller/question')->with('questions', $questions);
    }

    public function sellerQuestionSave(Request $request)
    {

        $question = new Question();
        $question->title = $request->question;
        $question->user_id = Auth::user()->id;
        $question->save();

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));

    }

    public function sellerQuestionDetails($question_id)
    {
        $question = Question::find($question_id);

        return view('frontend/seller/question-details')
            ->with('question', $question);
    }

    public function sellerQuestionReplySave(Request $request)
    {
        if (!empty(Auth::user()) && Auth::user()->user_type == UserTypeEnum::SELLER) {
            $answer = new QuestionAnswer();
            $answer->answer = $request->answer;
            $answer->user_id = Auth::user()->id;
            $answer->question_id = Crypt::decrypt($request->q_skip);
            $answer->save();
        }
        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
    }

    public function sellerQuestionDelete($id)
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

    // Seller Payment Claim
    public function sellerPaymentClaim(Request $request)
    {
        try {
            $sub_order = SubOrder::find(Crypt::decrypt($request->order_id));
            $sub_order->status = OrderStatusEnum::CLAIMED;
            $sub_order->save();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Payment Claimed Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Something went wrong.');
        }
    }


    // Seller Payments
    public function sellerMyEarnings()
    {
        $payments = SellerPayment::where('seller_id', Auth::user()->getSeller->id)->orderBy('created_at', 'desc')->get();

        return view('/frontend/seller/payment')->with('payments', $payments);
    }


    public function sellerShippingAndTex()
    {
        $shipping_texs = Shipping::where('seller_id', Auth::user()->getSeller->id)->orderBy('country_id')->get();

        return view('/frontend/seller/shipping-and-tax')->with('shipping_texs', $shipping_texs);
    }

    public function sellerAddShippingAndTex()
    {
        $all_countries = Country::join('cities', 'countries.id', '=', 'cities.country_id')
            ->select('countries.*')
            ->orderBy('name')
            ->where('cities.shipping_status', 1)
            ->groupBy('countries.id')->get();

        $countries=[];

        $shippings = Shipping::where('seller_id',Auth::user()->getSeller->id)->get();

        foreach ($all_countries as $country)
        {
            $found = 0;
            foreach ($shippings as $ship)
            {
                if($ship->country_id == $country->id && $ship->city_ids =="")
                {
                    $found = 1;
                    break;
                }

            }
            if(!$found)
                array_push($countries, $country);
        }
        return view('/frontend/seller/add-shipping-and-tax')->with('countries', $countries);
    }

    // Seller Shipping And Tex Save
    public function sellerShippingAndTexSave(Request $request)
    {
        try {
            if ($request->skip == 1) {
                $cities_html = Seller::createEligibleDeliveryCityList($request->country_id);
                return response()->json(['cities_html' => $cities_html]);

            } else {

                $shipping_check = false;
                if ($request->country_id != 'all') {
                    $cities = $request->city_id;
                    $shipping_check = Seller::isCitiesShippingEligible($request->country_id, $cities);
                }

                $shipping_type = $request->shipping_type;

                if ($shipping_check) {
                    DB::beginTransaction();
                    $shipping = new Shipping();
                    $shipping->country_id = $request->country_id;

                    $first = 0;
                    $city_ids = '';
                    for ($i = 0; $i < count($cities); $i++) {
                        if ($first == 0) {
                            $city_ids = $cities[$i];
                            $first = 1;
                        } else {
                            $city_ids .= ',' . $cities[$i];
                        }
                    }
                    $shipping->city_ids = $city_ids;
                    $shipping->shipping_type = $shipping_type;
                    $shipping->seller_id = Auth::user()->getSeller->id;
                    $shipping->tax = ($request->is_tax == 1) ? $request->tax_percentage : 0;
                    if ($shipping_type == ShippingTypeEnum::FLAT_RATE) $shipping->rate = $request->flat_rate_rate;
                    if ($shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) $shipping->delivery_time = $request->weight_est_delivery_time;
                    if ($shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) $shipping->delivery_time = $request->order_est_delivery_time;
                    $shipping->save();

                    $last_shipping_id = $shipping->id;

                    if ($shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {
                        $weight_counter = $request->weight_counter;

                        for ($i = 0; $i < $weight_counter; $i++) {
                            $shipping_rate_by_weight = new ShippingRate();
                            $shipping_rate_by_weight->seller_id = Auth::user()->getSeller->id;
                            $shipping_rate_by_weight->shipping_id = $last_shipping_id;
                            $shipping_rate_by_weight->range_start = $request->weight_range_start[$i];
                            $shipping_rate_by_weight->range_end = $request->weight_range_end[$i];
                            $shipping_rate_by_weight->rate = $request->weight_rate[$i];
                            $shipping_rate_by_weight->save();
                        }
                    } elseif ($shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
                        $order_price_counter = $request->order_counter;

                        for ($i = 0; $i < $order_price_counter; $i++) {
                            $shipping_rate_by_order = new ShippingRate();
                            $shipping_rate_by_order->seller_id = Auth::user()->getSeller->id;
                            $shipping_rate_by_order->shipping_id = $last_shipping_id;
                            $shipping_rate_by_order->range_start = $request->order_range_start[$i];
                            $shipping_rate_by_order->range_end = (!empty($request->order_range_end[$i])) ? $request->order_range_end[$i] : 0;
                            $shipping_rate_by_order->rate = $request->order_rate[$i];
                            $shipping_rate_by_order->save();
                        }
                    } elseif ($shipping_type == ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY) {
                        $shipping_store_pickup = new ShippingStorePickUp();
                        $shipping_store_pickup->seller_id = Auth::user()->getSeller->id;
                        $shipping_store_pickup->shipping_id = $last_shipping_id;
                        $shipping_store_pickup->pickup_title = $request->pickup_title;
                        $shipping_store_pickup->pickup_address = $request->pickup_address;
                        $shipping_store_pickup->country_id = $request->pickup_country;
                        $shipping_store_pickup->city_id = $request->pickup_city;
                        $shipping_store_pickup->state = $request->pickup_state;
                        $shipping_store_pickup->zip = $request->pickup_zip_code;
                        $shipping_store_pickup->save();
                    }
                    DB::commit();
                    return redirect('/seller/shipping-and-tax')->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Your Rule Added Successfully');
                } else {
                    return redirect('/seller/shipping-and-tax')->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'You Have Already a Shipping  in this Destination.');
                }
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Something went wrong. Please Try Again');
        }
    }

    public function sellerShippingAndTexUpdate(Request $request)
    {
        try {
            if ($request->skip == 1) {
                $cities_html = Seller::createEligibleDeliveryCityList($request->country_id);
                return response()->json(['cities_html' => $cities_html]);

            } else {
                $shipping_check = false;
                if ($request->country_id != 'all') {
                    $cities = $request->city_id;
                    $shipping_check = Seller::isCitiesShippingEligible($request->country_id, $cities);
                }
                $shipping_type = $request->shipping_type;

                if ($shipping_check == false) {
                    DB::beginTransaction();
                    $shipping = Shipping::find($request->shipping_id);
                    //$shipping->country_id = $request->country_id;
                    $shipping->city_ids = '';
                    $first = 0;
                    $city_ids = '';
                    for ($i = 0; $i < count($cities); $i++) {
                        if ($first == 0) {
                            $city_ids = $cities[$i];
                            $first = 1;
                        } else {
                            $city_ids .= ',' . $cities[$i];
                        }
                    }
                    $shipping->city_ids = $city_ids;
                    $shipping->shipping_type = $shipping_type;
                    $shipping->seller_id = Auth::user()->getSeller->id;
                    $shipping->tax = ($request->is_tax == 1) ? $request->tax_percentage : 0;
                    $shipping->rate = '';
                    if ($shipping_type == ShippingTypeEnum::FLAT_RATE) $shipping->rate = $request->flat_rate_rate;
                    if ($shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) $shipping->delivery_time = $request->weight_est_delivery_time;
                    if ($shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) $shipping->delivery_time = $request->order_est_delivery_time;
                    $shipping->save();

                    $last_shipping_id = $shipping->id;

                    if (!empty($shipping->getShippingRateByWeight)) $shipping->getShippingRateByWeight()->delete();
                    if (!empty($shipping->getShippingRateByOrderPrice)) $shipping->getShippingRateByOrderPrice()->delete();
                    if (!empty($shipping->getShippingPickup)) $shipping->getShippingPickup()->delete();

                    if ($shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {
                        $weight_counter = $request->weight_counter;

                        for ($i = 0; $i < $weight_counter; $i++) {
                            $shipping_rate_by_weight = new ShippingRate();
                            $shipping_rate_by_weight->seller_id = Auth::user()->getSeller->id;
                            $shipping_rate_by_weight->shipping_id = $last_shipping_id;
                            $shipping_rate_by_weight->range_start = $request->weight_range_start[$i];
                            $shipping_rate_by_weight->range_end = $request->weight_range_end[$i];
                            $shipping_rate_by_weight->rate = $request->weight_rate[$i];
                            $shipping_rate_by_weight->save();
                        }
                    } elseif ($shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
                        $order_price_counter = $request->order_counter;

                        for ($i = 0; $i < $order_price_counter; $i++) {
                            $shipping_rate_by_order = new ShippingRate();
                            $shipping_rate_by_order->seller_id = Auth::user()->getSeller->id;
                            $shipping_rate_by_order->shipping_id = $last_shipping_id;
                            $shipping_rate_by_order->range_start = $request->order_range_start[$i];
                            $shipping_rate_by_order->range_end = (!empty($request->order_range_end[$i])) ? $request->order_range_end[$i] : 0;
                            $shipping_rate_by_order->rate = $request->order_rate[$i];
                            $shipping_rate_by_order->save();
                        }
                    } elseif ($shipping_type == ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY) {

                        $shipping_store_pickup = new ShippingStorePickUp();
                        $shipping_store_pickup->seller_id = Auth::user()->getSeller->id;
                        $shipping_store_pickup->shipping_id = $last_shipping_id;
                        $shipping_store_pickup->pickup_title = $request->pickup_title;
                        $shipping_store_pickup->pickup_address = $request->pickup_address;
                        $shipping_store_pickup->country_id = $request->pickup_country;
                        $shipping_store_pickup->city_id = $request->pickup_city;
                        $shipping_store_pickup->state = $request->pickup_state;
                        $shipping_store_pickup->zip = $request->pickup_zip_code;
                        $shipping_store_pickup->save();
                    }
                    DB::commit();
                    return redirect('/seller/shipping-and-tax')->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Your Rule Updated Successfully');
                } else {
                    return redirect('/seller/shipping-and-tax')->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'You Have Already a Shipping  in this Destination.');
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Something went wrong. Please Try Again');
        }
    }

    // sellerEditShippingAndTex
    public function sellerEditShippingAndTex($id)
    {
        try {
            $shipping = Shipping::where('seller_id', Auth::user()->getSeller->id)->where('id', $id)->first();
            $countries = Country::join('cities', 'countries.id', '=', 'cities.country_id')
                ->select('countries.*')
                ->orderBy('name')
                ->where('cities.shipping_status', 1)
                ->groupBy('countries.id')->get();

            return view('/frontend/seller/edit-shipping-and-tax')->with('shipping', $shipping)->with('countries', $countries);
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Something went wrong. Please Try Again');
        }

    }



    // seller Delete Shipping And Tex
    public function sellerDeleteShippingAndTex($id)
    {
        try {
            $shipping = Shipping::where('seller_id', Auth::user()->getSeller->id)->where('id', $id)->first();
            $shipping_rate_by_weight_price = $shipping->getShippingRateByWeight;
            $shipping_rate_by_pickup = $shipping->getShippingPickup;

            if (isset($shipping_rate_by_weight_price[0])) {
                foreach ($shipping_rate_by_weight_price as $s) {
                    $s->delete();
                }
            }
            if (isset($shipping_rate_by_pickup)) {
                $shipping_rate_by_pickup->delete();
            }

            $shipping->delete();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Delete Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Something went wrong. Please Try Again');
        }

    }

    // Feature product
    public function featuredProduct(Request $request)
    {
        try {
            $featured_product_feas = Setting::where('key', SettingsEnum::FEATURED_PRODUCT_SUBSCRIPTION_FEE)->first();

            $products = explode(',', $request->feature_product);
            DB::beginTransaction();

            foreach ($products as $product) {
                $featured_product = new FeaturedProductSubscription();
                $featured_product->from_date = date('Y-m-d');
                $featured_product->to_date = date('Y-m-d', strtotime("+1 month"));
                $featured_product->fees = $featured_product_feas->value;
                $featured_product->seller_id = Auth::user()->getSeller->id;
                $featured_product->product_id = $product;
                $featured_product->save();
            }
            DB::commit();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Your Product Featured Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Something went wrong. Please Try Again');
        }
    }


    // Seller Memberships
    public function sellerMemberships()
    {
        $memberships = Subscription::orderBy('created_at', 'desc')->where('seller_id', Auth::user()->getSeller->id)->get();

        return view('frontend/seller/membership')->with('memberships', $memberships);
    }

    // Seller Membership Save
    public function sellerMembershipSave(Request $request)
    {
        try {
            $membership_plan = Subscription::where('seller_id', Auth::user()->getSeller->id)->get()->last();
            $membership_fees = Setting::where('key', SettingsEnum::MEMBERSHIP_FEE)->first();

            $date = date('Y-m-d');
            $date_one_month = date('Y-m-d', strtotime('+1 month'));

            if (isset($membership_plan)) {
                $date = $membership_plan->to_date;
                $date_one_month = date("Y-m-d", strtotime("+1 month", strtotime($date)));
            }

            $subscription = new Subscription();
            $subscription->from_date = $date;
            $subscription->to_date = $date_one_month;
            $subscription->fees = $membership_fees->value;
            $subscription->seller_id = Auth::user()->getSeller->id;
            $subscription->save();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Membership Subscription Completed');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . 'Something went wrong. Please Try Again');
        }
    }

}
