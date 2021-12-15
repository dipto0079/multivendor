@extends('frontend.master',['menu'=>'home'])
@section('title',__('messages.page_title.home'))
@section('stylesheet')
    <link href="{{asset(url('css/slider.css'))}}" media="screen" rel="stylesheet" type="text/css">
    <style>
         .container_12, .container_12 .grid_12 { width: 100%; }
         .slid_text {
             position: absolute;
             top: 65px;
             left: 80px;
             color: #131313;
         }

        .slid_text .slid_title span
        {
            font-size: 40px;
        }
         .slid_text p span {
             font-size: 25px;
         }
    </style>
@stop
@section('content')

    <div id="main" class="site-main" style="margin-bottom: 0;">
        <div class="container">
            <div class="row home_slider">
                <?php $advertisement_top = \App\Model\Advertisement::getAdvertisement(\App\Http\Controllers\Enum\AdvertisementTypeEnum::HOME_PAGE_TOP); ?>
                <div class="col-md-12">
                    @if(count($advertisement_top)>1)
                        <div class="slidprev"><span>Prev</span></div>
                        <div class="slidnext"><span>Next</span></div>@endif
                    <div id="slider">
                        @if(count($advertisement_top)>0)
                            @foreach($advertisement_top as $advertisement)
                                <div class="slide">
                                    @if(!empty($advertisement->link))<a
                                            href="{{url($advertisement->link)}}"> @endif
                                        <img src="{{url(asset('uploads/advertisement') . '/' . $advertisement->image)}}" alt="" title=""/>
                                        @if(!empty($advertisement->link))
                                    </a> @endif
                                    <div class="slid_text">
                                        @if(!empty($advertisement->main_title))<h3 class="slid_title">
                                            <span>@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->main_title}} @else {{$advertisement->ar_main_title}} @endif</span></h3>@endif
                                        @if(!empty($advertisement->sub_title_one))<p>
                                            <span>@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_one}} @else {{$advertisement->ar_sub_title_one}} @endif</span></p>@endif
                                        @if(!empty($advertisement->sub_title_two))<p>
                                            <span>@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_two}} @else {{$advertisement->ar_sub_title_two}} @endif</span></p>@endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="slide">
                                <img src="<?=Image::url(asset('image/services.png'), 1275, 520, array('crop'))?>"
                                     alt="" title=""/>
                            </div>
                        @endif
                    </div>
                    <div class="clear"></div>
                    <div id="myController"></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row home_product_info">
                <?php $advertisement_middle = \App\Model\Advertisement::getAdvertisement(\App\Http\Controllers\Enum\AdvertisementTypeEnum::HOME_PAGE_MIDDLE,4); ?>

                @if(!empty($advertisement_middle[0]))
                    @foreach($advertisement_middle as $advertisement)
                        <div class="col-sm-4">
                            <a href="{{$advertisement->link}}" class="home-product-thumbnail">
                              @if(!empty($advertisement->main_title) || !empty($advertisement->sub_title_one) || !empty($advertisement->sub_title_two))
                                <span class="roll_css">
                                  @if(!empty($advertisement->main_title))<h3 class="slid_title">
                                      <span>@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->main_title}} @else {{$advertisement->ar_main_title}} @endif</span></h3>@endif
                                  @if(!empty($advertisement->sub_title_one))<p>
                                      <span>@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_one}} @else {{$advertisement->ar_sub_title_one}} @endif</span></p>@endif
                                  @if(!empty($advertisement->sub_title_two))<p>
                                      <span>@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_two}} @else {{$advertisement->ar_sub_title_two}} @endif</span></p>@endif
                                </span>
                              @endif
                                <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'),365,365,['crop'])?>"
                                     data-src="<?=Image::url(asset('uploads/advertisement') . '/' . $advertisement->image,365,365,['crop'])?>" border="0" />
                            </a>
                            <div class="clearfix"></div>
                        </div>
                    @endforeach
                @endif
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div class="clearfix"></div>
            <div class="row home_user_type_section">
                <div class="col-sm-6">
                    <div class="user_type_section">
                        <h3>@lang('messages.home_page.want_to_buy')</h3>
                        <img class="img_background" src="{{asset('/image/default.jpg')}}" data-src="{{asset('/image/1_1.jpg')}}" alt="">
                        <div class="details">
                            <h4>@lang('messages.home_page.text-1')</h4>
                            <p>@lang('messages.home_page.text-1')</p>
                            <a href="{{url('/buyer-registration')}}" class="btn btn-primary">@lang('messages.home_page.sign_up')</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="user_type_section">
                        <h3>@lang('messages.home_page.want_to_sell')</h3>
                        <img class="img_background" src="{{asset('/image/default.jpg')}}" data-src="{{asset('/image/1_2.jpg')}}" alt="">
                        <div class="details">
                            <h4>@lang('messages.home_page.text-1')</h4>
                            <p>@lang('messages.home_page.text-1')</p>
                            <a href="{{url('/seller-registration')}}" class="btn btn-primary">@lang('messages.home_page.create_store')</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div class="clearfix"></div>
            <div class="row explode_more_section">
                <div class="col-md-12">
                    <h3 style = "text-align: center;">@lang('messages.home_page.explode_more')</h3>
                    <ul class="nav nav-pills nav-justified">
                        <li role="presentation"><a href="{{url('/products')}}">@lang('messages.home_page.products')</a></li>
                        <li role="presentation"><a href="{{url('/service')}}">@lang('messages.home_page.service')</a></li>
                        <li role="presentation"><a href="{{url('/deals')}}">@lang('messages.home_page.deals')</a></li>
                        <li role="presentation"><a href="{{url('/stores')}}">@lang('messages.home_page.stores')</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="success_story_section">
            <div class="container">
                <div class="row">
                    <h3>@lang('messages.home_page.success_stories')</h3>
                    <div class="clearfix"></div>
                   <?php $success_stories = \App\Model\SuccessStory::orderBy('created_at','desc')->take(4)->get(); ?>
                    @if(!empty($success_stories[0]))
                        @foreach($success_stories as $success_story)
                            <div class="col-sm-3">
                                <div class="success_story_item">
                                    <img src="<?=Image::url(asset('/image/default.jpg'),70,70,array('crop'))?>" @if(!empty($success_story->image))
                                         data-src="<?=Image::url(asset(env('SUCCESS_STORY_UPLOAD_PATH')).'/'.$success_story->image,70,70,array('crop'))?>"
                                         @else
                                         data-src="{{asset('image/default_author.png')}}"
                                         @endif class="img-circle img_background" alt="">
                                    <h3 class="name">@if(\App\UtilityFunction::getLocal()== "en") {{$success_story->name}} @else {{$success_story->ar_name}} @endif</h3>
                                    <p class="">@if(\App\UtilityFunction::getLocal()== "en") {!! strip_tags($success_story->details) !!} @else {!! strip_tags($success_story->ar_details) !!} @endif</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{asset(url('js/jquery.carouFredSel-6.2.0-packed.js'))}}"></script>
    <script src="{{asset(url('js/selectBox.js'))}}"></script>
    <script>

        $(document).ready(function () {

            $(function () {
                $("select").selectBox();
            });

            $(function () {
                $("#slider").carouFredSel({
                    prev: '.slidprev',
                    next: '.slidnext',
                    responsive: true,
                    pagination: '#myController',
                    scroll: 1,
                    items: {
                        visible: 1,
                        width: 1275,
                        height: "39%"
                    },
                    swipe: {
                        onMouse: true,
                        onTouch: true
                    }
                });
                $('#list_product').carouFredSel({
                    prev: '#prev_c1',
                    next: '#next_c1',
                    scroll: 1,
                    auto: false,
                    swipe: {
                        onMouse: true,
                        onTouch: true
                    }
                });
                $('#list_product2').carouFredSel({
                    prev: '#prev_c2',
                    next: '#next_c2',
                    scroll: 1,
                    auto: false,
                    swipe: {
                        onMouse: true,
                        onTouch: true
                    }
                });
                $('#list_banners').carouFredSel({
                    prev: '#ban_prev',
                    next: '#ban_next',
                    scroll: 1,
                    auto: false,
                    swipe: {
                        onMouse: true,
                        onTouch: true
                    }
                });
                $('#thumblist').carouFredSel({
                    prev: '#img_prev',
                    next: '#img_next',
                    scroll: 1,
                    auto: false,
                    circular: false,
                    swipe: {
                        onMouse: true,
                        onTouch: true
                    }
                });
                $(window).resize();
            });


            $(function () {
                $('.jqzoom').jqzoom({
                    zoomType: is_touch_device() ? 'innerzoom' : 'standard',
                    lens: true,
                    preloadImages: true,
                    alwaysOn: false
                });
            });

            $(function () {
                $('#wrapper_tab a').click(function () {
                    if ($(this).attr('class') != $('#wrapper_tab').attr('class')) {
                        $('#wrapper_tab').attr('class', $(this).attr('class'));
                    }
                    return false;
                });
            });

            //Primary menu(media < 984)
            $(function () {
                $('.primary .menu-select').toggle(function () {
                            $('.primary > ul').slideDown('slow');
                            $(this).addClass('minus');
                        }
                        , function () {
                            $('.primary > ul').slideUp('slow');
                            $(this).removeClass('minus');
                        });

                $('.primary .parent > a').toggle(function () {
                            $(this).next('ul.sub').slideDown('slow');
                            $(this).parent('.parent').addClass('minus');
                        }
                        , function () {
                            $(this).next('ul.sub').slideUp('slow');
                            $(this).parent('.parent').removeClass('minus');
                        });
            });

            $(function () {
                $(".phone_top").click(function () {
                    if ($(window).width() <= 410) {
                        var show = $(".phone_top span").width() == 0;
                        $(".phone_top span").animate({
                            width: show ? "120px" : "0px"
                        }, 500);
                        $(".valuta").animate({
                            marginRight: show ? "-120px" : "0px"
                        }, 500);
                    }
                });
            });

            function is_touch_device() {
                try {
                    document.createEvent("TouchEvent");
                    return true;
                } catch (e) {
                    return false;
                }
            }

        });


    </script>
@stop
