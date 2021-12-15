<?php
if (Auth::user() != null && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER) {
    $favorite_products = "";
    if (isset(Auth::user()->getBuyer))
        $favorite_products = \App\Model\FavoriteProduct::where('buyer_id', Auth::user()->getBuyer->id)->count();
}
?>
<div id="top_pomotion">

</div>

<div class="header-top">
    <div class="container">
        <ul class="pull-left contact-list">
            <li>
                <span class="sli icon-envelope-open" aria-hidden="true"></span>
                <span class="contact-list_label font-main font-weight-normal">@lang('messages.top_menu.email')</span>
            </li>
            <li>
                <span class="sli icon-call-out" aria-hidden="true"></span>
                <span class="contact-list_label font-main font-weight-normal">@lang('messages.top_menu.help')</span>
            </li>
        </ul>
        <ul class="nav nav-pills nav-top pull-right hidden-xs">
            <li class="dropdown langs">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <img @if(\App\UtilityFunction::getLocal() == 'en' ) src="{{asset('image/en.jpg')}}"
                         @else src="{{asset('image/ar.jpg')}}"
                         @endif alt="" style="width: 30px;">
                    <i class="fa fa-angle-down"></i></a>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="{{url('/switch-language')}}">
                            <img @if(\App\UtilityFunction::getLocal()=='en') src="{{asset('image/ar.jpg')}}"
                                 @else src="{{asset('image/en.jpg')}}" @endif alt="" style="width: 30px;"></a></li>
                </ul>
            </li>
            <li class="dropdown currency">
                <a href="#" data-target="#how_it_work"
                   data-toggle="modal">@lang('messages.top_menu.how_it_work')</a>
            </li>

            <li class="dropdown currency">
                <a href="{{url('seller-registration')}}">@lang('messages.top_menu.list_your_business')</a>
            </li>

            <?php

            $notification_count = 0;
            if (!empty(Auth::user()->email))
                $notification_count = App\Model\PushNotification::getNotificationCount(Auth::user()->id, Auth::user()->user_type);
            ?>



            @if(!empty(Auth::user()->email) && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER && Auth::user()->status != \App\Http\Controllers\Enum\UserStatusEnum::UNBLOCKED)
                <li class="dropdown my-account">
                    <a data-toggle="dropdown" class="dropdown-toggle"
                       href="#">@lang('messages.hello') {{Auth::user()->username}} <i
                                class="fa fa-angle-down"></i></a>
                    <ul role="menu" class="dropdown-menu">
                        <li><a href="{{url('/buyer/wish-list')}}"><i
                                        class="fa fa-barcode"></i> @lang('messages.buyer.menu.wish_list') @if(!empty($favorite_products))
                                    <span
                                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                            style="position: static; right: inherit;  top: inherit; float: right;">{{$favorite_products}}</span>@endif
                            </a></li>
                        <li><a href="{{url('/buyer/order-history')}}"><i
                                        class="fa fa-shopping-cart"></i> @lang('messages.buyer.menu.order_history')</a>
                        </li>
                        <li><a href="{{url('/buyer/edit-profile')}}"><i
                                        class="fa fa-cog"></i> @lang('messages.buyer.menu.edit_profile')</a></li>
                        <li><a href="{{url('/buyer/notification')}}"><i class="fa fa-bell-o"></i>
                                @lang('messages.buyer.menu.notification') @if($notification_count>0)<span
                                        class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                        style="position: static; right: inherit;  top: inherit; float: right;">{{$notification_count}}</span>@endif
                            </a></li>
                        <li><a href="{{url('/logout')}}"><i
                                        class="fa fa-power-off"></i> @lang('messages.buyer.menu.logout')</a></li>
                    </ul>
                </li>
            @elseif(!empty(Auth::user()->email) && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::SELLER && Auth::user()->status != \App\Http\Controllers\Enum\UserStatusEnum::UNBLOCKED)

                @if(Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)

                    <li class="dropdown my-account">
                        <a data-toggle="dropdown" class="dropdown-toggle"
                           href="#">@lang('messages.hello') {{Auth::user()->username}} <i
                                    class="fa fa-angle-down"></i></a>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="{{url('/seller/edit-profile')}}"><i
                                            class="fa fa-cog"></i> @lang('messages.seller.menu.edit_profile')</a></li>
                            <li><a href="{{url('/seller/products')}}"><i
                                            class="fa fa-circle-o"></i> @lang('messages.seller.menu.products')</a></li>
                            <li><a href="{{url('/seller/order-list')}}"><i
                                            class="fa fa-shopping-cart"></i> @lang('messages.seller.menu.order_list')
                                </a>
                            </li>
                            <li><a href="{{url('/seller/my-earnings')}}"><i
                                            class="fa fa-usd"></i> @lang('messages.seller.menu.my_earnings')</a></li>

                            <li><a href="{{url('/seller/notification')}}"><i class="fa fa-bell-o"></i>
                                    @lang('messages.seller.menu.notification') @if($notification_count>0)<span
                                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                            style="position: static; right: inherit;  top: inherit; float: right;">{{$notification_count}}</span>@endif
                                </a></li>
                            <li><a href="{{url('/logout')}}"><i
                                            class="fa fa-power-off"></i> @lang('messages.seller.menu.logout')</a></li>
                        </ul>
                    </li>

                @elseif(Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::SERVICE)

                    <li class="dropdown my-account">
                        <a data-toggle="dropdown" class="dropdown-toggle"
                           href="#">@lang('messages.hello') {{Auth::user()->username}} <i
                                    class="fa fa-angle-down"></i></a>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="{{url('/seller/edit-profile')}}"><i
                                            class="fa fa-cog"></i> @lang('messages.seller.menu.edit_profile')</a></li>
                            <li><a href="{{url('/seller/services')}}"><i
                                            class="fa fa-circle-o"></i> @lang('messages.seller.menu.services')</a></li>
                            <li><a href="{{url('/seller/memberships')}}"><i
                                            class="fa fa-user"></i> @lang('messages.seller.menu.membership')</a></li>
                            <li><a href="{{url('/seller/notification')}}"><i class="fa fa-bell-o"></i>
                                    @lang('messages.seller.menu.notification') @if($notification_count>0)<span
                                            class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                            style="position: static; right: inherit;  top: inherit; float: right;">{{$notification_count}}</span>@endif
                                </a></li>
                            <li><a href="{{url('/logout')}}"><i
                                            class="fa fa-power-off"></i> @lang('messages.seller.menu.logout')</a></li>
                        </ul>
                    </li>

                @endif



            @else
                <li><a href="{{url('/login')}}"><i class="fa fa-sign-in"></i> @lang('messages.top_menu.login')</a>
                </li>
            @endif

            <?php
            $sub_total = 0;
            $cart_item_count = 0;
            $cart_item_count_total = '';
            if (Auth::user() != null && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER) {
                $cart_items = App\Model\CartItem::where('buyer_id', Auth::user()->getBuyer->id)->get();
                $cart_item_count_total = $cart_items->sum('quantity');
            }
            ?>

            <li><a href="#" data-target=".example-modal-lg" data-toggle="modal"
                   class="font-additional color-main text-uppercase hover-focus-color"><span
                            class="font-icon icon-magnifier" aria-hidden="true"></span></a></li>
            @if(Auth::user() != null && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER)
                <li class="header-cart">
                    <a href="javascript:;" class="font-additional color-main text-uppercase hover-focus-color"
                       style="font-size: 16px;margin-top: 2px;padding-right: 17px;">
                        <span class="font-icon icon-bag" aria-hidden="true"></span>
                        <span class="add-to-cart-qty cart-qty font-main font-weight-semibold color-main customBgColor circle"
                              id="cart_item_count">@if($cart_item_count_total>0){{$cart_item_count_total}}@endif</span>
                    </a>

                    <div class="header-cart_product clearfix">
                        <div class="header-cart_product_div"
                             @if($cart_item_count_total<=0) style="display: none" @endif>
                            <h3 class="font-additional font-weight-bold">@lang('messages.buyer.cart_items')</h3>


                            <ul class="header-cart_product_list cart_show">
                                @if(!empty($cart_items))
                                    @foreach($cart_items as $cart_item)

                                        <?php
                                        $product_price = $cart_item->getProduct->price;
                                        $media = $cart_item->getProduct->getMedia;
                                        $cart_item_count = $cart_item_count + $cart_item->quantity;

                                        $product_deal = $cart_item->getProduct->getProductDeals;
                                        if (isset($product_deal)) {
                                            $discount = $product_deal->discount;
                                            if ($product_deal->discount_type == \App\Http\Controllers\Enum\DiscountTypeEnum::PERCENTAGE) $discount = $cart_item->getProduct->price * ($discount / 100);
                                            if ($discount < 0) $discount = 0;

                                            $product_price = $cart_item->getProduct->price - $discount;
                                        }
                                        ?>


                                        <li>
                                            <div class="header-cart_product_list_item clearfix">
                                                <a class="item-preview"
                                                   href="{{url('/product/details/'.$cart_item->getProduct->id)}}"><img
                                                            @if(!empty($media[0]))
                                                            src="<?=Image::url(asset('uploads/media/' . $media[0]->file_in_disk), 70, 70, ['crop'])?>"
                                                            @else
                                                            src="<?=Image::url(asset('image/no-media.jpg'), 70, 70, ['crop'])?>"
                                                            @endif
                                                            alt="Product"></a>
                                                <h4><a class="font-additional font-weight-normal hover-focus-color"
                                                       href="{{url('/product/details/'.$cart_item->getProduct->id)}}">{{$cart_item->getProduct->name}}</a>
                                                </h4>

                                                <span class="item-cat font-main font-weight-normal"><a
                                                            class="hover-focus-color"
                                                            href="#">{{$cart_item->getProduct->getSeller->getCategory->name}}</a></span>

                                                <span class="item-price font-additional font-weight-normal customColor">
                                                    <div style="font-size: 13px; text-align: right"
                                                         class="product-item_price font-additional font-weight-normal customColor">
                                                        @if($product_price < $cart_item->getProduct->price)
                                                            <span style="font-size: 11px;">{{env('CURRENCY_SYMBOL').number_format($cart_item->getProduct->price,2)}}</span>
                                                        @endif
                                                        {{env('CURRENCY_SYMBOL').number_format($product_price,2)}}
                                                        X {{$cart_item->quantity}}
                                                    </div>

                                                    <a class="item-del hover-focus-color" href="javascript:;"
                                                       data-id="{{Crypt::encrypt($cart_item->id)}}"><i
                                                                class="fa fa-trash-o"></i></a>

                                                    @if($cart_item->getProduct->quantity <= 0)
                                                        <span class=""
                                                              style="color: red; cursor: auto; font-weight: 700;  float: left;">@lang('messages.buyer.product_not_available')</span>
                                                    @endif

                                                </span>
                                            </div>

                                        </li>

                                        <?php
                                        $sub_total = $sub_total + ($product_price * $cart_item->quantity);
                                        ?>
                                    @endforeach
                                @endif
                            </ul>
                            <div class="cart-total text-right font-additional font-weight-normal">
                                @lang('messages.buyer.subtotal') <span
                                        class="customColor subtotal">{{env('CURRENCY_SYMBOL').number_format($sub_total,2)}}</span>
                            </div>
                            <div class="cart-buttons text-right">
                                {!! Form::open(['url'=>'/buyer/delivery-payment']) !!}
                                <a href="{{url('buyer/cart-list')}}"
                                   class="btn btn-white font-additional font-weight-bold hvr-shutter-out-horizontal before-bg">
                                    @lang('messages.buyer.view_cart')
                                </a>
                                <button type="submit"
                                        class="btn btn-white font-additional font-weight-bold hvr-shutter-out-horizontal before-bg">
                                    @lang('messages.buyer.checkout')
                                </button>
                                <input type="hidden" name="checkout" value="1">
                                {!! Form::close() !!}
                            </div>
                        </div>


                        <div class="header-cart_product_no_item" @if($cart_item_count_total>0) style="display: none"
                             @endif style="font-weight: bold">@lang('messages.buyer.no_item_added_yet')
                        </div>


                    </div>
                </li>
            @endif
        </ul>
        <div class="clearfix"></div>
        <nav class="navbar navbar-default visible-xs">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <ul class="mobile_top_nav">
                        <li><a href="#" data-target=".example-modal-lg" data-toggle="modal"
                               class="font-additional color-main text-uppercase hover-focus-color"><span
                                        class="font-icon icon-magnifier" aria-hidden="true"></span></a></li>
                        <li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="#"><span
                                        class="font-icon icon-user" aria-hidden="true"></span></a>
                            <ul role="menu" class="dropdown-menu">
                                @if(!empty(Auth::user()->email) && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER && Auth::user()->status != \App\Http\Controllers\Enum\UserStatusEnum::UNBLOCKED)
                                    <li class="account"><a href="{{url('/buyer/wish-list')}}"><i
                                                    class="fa fa-barcode"></i> @lang('messages.buyer.menu.wish_list') @if(!empty($favorite_products))
                                                <span
                                                        class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                                        style="position: static; right: inherit;  top: inherit; float: right;">{{$favorite_products}}</span>@endif
                                        </a></li>
                                    <li class="account"><a href="{{url('/buyer/order-history')}}"><i
                                                    class="fa fa-shopping-cart"></i> @lang('messages.buyer.menu.order_history')
                                        </a>
                                    </li>
                                    <li class="account"><a href="{{url('/buyer/edit-profile')}}"><i
                                                    class="fa fa-cog"></i> @lang('messages.buyer.menu.edit_profile')</a>
                                    </li>
                                    <li class="account"><a href="{{url('/buyer/notification')}}"><i
                                                    class="fa fa-bell-o"></i>
                                            @lang('messages.buyer.menu.notification') @if($notification_count>0)<span
                                                    class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                                    style="position: static; right: inherit;  top: inherit; float: right;">{{$notification_count}}</span>@endif
                                        </a></li>
                                    <li class="account"><a href="{{url('/logout')}}"><i
                                                    class="fa fa-power-off"></i> @lang('messages.buyer.menu.logout')</a>
                                    </li>
                                @elseif(!empty(Auth::user()->email) && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::SELLER && Auth::user()->status != \App\Http\Controllers\Enum\UserStatusEnum::UNBLOCKED)
                                    <li class="account"><a href="{{url('/seller/edit-profile')}}"><i
                                                    class="fa fa-cog"></i> Edit Profile</a></li>
                                    <li class="account"><a href="{{url('/seller/products')}}"><i
                                                    class="fa fa-circle-o"></i> Products</a></li>
                                    <li class="account"><a href="{{url('/seller/order-list')}}"><i
                                                    class="fa fa-shopping-cart"></i> Order List</a>
                                    </li>
                                    <li class="account"><a href="{{url('/seller/payments')}}"><i class="fa fa-usd"></i>
                                            Payments</a></li>
                                    <li class="account"><a href="{{url('/seller/membership')}}"><i
                                                    class="fa fa-user"></i> Memberships</a></li>
                                    <li class="account"><a href="{{url('/seller/notifications')}}"><i
                                                    class="fa fa-bell-o"></i>
                                            Notification @if($notification_count>0)<span
                                                    class="cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                                    style="position: static; right: inherit;  top: inherit; float: right;">{{$notification_count}}</span>@endif
                                        </a></li>
                                    <li class="account"><a href="{{url('/logout')}}"><i class="fa fa-power-off"></i>
                                            Logout</a></li>
                                @else
                                    <li><a href="{{url('/buyer/login')}}">Signin</a></li>
                                    <li><a href="{{url('/buyer-registration')}}">Register</a></li>
                                @endif
                            </ul>
                        </li>
                        @if(Auth::user() != null && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER)
                            <li class="header-cart" style="">
                                <a href="javascript:;"
                                   class="font-additional color-main text-uppercase hover-focus-color"
                                   style="font-size: 16px;padding-right: 5px;">
                                    <span class="font-icon icon-bag" style="color: #FF8300;" aria-hidden="true"></span>
                                    <span class="add-to-cart-qty cart-qty font-main font-weight-semibold color-main customBgColor circle"
                                          id="cart_item_count">@if($cart_item_count_total>0){{$cart_item_count_total}}@endif</span>
                                </a>

                                <div class="header-cart_product clearfix">
                                    <div class="header-cart_product_div"
                                         @if($cart_item_count_total<=0) style="display: none" @endif>
                                        <h3 class="font-additional font-weight-bold">@lang('messages.buyer.cart_items')</h3>


                                        <ul class="header-cart_product_list cart_show">
                                            @if(!empty($cart_items))
                                                @foreach($cart_items as $cart_item)

                                                    <?php
                                                    $media = $cart_item->getProduct->getMedia;
                                                    $cart_item_count = $cart_item_count + $cart_item->quantity;
                                                    ?>
                                                    <li>
                                                        <div class="header-cart_product_list_item clearfix">
                                                            <a class="item-preview" href="{{url('details/')}}"><img
                                                                        @if(!empty($media[0]))
                                                                        src="<?=Image::url(asset('uploads/media/' . $media[0]->file_in_disk), 70, 70, ['crop'])?>"
                                                                        @else
                                                                        src="<?=Image::url(asset('image/no-media.jpg'), 70, 70, ['crop'])?>"
                                                                        @endif
                                                                        alt="Product"></a>
                                                            <h4>
                                                                <a class="font-additional font-weight-normal hover-focus-color"
                                                                   href="{{url('details')}}">{{$cart_item->getProduct->name}}</a>
                                                            </h4>
                                                            <span class="item-cat font-main font-weight-normal"><a
                                                                        class="hover-focus-color"
                                                                        href="#">{{$cart_item->getProduct->getSeller->getCategory->name}}</a></span>
                                                            <span class="item-price font-additional font-weight-normal customColor">{{$cart_item->getProduct->price}}
                                                                X {{$cart_item->quantity}}
                                                                item<?php if ($cart_item->quantity > 1) echo 's';?></span>
                                                            <a class="item-del hover-focus-color" href="javascript:;"
                                                               data-id="{{Crypt::encrypt($cart_item->id)}}"><i
                                                                        class="fa fa-trash-o"></i></a>

                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <div class="cart-total text-right font-additional font-weight-normal">
                                            @lang('messages.buyer.subtotal') <span
                                                    class="customColor subtotal">${{$sub_total}}</span></div>
                                        <div class="cart-buttons text-right">
                                            {!! Form::open(['url'=>'/buyer/delivery-payment']) !!}
                                            <a href="{{url('buyer/cart-list')}}"
                                               class="btn btn-primary">
                                                @lang('messages.buyer.view_cart')
                                            </a>
                                            <button type="submit" value="on" name="buyer_shipping_address"
                                                    class="btn btn-primary">
                                                @lang('messages.buyer.checkout')
                                            </button>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>


                                    <div class="header-cart_product_no_item"
                                         @if($cart_item_count_total>0) style="display: none"
                                         @endif style="font-weight: bold">@lang('messages.buyer.no_item_added_yet')
                                    </div>


                                </div>
                            </li>
                        @endif
                        <li class="dropdown langs">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <img @if(\App\UtilityFunction::getLocal() == 'en' ) src="{{asset('image/en.jpg')}}"
                                     @else src="{{asset('image/ar.jpg')}}"
                                     @endif alt="" style="height:14px;">
                                <i class="fa fa-angle-down"></i></a>
                            <ul role="menu" class="dropdown-menu">
                                <li><a href="{{url('/switch-language')}}">
                                        <img @if(\App\UtilityFunction::getLocal()=='en') src="{{asset('image/ar.jpg')}}"
                                             @else src="{{asset('image/en.jpg')}}" @endif alt="" style="height: 14px;"></a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <a class="navbar-brand" href="{{url('/')}}"><img alt="Discount Deals, Offers at savetaka"
                                                                     src="{{asset('')}}/image/logo.png"/></a>
                </div>

            <?php
            $categories = \App\Model\ProductCategory::orderBy('name')->where('show_in_public_menu', 1)->get();
            $product_categories = $categories->where('product_category_type_id', \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT);
            $service_categories = $categories->where('product_category_type_id', \App\Http\Controllers\Enum\ProductTypeEnum::SERVICE);
            ?>
            <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse mobile-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li @if(!empty($menu) && $menu == 'home') class="active" @endif><a
                                    href="{{url('/')}}">@lang('messages.menu.home')</a></li>
                        {{--<li @if(!empty($menu) && $menu == 'product') class="active" @endif><a href="{{url('/products')}}">@lang('messages.menu.products')</a></li>--}}
                        <li class="dropdown @if(!empty($menu) && $menu == 'product') open  @endif">
                            <a href="{{url('/products')}}" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">@lang('messages.menu.products')
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu category">
                                @if(isset($product_categories))
                                    @foreach($product_categories as $product_category)
                                        <li><a href="{{url('/products/category/'.$product_category->id)}}">
                                                <span>
                                                    <img src="{{asset('uploads/category/'.$product_category->image)}}"
                                                         alt=""/>@if(\App\UtilityFunction::getLocal() == "en") {{$product_category->name}} @else {{$product_category->ar_name}} @endif
                                                </span>
                                            </a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                        <li class="dropdown @if(!empty($menu) && $menu == 'services') active  @endif">
                            <a href="{{url('/products')}}" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">@lang('messages.menu.services')
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu category">
                                @if(isset($service_categories))
                                    @foreach($service_categories as $service_category)
                                        <li><a href="{{url('/service/category/'.$service_category->id)}}">
                                                <span>
                                                    <img src="{{asset('uploads/category/'.$service_category->image)}}"
                                                         alt=""/>@if(\App\UtilityFunction::getLocal() == "en") {{$service_category->name}} @else {{$service_category->ar_name}} @endif
                                                </span>
                                            </a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                        <li @if(!empty($menu) && $menu == 'deals') class="active" @endif><a
                                    href="{{url('/deals')}}">@lang('messages.menu.deals')</a></li>
                        <li @if(!empty($menu) && $menu == 'stores') class="active" @endif><a
                                    href="{{url('/stores')}}">@lang('messages.menu.stores')</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
    </div>
</div>
<div class="clearfix"></div>
