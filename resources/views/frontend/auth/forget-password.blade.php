@extends('frontend.master')
@section('title',__('messages.page_title.forgot_password'))
@section('stylesheet')
    <style type="text/css">
        .eternity-form .login-form-section, .eternity-form .forgot-password-section {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
        }
        .btn-group { width: 100%; }
        .btn-group>.btn { width: 50%; }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="tab-content tabbox" id="myTabContent">
    <div id="profileedit" class="tab-pane fade active in">
      <div class="user-pannel all-form">
        <div class="row-fluid">
          <div class="col-sm-12">
            <div class="col-sm-6 col-sm-offset-3 box-shadow">
              <h1 class="signin-title" style="text-align:center">@lang('messages.forgot_password.forgot_password')</h1>
                @if(!empty(Session::get('success_message')))
                    <p class="text-center" style="color:green">{{Session::get('success_message')}}</p>
                @endif
              <p class="text-center" style="color: red">{{Session::get('message')}}</p>
                <div class="eternity-form">
                    <div class="login-form-section">
                        <ul id="login-dp" class="main-login">
                    <li>
                     <div class="col-sm-12">
                        <div class="col-md-12">
                        {!! Form::open(['url'=>'/password-email','id'=>'form_submit']) !!}
                            <div class="form-group">
                                 <label class="sr-only" for="email_id">@lang('messages.forgot_password.email')</label>
                                 <input type="email" name="email" class="form-control" id="email_id" placeholder="@lang('messages.forgot_password.email')" autocomplete="off" >
                                 <div class="email_id_msg"></div>
                            </div>
                            <div class="form-group">
                                <div class="btn-group" id="user_type" data-toggle="buttons">
                                    <label id="show" class="btn btn-login-radio btn-default" data-original-title="" title="">@lang('messages.forgot_password.buyer')
                                        <input type="radio" name="type" value="{{App\Http\Controllers\Enum\UserTypeEnum::USER}}" id="option2" autocomplete="off">
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </label>
                        
                                    <label id="hide" class="btn btn-login-radio btn-default" data-original-title="" title="">@lang('messages.forgot_password.seller')
                                        <input type="radio" value="{{App\Http\Controllers\Enum\UserTypeEnum::SELLER}}" name="type" id="option1" autocomplete="off">
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </label>
                                </div>
                                <div class="user_type_msg"></div>
                            </div>
                            <div class="form-group">
                                 <button type="submit" class="btn success btn-block" id="submit_btn" title="">@lang('messages.forgot_password.send')</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="bottom text-center">
                            @lang('messages.forgot_password.new_here') <a style="text-decoration:underline;" href="{{url('/buyer-registration')}}">@lang('messages.forgot_password.sign_up')</a>
                        </div>
                     </div>
                </li>
            </ul>
                
              </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
        </div>
    </div>
    <form action="{{url('/email-exists-checking')}}" id="email_checking">{{csrf_field()}}</form>
@stop

@section('script')
    <script>
        var email_exist = false;
        $('#email_id').keydown(function () {
            var Email = document.getElementById('email_id').value;
            var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

            var testEmailID = regexEmail.test(Email);

            $.ajax({
                type: "POST",
                url: $('#email_checking').attr('action') + '?email=' + Email,
                data: $('#email_checking').serialize(),
                dataType: "json",
                success: function (data) {
                    email_exist = false;
                    if(data.exists == true) {
                        email_exist = true;
                    }
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });

            if (regexEmail.test(Email)) {
                $(".email_id_msg").html('');
                $("#email_id").focus();
                $('#submit_btn').attr('disabled',false);
            }
            else if(email_exist == false){
                $(".email_id_msg").html('');
                $(".email_id_msg").html('@lang('messages.forgot_password.email_not_exists')');
                $('#submit_btn').attr('disabled',true);
            }
            else {
                $(".email_id_msg").html("@lang('messages.forgot_password.correct_email')");
                $('#submit_btn').attr('disabled',true);
                $("#email_id").focus();
            }
        });
        $(document.body).on('submit','#form_submit',function(){
            if($('#email_id').val() == ''){
                $(".email_id_msg").html('');
                $(".email_id_msg").html('@lang('messages.forgot_password.correct_email')');
                return false;
            }
            else if($("#user_type .btn-login-radio").hasClass('active') == false){
                $(".user_type_msg").html('');
                $(".user_type_msg").html('@lang('messages.forgot_password.select_type')');
                return false;
            }
            return true;
        });
        if($("#user_type .btn-login-radio").hasClass('active') == true) $(".user_type_msg").html('');

    </script>
@stop