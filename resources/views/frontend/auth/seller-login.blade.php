@extends('frontend.master')
@section('title',__('messages.page_title.seller_signin'))
@section('stylesheet')
    <style type="text/css">
        .shop form .form-row .required {
            color: #000;
        }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container shop-account">
            <div class="shop text-center">
                <h2 class="shop-account-heading">@lang('messages.seller-login.login')</h2>
                @if(!empty(Session::get('success_message')))
                    <p style="color:green">{{Session::get('success_message')}}</p>
                @endif
                <p style="color:red">{{Session::get('message')}}</p>

                {!! Form::open(array('url'=>'/seller/login','id'=>'seller_login','class'=>"login")) !!}
                <p class="form-row form-row-wide">
                    <label class="">@lang('messages.seller-login.email')</label>
                    <input type="email" required class="input-text" name="email" value=""/>
                </p>

                <p class="form-row form-row-wide">
                    <label class="">@lang('messages.seller-login.password')</label>
                    <input class="input-text" required type="password" name="password"/>
                </p>
                <p class="form-row form-row-wide">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <input type="text" required name="captcha"  placeholder="@lang('messages.captcha')"   id="captcha1" class="form-control no-border" maxlength="6">
                        </div>
                        <div class="col-md-2">
                            <img id="captcha_code1" src="{{url('/captcha-generate1')}}"  style="border: 1px solid #ddd; margin-right: 6px;" />
                                <span style="cursor: pointer;" id="refreshCaptcha1">
                                <i class="fa fa-refresh" id="refresh_icon1"></i>
                                </span>
                        </div>
                    </div>
                </div>
                </p>


                <p class="form-row">
                    <br>
                    <input type="submit" class="button" value="@lang('messages.seller-login.login')"/>
                </p>

                <p class="lost_password">
                    <a href="{{url('/forget?type=1')}}">@lang('messages.seller-login.lost-password')</a>
                </p>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        (function ($) {
            "use strict";


        })(jQuery);

        if(!$("#captcha1").val()) {
            $("#captcha-info").html("(required)");
            valid = false;
        }
        $(document.body).on('click', '#refreshCaptcha1', function () {
            $('#refresh_icon1').addClass('fa-spin');
            var timestamp = new Date().getTime();
            $("#captcha_code1").prop('src','{{url('/captcha-generate1')}}?r='+timestamp);
            $('#refresh_icon1').removeClass('fa-spin');
        });
    </script>
@stop