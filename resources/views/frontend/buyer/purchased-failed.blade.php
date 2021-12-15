@extends('frontend.master',['menu'=>'cart_list'])
@section('title',__('messages.page_title.cart'))
@section('stylesheet')
    <style>
        .payment_failed .fa { color:#f94444; }
        .payment_failed h3 { color: #f94444; }
        .shop .fa { font-size: 120px;  }
        .shop h3 { margin: 35px 0 20px; }
        .shop p { font-size: 15px; color: #999; }
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
                            @lang('messages.buyer.cart.payment_confirmation')
                        </div>
                        <div class="panel-body">
                            <div class="shop text-center payment_failed">
                                <i class="fa fa fa-exclamation-circle" aria-hidden="true"></i>
                                <h3>@lang('messages.buyer.cart.purchase_failed')</h3>
                                <p>@lang('messages.buyer.cart.please_check_email')</p>
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
@stop