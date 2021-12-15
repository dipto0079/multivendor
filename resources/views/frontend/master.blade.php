<!DOCTYPE html>
@if (\App\UtilityFunction::getLocal() == 'ar')
<html dir="rtl" lang="ar">   
@else
<html lang="en">
@endif

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{env('APP_NAME_'.\App\UtilityFunction::getLocal())}} - @yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('image/favicon.png')}}"/>

    <link href="{{asset('/')}}css/master.css" rel="stylesheet">
    <link href="{{asset('/')}}css/header4.css" rel="stylesheet">
    <link href="{{asset('/')}}css/shop.css" rel="stylesheet">


    <!-- SWITCHER -->
    <link rel="stylesheet" id="switcher-css" type="text/css" href="{{asset('/')}}plugins/switcher/css/switcher.css"
          media="all"/>
    <link rel="alternate stylesheet" type="text/css" href="{{asset('/')}}plugins/switcher/css/color1.css"
          title="color1" media="all"/>
    <link rel="alternate stylesheet" type="text/css" href="{{asset('/')}}plugins/switcher/css/color2.css"
          title="color2" media="all"/>
    <link rel="alternate stylesheet" type="text/css" href="{{asset('/')}}plugins/switcher/css/color3.css"
          title="color3" media="all"/>
    <link rel="alternate stylesheet" type="text/css" href="{{asset('/')}}plugins/switcher/css/color4.css"
          title="color4" media="all"/>
    <link rel="alternate stylesheet" type="text/css" href="{{asset('/')}}plugins/switcher/css/color5.css"
          title="color5" media="all"/>
    <link rel="stylesheet" href="{{asset('/build')}}/js/lib/toastr/toastr.min.css">

    <link href="{{asset('/')}}/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <script src="{{asset('/')}}js/jquery-1.11.2.min.js"></script>
    <script src="{{asset('/')}}js/jquery-ui.min.js"></script>
    <script src="{{asset('/')}}js/bootstrap.min.js"></script>
    <script src="{{asset('/build')}}/js/lib/toastr/toastr.min.js"></script>
    <script src="{{asset('/build')}}/js/lib/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


    <!-- CSS -->
    <link rel="stylesheet" href="{{asset('/')}}css/custom_new.css" type="text/css"/>


    <style>
      #back-top { width: 40px; height: 40px; text-align: center; position: fixed;
          bottom: 10px; right: 20px; cursor: pointer; border: 3px solid #ff8300;
          border-radius: 100%; -webkit-border-radius: 100%;
          -moz-border-radius: 100%; -o-border-radius: 100%;color: #ff8300;
      }
      #back-top i { font-size: 30px; }
        .loading{
            width: 100%;
            min-height: 100%;
            position: absolute;
            z-index: 100;
            background-color: rgba(255,255,255,.9);
            text-align: center;
        }
        .nav > li > a {
            position: relative;
            display: block;
            padding: 18px 14px 19px 15px;
        }

        .cart-qty {
            font-size: 11px;
            line-height: 18px;
            padding: 0 6px;
            position: absolute;
            right: 2px;
            top: 8px;
        }

        .nav-top > li > a {
            font-size: 14px;
        }

        .btn-white, .btn-white.hvr-shutter-out-horizontal {
            background-color: #e4e4e4;
            border-color: #c5c5c5;
            color: #333;
        }

        .search-wrapper {
            color: #fff;
            padding-top: 10%;
        }

        .search-wrapper .modal-content {
            background-color: rgba(0, 0, 0, 0);
            border: medium none;
            border-radius: 0;
            -moz-border-radius: 0;
            -webkit-border-radius: 0;
            box-shadow: none;
            -moz-box-shadow: none;
            -webkit-box-shadow: none;
        }
        .close, .modal-backdrop.in { opacity: .9; }

        .form-search {
            position: relative;
        }

        .form-search .form-group {
            width: 100%;
        }

        .form-search .form-control {
            background-color: rgba(0, 0, 0, 0);
            border-color: #fff;
            border-style: solid;
            border-width: 2px;
            width: 100%;
        }

        .form-search .btn {
            border: 0 none;
            font-size: 18px;
            height: 100%;
            line-height: 100%;
            padding: 0 35px;
            position: absolute;
            right: 0;
            top: 0;
        }
        .img_background{background-image:url(data:image/gif;base64,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);
          background-repeat:no-repeat;background-position:center center; }
        .input_loader {
            min-width: 35px;
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
    </style>

    @yield('stylesheet')

    @if (\App\UtilityFunction::getLocal() == 'ar')
      <link rel="stylesheet" href="{{asset('/')}}css/ar_custom_new.css" type="text/css"/>
        <style>
            * {
                font-family: DroidNaskh-Regular;
            }
            h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .btn  {
                font-family: DroidNaskh-Regular !important;
                text-align: right;
            }
            .btn { text-align: center; }
        </style>
    @endif

</head>
<body>




<div class="sp-body">
    <!-- Loader Landing Page -->
    {{--<div id="ip-container" class="ip-container">--}}
        {{--<div class="ip-header">--}}
            {{--<div class="ip-loader">--}}
                {{--<div class="text-center">--}}
                    {{--<div class="ip-logo">--}}
                        {{--<a class="logo"></a>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<svg class="ip-inner" width="60px" height="60px" viewBox="0 0 80 80">--}}
                    {{--<path class="ip-loader-circlebg"--}}
                          {{--d="M40,10C57.351,10,71,23.649,71,40.5S57.351,71,40.5,71 S10,57.351,10,40.5S23.649,10,39.3,10z"/>--}}
                    {{--<path id="ip-loader-circle" class="ip-loader-circle"--}}
                          {{--d="M40,10C57.351,10,71,23.649,71,40.5S57.351,71,40.5,71 S10,57.351,10,40.5S23.649,10,40.5,10z"/>--}}
                {{--</svg>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <!-- Loader end -->


    <!-- Start Switcher -->
    {{--<div class="switcher-wrapper">--}}
        {{--<div class="demo_changer">--}}
            {{--<div class="demo-icon customBgColor"><i class="fa fa-cog fa-spin fa-2x"></i></div>--}}
            {{--<div class="form_holder">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">--}}
                        {{--<div class="predefined_styles">--}}
                            {{--<div class="skin-theme-switcher">--}}
                                {{--<h4>Color</h4>--}}
                                {{--<a href="#" data-switchcolor="color1" class="styleswitch"--}}
                                   {{--style="background-color:#ff8300;"> </a>--}}
                                {{--<a href="#" data-switchcolor="color2" class="styleswitch"--}}
                                   {{--style="background-color:#4fb0fd;"> </a>--}}
                                {{--<a href="#" data-switchcolor="color3" class="styleswitch"--}}
                                   {{--style="background-color:#ffc73c;"> </a>--}}
                                {{--<a href="#" data-switchcolor="color4" class="styleswitch"--}}
                                   {{--style="background-color:#dc2c2c;"> </a>--}}
                                {{--<a href="#" data-switchcolor="color5" class="styleswitch"--}}
                                   {{--style="background-color:#02cc8b;"> </a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <!-- End Switcher -->


    <header id="header" class="header-v2">

        @include('frontend.top_menu')
        @include('frontend.menu')

        <div class="navigation">
            <div class="container">
                <div class="row">

                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </header>

    <div class="clearfix"></div>
    @yield('content')
    <div class="clearfix"></div>
    @include('frontend.footer')


</div>

<div aria-hidden="false" role="dialog" tabindex="-1" class="modal fade example-modal-lg search-wrapper in">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <p class="clearfix">
                <button data-dismiss="modal" class="close" type="button"><span class="icon_close color-main"
                                                                               aria-hidden="true"></span></button>
            </p>
            <form role="form" action="{{url('/go/search')}}" class="form-inline form-search" method="get">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="textsearch" class="sr-only">@lang('messages.enter_text_search')</label>
                    <input type="text" name="search" placeholder="@lang('messages.type_keyword')" id="textsearch"
                           class="form-control input-lg font-main font-weight-normal color-main"
                           @if (\App\UtilityFunction::getLocal() == 'ar')
                           style="padding-right: 220px;">
                            @else
                            style="padding-right: 120px;">
                            @endif

                </div>
                <button class="btn btn-white font-additional font-weight-normal color-main text-uppercase hover-focus-bg"
                        type="submit">@lang('messages.search')
                </button>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="false" role="dialog" tabindex="-1" id="how_it_work" class="modal fade how-it-work in">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <p class="clearfix">
                <button data-dismiss="modal" class="close" type="button">
                    <i class="fa fa-times"></i>
                    <span class="clearfix"></span>
                    @lang('messages.top_modal.esc')
                </button>
            </p>
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-sm-4">
                        <div class="txt-center margin-bottom-s">
                            <img alt="" class="img-responsive" height="251" src="https://res4.nbstatic.in/static/images/nb-how-it-works-explore.png" width="251" xsrc="https://res4.nbstatic.in/static/images/nb-how-it-works-explore.png" data-lzled="true">
                            <p class="h5 txt-primary margin-top-l margin-bottom-s font-weight-semibold txt-uppercase">@lang('messages.top_modal.explode') </p>
                            <p class="txt-primary"> @lang('messages.top_modal.explode_text') </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4">
                        <div class="txt-center margin-bottom-s">
                            <img alt="" class="img-responsive" height="251" src="https://res4.nbstatic.in/static/images/nb-how-it-works-buy.png" width="251" xsrc="https://res4.nbstatic.in/static/images/nb-how-it-works-buy.png" data-lzled="true">
                            <p class="h5 txt-primary margin-top-l margin-bottom-s font-weight-semibold txt-uppercase"> @lang('messages.top_modal.buy') </p>
                            <p class="txt-primary"> @lang('messages.top_modal.buy_text') </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4">
                        <div class="txt-center margin-bottom-s">
                            <img alt="" class="img-responsive" height="251" src="https://res4.nbstatic.in/static/images/nb-how-it-works-enjoy.png" width="251" xsrc="https://res4.nbstatic.in/static/images/nb-how-it-works-enjoy.png" data-lzled="true">
                            <p class="h5 txt-primary margin-top-l margin-bottom-s font-weight-semibold txt-uppercase"> @lang('messages.top_modal.enjoy') </p>
                            <p class="txt-primary"> @lang('messages.top_modal.enjoy_text') </p>
                        </div>
                    </div>
                </div>
                <div class="footer margin-top-xl">
                    <img alt="" class="img-responsive" height="251" src="{{asset('')}}/image/logo.png" width="251" data-lzled="true">
                    <p class="font-sm txt-tertiary" style="font-family: Helvetica, Arial, Sans-Serif;">@lang('messages.experience_the_world_around_you')</p>
                </div>
            </div>
        </div>
    </div>
</div>
@if(!empty(Session::get('registration_success_message')))
<script type="text/javascript">
  toastr.success('{{Session::get('registration_success_message')}}');
</script>
@endif
<span id="back-top"><i class="fa fa-angle-up"></i></span>
{{--Cart form--}}
<form action="{{url('/add-to-cart')}}" id="add_to_cart">{{csrf_field()}}</form>

<script src="{{asset('/')}}js/modernizr.custom.js"></script>
<script src="{{asset('/')}}js/jquery.placeholder.min.js"></script>
<script src="{{asset('/')}}js/smoothscroll.min.js"></script>
<!-- Loader -->
<script src="{{asset('/')}}plugins/loader/js/classie.js"></script>
<script src="{{asset('/')}}plugins/loader/js/pathLoader.js"></script>
<script src="{{asset('/')}}plugins/loader/js/main.js"></script>
<script src="{{asset('/')}}js/classie.js"></script>
<!--Owl Carousel-->
<script src="{{asset('/')}}plugins/owl-carousel/owl.carousel.min.js"></script>
<!-- bxSlider -->
<script src="{{asset('/')}}plugins/bxslider/jquery.bxslider.min.js"></script>
<!--Switcher-->
<script src="{{asset('/')}}plugins/switcher/js/bootstrap-select.js"></script>
<script src="{{asset('/')}}plugins/switcher/js/evol.colorpicker.min.js" type="text/javascript"></script>
<script src="{{asset('/')}}plugins/switcher/js/dmss.js"></script>
<!-- SCRIPTS -->
<script type="text/javascript" src="{{asset('/')}}plugins/isotope/jquery.isotope.min.js"></script>
@yield('script')
<!--THEME-->
<script src="{{asset('/')}}js/wow.min.js"></script>
<script src="{{asset('/')}}js/cssua.min.js"></script>
<script src="{{asset('/')}}js/theme.js"></script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $("#back-top").hide();
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
</script>
<script>
    $(document.body).on('click', '.parent', function () {
        $(this).closest('ul.categories-tree').find('li').removeClass('active');
        $(this).parent().addClass('active');
        $('.parent').parent().find('.collapse').removeClass('in');
        $('.parent').parent().find('.fa-caret-down').removeClass("fa-caret-down").addClass("fa-caret-right");
        if($(this).children().hasClass('icon fa fa-caret-right')) {
            $(this).children('.icon').removeClass("fa-caret-right").addClass("fa-caret-down");
        }
        else if($(this).children().hasClass('icon fa fa-caret-down')){
            $(this).children('.icon').removeClass("fa-caret-down").addClass("fa-caret-right");
        }
    });
    $(document.body).on('click', '.child', function () {
        $(this).closest('ul.collapse').find('li').removeClass('active');
        $(this).parent().addClass('active');
        $('.child').parent().parent().find('.collapse').removeClass('in');
        $('.child').parent().find('.fa-caret-down').removeClass("fa-caret-down").addClass("fa-caret-right");
        if($(this).children().hasClass('icon fa fa-caret-right')) {
            $(this).children('.icon').removeClass("fa-caret-right").addClass("fa-caret-down");
        }
        else if($(this).children().hasClass('icon fa fa-caret-down')){
            $(this).children('.icon').removeClass("fa-caret-down").addClass("fa-caret-right");
        }
    });

    $(document.body).on('click', '.city_location', function () {
        $('.city_location').parent().find('.collapse').removeClass('in');
        $('.city_location').parent().find('.fa-caret-down').removeClass("fa-caret-down").addClass("fa-caret-right");
        if($(this).children().hasClass('icon fa fa-caret-right')) {
            $(this).children('.icon').removeClass("fa-caret-right").addClass("fa-caret-down");
        }
        else if($(this).children().hasClass('icon fa fa-caret-down')){
            $(this).children('.icon').removeClass("fa-caret-down").addClass("fa-caret-right");
        }
    });

//    $('.collapse').on('shown.bs.collapse', function(){
//        $(this).parent().find(".fa-caret-right").removeClass("fa-caret-right").addClass("fa-caret-down");
//    }).on('hidden.bs.collapse', function(){
//        $(this).parent().find(".fa-caret-down").removeClass("fa-caret-down").addClass("fa-caret-right");
//    });


    $('ul.nav-top li.dropdown').hover(function() {
        $(this).addClass("open");
    }, function() {
        $(this).removeClass("open");
    });

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }



    function top_promotion() {
        var top_promotion = getCookie("top_promotion");
        if (top_promotion == "") {
            return true;
        }
        return false;
    }

    function hide_promotion_forever()
    {
        setCookie("top_promotion", "hide", 365);
    }

    function timeOutF(){
        setTimeout(function(){  $("#email_subscribe_msg").html(""); }, 3000);
    }

    $(document.body).on('submit','#subscribe_form',function (e) {
        var Email = document.getElementById('email_subscribe').value;
        var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

        var testEmailID = regexEmail.test(Email);

        if (regexEmail.test(Email)) {
            $("#email_subscribe_msg").html('');

            e.preventDefault();
            $("#email_subscribe_msg").html("@lang('messages.error_message.subscribing_please_wait')");
            $.ajax({
                type: "POST",
                url: $('#subscribe_form').attr('action'),
                data: $('#subscribe_form').serialize(),
                dataType: "json",
                success: function (data) {
                    if(data.data_g == 1) {
                        $("#email_subscribe_msg").html("@lang('messages.error_message.thanks_for_your_subscription')").css('color','#ff8300');
                        timeOutF();
                    }
                    else if(data.data_g == 0) {
                        $("#email_subscribe_msg").html("@lang('messages.error_message.you_already_subscribed')").css('color','red');
                        timeOutF();
                    }
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });
        }
        else if(Email == ''){
            $("#email_subscribe_msg").html('');
            $("#email_subscribe_msg").html("@lang('messages.seller_registration.please_email')").css('color','red');
            document.getElementById('email_subscribe').focus();
            timeOutF();
            return false;
        }
        else {
            $("#email_subscribe_msg").html('');
            $("#email_subscribe_msg").html("@lang('messages.seller_registration.please_email')").css('color','red');
            document.getElementById('email_subscribe').focus();
            timeOutF();
            return false;
        }
    });
</script>
<script>


</script>
<script>
var shoppingCartItems = [];
function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function checkCookie() {
    var user=getCookie("cart");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
       if (user != "" && user != null) {
           setCookie("cart", user, 30);
       }
    }
}
    // Add to cart code
    $(document.body).on('click','.add_to_cart',function(e){
        var product_id = $(this).data('id');
        var quantity = $('#quantity_input').val();
        if(quantity == undefined){
            quantity = '';
        }
        toastr.info("@lang('messages.error_message.processing')");
        $.ajax({
            type: 'POST',
            url: $('#add_to_cart').attr('action')+'?product_id='+product_id+'&quantity='+quantity,
            data: $('#add_to_cart').serialize(),
            dataType: 'json',
            success: function(data){
                if(data.success == true){
                    toastr.clear();
                    $('.cart_show').html(data.cart_items_generate);
                    $('.subtotal').html('{{env('CURRENCY_SYMBOL')}}'+data.sub_total);
                    $('#add_to_cart_count').html('<span class="add-to-cart-qty cart-qty font-main font-weight-semibold color-main customBgColor circle"'+
                            'style="position: static; right: inherit;  top: inherit; float: left;"></span>');
                    $('.add-to-cart-qty').html(data.cart_item_count);
                    $('.header-cart_product_div').css('display', 'block');
                    $('.header-cart_product_no_item').css('display', 'none');
                    @if(\App\UtilityFunction::getLocal()== "en")
                        if(data.low_quantity == 0){
                            toastr.success(data.cart_item_name+' added to your shopping cart.');
                        }else{
                            toastr.warning(data.cart_item_name+' has not enough quantity.');
                        }
                    @else
                        if(data.low_quantity == 0){
                            toastr.success(data.cart_item_name+' إضافة إلى سلة التسوق الخاصة بك');
                        }else{
                            toastr.warning(data.cart_item_name+' لم كمية كافية.');
                        }
                    @endif
                }else {
                    window.location.replace('{{url('/buyer/login')}}');
                }
            }
        }).fail(function (data) {
            var errors = data.responseJSON;
            console.log(errors);
        })
    });

    // Cart item delete ajax code
    $(document.body).on('click','.item-del',function(e){
        var item_id = $(this).data('id');
        var coupon = $('#coupon_id').val();
        var country_id = $('#country_id').val();
        var city_id = $('#city_id').val();
        $.ajax({
            type: 'GET',
            url: '{{url('/remove-cart-items')}}?item_id='+item_id + '&coupon='+coupon+'&country='+country_id+'&city='+city_id,
            dataType: 'json',
            context: this,
            success: function(data) {
                if(data.success == true) {
                    $(this).closest('li').remove();
                    $(this).closest('tr').remove();
                    $('.subtotal').html('$'+data.sub_total);
                    $('.add-to-cart-qty').html(data.cart_item_count);

                    if(coupon != '' && country_id != '' && city_id != ''){
                        $('#cart_amount_calculation').empty();
                        $('#cart_html').empty();
                        //$('#total_price_info').html(data.data_generate);
                        $('#cart_amount_calculation').html(data.data_generate);
                        $('#cart_html').html(data.cart_html);
                        getTouchSpin();
                        updateCartAmount();
                    }


                    if(data.cart_item_count == 0){
                        $('#cart_list_body').empty();
                        $('#cart_list_body').html('<div class="col-md-12"><h4>@lang('messages.buyer.no_product_added')</h4></div>');
                        $('.header-cart_product_no_item').show();
                        $('.header-cart_product_div').hide();
                    }
                }else{
                    toastr.warning("@lang('messages.error_message.item_not_removed_please_try_again')");
                }
            }
        }).fail(function(data){
            var errors = data.responseJSON;
            toastr.warning("@lang('messages.error_message.something_went_wrong_please_try_again')");
            console.log(errors);
        })
    });
    // Add to wish list code
    $(document.body).on('click','.add_to_wish_list',function(e){
        var product = $(this).data('id');
        $.ajax({
            type: "GET",
            url: '{{url('/add-to-wish-list')}}?product='+product,
            dataType: 'json',
            success: function (data){
                toastr.clear();
                if(data.success == true){
                    if(data.exists == 1){
                        toastr.success("@lang('messages.error_message.product_added_successfully')");
                    }else{
                        toastr.warning("@lang('messages.error_message.already_in_your_wise_list')");
                    }
                } else {
                    window.location.replace('{{url('/buyer/login')}}');
                }
            }
        }).fail(function(data){
            var errors = data.responseJSON;
        });
    });

    // Alert message removed in 5 seconds
//    setTimeout(function(){ $('.alert').remove(); }, 5000);
</script>


<script>
    if(top_promotion())
    {
        $('#top_pomotion').append('<div class="sticky-header-banner sale-header-banner alert alert-dismissible fade in">' +
                '<div class="container">' +
//            '<img src="https://d1nrhamtcpp354.cloudfront.net/modules/web/assets/images/seasonal-header-new.png">' +
                '<div class="banner-text"><span>We use cookies. By closing this message or continuing to browse the site, you are agreeing to our <a href="{{url('/privacy-policy')}}">cookie policy</a>.  </span></div>' +
                '<div onclick=hide_promotion_forever() class="close" data-dismiss="alert" aria-label="Close">×</div></div></div>');
    }
</script>
<script>
    function init() {
        var imgDefer = document.getElementsByTagName('img');
        for (var i = 0; i < imgDefer.length; i++) {
            if (imgDefer[i].getAttribute('data-src')) {
                imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
//                imgDefer[i].removeAttribute('class');
                imgDefer[i].classList.remove('img_background');
            }
        }
    }
    window.onload = init;
</script>
<script>
    $(document).ready(function () {
        $('.more').each(function (event) { /* select all divs with the item class */
            var max_length = 250;
            /* set the max content length before a read more link will be added */
            if ($(this).html().length > max_length) { /* check for content length */
                var short_content = $(this).html().substr(0, max_length);
                /* split the content in two parts */
                var long_content = $(this).html().substr(max_length);

                $(this).html(short_content +
                        ' <a href="javascript:;" class="read_more">@lang('messages.read_more')</a>' +
                        '<span class="more_text" style="display:none;">' + long_content + '</span>');
                /* Alter the html to allow the read more functionality */
                $(this).find('a.read_more').click(function (event) { /* find the a.read_more element within the new html and bind the following code to it */
                    event.preventDefault();
                    /* prevent the a from changing the url */
                    $(this).hide();
                    /* hide the read more button */
                    $(this).parents('.more').find('.more_text').show();
                    /* show the .more_text span */
                });
            }
        });
    });
</script>
<script type="text/javascript">
function textareaMaxLength(){
  $('textarea[maxlength]').keyup(function(){
      var max = parseInt($(this).attr('maxlength'));
      if($(this).val().length > max){
          $(this).val($(this).val().substr(0, $(this).attr('maxlength')));
      }
      @if(\App\UtilityFunction::getLocal()== "en")
            $(this).parent().find('.charsRemaining').html('You have ' + (max - $(this).val().length) + ' characters remaining');
      @else
            $(this).parent().find('.charsRemaining').html( 'لديك ' +' '+(max - $(this).val().length) +' '+ 'الأحرف المتبقية');
      @endif
  });
  }

  textareaMaxLength();
</script>
<script type="text/javascript">
  function confirmDelete(){
    confirm('Do you want to delete?');
  }
</script>
</body>
</html>
