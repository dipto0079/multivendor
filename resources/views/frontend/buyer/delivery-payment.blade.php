@extends('frontend.master',['menu'=>'delivery_payment'])
@section('title',__('messages.page_title.cart'))
@section('stylesheet')
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
            height: 37px;
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

        .coupon_code_loader {
            width: 35px;
            height: 35px;
            position: absolute;
            top: 4px;
            left: 44%;
            display: inline-block;
            background-image: url('{{asset('/image/pageloader.gif')}}') !important;
            background-position: right;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .board {
            /*min-height: 500px; */
            background: #fff;
        }

        .board-inner-payment  .nav-tabs {
            position: relative;
            margin-bottom: 0;
            box-sizing: border-box;
            border-bottom-width: 0;
            text-align: center;
        }

        .board-inner-payment  {
            position: relative;
            margin-bottom: 70px;
        }

        .board-inner-payment  .liner {
            height: 2px;
            background: #ddd;
            position: absolute;
            width: 50%;
            margin: 0 auto;
            left: 0;
            right: 0;
            top: 50%;
            z-index: 1;
        }

        .board-inner-payment .nav-tabs > li.active > a,
        .board-inner-payment .nav-tabs > li.active > a:hover,
        .board-inner-payment .nav-tabs > li.active > a:focus {
            color: #555555;
            cursor: default;
            border: 0;
            border-bottom-color: transparent;
        }

        span.round-tabs {
            width: 70px;
            height: 70px;
            line-height: 70px;
            display: inline-block;
            border-radius: 100px;
            background: white;
            z-index: 2;
            position: absolute;
            left: 0;
            text-align: center;
            font-size: 25px;
        }

        span.round-tabs.one {
            color: rgb(34, 194, 34);
            border: 2px solid rgb(34, 194, 34);
        }

        li.active span.round-tabs.one {
            background: #fff !important;
            border: 2px solid #ddd;
            color: rgb(34, 194, 34);
        }

        .board-inner-payment .nav-tabs > li.active > a span.round-tabs {
            background: #fafafa;
        }

        .board-inner-payment .nav-tabs > li {
            width: 25%;
            text-align: center;
            display: inline-block;
            float: none;
        }

        .board-inner-payment .nav-tabs > li .step_text {
            position: relative;
            top: 15px;
        }

        .board-inner-payment .nav-tabs > li a {
            padding: 0;
        }

        .board-inner-payment .nav-tabs > li a:hover {
            background: transparent;
        }

        .board-inner-payment .nav-tabs li .form-tabs_steps {
            width: 32px;
            height: 32px;
            line-height: 20px;
        }

        .board-inner-payment .nav-tabs li.active .form-tabs_steps {
            background-color: #000;
            width: 40px;
            height: 40px;
            line-height: 30px;
            font-size: 20px;
            border-color: #000;
            top: 12px;
            color: #ffffff;
        }

        .board-inner-payment .nav-tabs li.done .form-tabs_steps {
            background-color: #999999;
            border-color: #999999;
            color: #dddddd;
        }

        .board-inner-payment .form-tabs_steps {
            background: #fff;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: inline-block;
            position: relative;
            z-index: 9;
            font-size: 14px;
            padding: 4px;
            border: 1px solid #ddd;
            top: 6px;
            color: #999999;
        }

        .board-inner-payment .step_text {
            color: #999999;
        }

        .board-inner-payment .nav-tabs li.active .step_text {
            color: #000;
            font-size: 16px;
            top: 20px;
        }
        #total_price_info table tbody tr:last-child { border-top: 1px solid #DDDDDD; }
        #total_price_info table tbody tr td { padding: 5px 0; line-height: normal; }
        #total_price_info table tbody tr td strong { color: #FF8300; }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'delivery_payment'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.buyer.cart.cart')
                        </div>
                        <div class="panel-body">
                            <div class="board-inner-payment">
                                <ul class="nav nav-tabs" role="tablist">
                                    <div class="liner"></div>
                                    <li class="done">
                                        <span class="form-tabs_steps"> @lang('messages.buyer.cart.step_1')</span>
                                        <div class="step_text"> @lang('messages.buyer.cart.cart')</div>
                                    </li>
                                    <li class="active">
                                        <span class="form-tabs_steps"> @lang('messages.buyer.cart.step_2')</span>
                                        <div class="step_text"> @lang('messages.buyer.cart.delivery_payment')</div>
                                    </li>
                                    <li class="disabled">
                                        <span class="form-tabs_steps"> @lang('messages.buyer.cart.step_3')</span>
                                        <div class="step_text"> @lang('messages.buyer.cart.thank_you')</div>
                                    </li>
                                </ul>
                            </div>
                            <?php
                                $buyer = Auth::user()->getBuyer;
                            ?>
                            <div class="clearfix"></div>
                            {!! Form::open(['url'=>'/buyer/place-order','id'=>'order_form','files'=>true, 'class'=>"form-horizontal"]) !!}
                            <div class="col-md-8 col-sm-12">
                                <h4>@lang('messages.buyer.cart.delivery_address')</h4>
                                <hr>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">@lang('messages.buyer.country')</label>

                                    <div class="col-sm-8">
                                        <span class="form-control">{{App\Model\Country::getCountryName(Session::get('delivery_info')['country'])}}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">@lang('messages.buyer.city')</label>

                                    <div class="col-sm-8">
                                        <span class="form-control">{{\App\Model\City::getCityName(Session::get('delivery_info')['city'])}}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">@lang('messages.buyer.state')</label>

                                    <div class="col-sm-4">
                                        <span class="form-control">{{Session::get('delivery_info')['state']}}</span>
                                    </div>
                                    <label for="" class="col-sm-1 control-label">@lang('messages.buyer.zip')</label>

                                    <div class="col-sm-3">
                                        <span class="form-control">{{Session::get('delivery_info')['zip']}}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">@lang('messages.buyer.street')</label>

                                    <div class="col-sm-8">
                                        <span class="form-control">{{Session::get('delivery_info')['street']}}</span>
                                    </div>
                                </div>


                                <br>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <h4>@lang('messages.buyer.cart.billing_information')</h4>
                                <hr>
                                <div class="box">
                                    <p>{{Auth::user()->username}}</p>
                                    <p>{{Auth::user()->email}}</p>
                                    <p>{{$buyer->street}}</p>
                                    <p>@if(!empty($buyer->getDistrict)){{$buyer->getDistrict->name}}@endif {{', '.$buyer->state.' '.$buyer->zip}}</p>
                                    <p>@if(!empty($buyer->getCountryName)){{$buyer->getCountryName->name}}@endif</p>
                                    <p>{{Auth::user()->phone}}</p>
                                    <a href="{{url('/buyer/edit-profile')}}"><i class="fa fa-pencil-square-o"></i> @lang('messages.buyer.cart.edit_billing_information')</a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-8 col-sm-12">
                                <h4>@lang('messages.buyer.cart.payment_method')</h4>
                                <hr>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">
                                        <span class="identity-circle"></span>
                                    </label>

                                    <div class="col-sm-8">
                                        <h3>@lang('messages.buyer.cart.credit_debit_card')</h3>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">@lang('messages.buyer.cart.card_no')</label>

                                    <div class="col-sm-8">
                                        <input type="number" min="0" name="card_number" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">@lang('messages.buyer.cart.name_on_card')</label>

                                    <div class="col-sm-8">
                                        <input type="text" name="card_name"  maxlength="25" class="form-control" required>
                                    </div>
                                </div>

                                <?php
                                    $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'Octber', 11 => 'November', 12 => 'December');
                                ?>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">@lang('messages.buyer.cart.expire')</label>

                                    <div class="col-sm-4">
                                        <select name="card_expiration_month" class="form-control" required>
                                            <option value="">@lang('messages.buyer.cart.select_month')</option>
                                            @foreach($months as $key => $month)
                                                <option value="{{sprintf("%02d",$key)}}">{{sprintf("%02d",$key)}} - {{$month}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select name="card_expiration_year" class="form-control" required>
                                            <option value="">@lang('messages.buyer.cart.select_year')</option>
                                            @for($i = date('Y'); $i<=2040; $i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 col-xs-12 control-label">@lang('messages.buyer.cart.security_code')</label>

                                    <div class="col-sm-5 col-xs-8">
                                        <input type="number" min="0" name="security_code" class="form-control" required>
                                    </div>
                                    <i class="glyphicon glyphicon-question-sign" data-toggle="tooltip"
                                       data-placement="top" title="@lang('messages.buyer.cart.cvv_meaning')"
                                       style="font-size: 20px;margin-top: 4px;"></i>
                                </div>

                            </div>
                            <div class="col-md-4 col-sm-12">
                                <h4>@lang('messages.buyer.cart.order_summary')</h4>
                                <hr>
                                <div class="box">
                                    <div id="total_price_info">
                                        {!! $cart_calculation !!}
                                    </div>
                                    <p>
                                        <small>@lang('messages.buyer.cart.thank_you')@lang('messages.buyer.cart.condition_1') <a href="{{url('/term-and-condition')}}">@lang('messages.buyer.cart.tc')</a>
                                            @lang('messages.buyer.cart.condition_2') <a href="{{url('/privacy-policy')}}">@lang('messages.buyer.cart.privacy_policy')</a>.
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                @if($shipping_status == 0)<button type="submit" class="btn btn-primary font-additional pull-right">@lang('messages.buyer.cart.place_your_order') >
                                </button> @endif
                                <input type="hidden" name="coupon" id="coupon" value="{{$coupon}}">
                                <input type="hidden" name="skip" id="skip" value="0">
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
@stop
