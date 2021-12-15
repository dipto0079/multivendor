@extends('frontend.master',['menu'=>'deals'])
@section('title',__('messages.page_title.deals'))
@section('stylesheet')
    <style>
        .product-item {
            max-width: none;
        }

        .slider-container {
            padding: 30px 0 58px;
        }

        .temp_table td {
            border: 1px solid gray;
            width: 50px;
            padding: 5px
        }
    </style>
@stop
@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <?php $topDealInCategories = \App\Model\Product::topDealInCategories(); ?>
            @if(count($topDealInCategories)>0)
                <section id="slider" class="slider-container slider-top-pagination">
                    <div class="">
                        <h2 data-wow-delay="0.3s">@lang('messages.deals.top_deal')</h2>

                        <div class="starSeparatorBox2 clearfix">

                            <div id="owl-product-slider"
                                 class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                                 data-wow-delay="0.7s" data-navigation="true" data-pagination="false"
                                 data-single-item="false"
                                 data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                                 data-min600="2" data-min800="3" data-min1200="3">

                                @foreach($topDealInCategories as $tdc)
                                    @if($tdc->deal_count > 0)
                                        <?php $url_link = ""?>

                                        @if($tdc->product_category_type_id == \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)
                                            <?php $url_link = url('/products/category') . '/' . $tdc->id . '?filter=deals';?>
                                        @else
                                            <?php $url_link = url('/service/category') . '/' . $tdc->id . '?filter=deals';?>
                                        @endif

                                        <div class="item">
                                            <div class="product-item hvr-underline-from-center">
                                                <div class="product-item_body product-border">

                                                    <img class="product-item_image img_background"
                                                         src="<?=Image::url(asset('/image/default.jpg'), 350, 210, array('crop'))?>"
                                                         @if(!empty($tdc->image)) data-src="<?=Image::url(asset(env('CATEGORY_PHOTO_PATH')) . '/' . $tdc->banner_image, 350, 210, array('crop'))?>"
                                                         @else data-src="<?=Image::url(asset('/image/no-media.jpg'), 350, 210, array('crop'))?>"
                                                            @endif>


                                                    <a href="{{$url_link}}" class="product-item_link">
                                                        @if($tdc->product_category_type_id == \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)
                                                            <span class="product-item_sale color-main font-additional customBgColor circle">{{$tdc->deal_count}}</span>
                                                        @else
                                                            <span class="product-item_new color-main font-additional text-uppercase circle">{{$tdc->deal_count}}</span>
                                                        @endif
                                                    </a>
                                                    <ul class="product-item_info transition">
                                                        <li>
                                                            <a href="{{$url_link}}">
                                                                <span aria-hidden="true" class="icon-eye"></span>

                                                                <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                    @lang('messages.view')
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <a class="product-item_footer" href="{{$url_link}}">
                                                    <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                                        @if(\App\UtilityFunction::getLocal()=="en")
                                                            {{$tdc->name}}
                                                        @else
                                                            {{$tdc->ar_name}}
                                                        @endif
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <?php $dealOfTheDay = \App\Model\Product::dealOfTheDayProducts(); ?>
            @if(isset($dealOfTheDay[0]))
                <section id="slider" class="slider-container slider-top-pagination">
                    <div class="">
                        <h2 data-wow-delay="0.3s">@lang('messages.deals.deal_of_the_day')</h2>

                        <div class="starSeparatorBox2 clearfix">

                            <div id="owl-product-slider"
                                 class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                                 data-wow-delay="0.7s" data-navigation="true" data-pagination="false"
                                 data-single-item="false"
                                 data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                                 data-min600="2" data-min800="3" data-min1200="4">

                                @foreach($dealOfTheDay as $d)
                                    <?php
                                    $product = \App\Model\Product::find($d->id);
                                    $discountArray = $product->getDealDiscountHTMLArray(); ?>

                                    <div class="item">
                                        <div class="product-item hvr-underline-from-center">
                                            <div class="product-item_body">

                                                <?php $m = $product->getMedia; ?>

                                                <img class="product-item_image img_background"
                                                     src="<?=Image::url(asset('/image/default.jpg'), 250, 150, ['crop'])?>"
                                                     @if(count($m)>0) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $m[0]->file_in_disk), 250, 150, ['crop'])?>"
                                                     @else data-src="<?=Image::url(asset('/image/no-media.jpg'), 250, 150, ['crop'])?>"
                                                     @endif @if(\App\UtilityFunction::getLocal()=="en") alt="{{$d->name}}"
                                                     title="{{$d->name}}" @else alt="{{$d->ar_name}}"
                                                     title="{{$d->name}}" @endif>


                                                <a class="product-item_link"
                                                   href="{{url('/product/details/'.$d->id)}}">
                                                    {!! $discountArray[1] !!}
                                                </a>
                                                <ul class="product-item_info transition">
                                                    <li>
                                                        <a href="javascript:;" class="add_to_cart" data-id="{{Crypt::encrypt($d->id)}}">
                                                            <span class="icon-bag" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{url('/product/details/'.$d->id)}}">
                                                            <span class="icon-eye" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                @lang('messages.view')
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" class="add_to_wish_list" data-id="{{Crypt::encrypt($d->id)}}">
                                                            <span class="icon-heart" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="{{url('/product/details/'.$d->id)}}" class="product-item_footer">
                                                <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">@if(\App\UtilityFunction::getLocal()=="en") {{str_limit($d->name,50)}} @else {{str_limit($d->ar_name,50)}}  @endif</div>
                                                <div class="product-item_short font-weight-normal text-center">@if(\App\UtilityFunction::getLocal()=="en") {{$d->deal_title}} @else {{$d->ar_deal_title}}  @endif
                                                </div>
                                                <div class="product-item_price font-additional font-weight-normal customColor">{!! $discountArray[0] !!}</div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <?php $trending_offers = \App\Model\Product::trendingOffers(); ?>
            @if(isset($trending_offers[0]))
                <section id="slider" class="slider-container slider-top-pagination">
                    <div class="">
                        <h2 data-wow-delay="0.3s">@lang('messages.deals.trending_offers')</h2>

                        <div class="starSeparatorBox2 clearfix">

                            <div id="owl-product-slider"
                                 class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                                 data-wow-delay="0.7s" data-navigation="true" data-pagination="false"
                                 data-single-item="false"
                                 data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                                 data-min600="2" data-min800="3" data-min1200="4">

                                @foreach($trending_offers as $to)
                                    <?php
                                    $product = \App\Model\Product::find($to->id);
                                    $discountArray = $product->getDealDiscountHTMLArray(); ?>
                                    <div class="item">
                                        <div class="product-item hvr-underline-from-center">
                                            <div class="product-item_body">
                                                <?php $m = $product->getMedia; ?>

                                                <img class="product-item_image img_background"
                                                     src="<?=Image::url(asset('/image/default.jpg'), 250, 150, ['crop'])?>"
                                                     @if(count($m)>0) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $m[0]->file_in_disk), 250, 150, ['crop'])?>"
                                                     @else data-src="<?=Image::url(asset('image/no-media.jpg'), 250, 150, ['crop'])?>"
                                                     @endif @if(\App\UtilityFunction::getLocal()=="en") alt="{{$to->name}}"
                                                     title="{{$to->name}}" @else alt="{{$to->ar_name}}"
                                                     title="{{$to->name}}" @endif>

                                                <a class="product-item_link"
                                                   href="{{url('/product/details/'.$to->id)}}">
                                                    {!! $discountArray[1] !!}
                                                </a>

                                                <ul class="product-item_info transition">
                                                    <li>
                                                        <a href="javascript:;" class="add_to_cart" data-id="{{Crypt::encrypt($to->id)}}">
                                                            <span class="icon-bag" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{url('/product/details/'.$to->id)}}">
                                                            <span class="icon-eye" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                @lang('messages.view')
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" class="add_to_wish_list" data-id="{{Crypt::encrypt($to->id)}}">
                                                            <span class="icon-heart" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="{{url('/product/details/'.$to->id)}}" class="product-item_footer">
                                                <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">@if(\App\UtilityFunction::getLocal()=="en") {{str_limit($to->name,50)}} @else {{str_limit($to->ar_name,50)}}  @endif</div>
                                                <div class="product-item_short font-weight-normal text-center">@if(\App\UtilityFunction::getLocal()=="en") {{$to->deal_title}} @else {{$to->ar_deal_title}}  @endif
                                                </div>
                                                <div class="product-item_price font-additional font-weight-normal customColor">
                                                    {!! $discountArray[0] !!}</div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <?php $editors_choice = \App\Model\Product::editorsChoice(); ?>
            @if(isset($editors_choice[0]))
                <section id="slider" class="slider-container slider-top-pagination">
                    <div class="">
                        <h2 data-wow-delay="0.3s">@lang('messages.deals.editor_choice')</h2>

                        <div class="starSeparatorBox2 clearfix">

                            <div id="owl-product-slider"
                                 class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                                 data-wow-delay="0.7s" data-navigation="true" data-pagination="false"
                                 data-single-item="false"
                                 data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                                 data-min600="2" data-min800="3" data-min1200="4">

                                @foreach($editors_choice as $ec)
                                    <?php
                                    $product = \App\Model\Product::find($ec->id);
                                    $discountArray = $product->getDealDiscountHTMLArray(); ?>
                                    <div class="item">
                                        <div class="product-item hvr-underline-from-center">
                                            <div class="product-item_body">
                                                <?php $m = $product->getMedia; ?>

                                                <img class="product-item_image img_background"
                                                     src="<?=Image::url(asset('/image/default.jpg'), 250, 150, ['crop'])?>"
                                                     @if(count($m)>0) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $m[0]->file_in_disk), 250, 150, ['crop'])?>"
                                                     @else data-src="<?=Image::url(asset('/image/no-media.jpg'), 250, 150, ['crop'])?>"
                                                     @endif @if(\App\UtilityFunction::getLocal()=="en") alt="{{$ec->name}}"
                                                     title="{{$ec->name}}" @else alt="{{$ec->ar_name}}"
                                                     title="{{$ec->name}}" @endif>

                                                <a class="product-item_link"
                                                   href="{{url('/product/details/'.$ec->id)}}">
                                                    {!! $discountArray[1] !!}
                                                </a>

                                                <ul class="product-item_info transition">
                                                    <li>
                                                        <a href="javascript:;" class="add_to_cart" data-id="{{Crypt::encrypt($ec->id)}}">
                                                            <span class="icon-bag" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{url('/product/details/'.$ec->id)}}">
                                                            <span class="icon-eye" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                @lang('messages.view')
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" class="add_to_wish_list" data-id="{{Crypt::encrypt($ec->id)}}">
                                                            <span class="icon-heart" aria-hidden="true"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="{{url('/product/details/'.$ec->id)}}" class="product-item_footer">
                                                <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">@if(\App\UtilityFunction::getLocal()=="en") {{str_limit($ec->name,50)}} @else {{str_limit($ec->ar_name,50)}}  @endif</div>
                                                <div class="product-item_short font-weight-normal text-center">@if(\App\UtilityFunction::getLocal()=="en") {{$ec->deal_title}} @else {{$ec->ar_deal_title}}  @endif</div>
                                                <div class="product-item_price font-additional font-weight-normal customColor">
                                                    {!! $discountArray[0] !!}
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

            <?php $top_sellers = \App\Model\Product::topSeller(); ?>
            @if(isset($top_sellers[0]))
                <section id="slider" class="slider-container slider-top-pagination">
                    <div class="">
                        <h2 data-wow-delay="0.3s">@lang('messages.deals.top_sellers')</h2>

                        <div class="starSeparatorBox2 clearfix">

                            <div id="owl-product-slider"
                                 class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                                 data-wow-delay="0.7s" data-navigation="true" data-pagination="false"
                                 data-single-item="false"
                                 data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                                 data-min600="2" data-min800="3" data-min1200="3">
                                @foreach($top_sellers as $ts)
                                    <div class="item">
                                        <div class="product-item hvr-underline-from-center">
                                            <div class="product-item_body product-border">
                                                <img alt="Product"
                                                     src="<?=Image::url(asset('/image/default.jpg'), 358, 215, ['crop'])?>"
                                                     @if(!empty($ts->getUser->photo)) data-src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $ts->getUser->photo), 358, 215, ['crop'])?>"
                                                     @else src="<?=Image::url(asset('image/no-media.jpg'), 358, 215, ['crop'])?>"
                                                     alt=""
                                                     @endif class="img_background product-item_image">


                                                <a href="{{url('/store/'.$ts->store_name)}}" class="product-item_link">
                                                    <span class="product-item_sale color-main font-additional customBgColor circle">{{$ts->order_items_count}}</span>
                                                </a>
                                                <ul class="product-item_info transition">
                                                    <li>
                                                        <a href="#">
                                                            <span aria-hidden="true" class="icon-eye"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                @lang('messages.view')
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" class="favorite_store"
                                                           data-id="{{base64_encode($ts->id)}}">
                                                            <span aria-hidden="true" class="icon-heart"></span>

                                                            <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                @lang('messages.add_to_favorites')
                                                            </div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a class="product-item_footer" href="{{url('/store/'.$ts->store_name)}}">
                                                <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                                    @if(\App\UtilityFunction::getLocal()=="en") {{$ts->getCategory->name}} @else {{$ts->getCategory->ar_name}}  @endif</div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
            {{--<div class="text-center">--}}
            {{--<a href="{{url('/deal/all')}}" class="btn btn-primary font-additional hvr-grow">VIEW ALL Deals</a>--}}
            {{--</div>--}}
        </div>
    </div>

@stop


{{--<div class="item">--}}
{{--<div class="product-item hvr-underline-from-center">--}}
{{--<div class="product-item_body product-border">--}}
{{--<img alt="Product" src="{{asset('')}}/image/deal/2.jpg"--}}
{{--class="product-item_image">--}}
{{--<a href="{{url('/service/provider')}}" class="product-item_link">--}}
{{--<span class="product-item_new color-main font-additional text-uppercase circle">500</span>--}}
{{--</a>--}}
{{--<ul class="product-item_info transition">--}}
{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-eye"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--VIEW--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-heart"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--@lang('messages.add_to_favorites')--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--</ul>--}}
{{--</div>--}}
{{--<a class="product-item_footer" href="{{url('/service/provider')}}">--}}
{{--<div class="product-item_title font-additional font-weight-bold text-center text-uppercase">--}}
{{--Interior Designer--}}
{{--</div>--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="item">--}}
{{--<div class="product-item hvr-underline-from-center">--}}
{{--<div class="product-item_body product-border">--}}
{{--<img alt="Product" src="{{asset('')}}/image/deal/3.jpg"--}}
{{--class="product-item_image">--}}
{{--<a href="{{url('/service/provider')}}" class="product-item_link">--}}
{{--<span class="product-item_outofstock color-main font-additional circle">OUT OF SERVICE</span>--}}
{{--</a>--}}
{{--<ul class="product-item_info transition">--}}
{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-eye"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--VIEW--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-heart"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--@lang('messages.add_to_favorites')--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--</ul>--}}
{{--</div>--}}
{{--<a class="product-item_footer" href="{{url('/service/provider')}}">--}}
{{--<div class="product-item_title font-additional font-weight-bold text-center text-uppercase">--}}
{{--Architect--}}
{{--</div>--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="item">--}}
{{--<div class="product-item hvr-underline-from-center">--}}
{{--<div class="product-item_body product-border">--}}
{{--<img alt="Product" src="{{asset('')}}/image/deal/2.jpg"--}}
{{--class="product-item_image">--}}
{{--<a href="{{url('/service/provider')}}" class="product-item_link">--}}
{{--<span class="product-item_sale color-main font-additional customBgColor circle">200</span>--}}
{{--</a>--}}
{{--<ul class="product-item_info transition">--}}
{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-eye"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--@lang('messages.view')--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-heart"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--@lang('messages.add_to_favorites')--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--</ul>--}}
{{--</div>--}}
{{--<a class="product-item_footer" href="{{url('/service/provider')}}">--}}
{{--<div class="product-item_title font-additional font-weight-bold text-center text-uppercase">--}}
{{--House Painters--}}
{{--</div>--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="item">--}}
{{--<div class="product-item hvr-underline-from-center">--}}
{{--<div class="product-item_body product-border">--}}
{{--<img alt="Product" src="{{asset('')}}/image/deal/1.jpg"--}}
{{--class="product-item_image">--}}
{{--<a href="{{url('/service/provider')}}" class="product-item_link">--}}
{{--<span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>--}}
{{--</a>--}}
{{--<ul class="product-item_info transition">--}}

{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-eye"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--VIEW--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--<li>--}}
{{--<a href="#">--}}
{{--<span aria-hidden="true" class="icon-heart"></span>--}}

{{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
{{--@lang('messages.add_to_favorites')--}}
{{--</div>--}}
{{--</a>--}}
{{--</li>--}}
{{--</ul>--}}
{{--</div>--}}
{{--<a class="product-item_footer" href="{{url('/service/provider')}}">--}}
{{--<div class="product-item_title font-additional font-weight-bold text-center text-uppercase">--}}
{{--Construction and Renovation--}}
{{--</div>--}}

{{--</a>--}}
{{--</div>--}}
{{--</div>--}}

@section('script')
    <script>
        $(document.body).on('click', '.favorite_store', function (e) {
            var seller_id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: '{{url('/add-to-favorite-store')}}?seller_id=' + seller_id,
                dataType: 'json',
                context: this,
                success: function (data) {
                    if (data.success == true) {
                        if (data.exists == 1) {
                            $(this).find('i.fa').removeClass('fa-heart-o');
                            $(this).find('i.fa').addClass('fa-heart');
                            toastr.success('Store added successfully.');
                        } else {
                            toastr.warning('This store is already in your favorite list.');
                        }

                    } else {
                        window.location.replace('{{url('/buyer/login')}}');
                    }
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
            });
        });
    </script>
@stop
