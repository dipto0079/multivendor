@extends('frontend.master')
@section('title',__('messages.page_title.buyer_registration'))
@section('stylesheet')
    <link rel="stylesheet" type="text/css" href="{{asset('css')}}/bootstrap-social.css" />
    <style type="text/css">
        .site-main {
            margin-top: 30px;
        }

        body .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        div.required .control-label::before {
            color: #f00;
            content: "* ";
            font-weight: bold;
        }
        .success {
            background-color: #ff8300;
            color: #FFF;
            /*font-family: 'open_sanssemibold';*/
            border-radius: 3px;
        }
        .buttons .right {
            float: right;
            text-align: right;
        }
        .form-group {
            width: auto;
        }

        .error_form{
            border: 1px solid red;
        }
    </style>
@stop











@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="tab-content tabbox" id="myTabContent">
                <div id="profileedit" class="tab-pane fade active in">
                    <div class="user-pannel all-forms">
                        <div class="container-fluid">
                            <div class="row login-box">
                                <div class="col-sm-6">
                                    <div class="page-header"><h3>@lang('messages.buyer_registration.buyer_signup')</h3></div>
                                    {!! Form::open(['url'=>'/buyer-registration/save','class'=>'form-horizontal','id'=>'buyer_registration_form']) !!}
                                    {{--<form action="#" method="post" accept-charset="utf-8" class="form-horizontal">--}}
                                            <div class="form-group required">
                                                <label for="Name" class="col-sm-4 control-label">@lang('messages.buyer_registration.name') </label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="name" id="name" type="text" value="" >
                                                </div>
                                            </div>
                                            <div class="form-group required">
                                                <label for="Email" class="col-sm-4 control-label">@lang('messages.buyer_registration.email') </label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="email" id="email" value="" >
                                                </div>
                                            </div>
                                            <div class="form-group required">
                                                <label for="Mobile" class="col-sm-4 control-label">@lang('messages.buyer_registration.mobile') </label>
                                                <div class="col-sm-8">
                                                    {{--<div class="input-group">--}}
                                                        {{--<span class="input-group-addon" id="basic-addon1"></span>--}}
                                                        {{--<input class="form-control mobile" name="mobile" maxlength="11" id="mobile" type="text" value="" >--}}
                                                        <input class="form-control" name="mobile" maxlength="11" id="mobile" type="text" value="" >
                                                    {{--</div>--}}
                                                    {{--<div class="help-block">You can receive sms voucher for selected deals on your mobile phone directly</div>--}}
                                                </div>
                                            </div>

                                            <div class="controls">
                                                <div class="form-group required">
                                                    <label for="Password" class="col-sm-4 control-label">@lang('messages.buyer_registration.password') </label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" autocomplete="off" name="password" id="password" type="password" value="" >
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="Password Confirm" class="col-sm-4 control-label">@lang('messages.buyer_registration.confirm_password') </label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" autocomplete="off" name="password_confirm" id="password_confirm" type="password" value="">
                                                    </div>
                                                </div>
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <span>@lang('messages.buyer_registration.by_clicking') <a style="text-decoration:underline;" target="_blank" alt="@lang('messages.buyer_registration.tc')" href="{{url('term-and-condition')}}" class="colorbox cboxElement">@lang('messages.buyer_registration.tc')</a></span>
                                                </div>
                                            </div>
                                            <div class="buttons">
                                                <div class="right">
                                                    <label class="checkbox-inline">
                                                        <button type="submit" class="btn success">@lang('messages.buyer_registration.sign_up')
                                                        </button>
                                                    </label>
                                                </div>
                                            </div>
                                    {{--</form>--}}
                                    {!! Form::close() !!}
                                    <div class="clearfix"></div>
                                    <div class="col-sm-12">
                                        <p class="lines text-center"> @lang('messages.buyer_registration.or')</p>
                                        <p class="text-center">@lang('messages.buyer_registration.already_sign_in') <a href="{{url('/buyer/login')}}"> @lang('messages.buyer_registration.sign_in')</a></p>
                                    </div>
                                </div>
                                <div class="col-sm-1 reg-devider"
                                @if (\App\UtilityFunction::getLocal() == 'ar')     
                                style="border-left:1px solid #999;min-height:400px"></div>
                                @else
                                style="border-right:1px solid #999;min-height:400px"></div>
                                @endif
                                <div class="col-sm-1"></div>
                                <div class="col-sm-4">
                                    <div class="page-header"><h3>@lang('messages.buyer_registration.recommended')</h3></div>
                                    <a href="{{url('login/facebook')}}" class="btn btn-block btn-social btn-facebook btn-lg"><span class="fa fa-facebook"></span> @lang('messages.buyer_registration.facebook_signup')</a>
                                    <a href="{{url('login/google')}}" class="btn btn-block btn-social btn-google-plus btn-lg"><span class="fa fa-google-plus"></span> @lang('messages.buyer_registration.google_signup')</a>
                                </div>
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
    @if(!empty(Session::get('error_msg')))
        <script>
            toastr.warning('{{Session::get('error_msg')}}');
        </script>
        {{ Session::flush() }}
    @endif



    <script>

        $(document).ready(function(){
            $(document.body).on('submit','#buyer_registration_form',function(e) {
                var password = $('#password').val();
                var con_password=$('#password_confirm').val();
                var name=$('#name').val();
                var email=$('#email').val();
                var mobile=$('#mobile').val();
                if(password==''){$('#password').addClass('error_form');e.preventDefault();}
                if(con_password==''){ $('#password_confirm').addClass('error_form');e.preventDefault();}
                if(name==''){ $('#name').addClass('error_form');e.preventDefault();}
                if(email==''){ $('#email').addClass('error_form');e.preventDefault();}
                if(mobile==''){ $('#mobile').addClass('error_form');e.preventDefault();}
                if(password!=con_password){toastr.warning('Password Not Match');e.preventDefault();}
            });
            $(document.body).on('keyup','input',function(e) {
                var password = $('#password').val();
                var con_password=$('#password_confirm').val();
                var name=$('#name').val();
                var email=$('#email').val();
                var mobile=$('#mobile').val();
                if(password!=''){$('#password').removeClass('error_form');}
                if(con_password!=''){ $('#password_confirm').removeClass('error_form');}
                if(name!=''){ $('#name').removeClass('error_form');}
                if(email!=''){ $('#email').removeClass('error_form');}
                if(mobile!=''){ $('#mobile').removeClass('error_form');}
            });
        });


//        $(document).ready(function(){
//           $(document.body).on('submit','#buyer_registration_form',function(e){
//
//               var text_input_count = $('input[type="text"]').length;
////               if()
//
//               for(var i=0; i<text_input_count;i++){
//                   var text_input_val='';
//                   text_input_val = $('input[type="text"]').val();
//                   alert(text_input_val);
//                   if(text_input_val==''){
//                       $('input[type="text"]').addClass('error_form');
//                       e.preventDefault();
//                   }
//               }
////               alert(text_input);
//           });
//        });

    </script>
@stop
