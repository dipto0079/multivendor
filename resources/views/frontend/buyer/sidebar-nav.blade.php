<?php
    $favorite_stores = \App\Model\FavoriteStore::where('buyer_id', Auth::user()->getBuyer->id)->count();
    $favorite_products = \App\Model\FavoriteProduct::where('buyer_id', Auth::user()->getBuyer->id)->count();
?>
<div class="col-md-3 col-sm-4">
    <div class="sidebar-nav">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="profile-img">
                    <img alt=""
                         @if(!empty(Auth::user()->photo) && stripos(Auth::user()->photo,'https://')!== false)
                         src="{{Auth::user()->photo}}"
                         @elseif(!empty(Auth::user()->photo))
                         src="{{asset(env('USER_PHOTO_PATH').Auth::user()->photo)}}"
                         @else
                         src="{{asset('image/default_author.png')}}"
                            @endif
                    >
                </div>
                <div class="profile-info">
                    <h3>{{Auth::user()->username}}</h3>
                    <span>@lang('messages.buyer.menu.buyer')</span>
                </div>
            </div>
        </div>
        <?php
            $notification_count = App\Model\PushNotification::getNotificationCount(Auth::user()->id,\App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum::BUYER);
            $cart_item_count = App\Model\CartItem::where('buyer_id', Auth::user()->getBuyer->id)->sum('quantity');
            $order_history_count = App\Model\Order::where('buyer_id', Auth::user()->getBuyer->id)->count();
        ?>
        <div class="list-group hidden-xs">
            <a @if(!empty($menu) && $menu != 'edit_profile')  href="{{url('/buyer/edit-profile')}}"
               @endif class="list-group-item @if(!empty($menu) && $menu == 'edit_profile') active @endif"><i
                        class="fa fa-user"></i> @lang('messages.buyer.menu.edit_profile')</a>
            <a @if(!empty($menu) && $menu != 'cart_list') href="{{url('/buyer/cart-list')}}"
               @endif class="list-group-item @if(!empty($menu) && $menu == 'cart_list' ||  $menu == 'delivery_payment') active @endif"><i
                        class="font-icon icon-bag"></i> @lang('messages.buyer.menu.cart')
                <span id="add_to_cart_count">
                @if($cart_item_count>0) <span
                        class="add-to-cart-qty cart-qty font-main font-weight-semibold color-main customBgColor circle"
                        style="position: static; right: inherit;  top: inherit; float: right;">
                    {{$cart_item_count}}
                </span> @endif</span></a>

            <a @if(!empty($menu) && $menu != 'wishlist')  href="{{url('/buyer/wish-list')}}"
               @endif class="list-group-item @if(!empty($menu) && $menu == 'wishlist') active @endif"><i
                        class="fa fa-barcode"></i> @lang('messages.buyer.menu.wish_list')
                @if(!empty($favorite_products))<span
                        class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                        style="position: static; right: inherit;  top: inherit; float: right;">{{$favorite_products}}</span>@endif
            </a>

            <a @if(!empty($menu) && $menu != 'order_history')  href="{{url('/buyer/order-history')}}"
               @endif class="list-group-item @if(!empty($menu) && $menu == 'order_history') active @endif"><i
                        class="fa fa-shopping-cart"></i> @lang('messages.buyer.menu.order_history')@if(!empty($order_history_count))<span
                        class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                        style="position: static; right: inherit;  top: inherit; float: right;">{{$order_history_count}}</span>@endif</a>

            <a @if(!empty($menu) && $menu != 'favourite_store')  href="{{url('/buyer/favourite-store')}}"
               @endif class="list-group-item @if(!empty($menu) && $menu == 'favourite_store') active @endif"><i
                        class="fa fa-heart"></i> @lang('messages.buyer.menu.favorite_store')
                @if(!empty($favorite_stores))<span
                        class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                        style="position: static; right: inherit;  top: inherit; float: right;">{{$favorite_stores}}</span>@endif
            </a>

            @if(!empty(Auth::user()->password))
                <a @if(!empty($menu) && $menu != 'password')  href="{{url('/buyer/password')}}"
                   @endif class="list-group-item @if(!empty($menu) && $menu == 'password') active @endif"><i
                            class="fa fa-lock"></i> @lang('messages.buyer.menu.password')</a>
            @endif
            <a @if(!empty($menu) && $menu != 'notification')  href="{{url('/buyer/notification')}}"
               @endif class="list-group-item @if(!empty($menu) && $menu == 'notification') active @endif"><i
                        class="fa fa-bell-o"></i> @lang('messages.buyer.menu.notification')
                @if($notification_count>0) <span
                        class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                        style="position: static; right: inherit;  top: inherit; float: right;">
                    {{$notification_count}}
                </span>
                @endif
            </a>

            <a href="{{url('/buyer/question')}}" class="list-group-item @if($menu == 'question') active @endif"><i class="fa fa-question-circle-o" aria-hidden="true"></i> @lang('messages.buyer.menu.question')
               </a>

            <a href="{{url('/logout')}}" class="list-group-item"><i class="fa fa-power-off"></i> @lang('messages.buyer.menu.logout')</a>
        </div>
        <div class=" hidden-xs">
            <h3> @lang('messages.buyer.menu.question')</h3>
            <a href="#" class="btn btn-block btn-primary font-additional btn-bordered"><i class="fa fa-comments" aria-hidden="true"></i> @lang('messages.buyer.menu.chat_now')</a>
        </div>
    </div>
</div>
