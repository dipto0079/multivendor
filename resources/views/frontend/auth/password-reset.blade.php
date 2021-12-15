@extends('frontend.master')
@section('title',__('messages.page_title.password_reset'))
@section('stylesheet')
    <style type="text/css">
        .eternity-form .login-form-section, .eternity-form .forgot-password-section {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
        }
        .btn-group { width: 100%; }
        .btn-group>.btn { width: 50%; }
        .icon_div  { position: relative; }
        .icon_msg { position: absolute; top: 6px; right: 10px; }
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
              <h1 class="signin-title" style="text-align:center">Reset Password</h1>
              <p style="color: red">{{Session::get('message')}}</p>
                <div class="eternity-form">
                    <div class="login-form-section">
                        <ul id="login-dp" class="main-login">
                    <li>
                     <div class="col-sm-12">
                        <div class="col-md-12">
                        {!! Form::open(['url'=>'/password-change']) !!}
                            <div class="form-group">
                                 <label class="sr-only" for="email_id">Password</label>
                                 <input type="password" name="password" class="form-control" id="password" placeholder="Password" autocomplete="off" required="">
                            </div>
                            <div class="form-group">
                                 <label class="sr-only" for="email_id">Confirm Password</label>
                                 <div class="icon_div">
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password" autocomplete="off" required="">
                                    <div class="icon_msg"></div>
                                 </div>
                            </div>

                            </div>
                            <div class="form-group">
                                 <button type="submit" class="btn success btn-block" id="submit_btn" title="">Change Password</button>
                            </div>
                            <input type="hidden" name="type" value="{{base64_encode($email.'::'.$user_type)}}">
                            {!! Form::close() !!}
                        </div>
                        <div class="bottom text-center">
                            New here ? <a style="text-decoration:underline;" href="{{url('/')}}">Sign Up</a>
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
        $('#confirm_password').keyup(function () {
          if ($('#password').val() == $('#confirm_password').val()) {
            $('.icon_msg').html('<i class="fa fa-check"></i>').css('color', '#ff8300');
          } else
            $('.icon_msg').html('<i class="fa fa-times"></i>').css('color', 'red');
        });
    </script>
@stop
