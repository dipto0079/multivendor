<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>70doors - Admin Login</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{asset('image/favicon.png')}}"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/pages/login.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/main.css">
</head>
<body>

<div class="page-center">
    <div class="page-center-in">
        <div class="container-fluid">
            {!! Form::open(array('url'=>'/admin/login', 'class'=>"sign-box" )) !!}
                {{ csrf_field() }}
                <div class="sign-avatar">
                    <img src="{{asset('/build')}}/img/avatar-sign.png" alt="">
                </div>
                <header class="sign-title">Sign In</header>
                @if(!empty(Session::get('message')))
                    <div class="alert alert-danger">
                        {{Session::get('message')}}
                    </div>
                @endif
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    {{--<input type="text" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"/>--}}
                    <input type="text" class="form-control" placeholder="Email" name="email" value="alamgir@aitl.com" required/>
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    {{--<input type="password" class="form-control" placeholder="Password" name="password" required/>--}}
                    <input type="password" class="form-control" placeholder="Password" value="123" name="password" required/>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-7">
                            <input type="text" name="captcha"  placeholder="Captcha"   id="captcha" class="form-control no-border" maxlength="6">
                        </div>
                        <div class="col-xs-5">
                        <span id="">
                                <img id="captcha_code" src="{{url('/captcha-generate')}}"  style="border: 1px solid #ddd; margin-right: 6px;" />
                            </span>

                            <span style="cursor: pointer;" id="refreshCaptcha">
                                <i class="fa fa-refresh" id="refresh_icon"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {{--<div class="checkbox float-left">--}}
                        {{--<input type="checkbox" id="signed-in"/>--}}
                        {{--<label for="signed-in">Keep me signed in</label>--}}
                    {{--</div>--}}
                    <div class="float-right reset">
                        {{--<a href="reset">Reset Password</a>--}}
                    </div>
                </div>
                <button type="submit" class="btn btn-rounded">Sign in</button>
                {{--<p class="sign-note">New to our website? <a href="sign-up.html">Sign up</a></p>--}}
                <!--<button type="button" class="close">
                    <span aria-hidden="true">&times;</span>
                </button>-->
            {!! Form::close() !!}
        </div>
    </div>
</div><!--.page-center-->


<script src="{{asset('/build')}}/js/lib/jquery/jquery.min.js"></script>
<script src="{{asset('/build')}}/js/lib/tether/tether.min.js"></script>
<script src="{{asset('/build')}}/js/lib/bootstrap/bootstrap.min.js"></script>
<script src="{{asset('/build')}}/js/plugins.js"></script>
<script type="text/javascript" src="{{asset('/build')}}/js/lib/match-height/jquery.matchHeight.min.js"></script>
<script>
    $(function() {
        $('.page-center').matchHeight({
            target: $('html')
        });

        $(window).resize(function(){
            setTimeout(function(){
                $('.page-center').matchHeight({ remove: true });
                $('.page-center').matchHeight({
                    target: $('html')
                });
            },100);
        });
    });
</script>
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
<script src="{{asset('/build')}}/js/app.js"></script>
</body>
</html>





{{--<div class="container">--}}
    {{--<div class="row">--}}
        {{--<div class="col-md-8 col-md-offset-2">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">Login</div>--}}
                {{--<div class="panel-body">--}}
                    {{--<form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">--}}
                        {{--{{ csrf_field() }}--}}

                        {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
                            {{--<label for="email" class="col-md-4 control-label">E-Mail Address</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>--}}

                                {{--@if ($errors->has('email'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('email') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">--}}
                            {{--<label for="password" class="col-md-4 control-label">Password</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="password" type="password" class="form-control" name="password" required>--}}

                                {{--@if ($errors->has('password'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('password') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<div class="checkbox">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-8 col-md-offset-4">--}}
                                {{--<button type="submit" class="btn btn-primary">--}}
                                    {{--Login--}}
                                {{--</button>--}}

                                {{--<a class="btn btn-link" href="{{ route('password.request') }}">--}}
                                    {{--Forgot Your Password?--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
                {{--<div class="clearfix"></div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}
