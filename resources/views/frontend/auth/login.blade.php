@extends('frontend.master')
@section('title',__('messages.page_title.signin'))
@section('stylesheet')
    <link rel="stylesheet" type="text/css" href="{{asset('css')}}/bootstrap-social.css"/>
    <style type="text/css">
        .shop form .form-row .required {
            color: #000;
        }

        #social a:hover {
            color: #fff !important;
        }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container shop-account">
            <div class="shop text-center">
                <h2 class="shop-account-heading">@lang('messages.login.login')</h2>

                <div class="col-sm-6" style="border-right: 1px solid #dddddd;">
                  <h2 class="shop-account-heading">@lang('messages.login_buyer')</h2>
                  @if(!empty(Session::get('success_message')))
                      <p style="color:green">{{Session::get('success_message')}}</p>
                  @endif
                  <p style="color:red">{{Session::get('buyer_message')}}</p>

                  {!! Form::open(array('url'=>'/buyer/login','id'=>'buyer_login','class'=>"login",'style'=>'padding: 0;')) !!}
                  <p class="form-row form-row-wide">
                      <label class="">@lang('messages.login.email')</label>
                      <input type="email" class="input-text" required name="email" value=""/>
                  </p>

                  <p class="form-row form-row-wide">
                      <label class="">@lang('messages.login.password')</label>
                      <input class="input-text" type="password" required name="password"/>
                  </p>
                    <p class="form-row form-row-wide">
                     <div class="row">
                        <div class="form-group">
                            <div class="col-md-6">
                                <input type="text" required name="captcha"  placeholder="@lang('messages.captcha')"   id="captcha" class="form-control no-border" maxlength="6">
                            </div>
                            <div class="col-md-6">
                                <img id="captcha_code" src="{{url('/captcha-generate')}}"  style="border: 1px solid #ddd; margin-right: 6px;" />
                                <span style="cursor: pointer;" id="refreshCaptcha">
                                <i class="fa fa-refresh" id="refresh_icon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                  </p>

                  <p class="form-row">
                      <br>
                      <input type="submit" class="button" value="@lang('messages.login.login')"/>
                  </p>

                  <p class="lost_password">
                      <a href="{{url('/forget')}}">@lang('messages.login.lost_password')</a>
                  </p>
                  <div class="user-login-or"><span>or</span></div>
                  <div id="social" style="margin-top: 20px;text-align:center;">
                      <a class="btn-login-facebook" href="{{url('login/facebook')}}">
                          <i class="fa fa-facebook"></i>@lang('messages.login.facebook_login')
                      </a>
                      <a href="{{url('login/google')}}" class="btn-login-facebook" style="background: #dd4b39">
                          <i class="fa fa-google-plus"></i>@lang('messages.login.google_login')
                      </a>
                  </div>

                  {!! Form::close() !!}
                </div>
                <div class="col-sm-6">
                  <h2 class="shop-account-heading">@lang('messages.login_seller')</h2>
                  @if(!empty(Session::get('success_message')))
                      <p style="color:green">{{Session::get('success_message')}}</p>
                  @endif
                  <p style="color:red">{{Session::get('message')}}</p>

                  {!! Form::open(array('url'=>'/seller/login','id'=>'seller_login','class'=>"login",'style'=>'padding: 0;')) !!}
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
                            <div class="col-md-6">
                                <input type="text" required name="captcha"  placeholder="@lang('messages.captcha')"   id="captcha1" class="form-control no-border" maxlength="6">
                            </div>
                            <div class="col-md-6">
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
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    @if(!empty(Session::get('error_msg')))
        <script>
            toastr.warning('{{Session::get('error_msg')}}');
        </script>
    @endif
    <script>
        if(!$("#captcha").val()) {
            $("#captcha-info").html("(required)");
            valid = false;
        }
        $(document.body).on('click', '#refreshCaptcha', function () {
            $('#refresh_icon').addClass('fa-spin');
            var timestamp = new Date().getTime();
            $("#captcha_code").prop('src','{{url('/captcha-generate')}}?r='+timestamp);
            $('#refresh_icon').removeClass('fa-spin');
        });
    </script>
    <script>
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
