@extends('frontend.master',['menu'=>'cart_list'])
@section('title',__('messages.page_title.cart'))
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('build/css/separate/vendor/bootstrap-touchspin.min.css')}}">
    <link rel="stylesheet" href="{{asset('build/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('build/css/lib/clockpicker/bootstrap-clockpicker.min.css')}}">
    <style>
        .qty {
            font-family: 'Open Sans', sans-serif;
            box-sizing: border-box;
            font-size: 13.5px;
            letter-spacing: 0;
            padding: 0px 12px 0 12px;
            vertical-align: middle;
            border: 1px solid #dddddd;
            display: inline-block;
            height: 34px;
            border-radius: 3px;
            transition: all .3s linear;
            width: 80px;
        }

        .coupon {
            position: relative;
        }

        .coupon input {
            border: 2px solid #ececec;
            height: 44px;
            padding: 0 10px;
        }

        .cart_totals > h2 {
            text-transform: uppercase;
            font-size: 20px;
            font-weight: 400;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .shop_table {
            width: 100%;
            table-layout: auto !important;
        }

        .product-category {
            color: gray;
            font-size: 11px;
        }

        .product-category span {
            font-weight: bold;
        }

        .checkout_table {
            margin-bottom: 20px;
            border-spacing: 0;
            border-width: 1px 0 0 1px;
            table-layout: auto;
            width: 100%;
            border-top: 2px solid #ff8300;
        }

        .checkout_table tr th {
            font-weight: 600;
            padding: 20px;
            border: 1px solid #f0f0f0;
        }

        .checkout_table tr td {
            vertical-align: middle;
            padding: 20px;
            border: 1px solid #f0f0f0;
        }

        .proceed-to-checkout {
            float: right;
        }

        .discount_info {
            color: #999;
        }

        .popover-content img {
            max-width: inherit !important;
        }

        .label {
            white-space: inherit;
        }

        .cart-category a {
            color: white;
            font-size: 11px !important;
        }

        .cart-category {
            background-color: #46c35f;
            padding: 0px 7px 2px !important;
        }

        .cart-category a:hover {
            color: white !important;
        }

        .order-total th, .order-total td
        {
            font-size: 16px;
        }

        .large th, .large td
        {
            font-size: 22px;
        }

    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'cart_list'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.cart')
                        </div>
                        <div class="panel-body">
                            <div class="shop" id="cart_list_body">
                                <?php $countries = \App\Model\Country::orderBy('name', 'asc')->get(); ?>
                                @if(!empty($cart_items[0]))
                                    {!! Form::open(['url'=>'/buyer/update/cart-list','id'=>'cart_list_form']) !!}
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-remove"></th>
                                            <th class="product-thumbnail" width="80"></th>
                                            <th class="product-name"><span
                                                        class="nobr">@lang('messages.buyer.product')</span></th>
                                            <th style="width:100px" class="product-price text-center"><span
                                                        class="nobr">@lang('messages.buyer.unit_price')</span></th>
                                            <th class="product-stock-stauts" width="150"><span class="nobr">@lang('messages.buyer.qty')
                                                    <i class="glyphicon glyphicon-question-sign" data-toggle="tooltip"
                                                       data-placement="top"
                                                       title="@lang('messages.buyer.cart.qty_message')"
                                                       style="font-size: 16px;margin-top: 4px;"></i></span></th>
                                            <th class="product-add-to-cart">@lang('messages.buyer.total')</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cart_html">
                                        <?php
                                        $cart_item_count = 0;
                                        $sub_total = 0;
                                        $tax = 0;
                                        $seller_id = [];
                                        $first = 0;
                                        $products = [];
                                        ?>
                                        @foreach($cart_items as $cart_item)
                                            <?php

                                            $media = $cart_item->getProduct->getMedia;
                                            $cart_item_count = $cart_item_count + $cart_item->quantity;

                                            if (!in_array($cart_item->getProduct->getSeller->id, $seller_id)) {
                                                $seller_id[] = $cart_item->getProduct->getSeller->id;
                                            }
                                            $product_deal = $cart_item->getProduct->getProductDeals;

                                            $product_price = $cart_item->getProduct->price;
                                            if (isset($product_deal)) {
                                                $discount = $product_deal->discount;
                                                if ($product_deal->discount_type == \App\Http\Controllers\Enum\DiscountTypeEnum::PERCENTAGE) $discount = $cart_item->getProduct->price * ($discount / 100);
                                                if ($discount < 0) $discount = 0;

                                                $product_price = $cart_item->getProduct->price - $discount;
                                            }

                                            $product = $cart_item->getProduct;
                                            $products[] = ['product_id' => $product->id, 'seller_id' => $product->seller_id, 'price' => $product_price * $cart_item->quantity];
                                            ?>
                                            <tr>
                                                <td class="product-remove">
                                                    {{--<a href="{{url('/buyer/remove-cart-list/'.$cart_item->id)}}" class="remove remove_from_wishlist">×</a>--}}
                                                    <a href="javascript:;" data-id="{{Crypt::encrypt($cart_item->id)}}"
                                                       class="item-del remove remove_from_wishlist">×</a>
                                                </td>
                                                <td class="product-thumbnail">
                                                    <a href="{{url('/buyer/remove-cart-list/'.$cart_item->id)}}"
                                                       class="remove remove_from_wishlist visible-xs visible-sm pull-right">×</a>

                                                    @if(!empty($media[0]))
                                                        <span class="img_popover" data-toggle="popover" data-html="true"
                                                              data-trigger="focus"
                                                              data-content="<img src='<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 350, 350, ['crop'])?>' alt='Product-1'>">
                                                        <img width="150" height="150" class="img_background"
                                                             src="{{asset('/image/default.jpg')}}"
                                                             data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 150, 150, ['crop'])?>"
                                                             alt="Product-1">
                                                       </span>
                                                    @else
                                                        <img width="150" height="150" class="img_background"
                                                             src="{{asset('/image/default.jpg')}}"
                                                             data-src="<?=Image::url(asset('/image/default.jpg'), 150, 150, ['crop'])?>"
                                                             alt="Product-1">
                                                    @endif
                                                </td>
                                                <td class="product-name">
                                                    <a style="font-size: 13px;"
                                                       href="{{url('/product/details/'.$cart_item->getProduct->id)}}">@if(\App\UtilityFunction::getLocal()== "en"){{$cart_item->getProduct->name}}@else{{$cart_item->getProduct->ar_name}}@endif</a>

                                                    <div class="product-category">
                                                        <span class="label label-custom label-pill label-success cart-category">
                                                            @if(\App\UtilityFunction::getLocal()== "en")<a
                                                                    href="{{url('/products/category/'.$cart_item->getProduct->getSeller->getCategory->id)}}">{{$cart_item->getProduct->getSeller->getCategory->name}}
                                                                @else{{$cart_item->getProduct->getSeller->getCategory->ar_name}}</a>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </td>
                                                <td style="width:100px" class="product-price">
                                                <span class="amount">
                                                    <div style="font-size: 13px;" class="product-item_price font-additional font-weight-normal customColor">{{env('CURRENCY_SYMBOL').number_format($product_price,2)}}
                                                        @if($product_price < $cart_item->getProduct->price)
                                                            <span style="font-size: 11px;">{{env('CURRENCY_SYMBOL').number_format($cart_item->getProduct->price,2)}}</span>
                                                        @endif
                                                    </div>
                                                </span>
                                                </td>
                                                <td class="product-stock-status">
                                                    <div class="form-group wishlist-in-stock"
                                                         style="margin-bottom: 5px;">
                                                        @if($cart_item->getProduct->quantity > 0)
                                                            <input type="text" class="quantity_input qty" name="qty[]"
                                                                   onpaste="qtyNumber()"
                                                                   max="{{$cart_item->getProduct->quantity}}"
                                                                   data-bts-max="{{$cart_item->getProduct->quantity}}"
                                                                   value="{{$cart_item->quantity}}"
                                                                   data-price="{{$product_price}}">
                                                            <input type="hidden" name="skip[]"
                                                                   value="{{Crypt::encrypt($cart_item->id)}}">
                                                        @else
                                                            <strong style="color: red">@lang('messages.buyer.product_not_available')</strong>
                                                            <div class="clearfix"></div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="product-add-to-cart">
                                                    <span class="amount"> @if($cart_item->getProduct->quantity <= 0)
                                                            0.00 @else {{env('CURRENCY_SYMBOL').number_format($product_price * $cart_item->quantity,2)}} @endif</span>
                                                </td>
                                            </tr>
                                            <?php
                                            // if ($cart_item->getProduct->quantity > $cart_item->quantity)
                                            $sub_total = $sub_total + ($product_price * $cart_item->quantity);
                                            ?>
                                        @endforeach
                                        <?php
                                        $paid_amount = $sub_total;
                                        ?>
                                        </tbody>
                                        <tbody>
                                        <tr>
                                            <td colspan="6" class="actions">

                                                <div class="coupon">
                                                    <label for="coupon_code">@lang('messages.buyer.coupon')</label>
                                                    <input type="text" name="coupon_code" autocomplete="off"
                                                           id="coupon_code"
                                                           @if(!empty(Request::get('coupon_code'))) value="{{Request::get('coupon_code')}}"
                                                           @endif
                                                           placeholder="@lang('messages.buyer.cart_coupon_code')">
                                                    <span id="coupon_code_span" class=""></span>
                                                    <input type="hidden" name="sub_total" id="sub_total"
                                                           value="{{$sub_total}}">
                                                    <button type="button" name="apply_coupon" id="apply_coupon"
                                                            class="btn btn-primary font-additional">
                                                        @lang('messages.buyer.apply_coupon')
                                                    </button>
                                                </div>
                                                <button type="submit" name="update_cart" id="update_cart"
                                                        class="btn btn-primary font-additional">
                                                    @lang('messages.buyer.update_cart')
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    {!! Form::close() !!}
                                    {!! Form::open(['url'=>'/buyer/delivery-payment', 'class'=>"form-horizontal",'id'=>'delivery_form']) !!}
                                    <br>
                                    <h4>@lang('messages.buyer.cart.delivery_address')</h4>
                                    <hr>
                                    <div class="form-group">
                                        <label for=""
                                               class="col-sm-4 control-label">@lang('messages.buyer.cart.ship_to_my_address')</label>
                                        <div class="col-sm-8">
                                            <input type="checkbox"
                                                   class="form-control @if(\App\UtilityFunction::getLocal()== "ar") pull-right @endif"
                                                   id="buyer_shipping_address" name="buyer_shipping_address">
                                        </div>
                                    </div>

                                    <div id="other_shipping_address">
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.country')</label>

                                            <div class="col-sm-8">
                                                <select name="country" id="country_id" required class="form-control">
                                                    <option value="">@lang('messages.select')</option>
                                                    @foreach($countries as $c)
                                                        <option value="{{$c->id}}">@if(\App\UtilityFunction::getLocal()=='en'){{$c->name}} @else {{$c->ar_name}}@endif</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.city')</label>

                                            <div class="col-sm-8">
                                                <select name="city" id="city_id" class="form-control" required
                                                        style="position: static;">
                                                    <option value="">@lang('messages.select')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.state')</label>

                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" name="state" value="" required>
                                            </div>
                                            <label for=""
                                                   class="col-sm-1 control-label">@lang('messages.buyer.zip')</label>

                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="zip" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.street')</label>

                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" required name="street" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="buyer_shipping_address_info" style="display: none;">
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.country')</label>

                                            <div class="col-sm-8">
                                                <span class="form-control">@if (\App\UtilityFunction::getLocal() == "en" && !empty(Auth::user()->getBuyer->getCountryName)) {{Auth::user()->getBuyer->getCountryName->name}}
                                                    @else {{Auth::user()->getBuyer->getCountryName->ar_name}} @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.city')</label>

                                            <div class="col-sm-8">
                                                <span class="form-control">@if (\App\UtilityFunction::getLocal() == "en" && !empty(Auth::user()->getBuyer->getDistrict)) {{Auth::user()->getBuyer->getDistrict->name}}
                                                    @else {{Auth::user()->getBuyer->getDistrict->ar_name}} @endif</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.state')</label>

                                            <div class="col-sm-4">
                                                <span class="form-control">{{Auth::user()->getBuyer->state}}</span>
                                            </div>
                                            <label for=""
                                                   class="col-sm-1 control-label">@lang('messages.buyer.zip')</label>

                                            <div class="col-sm-3">
                                                <span class="form-control">{{Auth::user()->getBuyer->zip}}</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for=""
                                                   class="col-sm-4 control-label">@lang('messages.buyer.street')</label>

                                            <div class="col-sm-8">
                                                <span class="form-control">{{Auth::user()->getBuyer->street}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for=""
                                               class="col-sm-4 col-xs-12 control-label">@lang('messages.buyer.cart.delivery_schedule')</label>
                                        <div class="col-xs-8">
                                            <input type="checkbox" name="checked_d_s" id="delivery_schedule_checkbox"
                                                   class=" @if(\App\UtilityFunction::getLocal()== "ar") pull-right @endif">
                                        </div>
                                    </div>
                                    <div class="form-group" id="delivery_schedule" style="display: none;">
                                        <label for=""
                                               class="col-sm-4 control-label">@lang('messages.buyer.cart.date_time')</label>
                                        <div class="col-sm-4">
                                            <div class="input-group date">
                                                <input type="text" name="delivery_schedule_date"
                                                       class="form-control"><span class="input-group-addon"><i
                                                            class="glyphicon glyphicon-th"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group clockpicker" data-autoclose="true">
                                                <input type="text" name="delivery_schedule_time" class="form-control"
                                                       value="{{date('h:i')}}">
                                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time font-icon"></span>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cart-collaterals" id="cart_amount_calculation">

                                    </div>
                                    <input type="hidden" name="coupon_id" id="coupon_id">
                                    <input type="hidden" name="skip" id="skip" value="0">
                                    {!! Form::close() !!}
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.buyer.no_product_added')</h4></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script type="text/javascript">
        $('.img_popover').popover({title: "", trigger: "hover"});
    </script>
    <script src="{{asset('build/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $('.input-group.date').datepicker({
            format: "dd-mm-yyyy",
            orientation: "bottom left",
            daysOfWeekHighlighted: "5,6",
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });
    </script>
    <script src="{{asset('build/js/lib/clockpicker/bootstrap-clockpicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.clockpicker').clockpicker({
                autoclose: true,
                donetext: 'Done',
                'default': 'now'
            });
        });
        $(document.body).on('click', '#delivery_schedule_checkbox', function () {
            if ($(this).prop('checked')) {
                $('#delivery_schedule').show();
                $('#delivery_schedule input').prop('required', true);
            } else {
                $('#delivery_schedule').hide();
                $('#delivery_schedule input').prop('required', false);
            }
        });
    </script>
    <script src="{{asset('build/js/lib/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
    <script>
        function getTouchSpin() {
            $(".quantity_input").TouchSpin({
                verticalbuttons: true,
                verticalupclass: 'glyphicon glyphicon-plus',
                verticaldownclass: 'glyphicon glyphicon-minus',
                step: 1,
                decimals: 0,
                min: 1
            });
        }
        getTouchSpin();
        function updateCartAmount() {
            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            $('.qty').on('change', function () {
                var qty = $(this).val();
                var price = $(this).data('price');
                var total = numberWithCommas((qty * price).toFixed(2));
                $(this).closest('tr').find('.product-add-to-cart').find('.amount').html('{{env('CURRENCY_SYMBOL')}}' + total);
            });
        }
        updateCartAmount();
    </script>
    <script>
        $(document.body).on('click', '#apply_coupon', function (e) {
            var coupon_code = $('#coupon_code').val();
            var sub_total = $('#sub_total').val();
            var tax = $('#tax').val();
            var shipping_charge = $('#shipping_charge').val();

            var country_id = $('#country_id').val();
            var city_id = $('#city_id').val();

            var buyer_shipping_address = $('#buyer_shipping_address').prop('checked');

            toastr.clear();
            if (coupon_code != '') {
                $('#coupon_code_span').addClass('input_loader');
                $.ajax({
                    type: "GET",
                    url: '{{url('/buyer/apply-coupon')}}' + '?coupon_code=' + coupon_code + '&country=' + country_id + '&city=' + city_id + '&buyer_shipping_address=' + buyer_shipping_address,
                    dataType: "json",
                    success: function (data) {
                        $('#coupon_code_span').removeClass('input_loader');
                        if (data.success == true) {
                            $('#coupon_id').val(data.coupon_id);

                            if (buyer_shipping_address || (country_id != '' && city_id != '') || data.data_generate != '') {
                                $('#cart_amount_calculation').empty();
                                $('#cart_html').empty();
                                //$('#total_price_info').html(data.data_generate);
                                $('#cart_amount_calculation').html(data.data_generate);
                                $('#cart_html').html(data.cart_html);
                                getTouchSpin();
                                updateCartAmount();
                            }

                            toastr.success('@lang('messages.buyer.coupon_is_activated')');
                            $('#coupon_code').val('');
                        } else {
                            toastr.warning('@lang('messages.buyer.invalid_coupon')');
                        }
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            }
            else {
                toastr.warning('Coupon field is empty');
            }

        })
    </script>
    <script>
        $(document.body).on('change', '#country_id', function () {
            var country_id = $(this).val();
            $('#skip').val(1);
            $('#city_id').addClass('input_loader');
            $.ajax({
                type: 'POST',
                url: $('#delivery_form').attr('action') + '?country_id=' + country_id,
                data: $('#delivery_form').serialize(),
                dataType: 'json',
                success: function (data) {
                    $('#city_id').removeClass('input_loader');
                    $('#skip').val(0);
                    $('#city_id').empty();

                    $('#city_id').append('<option value="">@lang('messages.select')</option>');
                    $('#city_id').append(data.cities_html);
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            })
        });
    </script>
    <script>
        $(document.body).on('click', '#buyer_shipping_address', function () {
            if ($(this).is(":checked")) {
                $('#other_shipping_address').hide();
                $('#buyer_shipping_address_info').show();
                $('#other_shipping_address input').prop('required', false).val('');
                $('#other_shipping_address select').prop('required', false).val('');

                var coupon = $('#coupon_id').val();
                $.ajax({
                    type: 'GET',
                    url: '{{url('/buyer/shipping-calculation')}}?coupon=' + coupon,
                    dataType: 'json',
                    success: function (data) {
                        $('#cart_amount_calculation').empty();
                        $('#cart_html').empty();
                        //$('#total_price_info').html(data.data_generate);
                        $('#cart_amount_calculation').html(data.data_generate);
                        $('#cart_html').html(data.cart_html);
                        getTouchSpin();
                        updateCartAmount();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            } else {
                $('#total_price_info').empty();
                $('#other_shipping_address').show();
                $('#buyer_shipping_address_info').hide();
                $('#other_shipping_address input').prop('required', true);
                $('#other_shipping_address select').prop('required', true);
            }
        });
        $(document.body).on('change', '#city_id', function () {
            var coupon = $('#coupon_id').val();
            var country_id = $('#country_id').val();
            var city_id = $('#city_id').val();
            $.ajax({
                type: 'GET',
                url: '{{url('/buyer/shipping-calculation')}}?coupon=' + coupon + '&country=' + country_id + '&city=' + city_id,
                dataType: 'json',
                success: function (data) {
                    $('#cart_amount_calculation').empty();
                    $('#cart_html').empty();
                    //$('#total_price_info').html(data.data_generate);
                    $('#cart_amount_calculation').html(data.data_generate);
                    $('#cart_html').html(data.cart_html);
                    getTouchSpin();
                    updateCartAmount();
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            })
        });
    </script>
    <script>
        function qtyNumber() {
            $(this).bind("cut copy paste", function (e) {
                e.preventDefault();
            });
        }
    </script>
@stop
