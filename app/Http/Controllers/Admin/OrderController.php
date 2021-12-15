<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Enum\CouponDiscountTypeEnum;
use App\Http\Controllers\Enum\DiscountTypeEnum;
use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\ProductTypeEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;
use App\Http\Controllers\Enum\ShippingTypeEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Model\Coupon;
use App\Model\Order;
use App\Model\Buyer;
use App\Model\OrderItem;
use App\Model\Product;
use App\Model\PushNotificationReceiver;
use App\Model\SubOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Enum\OrderStatusEnum;
use Image;
use Mail;

class OrderController extends Controller
{
    public function pendingOrderList()
    {
        $status = OrderStatusEnum::PENDING;
        $from = '';
        $to = '';

        if (!empty($_GET['from'])) $from = date('Y-m-d H:i:s', strtotime($_GET['from'] . ' 00:00:01'));
        if (!empty($_GET['to'])) $to = date('Y-m-d H:i:s', strtotime($_GET['to'] . ' 23:59:59'));

        $orders = Order::orderby('created_at', 'desc')
            ->where('status', $status);

        if (!empty($from) && !empty($to)) {
            $orders = $orders->whereBetween('created_at', [$from, $to]);
        }

        $orders = $orders->paginate(env('PAGE_PAGINATE'));

        return view('/admin/order/list')
            ->with('status', $status)
            ->with('orders', $orders);
    }

    public function acceptedOrderList()
    {
        $status = OrderStatusEnum::ACCEPTED;
        $from = '';
        $to = '';

        if (!empty($_GET['from'])) $from = date('Y-m-d H:i:s', strtotime($_GET['from'] . ' 00:00:01'));
        if (!empty($_GET['to'])) $to = date('Y-m-d H:i:s', strtotime($_GET['to'] . ' 23:59:59'));

        $orders = Order::orderby('created_at', 'desc')
            ->where('status', $status);

        if (!empty($from) && !empty($to)) {
            $orders = $orders->whereBetween('created_at', [$from, $to]);
        }

        $orders = $orders->paginate(env('PAGE_PAGINATE'));
        return view('/admin/order/list')
            ->with('status', $status)
            ->with('orders', $orders);
    }

    public function deliveredFeedbackPendingOrderList()
    {
        $status = OrderStatusEnum::DELIVERED;
        $from = '';
        $to = '';

        if (!empty($_GET['from'])) $from = date('Y-m-d H:i:s', strtotime($_GET['from'] . ' 00:00:01'));
        if (!empty($_GET['to'])) $to = date('Y-m-d H:i:s', strtotime($_GET['to'] . ' 23:59:59'));

        $orders = Order::orderby('created_at', 'desc')
            ->where('status', $status);

        if (!empty($from) && !empty($to)) {
            $orders = $orders->whereBetween('created_at', [$from, $to]);
        }

        $orders = $orders->paginate(env('PAGE_PAGINATE'));

        return view('/admin/order/list')
            ->with('status', $status)
            ->with('orders', $orders);
    }

    public function finalizedOrderList()
    {
        $status = OrderStatusEnum::FINALIZED;
        $from = '';
        $to = '';

        if (!empty($_GET['from'])) $from = date('Y-m-d H:i:s', strtotime($_GET['from'] . ' 00:00:01'));
        if (!empty($_GET['to'])) $to = date('Y-m-d H:i:s', strtotime($_GET['to'] . ' 23:59:59'));

        $orders = Order::orderby('created_at', 'desc')
            ->where('status', $status);

        if (!empty($from) && !empty($to)) {
            $orders = $orders->whereBetween('created_at', [$from, $to]);
        }

        $orders = $orders->paginate(env('PAGE_PAGINATE'));

        return view('/admin/order/list')
            ->with('status', $status)
            ->with('orders', $orders);
    }

    public function rejectedOrderList()
    {
        $status = OrderStatusEnum::REJECTED;
        $from = '';
        $to = '';

        if (!empty($_GET['from'])) $from = date('Y-m-d H:i:s', strtotime($_GET['from'] . ' 00:00:01'));
        if (!empty($_GET['to'])) $to = date('Y-m-d H:i:s', strtotime($_GET['to'] . ' 23:59:59'));

        $orders = Order::orderby('created_at', 'desc')
            ->where('status', $status);

        if (!empty($from) && !empty($to)) {
            $orders = $orders->whereBetween('created_at', [$from, $to]);
        }

        $orders = $orders->paginate(env('PAGE_PAGINATE'));

        return view('/admin/order/list')
            ->with('status', $status)
            ->with('orders', $orders);
    }

    public function productSellerList(Request $request)
    {
        $status = SellerStatusEnum::APPROVED;

        if ($request->status != null) $status = $request->status;

        $sellers = Seller::orderby('company_name', 'asc')
            ->where('business_type', ProductTypeEnum::PRODUCT)
            ->where('status', $status)->paginate(env('PAGE_PAGINATE'));

        return view('/admin/seller/product/list')
            ->with('status', $status)
            ->with('sellers', $sellers);
    }

    public function publishOrder(Request $request)
    {
        try {
            $order = Order::find($request->order_id);
            $order->status = OrderStatusEnum::ACCEPTED;
            $order->save();

            $order_items = $order->getSubOrders;

            foreach ($order_items as $order_item) {

                $email = $order_item->getSeller->getUser->email;
                $orders = $order_item->getSubOrderItems;
                $name = $order_item->getSeller->getUser->username;

                $data = [
                    'order_lists' => $orders,
                    'order' => $order,
                    'name' => $name,
                ];

                Mail::send('emails.sellerOrderInformation', $data, function ($message) use ($email) {
                    $message->to($email)->subject('Order List');
                });
            }

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Order accepted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . 'Something Went Wrong.');
        }
    }

    public function orderRejected($id)
    {
        try {

            DB::beginTransaction();

            $order = Order::find($id);
            $order->status = OrderStatusEnum::REJECTED;
            $order->save();

            //need to restore all the quantity, need to back the buyer payments etc
            $subOrders = $order->getSubOrders;
            foreach ($subOrders as $suborder) {
                $order_items = $suborder->getSubOrderItems;

                foreach ($order_items as $order_item) {
//                    foreach ($products as $product) {
                    $storedProduct = Product::find($order_item->product_id);
                    $storedProduct->quantity = $storedProduct->quantity + $order_item->quantity; //restore the quantity
                    $storedProduct->save();
//                    }
                }
            }

            $buyser_info = $order->getBuyer->getUser;
            $buyer_email = $buyser_info->email;
            $name = $buyser_info->username;

            if (!empty($buyer_email)) {
                $data = [
                    'name' => $name,
                    'order_number' => $order->id,
                    'status' => $order->status,
                ];

                Mail::send('emails.orderStatusEmail', $data, function ($message) use ($buyer_email) {
                    $message->to($buyer_email)->subject('Order Status');
                });
            }
            DB::commit();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Order rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function orderDetails($id, $seller_id=null, $calculate_admin_commission=1)
    {
        $admin_commission_percentage = '';
        $shipping_type='';
        $tax_rate='';

        $order = Order::where('id', $id)->first();

        $sub_order = null;
        if(isset($seller_id) && $seller_id!=0) {
            $sub_order = SubOrder::where('order_id', $id)->where('seller_id', $seller_id)->first();

            $details = \App\Model\SubOrder::getSubOrderSummaryById($sub_order->id);

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

            $order_items = $sub_order->getSubOrderItems;

        }
        else {
            $details = \App\Model\Order::getOrderSummaryById($id);

            $tax= $order->vat_amount;
            $shipping_cost = $order->shipping_rate;

            $order_items = OrderItem::wherein('sub_order_id', $order->getSubOrdersArray())->get();
        }

        $discount = $order->discount;

        $order_total = $details['sub_total'];
        $admin_commission = $details['admin_commission'];

        if($calculate_admin_commission)
            $paid_amount = $order_total + $tax + $shipping_cost - $admin_commission;
        else
            $paid_amount = $order_total + $tax + $shipping_cost;


        if(!isset($seller_id) || $seller_id==0) {
            $paid_amount = $paid_amount - $discount;
        }

        $buyer_information = $order->getBuyer;


        $data_generate = '';
        $data_generate .= '

                      <section class="tabs-section">
				<div class="tabs-section-nav tabs-section-nav-icons">
					<div class="tbl">
						<ul class="nav" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										Order List
									</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										Buyer and Delivery Details
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div>';

        $data_generate .= '<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1">';

        $data_generate .= '<table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Store Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th class="text-center"  width="120">Total</th>
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
            $data_generate .= '<td><a target="_blank" href="' . url('admin/product/seller/' . $order_item->getProduct->getSeller->id . '/product/list') . '">' . $order_item->getProduct->getSeller->store_name . '</a></td>';

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
            <td colspan="5" class="text-right">Sub Total</td>
            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order_total, 2) . '</td>
        </tr>';

        if(!isset($seller_id) || $seller_id==0) {
            if (!empty($order->coupon)) {
                $coupon = Coupon::where('coupon', $order->coupon)->first();
                $data_generate .= '<tr>
                <td colspan="5" class="text-right"><span class="label label-custom label-pill label-danger">- Discount: (<a style="color:white" target="_blank" href="'.url('/admin/settings/coupon').'">';
                if ($coupon->discount_type == CouponDiscountTypeEnum::FIXED) {
                    $data_generate .= $coupon->coupon;
                }
                if ($coupon->discount_type == CouponDiscountTypeEnum::PERCENTAGE) {
                    $data_generate .= $coupon->discount . '% - ' . $coupon->coupon;
                }
                $data_generate .= '</a>)</span></td>
                <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order->discount, 2) . '</td>
            </tr>';
            }
        }



        $data_generate .= '<tr>
           <td colspan="5" class="text-right"><span class="label label-custom label-pill label-warning">+ VAT '.$tax_rate.'</span> </td>
            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($tax, 2) . '</td>
        </tr>';

        $data_generate .= '<tr>
            <td colspan="5" class="text-right"><span class="label label-custom label-pill label-warning">+ Shipping Cost '.$shipping_type.'</span> </td>
            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($shipping_cost, 2) . '</td>
        </tr>';

        if($calculate_admin_commission)
            $data_generate .= '<tr>
            <td colspan="5" class="text-right"><span class="label label-custom label-pill label-success">- Admin Commission '.$admin_commission_percentage.'</span></td>
            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($admin_commission, 2) . '</td>
        </tr>';





        $data_generate .= '<tr>
            <td colspan="5" class="text-right" style="font-weight: bold; font-size: 22px">TOTAL</td>
            <td class="text-right" style="font-weight: bold; font-size: 22px">' . env('CURRENCY_SYMBOL') . number_format($paid_amount, 2) . '</td>
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
                <span><strong>Email:</strong> ' . $buyer_information->getUser->email . '</span><br>
                <span><strong>Phone:</strong> ' . $buyer_information->getUser->phone . '</span><br>
                <span><strong>Street:</strong> ' . $buyer_information->street . '</span><br>
                <span><strong>City:</strong> ' . $buyer_information->getDistrict->name . '</span><br>
                <span><strong>Zip Code:</strong> ' . $buyer_information->zip . '</span><br>
                <span><strong>State:</strong> ' . $buyer_information->state . '</span><br>
                <span><strong>County:</strong> ';
        if (!empty($buyer_information->getCountryName))
            $data_generate .= $buyer_information->getCountryName->name;
        $data_generate .= '</span><div class="clearfix"></div></div><br><hr>';
        $data_generate .= '<h4 class="media-heading">Details</h4>
        <span><strong>Street: </strong>' . $order->delivery_street . '</span><br>
            <span><strong>City: </strong>';
        if (isset($order->getOrderCity)) $data_generate .= $order->getOrderCity->name;
        $data_generate .= '</span><br>
            <span><strong>State: </strong>' . $order->delivery_state . '</span><br>
            <span><strong>Zip Code: </strong>' . $order->delivery_zip . '</span><br>
            <span><strong>Country: </strong>' . $order->getCountryName->name . '</span>
              </div>
            </div>';
        $data_generate .= '</div></div></section>';


        return response()->json(['success' => true, 'data_generate' => $data_generate]);

    }

    // Order List Details
    public function orderListDetails()
    {
        $data_generate = '';
        try {
            $id = Crypt::decrypt($_GET['order_id']);

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
            $data_generate .= 'Something went wrong. Please try again.';
            return response()->json(['success' => false, 'data_generate' => $data_generate]);
        }

    }

//    public function productSellerOrderDetails(Request $request)
//    {
//        try {
//            $sub_order = SubOrder::where('order_id', $request->order_id)->where('seller_id', $request->seller_id)->first();
//            $buyer_information = $sub_order->getOrder->getBuyer;
//            $order_items = $sub_order->getSubOrderItems;
//            $data_generate = '';
//            $data_generate .= '
//                      <section class="tabs-section">
//				<div class="tabs-section-nav tabs-section-nav-icons">
//					<div class="tbl">
//						<ul class="nav" role="tablist">
//							<li class="nav-item">
//								<a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
//									<span class="nav-link-in">
//									Order List
//									</span>
//								</a>
//							</li>
//							<li class="nav-item">
//								<a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
//									<span class="nav-link-in">
//										Buyer and Delivery Details
//									</span>
//								</a>
//							</li>
//						</ul>
//					</div>
//				</div>';
//
//
//            $data_generate .= '<div class="tab-content">
//					<div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1">';
//
//            $data_generate .= '<table class="table table-bordered table-hover">
//                <thead>
//                <tr>
//                    <th>Image</th>
//                    <th>Product</th>
//                    <th>Store Name</th>
//                    <th>Price</th>
//                    <th>Quantity</th>
//                    <th class="text-center"  width="120">Total</th>
//                </thead>
//                <tbody>';
//
//            $details = \App\Model\Order::getOrderSummaryById($request->order_id);
//            $order_total = $details['sub_total'];
//            $admin_commission = $details['admin_commission'];
//
//
//            foreach ($order_items as $order_item) {
//                $media = $order_item->getProduct->getMedia;
//
//                $data_generate .= '<tr>';
//
//                if (isset($media[0]))
//                    $data_generate .= '<td><img src="' . Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 80, 75, ['crop']) . '"></td>';
//                else
//                    $data_generate .= '<td><img src="' . asset('/image/noimage_80x75.jpg') . '"></td>';
//
//                $data_generate .= '<td>' . $order_item->getProduct->name . '</td>';
//                $data_generate .= '<td><a target="_blank" href="' . url('admin/product/seller/' . $order_item->getProduct->getSeller->id . '/product/list') . '">' . $order_item->getProduct->getSeller->store_name . '</a></td>';
//
//
//                $data_generate .= '<td class="text-right">';
//                if (isset($order_item->discount)) {
//                    $data_generate .= "<span>" . env('CURRENCY_SYMBOL') . number_format($order_item->deal_price, 2) . "</span></br>";
//                    $data_generate .= "<span style='color:red;font-size:14px; text-decoration: line-through;'>" . env('CURRENCY_SYMBOL') . number_format($order_item->product_price, 2) . "</span></br>";
//
//                    if ($order_item->discount_type == DiscountTypeEnum::FIXED) $data_generate .= '<span  style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '</span>';
//                    else if ($order_item->discount_type == DiscountTypeEnum::PERCENTAGE) $data_generate .= '<span style="font-size:12px;" class="label label-custom label-pill label-warning">Discount: ' . $order_item->discount_rate . '%</span>';
//
//                } else {
//                    $data_generate .= env('CURRENCY_SYMBOL') . number_format($order_item->deal_price, 2);
//                }
//                $data_generate .= '</td>';
//
//                $data_generate .= '<td class="text-center">' . $order_item->quantity . '</td>';
//                $data_generate .= '<td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order_item->deal_price * $order_item->quantity, 2) . '</td>';
//                $data_generate .= '</tr>';
//            }
//
//            $paid_amount = $order_total + $sub_order->tax + $sub_order->shipping_cost - $admin_commission;
//
//            $data_generate .= '<tr>
//            <td colspan="5" class="text-right">Sub Total</td>
//            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($order_total, 2) . '</td>
//        </tr>';
//
//            $data_generate .= '<tr>
//            <td colspan="5" class="text-right"><span class="label label-custom label-pill label-warning">+ VAT (' . $sub_order->tax_rate . '%)</span> </td>
//            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($sub_order->tax, 2) . '</td>
//        </tr>';
//
//            $shipping_type = "";
//            if ($sub_order->shipping_type == ShippingTypeEnum::FREE_SHIPPING) {
//                $shipping_type = "Free";
//            } elseif ($sub_order->shipping_type == ShippingTypeEnum::FLAT_RATE) {
//                $shipping_type = "Flat";
//            } elseif ($sub_order->shipping_type == ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY) {
//                $shipping_type = "Store Pickup";
//            } elseif ($sub_order->shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
//                $shipping_type = "Rate by Price";
//            } elseif ($sub_order->shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {
//                $shipping_type = "Rate by Weight";
//            }
//
//
//            $data_generate .= '<tr>
//            <td colspan="5" class="text-right"><span class="label label-custom label-pill label-warning">+ Shipping (' . $shipping_type . ')</span> </td>
//            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($sub_order->shipping_cost, 2) . '</td>
//        </tr>';
//
//            $data_generate .= '<tr>
//            <td colspan="5" class="text-right"><span class="label label-custom label-pill label-success">- Admin Commission (' . $sub_order->admin_commission . '%)</span></td>
//            <td class="text-right">' . env('CURRENCY_SYMBOL') . number_format($admin_commission, 2) . '</td>
//        </tr>';
//
//            $data_generate .= '<tr>
//            <td colspan="5" class="text-right" style="font-weight: bold; font-size: 22px">TOTAL</td>
//            <td class="text-right" style="font-weight: bold; font-size: 22px">' . env('CURRENCY_SYMBOL') . number_format($paid_amount, 2) . '</td>
//        </tr>';
//
//            $data_generate .= '</tbody></table>';
//
//            $data_generate .= '</div>';
//
//            $data_generate .= '<div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2">';
//
//            $data_generate .= '<div class="media">
//              <div class="media-left">
//                <a href="#">
//                  <img class="media-object" width="100" height="100" ';
//            if (!empty($buyer_information->getUser->photo) && stripos($buyer_information->getUser->photo, 'https://') !== false) {
//                $data_generate .= 'src="' . $buyer_information->getUser->photo . '"';
//            } elseif (!empty($buyer_information->getUser->photo)) {
//                $data_generate .= 'src="' . Image::url(asset(env('USER_PHOTO_PATH') . $buyer_information->getUser->photo), 200, 200, ['crop']) . '"';
//            } else {
//                $data_generate .= 'src="' . asset('image/default_author.png') . '"';
//            }
//            $data_generate .= 'alt="">
//                </a>
//              </div>
//              <div class="media-body">
//                <h4 class="media-heading">' . $buyer_information->getUser->username . '</h4>
//                <span><strong>Email:</strong> ' . $buyer_information->getUser->email . '</span><br>
//                <span><strong>Phone:</strong> ' . $buyer_information->getUser->phone . '</span><br>
//                <span><strong>Street:</strong> ' . $buyer_information->street . '</span><br>
//                <span><strong>City:</strong> ' . $buyer_information->getDistrict->name . '</span><br>
//                <span><strong>Zip Code:</strong> ' . $buyer_information->zip . '</span><br>
//                <span><strong>State:</strong> ' . $buyer_information->state . '</span><br>
//                <span><strong>County:</strong> ';
//            if (!empty($buyer_information->getCountryName))
//                $data_generate .= $buyer_information->getCountryName->name;
//            $data_generate .= '</span><div class="clearfix"></div></div><br><hr>';
//            $data_generate .= '<h4 class="media-heading">Details</h4>
//        <span><strong>Street: </strong>' . $sub_order->getOrder->delivery_street . '</span><br>
//            <span><strong>City: </strong>';
//            if (isset($sub_order->getOrder->getOrderCity)) $data_generate .= $sub_order->getOrder->getOrderCity->name;
//            $data_generate .= '</span><br>
//            <span><strong>State: </strong>' . $sub_order->getOrder->delivery_state . '</span><br>
//            <span><strong>Zip Code: </strong>' . $sub_order->getOrder->delivery_zip . '</span><br>
//            <span><strong>Country: </strong>' . $sub_order->getOrder->getCountryName->name . '</span>
//              </div>
//            </div>';
//            $data_generate .= '</div></div></section>';
//
//
//            return response()->json(['success' => true, 'data_generate' => $data_generate]);
//        } catch (\Exception $e) {
//        }
//    }



    //  Seller order status
    public function orderSubOrderStatus($id)
    {

        $order = Order::where('id', $id)->where('status', OrderStatusEnum::ACCEPTED)->first();
        $sub_orders = $order->getSubOrders;

        $data_generate = '';

        if (!empty($sub_orders[0])) {
            $data_generate .= '<table class="table table-bordered table-hover">
          <thead>
            <tr>
              <td>Store Name</td>
              <td>Status</td>
            </tr>
          </thead>
          <tbody>';
            foreach ($sub_orders as $sub_order) {
                $data_generate .= '<tr>';
                $data_generate .= '<td style="padding: 3px 10px 3px; height: auto;"><a target="_blank" href="' . url('admin/product/seller/' . $sub_order->getSeller->id . '/product/list') . '">' . $sub_order->getSeller->store_name . '</a></td>';
                $data_generate .= '<td style="padding: 3px 10px 3px; height: auto;">';
                if ($sub_order->status == OrderStatusEnum::PENDING) $data_generate .= '<span class="label label-default">Pending</span>';
                elseif ($sub_order->status == OrderStatusEnum::DELIVERED) $data_generate .= '<span class="label label-success">Delivered</span>';
                elseif ($sub_order->status == OrderStatusEnum::CLAIMED) $data_generate .= '<span class="label label-warning">Cliamed</span>';
                elseif ($sub_order->status == OrderStatusEnum::REJECTED) $data_generate .= '<span class="label label-danger">Rejected</span>';
                elseif ($sub_order->status == OrderStatusEnum::FINALIZED) $data_generate .= '<span class="label label-info">Complete</span>';

                $data_generate .= '</td>';
                $data_generate .= '</tr>';
            }

            $data_generate .= '</tbody></table>';
        }

        return response()->json(['success' => true, 'data_generate' => $data_generate]);
    }
}
