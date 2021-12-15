<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{env('APP_FULL_NAME')}} - @yield('title')</title>

    {{--<link href="img/favicon.144x144.png" rel="apple-touch-icon" type="image/png" sizes="144x144">--}}
    {{--<link href="img/favicon.114x114.png" rel="apple-touch-icon" type="image/png" sizes="114x114">--}}
    {{--<link href="img/favicon.72x72.png" rel="apple-touch-icon" type="image/png" sizes="72x72">--}}
    {{--<link href="img/favicon.57x57.png" rel="apple-touch-icon" type="image/png">--}}
    {{--<link href="img/favicon.png" rel="icon" type="image/png">--}}
    {{--<link href="img/favicon.ico" rel="shortcut icon">--}}

    <link rel="shortcut icon" type="image/x-icon" href="{{asset('image/favicon.png')}}"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="{{asset('/build')}}/css/lib/lobipanel/lobipanel.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/vendor/lobipanel.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/lib/jqueryui/jquery-ui.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/pages/widgets.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/lib/bootstrap/bootstrap.min.css">

    <link rel="stylesheet" href="{{asset('/')}}/css/style.css">

    <link rel="stylesheet" href="{{asset('/')}}/css/admin.css">
    <link rel="stylesheet" href="{{asset('/build')}}/js/lib/toastr/toastr.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/vendor/bootstrap-select/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/vendor/select2.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/vendor/typeahead.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/lib/datatables-net/datatables.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/vendor/datatables-net.min.css">
    <style>
        .control-panel .page-content {
            padding-right: 0;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #d8e2e7;
        }

        .required::after {
            content: "*";
            color: #ff0000;
            font-size: 20px;
            font-weight: 600;
            line-height: 1;
            margin-left: 3px;
        }

        .dt-bootstrap .pagination .paginate_button {
            display: inherit !important;
            float: left;
        }

        .dt-bootstrap .pagination .paginate_button a {
            background-color: #ffffff;
        }

        .site-header .header-alarm:after {
            border: 0;
        }

        #back-top {
            width: 30px;
            height: 30px;
            text-align: center;
            position: fixed;
            bottom: 10px;
            right: 10px;
            cursor: pointer;
            border: 3px solid #ff8300;
            border-radius: 100%;
            -webkit-border-radius: 100%;
            -moz-border-radius: 100%;
            -o-border-radius: 100%;
            color: #ff8300;
            z-index: 100;
        }

        #back-top i {
            font-size: 22px;
        }

        .loading {
            width: 100%;
            min-height: 100%;
            position: absolute;
            z-index: 100;
            background-color: rgba(255, 255, 255, .9);
            text-align: center;
        }
    </style>
    <script>
        function yesDetete() {
            return confirm('Are You Sure You Want To Delete?')
        }
    </script>

    <link rel="stylesheet" href="{{asset('/build')}}/css/main.css">
    @yield('stylesheet')


</head>
<body onload="refresh()" class="with-side-menu-compact with-side-menu-addl">

<header class="site-header">
    <div class="container-fluid">

        <a href="{{url('/admin/dashboard')}}" class="site-logo">
            <img class="hidden-md-down" src="{{url('/image/logo_admin.png')}}" alt="">
            <img class="hidden-lg-up" src="{{asset('/build')}}/img/logo-2-mob.png" alt="">
        </a>

        {{--<button id="show-hide-sidebar-toggle" class="show-hide-sidebar">--}}
        {{--<span>toggle menu</span>--}}
        {{--</button>--}}

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>
        <div class="site-header-content">
            <div class="site-header-content-in" style="margin-left: 77px;">
                <div class="site-header-shown">
                    @if(!empty($user_name))
                        <span class="pull-left">{{$user_name}}</span>
                    @endif
                    <?php
                    $questions = \App\Model\Question::orderBy('created_at', 'desc')->where('is_reviewed', 0)->get();
                    $question_answers = \App\Model\QuestionAnswer::orderBy('created_at', 'desc')->where('is_viewed', 0)->get();
                    ?>
                    <div class="dropdown dropdown-notification messages">
                        <a href="#"
                           class="header-alarm dropdown-toggle @if(!empty(count($questions)) || !empty(count($question_answers))) active @endif"
                           id="dd-messages"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false">
                            <i class="font-icon-alarm"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-messages"
                             aria-labelledby="dd-messages">
                            <div class="dropdown-menu-messages-header">
                                <ul class="nav" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active"
                                           data-toggle="tab"
                                           href="#tab-incoming"
                                           role="tab" id="question_count">
                                            Question
                                            @if(!empty(count($questions)))
                                                <span class="label label-pill label-danger">{{count($questions)}}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link"
                                           data-toggle="tab"
                                           href="#tab-outgoing"
                                           role="tab" id="question_answer_count">Reply
                                            @if(!empty(count($question_answers)))
                                                <span class="label label-pill label-danger">{{count($question_answers)}}</span>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                                <!--<button type="button" class="create">
                                    <i class="font-icon font-icon-pen-square"></i>
                                </button>-->
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-incoming" role="tabpanel">
                                    <div class="dropdown-menu-messages-list" id="questions">
                                        @if(!empty(count($questions)))
                                            @foreach($questions as $question)
                                                <a href="{{url('/admin/settings/question/details/'.$question->id)}}"
                                                   class="mess-item">
                                                    <span class="avatar-preview avatar-preview-32">
                                                        <img @if(!empty($question->getUser->photo))
                                                             src="{{asset(env('USER_PHOTO_PATH').$question->getUser->photo)}}"
                                                             @else
                                                             src="{{asset('/image/default_author.png')}}"
                                                             @endif alt="">
                                                    </span>
                                                    <span class="mess-item-name">{{$question->getUser->username}}</span>
                                                    <span class="mess-item-txt">{{str_limit($question->title,35)}}</span>
                                                    <span class="mess-item-txt">{{Carbon\Carbon::createFromTimeStamp(strtotime($question->created_at))->diffForHumans()}}</span>
                                                </a>
                                            @endforeach
                                        @else
                                            <h5 class="text-center">No Question Avaiable</h5>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-outgoing" role="tabpanel">
                                    <div class="dropdown-menu-messages-list" id="question_answers">
                                        @if(!empty(count($question_answers)))
                                            @foreach($question_answers as $question_answer)
                                                <a href="{{url('/admin/settings/question/details/'.$question_answer->getQuestion->id)}}"
                                                   class="mess-item">
                                                    <span class="avatar-preview avatar-preview-32">
                                                        <img @if(!empty($question_answer->getUser->photo))
                                                             src="{{asset(env('USER_PHOTO_PATH').$question_answer->getUser->photo)}}"
                                                             @else
                                                             src="{{asset('/image/default_author.png')}}"
                                                             @endif alt="">
                                                    </span>
                                                    <span class="mess-item-name">{{$question_answer->getUser->username}}</span>
                                                    <span class="mess-item-txt">{{str_limit($question_answer->answer,35)}}</span>
                                                    <span class="mess-item-txt">{{Carbon\Carbon::createFromTimeStamp(strtotime($question_answer->created_at))->diffForHumans()}}</span>
                                                </a>
                                            @endforeach
                                        @else
                                            <h5 class="text-center">No Reply Avaiable</h5>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-menu-notif-more">
                                <a href="{{url('/admin/settings/question')}}">See more</a>
                            </div>
                        </div>
                    </div>


                    <div class="dropdown user-menu">
                        <button class="dropdown-toggle" style="font-size:18px;" id="dd-user-menu" type="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{Auth::user()->username}}<img @if(!empty(Auth::user()->photo))
                                                           src="{{asset(env('USER_PHOTO_PATH').Auth::user()->photo)}}"
                                                           @else
                                                           src="{{asset('/build')}}/img/avatar-2-64.png"
                                                           @endif alt="">
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                            <a class="dropdown-item" href="#site_admin_profile_update" data-toggle="modal"><span
                                        class="font-icon glyphicon glyphicon-user"></span>Profile</a>
                            {{--<a class="dropdown-item" href="#"><span class="font-icon glyphicon glyphicon-cog"></span>{{trans('messages.common.settings')}}</a>--}}
                            {{--<a class="dropdown-item" href="#"><span class="font-icon glyphicon glyphicon-question-sign"></span>{{trans('messages.common.help')}}</a>--}}
                            <div class="dropdown-divider"></div>


                            {{--<a href="http://localhost/bdshorts/public/logout" onclick="event.preventDefault();--}}
                            {{--document.getElementById('logout-form').submit();">--}}
                            {{--Logout--}}
                            {{--</a>--}}

                            {!! Form::open(array('url'=>'/admin/logout','id'=>'logout-form')) !!}
                            <a class="dropdown-item" href="{{url('/admin/logout')}}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span
                                        class="font-icon glyphicon glyphicon-log-out"></span>Logout</a>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <button type="button" class="burger-right">
                        <i class="font-icon-menu-addl"></i>
                    </button>
                </div><!--.site-header-shown-->

                <div class="mobile-menu-right-overlay"></div>
                {{--<div class="site-header-collapsed">--}}
                {{--<div class="site-header-collapsed-in">--}}
                {{--<div class="dropdown dropdown-typical">--}}
                {{--<div class="dropdown-menu" aria-labelledby="dd-header-sales">--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="dropdown dropdown-typical">--}}
                {{--<a class="dropdown-toggle" id="dd-header-marketing" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                {{--<span class="font-icon font-icon-cogwheel"></span>--}}
                {{--<span class="lbl" style="display: inline"> Settings</span>--}}
                {{--</a>--}}

                {{--<div class="dropdown-menu" aria-labelledby="dd-header-marketing">--}}
                {{--<a class="dropdown-item" href="{{url('/admin/department/list')}}"><span class="font-icon font-icon-cart"></span> Department</a>--}}
                {{--<a class="dropdown-item" href="{{url('/admin/designation/list')}}"><span class="font-icon font-icon-cart"></span> Designation</a>--}}
                {{--<div class="dropdown-divider"></div>--}}
                {{--<div class="dropdown-header">Recent issues</div>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>--}}
                {{--<div class="dropdown-more">--}}
                {{--<div class="dropdown-more-caption padding">more...</div>--}}
                {{--<div class="dropdown-more-sub">--}}
                {{--<div class="dropdown-more-sub-in">--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="dropdown-divider"></div>--}}
                {{--<a class="dropdown-item" href="#">Import Issues from CSV</a>--}}
                {{--<div class="dropdown-divider"></div>--}}
                {{--<div class="dropdown-header">Filters</div>--}}
                {{--<a class="dropdown-item" href="#">My Open Issues</a>--}}
                {{--<a class="dropdown-item" href="#">Reported by Me</a>--}}
                {{--<div class="dropdown-divider"></div>--}}
                {{--<a class="dropdown-item" href="#">Manage filters</a>--}}
                {{--<div class="dropdown-divider"></div>--}}
                {{--<div class="dropdown-header">Timesheet</div>--}}
                {{--<a class="dropdown-item" href="#">Subscribtions</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="dropdown dropdown-typical">--}}
                {{--<a class="dropdown-toggle" id="dd-header-social" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                {{--<span class="font-icon font-icon-share"></span>--}}
                {{--<span class="lbl">Social media</span>--}}
                {{--</a>--}}

                {{--<div class="dropdown-menu" aria-labelledby="dd-header-social">--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="dropdown dropdown-typical">--}}
                {{--<a href="#" class="dropdown-toggle no-arr">--}}
                {{--<span class="font-icon font-icon-page"></span>--}}
                {{--<span class="lbl">Projects</span>--}}
                {{--<span class="label label-pill label-danger">35</span>--}}
                {{--</a>--}}
                {{--</div>--}}

                {{--<div class="dropdown dropdown-typical">--}}
                {{--<a class="dropdown-toggle" id="dd-header-form-builder" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                {{--<span class="font-icon font-icon-pencil"></span>--}}
                {{--<span class="lbl">Form builder</span>--}}
                {{--</a>--}}

                {{--<div class="dropdown-menu" aria-labelledby="dd-header-form-builder">--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>--}}
                {{--<a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="dropdown">--}}
                {{--<button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                {{--Add--}}
                {{--</button>--}}
                {{--<div class="dropdown-menu" aria-labelledby="dd-header-add">--}}
                {{--<a class="dropdown-item" href="#">Quant and Verbal</a>--}}
                {{--<a class="dropdown-item" href="#">Real Gmat Test</a>--}}
                {{--<a class="dropdown-item" href="#">Prep Official App</a>--}}
                {{--<a class="dropdown-item" href="#">CATprer Test</a>--}}
                {{--<a class="dropdown-item" href="#">Third Party Test</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="help-dropdown">--}}
                {{--<button type="button">--}}
                {{--<i class="font-icon font-icon-help"></i>--}}
                {{--</button>--}}
                {{--<div class="help-dropdown-popup">--}}
                {{--<div class="help-dropdown-popup-side">--}}
                {{--<ul>--}}
                {{--<li><a href="#">Getting Started</a></li>--}}
                {{--<li><a href="#" class="active">Creating a new project</a></li>--}}
                {{--<li><a href="#">Adding customers</a></li>--}}
                {{--<li><a href="#">Settings</a></li>--}}
                {{--<li><a href="#">Importing data</a></li>--}}
                {{--<li><a href="#">Exporting data</a></li>--}}
                {{--</ul>--}}
                {{--</div>--}}
                {{--<div class="help-dropdown-popup-cont">--}}
                {{--<div class="help-dropdown-popup-cont-in">--}}
                {{--<div class="jscroll">--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Lorem Ipsum is simply--}}
                {{--<span class="describe">Lorem Ipsum has been the industry's standard dummy text </span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Contrary to popular belief--}}
                {{--<span class="describe">Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--The point of using Lorem Ipsum--}}
                {{--<span class="describe">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Lorem Ipsum--}}
                {{--<span class="describe">There are many variations of passages of Lorem Ipsum available</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Lorem Ipsum is simply--}}
                {{--<span class="describe">Lorem Ipsum has been the industry's standard dummy text </span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Contrary to popular belief--}}
                {{--<span class="describe">Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--The point of using Lorem Ipsum--}}
                {{--<span class="describe">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Lorem Ipsum--}}
                {{--<span class="describe">There are many variations of passages of Lorem Ipsum available</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Lorem Ipsum is simply--}}
                {{--<span class="describe">Lorem Ipsum has been the industry's standard dummy text </span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Contrary to popular belief--}}
                {{--<span class="describe">Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--The point of using Lorem Ipsum--}}
                {{--<span class="describe">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text</span>--}}
                {{--</a>--}}
                {{--<a href="#" class="help-dropdown-popup-item">--}}
                {{--Lorem Ipsum--}}
                {{--<span class="describe">There are many variations of passages of Lorem Ipsum available</span>--}}
                {{--</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div><!--.help-dropdown-->--}}
                {{--<a class="btn btn-nav btn-rounded btn-inline btn-danger-outline" href="http://themeforest.net/item/startui-premium-bootstrap-4-admin-dashboard-template/15228250">--}}
                {{--Buy Theme--}}
                {{--</a>--}}
                {{--<div class="site-header-search-container">--}}
                {{--<form class="site-header-search closed">--}}
                {{--<input type="text" placeholder="Search"/>--}}
                {{--<button type="submit">--}}
                {{--<span class="font-icon-search"></span>--}}
                {{--</button>--}}
                {{--<div class="overlay"></div>--}}
                {{--</form>--}}
                {{--</div>--}}
                {{--</div><!--.site-header-collapsed-in-->--}}
                {{--</div><!--.site-header-collapsed-->--}}
            </div><!--site-header-content-in-->
        </div><!--.site-header-content-->
    </div><!--.container-fluid-->
</header><!--.site-header-->

<div class="mobile-menu-left-overlay"></div>
<nav class="side-menu side-menu-compact">

    <?php
    $url = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "/admin"));
    //remove the query string part
    if (strpos($url, "?") > 0) $url = substr($url, 0, strpos($url, "?"));

    ?>

    <ul class="side-menu-list">
        <?php
        $order_count = \App\Model\Order::where('status', \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)->count();
        $payment_count = \App\Model\SubOrder::where('status',\App\Http\Controllers\Enum\OrderStatusEnum::CLAIMED)->count();

        $pending_product_seller = \App\Model\Seller::where('status', \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)->where('business_type', \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)->count();
        $pending_service_seller = \App\Model\Seller::where('status', \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)->where('business_type', \App\Http\Controllers\Enum\ProductTypeEnum::SERVICE)->count();
        ?>

        <li class="red divider   @if (strpos($url, '/admin/dashboard')===0) opened @endif">
            <a href="{{url('/admin/dashboard')}}">
                <span id="new_service_seller"
                      class="font-icon font-icon-speed"></span>
                <span class="lbl">Impression</span>
            </a>
        </li>

        <li class="green divider  @if (strpos($url, '/admin/order')===0) opened @endif">
            <a href="{{url('/admin/order/pending')}}">
                <span id="new_order" class="fa fa-shopping-cart @if($order_count) active @endif"></span>
                <span class="lbl">Orders</span>
            </a>
        </li>

        <li class="blue divider  @if (strpos($url, '/admin/payment')===0) opened @endif">
            <a href="{{url('/admin/payment')}}">
                <span id="new_payment" class="fa fa-usd @if($payment_count) active @endif"></span>
                <span class="lbl">Payment</span>
            </a>
        </li>

        <li class="grey divider   @if (strpos($url, '/admin/service/seller')===0) opened @endif">
            <a href="{{url('/admin/service/seller')}}">
                <span id="new_service_seller"
                      class="font-icon font-icon-user  @if($pending_service_seller) active @endif"></span>
                <span class="lbl">Service Seller</span>
            </a>
        </li>
        <li class="grey divider @if (strpos($url, '/admin/product/seller')===0) opened @endif">
            <a href="{{url('/admin/product/seller')}}">
                <span id="new_product_seller"
                      class="font-icon font-icon-user  @if($pending_product_seller) active @endif"></span>
                <span class="lbl">Product Seller</span>
            </a>
        </li>
        <li class="grey divider @if (strpos($url, '/admin/buyer')===0) opened @endif">
            <a href="{{url('/admin/buyer')}}">
                <span class="font-icon font-icon-users"></span>
                <span class="lbl">Buyer</span>
            </a>
        </li>


        <li class="gold divider @if(strpos($url, '/admin/settings')===0) opened @endif">
            <a href="{{url('/admin/settings')}}">
                <span class="font-icon font-icon-dots"></span>
                <span class="lbl">More</span>
            </a>
        </li>

    </ul>

</nav><!--.side-menu-->

<!-- Modal -->
<div class="modal fade" id="site_admin_profile_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Profile Update</h4>
            </div>
            <form action="{{url('admin/profile/update')}}" id="profile_update" method="POST"
                  enctype="multipart/form-data">
                <div class="modal-body">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="company_name">Name</label>
                                <input type="text" class="form-control" name="username"
                                       value="{{Auth::user()->username}}" id="company_name" placeholder="Company Name">
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email"
                                       value="{{Auth::user()->email}}" placeholder="Email">
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" value=""
                                       placeholder="Password">
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label" for="confirm_passeord">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_passeord"
                                       name="confirm_passeord" value="" placeholder="Password">
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <p id="password_msg" style="color: red;"></p>
                        </div>
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label" for="photo">Photo</label>
                                <input type="file" name="photo" id="photo" class="form-control">
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<span id="back-top"><i class="fa fa-angle-up"></i></span>

@yield('content')


<script src="{{asset('/build')}}/js/lib/jquery/jquery.min.js"></script>
<script src="{{asset('/build')}}/js/lib/tether/tether.min.js"></script>
<script src="{{asset('/build')}}/js/lib/bootstrap/bootstrap.min.js"></script>
<script src="{{asset('/build')}}/js/plugins.js"></script>

<script type="text/javascript" src="{{asset('/build')}}/js/lib/jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{asset('/build')}}/js/lib/lobipanel/lobipanel.min.js"></script>
<script type="text/javascript" src="{{asset('/build')}}/js/lib/match-height/jquery.matchHeight.min.js"></script>
{{--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>--}}
<script>
    $(document.body).on('submit', '#profile_update', function () {
        var password = $('#password').val();
        var confirm_password = $('#confirm_passeord').val();
        $('#password_msg').html('');
        if (password != '' && password != confirm_password) {
            $('#password_msg').html('Password not match.');
            return false;
        }
    });
</script>
<script>
    $(document).ready(function () {
        // Back to top function
        $(function () {
            $(window).scroll(function () {
                if ($(this).scrollTop() > 250) {
                    $('#back-top').fadeIn();
                } else {
                    $('#back-top').fadeOut();
                }
            });
            $('#back-top').click(function () {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });
        });

        $.fn.lobiPanel.DEFAULTS = {
            //Makes <b>unpinned</b> panel draggable
            //Warning!!! This requires jquery ui draggable widget to be included
            draggable: true,
            //Makes <b>pinned</b> panels sortable
            //Warning!!! This requires jquery ui sortable widget to be included
            sortable: false,
            //jquery ui sortable plugin option.
            //To avoid any problems this option must be same for all panels which are direct children of their parent
            connectWith: '.ui-sortable',
            //This parameter accepts string ['both', 'vertical', 'horizontal', 'none']. none means disable resize
            resize: 'both',
            //Minimum width <b>unpin, resizable</b> panel can have.
            minWidth: 200,
            //Minimum height <b>unpin, resizable</b> panel can have.
            minHeight: 100,
            //Maximum width <b>unpin, resizable</b> panel can have.
            maxWidth: 1200,
            //Maximum height <b>unpin, resizable</b> panel can have.
            maxHeight: 700,
            //The url which will be used to load content. If not provided reload button will do nothing
            loadUrl: "",
            //If loadUrl is provided plugin will load content as soon as plugin is initialized
            autoload: true,
            bodyHeight: 'auto',
            //This will enable tooltips on panel controls
            tooltips: true,
            toggleIcon: 'glyphicon glyphicon-cog',
            expandAnimation: 100,
            collapseAnimation: 100,
            state: 'pinned', // pinned, unpinned, collapsed, minimized, fullscreen,
            initialIndex: null,
            stateful: false, // If you set this to true you must specify data-inner-id. Plugin will save (in localStorage) it's states such as
                             // pinned, unpinned, collapsed, minimized, fullscreen, position among it's siblings
                             // and apply them when you reload the browser
            unpin: false,
            reload: {},
            minimize: {
                icon: 'glyphicon glyphicon-minus', //icon is shown when panel is not minimized
                icon2: 'glyphicon glyphicon-plus', //icon2 is shown when panel is minimized
                tooltip: 'Minimize'         //tooltip text, If you want to disable tooltip, set it to false
            },
            expand: {
                icon: 'glyphicon glyphicon-resize-full', //icon is shown when panel is not on full screen
                icon2: 'glyphicon glyphicon-resize-small', //icon2 is shown when pane is on full screen state
                tooltip: 'Fullscreen'       //tooltip text, If you want to disable tooltip, set it to false
            },
            editTitle: false,
            close: false,
        };


        $('.panel').lobiPanel({
            sortable: false
        });
        $('.panel').on('dragged.lobiPanel', function (ev, lobiPanel) {
            $('.dahsboard-column').matchHeight();
        });
    });
</script>
<script type="text/javascript" src="{{asset('/build')}}/js/lib/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{asset('/build')}}/js/lib/select2/select2.full.min.js"></script>
<script src="{{asset('/build')}}/js/lib/toastr/toastr.min.js"></script>
<script src="{{asset('/build')}}/js/lib/bootstrap-notify/bootstrap-notify.min.js"></script>
<script>
    function refresh() {
        $('#loading_img').html('<div class="text-center"><img src="{{asset('image/pageloader.gif')}}"></div>');
        $.ajax({
            url: "{{url('/admin/dashboard/ajax')}}",
            dataType: "json",
            success: function (data) {
                $('#pending_order').html(data.pending_order);
                $('#total_service_seller').html(data.total_service_seller);
                $('#total_product_seller').html(data.total_product_seller);
                $('#total_buyer').html(data.total_buyer);
                $('#total_deals').html(data.total_deals);
                $('#sales_today').html('{{env('CURRENCY_SYMBOL')}} ' + data.sales_today);

                if (data.new_service_seller != 0) $('#new_service_seller').addClass('active');
                else $('#new_service_seller').removeClass('active');

                if (data.new_product_seller != 0) $('#new_product_seller').addClass('active');
                else $('#new_product_seller').removeClass('active');

                if (data.pending_order != 0) $('#new_order').addClass('active');
                if (data.new_payment != 0) $('#new_payment').addClass('active');

                if (data.question_answer_count != 0 || data.question_count != 0) $('#dd-messages').addClass('active');
                $('#question_count').html('Question <span class="label label-pill label-danger">' + data.question_count + '</span>');
                $('#question_answer_count').html('Reply <span class="label label-pill label-danger">' + data.question_answer_count + '</span>');

                $('#questions').html(data.question_html);
                $('#question_answers').html(data.question_answer_html);
                $('#total_product').html(data.total_product);

                setTimeout(refresh, 30000);
                $('#loading_img').empty();
            }
        });
    }

</script>
@yield('script')
<script src="{{asset('/build')}}/js/app.js"></script>
</body>
</html>
