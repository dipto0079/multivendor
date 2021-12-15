@extends('frontend.master',['menu'=>'services'])
@section('title',trans('messages.page_title.services'))
@section('stylesheet')
    <link href="{{asset(url('css/slider.css'))}}" media="screen" rel="stylesheet" type="text/css">


    <style>
        .starSeparatorBox2 {
            padding: 0px 0;
            text-align: center;
        }

        .owl-product-slider {
            padding: 30px 0 0;
        }

        .h2, h2 {
            font-size: 25px;
        }

    </style>

    @if (\App\UtilityFunction::getLocal() == 'ar')
        <style>
            .cata-box {
                text-align: right !important;
            }

            .cata-box img {
                float: right !important;
            }

        </style>
    @endif
@stop
@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 banner-slider">
                    <div class="row">
                        <div class="col-md-3 col-sm-4 hidden-xs" @if (\App\UtilityFunction::getLocal() == 'ar') style="float: right;" @endif>
                            <div class="row">
                                <div class="col-lg-12 res-catagories">
                                    @if(isset($service_categories[0]))
                                        @foreach($service_categories as $service_category)
                                            <a class="cata-box" href="{{url('/service/category/'.$service_category->id)}}">
                                                <span>
                                                   <img class="img_background" src="{{asset('/image/default.jpg')}}" data-src="{{asset('uploads/category/'.$service_category->image)}}"
                                                                alt="">@if(\App\UtilityFunction::getLocal() == "en") {{$service_category->name}} @else {{$service_category->ar_name}} @endif
                                                </span>
                                            </a>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>

                        <div class="col-md-9 col-sm-8">
                            <?php $advertisement_top = \App\Model\Advertisement::getAdvertisement(\App\Http\Controllers\Enum\AdvertisementTypeEnum::SERVICE_PAGE_TOP); ?>
                            <div class="container_12">
                                <div class="grid_12">
                                    @if(count($advertisement_top)>1)
                                        <div class="slidprev" @if (\App\UtilityFunction::getLocal() == 'en') style="margin: 140px 0 0 0;"  @else style="margin: 140px 815px 0 0px;" @endif ><span>Prev</span></div>
                                        <div class="slidnext" style="margin: 140px 0 0 0;"><span>Next</span></div>@endif
                                    <div id="slider">
                                        @if(count($advertisement_top)>0)
                                            @foreach($advertisement_top as $advertisement)
                                                <div class="slide">
                                                    @if(!empty($advertisement->link))<a href="{{url($advertisement->link)}}"> @endif
                                                        <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 870, 340, array('crop'))?>" data-src="<?=Image::url(asset('uploads/advertisement') . '/' . $advertisement->image, 870, 340, array('crop'))?>" alt="">
                                                        @if(!empty($advertisement->link)) </a> @endif
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
                                                <img src="<?=Image::url(asset('image/services.png'), 870, 340, array('crop'))?>"
                                                     alt="" title=""/>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="clear"></div>

                                    <div id="myController">
                                    </div>

                                </div><!-- .grid_12 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($service_categories[0]))
        <?php $i = 0; ?>
        @foreach($service_categories as $service_category)
            <?php $sub_category = $service_category->getSubCategory ?>
            @if(count($sub_category)>0)
                <section id="slider" class="slider-container slider-top-pagination"
                         @if($i==3)style="padding:0; padding-top:40px" @elseif($i>0)style="padding: 0;" @endif>
                    <div class="container">
                        <h2 data-wow-delay="0.3s">@if(\App\UtilityFunction::getLocal() == "en") {{$service_category->name}} @else {{$service_category->ar_name}} @endif</h2>

                        <div class="starSeparatorBox2 clearfix">

                            <div id="owl-product-slider"
                                 class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                                 data-wow-delay="0.7s" data-navigation="true" data-pagination="false"
                                 data-single-item="false"
                                 data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                                 data-min600="2" data-min800="3" data-min1200="4">
                                @foreach($sub_category as $sub)
                                    <?php
                                    $product_count = !empty(\App\Model\ProductCategory::getProductCount($sub,\App\Http\Controllers\Enum\ProductTypeEnum::SERVICE));
                                    ?>

                                    <div class="item">
                                        <div class="product-item hvr-underline-from-center">
                                            <div class="product-item_body product-border">
                                                <img class="img_background product-item_image" src="<?=Image::url(asset('/image/default.jpg'), 261, 136, array('crop'))?>" data-src="<?=Image::url(asset(env('CATEGORY_PHOTO_PATH')).'/'. $sub->image, 261, 136, array('crop'))?>" alt="" >
                                                @if($product_count)<a href="{{url('/service/category/'.$service_category->id.'/'.$sub->id)}}"
                                                                      class="product-item_link">@endif
                                                    @if(empty($product_count))
                                                        <span class="product-item_outofstock color-main font-additional circle">
                                                            @lang('messages.out_of_service')
                                                        </span>
                                                    @endif
                                                    @if($product_count)</a>@endif
                                                @if($product_count)
                                                    <ul class="product-item_info transition">
                                                        <li>
                                                            <a  href="{{url('/service/category/'.$service_category->id.'/'.$sub->id)}}">
                                                                <span aria-hidden="true" class="icon-eye"></span>

                                                                <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                    @lang('messages.view')
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>@endif
                                            </div>
                                            <a class="product-item_footer"  href="{{url('/service/category/'.$service_category->id.'/'.$sub->id)}}">
                                                <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                                    @if(\App\UtilityFunction::getLocal() == "en") {{$sub->name}} @else {{$sub->ar_name}} @endif
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
            @if($i==2)
                <section id="banner" style="margin-bottom: 50px;">
                    <div class="container banner-container wow fadeInUp" data-wow-delay="0.3s">
                        <?php $advertisements = \App\Model\Advertisement::getAdvertisement(\App\Http\Controllers\Enum\AdvertisementTypeEnum::SERVICE_PAGE_MIDDLE); ?>
                        @if(!empty($advertisements))

                            <div class="col-md-12">
                                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">

                                    <!-- Wrapper for slides -->
                                    <div class="carousel-inner" role="listbox">
                                        <?php $j = 0; ?>
                                        @foreach($advertisements as $advertisement)
                                            <div class="item  @if($j==0) active @endif">
                                                <div class="banner-item grid">
                                                    <figure class="effect-bubba">
                                                        @if(!empty($advertisement->link))<a href="{{url($advertisement->link)}}"> @endif
                                                            <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 1170, 300, array('crop'))?>" data-src="<?=Image::url(asset('uploads/advertisement') . '/' . $advertisement->image, 1170, 300, array('crop'))?>" alt="">
                                                        @if(!empty($advertisement->link)) </a> @endif
                                                        <figcaption>
                                                            <h2 class="font-third font-weight-light text-uppercase color-main">@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_one}} @else {{$advertisement->ar_sub_title_one}} @endif</h2>
                                                            @if(!empty($advertisement->main_title))
                                                                <div class="font-additional font-weight-bold text-uppercase customColor">@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->main_title}} @else {{$advertisement->ar_main_title}} @endif</div>
                                                            @endif
                                                            @if(!empty($advertisement->sub_title_two))
                                                                <p class="font-third font-weight-light text-uppercase color-main line-text line-text_white">@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_two}} @else {{$advertisement->ar_sub_title_two}} @endif</p>
                                                            @endif
                                                        </figcaption>
                                                    </figure>
                                                </div>
                                            </div>
                                            <?php $j++; ?>
                                        @endforeach
                                    </div>

                                    @if(count($advertisements)>1)
                                            <!-- Indicators -->
                                    <ol class="carousel-indicators">
                                        @for($i=0;$i<count($advertisements);$i++)
                                            <li data-target="#carousel-example-generic" data-slide-to="{{$i}}"
                                                @if($i==0) class="active" @endif></li>
                                        @endfor
                                    </ol>

                                    <!-- Controls -->
                                    <a class="left carousel-control" href="#carousel-example-generic" role="button"
                                       data-slide="prev">
                                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="right carousel-control" href="#carousel-example-generic" role="button"
                                       data-slide="next">
                                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                    @endif
                                </div>
                            </div>

                        @endif
                    </div>
                </section>
            @endif

            <?php $i++; ?>
        @endforeach
    @endif

    <div class="clearfix"></div>
    <section id="freeShpping" class="borderTopSeparator">
        <div class="container freeshpping-container">
            <div class="row">
                <div class="freeshpping col-lg-3 col-md-3 col-sm-6 col-xs-12 clearfix">
                    <div class="freeshpping-item font-additional wow fadeInLeft" data-wow-delay="0.3s">
                        <span class="icon-trophy customColor" aria-hidden="true"></span>
                        @lang('messages.live_service')
                    </div>
                </div>
                <div class="freeshpping col-lg-3 col-md-3 col-sm-6 col-xs-12 clearfix">
                    <div class="freeshpping-item font-additional wow fadeInUp" data-wow-delay="0.3s">
                        <span class="icon-user-following customColor" aria-hidden="true"></span>
                        @lang('messages.verified_experts')
                    </div>
                </div>
                <div class="freeshpping col-lg-3 col-md-3 col-sm-6 col-xs-12 clearfix">
                    <div class="freeshpping-item font-additional wow fadeInRight" data-wow-delay="0.3s">
                        <span class="icon-handbag customColor" aria-hidden="true"></span>
                        @lang('messages.customer_served')
                    </div>
                </div>
                <div class="freeshpping col-lg-3 col-md-3 col-sm-6 col-xs-12 clearfix">
                    <div class="freeshpping-item font-additional wow fadeInRight" data-wow-delay="0.3s">
                        <span class="icon-star customColor" aria-hidden="true"></span>
                        @lang('messages.average_rating')
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                        width: 856,
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
