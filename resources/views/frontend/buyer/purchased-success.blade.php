@extends('frontend.master',['menu'=>'cart_list'])
@section('title',__('messages.page_title.cart'))
@section('stylesheet')
    <style>
        .payment_success .fa { color: #10b510; }
        .payment_success h3 { color: #10b510; text-align: center; }
        .shop .fa { font-size: 120px; }
        .shop h3 { margin: 35px 0 20px; }
        .shop p { font-size: 15px; color: #999; text-align: center; }
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
                            <div class="shop text-center payment_success">
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <h3>@lang('messages.buyer.cart.transaction_completed_successfully')</h3>
                                <p>@lang('messages.buyer.cart.your_will_receive_an_email')</p>
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