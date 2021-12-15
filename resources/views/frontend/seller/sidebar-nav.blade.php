<?php
$order_list_count = \App\Model\SubOrder::join('orders', 'orders.id', '=', 'sub_orders.order_id')
    ->where('sub_orders.seller_id', Auth::user()->getSeller->id)
    ->where('sub_orders.status', \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)
    ->where('orders.status', \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED)
    ->count();

$order_history_count = \App\Model\SubOrder::where('seller_id', Auth::user()->getSeller->id)->where('status', '!=', \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)->count();
$notification_count = App\Model\PushNotification::getNotificationCount(Auth::user()->id, \App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum::SELLER);
$product_count = App\Model\Product::where('seller_id', Auth::user()->getSeller->id)->where('status', \App\Http\Controllers\Enum\ProductStatusEnum::SHOWN)->count();

$membership_plan = App\Model\Subscription::where('seller_id', Auth::user()->getSeller->id)->exists();
$disabled = false;
if (Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::SERVICE && $membership_plan == false) {
    $disabled = true;
}
?>

<div class="col-md-3 col-sm-4 ">
    <div class="sidebar-nav">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="profile-img">

                    <?php
                    $seller = \App\Model\Seller::getSellerByUserID(Auth::user()->id);
                    ?>
                    <img
                            @if(!empty(Auth::user()->photo)) src="{{asset(env('USER_PHOTO_PATH').Auth::user()->photo)}}"
                            @else src="{{asset('image/default_author.png')}}" alt=""
                            @endif
                    >
                </div>
                <div class="profile-info">
                    <div style="font-weight: bold">{{$seller->company_name}}</div>
                    <span>{{$seller->business_email}}</span><br>
                    <a class="label label-success" href="{{url('/store/'.$seller->store_name)}}"
                       style="font-size: 11px; margin-top: 5px;padding-bottom: 3px;">{{$seller->store_name}}</a>
                </div>
            </div>
        </div>
        <div class="list-group">

            @if(Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)

                <a href="{{url('/seller/edit-profile')}}"
                   class="list-group-item @if($menu == 'edit-profile') active @endif"><i
                            class="fa fa-cog"></i> @lang('messages.seller.menu.edit_profile')</a>


                <a @if($disabled == false) href="{{url('/seller/products')}}"
                   @endif class="list-group-item @if($disabled) disabled @else @if($menu == 'products') active @endif @endif"><i
                            class="fa fa-circle-o"></i> @lang('messages.seller.menu.products')
                    @if($product_count>0) <span
                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                            style="position: static; right: inherit;  top: inherit; float: right;">{{$product_count}}</span> @endif
                </a>

                <a href="{{url('/seller/order-list')}}"
                   class="list-group-item @if($menu == 'order_list') active @endif"><i
                            class="fa fa-shopping-cart"></i> @lang('messages.seller.menu.order_list')
                    @if($order_list_count>0) <span
                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                            style="position: static; right: inherit;  top: inherit; float: right;">{{$order_list_count}}</span> @endif
                </a>
                <a href="{{url('/seller/order-history')}}"
                   class="list-group-item @if($menu == 'order_history') active @endif"><i
                            class="fa fa-history"></i> @lang('messages.seller.menu.order_history')
                    @if($order_history_count>0) <span
                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                            style="position: static; right: inherit;  top: inherit; float: right;">{{$order_history_count}}</span> @endif
                </a>
                <a href="{{url('/seller/my-earnings')}}" class="list-group-item @if($menu == 'payments') active @endif"><i
                            class="fa fa-usd"></i> @lang('messages.seller.menu.my_earnings')</a>
                <a href="{{url('/seller/shipping-and-tax')}}"
                   class="list-group-item @if($menu == 'shipping_tex') active @endif"><i
                            class="fa fa-percent"></i> @lang('messages.seller.menu.shipping_tex')</a>
                <a href="{{url('/seller/memberships')}}"
                   class="list-group-item @if($menu == 'membership') active @endif"><i
                            class="fa fa-user"></i> @lang('messages.seller.menu.membership')</a>
                <a href="{{url('/seller/notification')}}"
                   class="list-group-item @if($menu == 'notification') active @endif"><i
                            class="fa fa-bell-o"></i> @lang('messages.seller.menu.notification')
                    @if($notification_count>0) <span
                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                            style="position: static; right: inherit;  top: inherit; float: right;">
                    {{$notification_count}}
                </span>
                    @endif</a>

                <a href="{{url('/seller/question')}}" class="list-group-item @if($menu == 'question') active @endif"><i
                            class="fa fa-question-circle-o"
                            aria-hidden="true"></i> @lang('messages.seller.menu.question')
                </a>

                <a href="{{url('/logout')}}" class="list-group-item"><i
                            class="fa fa-edit"></i> @lang('messages.seller.menu.logout')</a>

            @elseif(Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::SERVICE)

                <a href="{{url('/seller/edit-profile')}}"
                   class="list-group-item @if($menu == 'edit-profile') active @endif"><i
                            class="fa fa-cog"></i> @lang('messages.seller.menu.edit_profile')</a>


                <a @if($disabled == false) href="{{url('/seller/services')}}"
                   @endif class="list-group-item @if($disabled) disabled @else @if($menu == 'products') active @endif @endif"><i
                            class="fa fa-circle-o"></i> @lang('messages.seller.menu.services')
                    @if($product_count>0) <span
                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                            style="position: static; right: inherit;  top: inherit; float: right;">{{$product_count}}</span> @endif
                </a>

                {{--<a href="{{url('/seller/order-list')}}"--}}
                   {{--class="list-group-item @if($menu == 'order_list') active @endif"><i--}}
                            {{--class="fa fa-shopping-cart"></i> @lang('messages.seller.menu.order_list')--}}
                    {{--@if($order_list_count>0) <span--}}
                            {{--class="cart-qty font-main font-weight-semibold color-main customBgColor circle"--}}
                            {{--style="position: static; right: inherit;  top: inherit; float: right;">{{$order_list_count}}</span> @endif--}}
                {{--</a>--}}
                {{--<a href="{{url('/seller/order-history')}}"--}}
                   {{--class="list-group-item @if($menu == 'order_history') active @endif"><i--}}
                            {{--class="fa fa-history"></i> @lang('messages.seller.menu.order_history')--}}
                    {{--@if($order_history_count>0) <span--}}
                            {{--class="cart-qty font-main font-weight-semibold color-main customBgColor circle"--}}
                            {{--style="position: static; right: inherit;  top: inherit; float: right;">{{$order_history_count}}</span> @endif--}}
                {{--</a>--}}
                {{--<a href="{{url('/seller/my-earnings')}}" class="list-group-item @if($menu == 'payments') active @endif"><i--}}
                            {{--class="fa fa-usd"></i> @lang('messages.seller.menu.my_earnings')</a>--}}

                <a href="{{url('/seller/memberships')}}"
                   class="list-group-item @if($menu == 'membership') active @endif"><i
                            class="fa fa-user"></i> @lang('messages.seller.menu.membership')</a>
                <a href="{{url('/seller/notification')}}"
                   class="list-group-item @if($menu == 'notification') active @endif"><i
                            class="fa fa-bell-o"></i> @lang('messages.seller.menu.notification')
                    @if($notification_count>0) <span
                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                            style="position: static; right: inherit;  top: inherit; float: right;">
                    {{$notification_count}}
                </span>
                    @endif</a>

                <a href="{{url('/seller/question')}}" class="list-group-item @if($menu == 'question') active @endif"><i
                            class="fa fa-question-circle-o"
                            aria-hidden="true"></i> @lang('messages.seller.menu.question')
                </a>

                <a href="{{url('/logout')}}" class="list-group-item"><i
                            class="fa fa-edit"></i> @lang('messages.seller.menu.logout')</a>
            @endif


        </div>
    </div>
</div>
