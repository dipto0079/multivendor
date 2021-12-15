@extends('frontend.master',['menu'=>''])
@section('title',__('messages.page_title.search'))
@section('stylesheet')


    <link rel="stylesheet" href="{{asset('/css/starwars.css')}}">
    <style>
        .cata-box {
            border-bottom: 1px solid #ececec !important;
        }

        .cata-box span {
            font-size: 14px !important;
        }

        .category-images li {
            width: 100%;
        }

        .category-images {
            padding: 0;
        }

        .inline-select {
            position: relative;
            border-right: 1px solid #e0e0e0;
            padding-right: 10px;
            margin-right: 5px;
            background: transparent;
        }

        .search-facet-header > h2, .search-facet-header--horizontal > h2 {
            display: inline-block;
            pointer-events: none;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
            padding: 0 0 10px;
            font-size: 14px;
            font-weight: bold;
            line-height: normal;
            color: #454545;
        }

        .inline-select > select {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            width: 190px;
            padding: 0 25px 0 20px;
            background: none;
            border: 0;
            font-size: 14px;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            height: 28px;
            line-height: 28px;
            color: #666666;
            -moz-appearance: none;
            -webkit-appearance: none;
        }

        .inline-select:before {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 5px solid #666666;
            border-top: none;
            height: 0;
            width: 0;
            top: 30%;
        }

        .inline-select:after, .inline-select:before {
            content: "";
            pointer-events: none;
            position: absolute;
            right: 15px;
        }

        .inline-select:after {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #666666;
            border-bottom: none;
            height: 0;
            width: 0;
            top: 55%;
        }

        .inline-select:after, .inline-select:before {
            content: "";
            pointer-events: none;
            position: absolute;
            right: 15px;
        }

        .inline-select-wrapper {
            display: inline-block;
            margin-bottom: 10px;
        }

        .restInfoWrap {
            text-align: initial;
            margin-bottom: 60px;
        }

        .restLogoWrap {
            float: left;
        }

        .restLogoWrap #logoImg {
            height: 110px;
            width: 110px;
        }

        .restTitleRatingWrap {
            margin-left: 130px;
        }

        .restTitle {
            padding: 10px 0;
        }

        .restTitleText {
            font-size: 30px;
            color: #ffffff;
        }

        .favHeart {
            color: #ec2028;
            font-size: 21px;
            margin-left: 5px;
            cursor: pointer;
        }

        .itemSearchWrap {
            position: relative;
        }

        .itemSearchWrap #menuSearchInput {
            border: 0;
            border-radius: 4px;
            height: 49px;
            width: 100%;
            padding: 10px 15px;
            color: #585858;
            font-size: 16px;
        }

        .itemSearchWrap .searchIcon {
            position: absolute;
            right: 16px;
            top: 13px;
            color: #a6a6a6;
            font-size: 23px;
        }

        .restPriceInfoWrap {
            color: #f2f2f2;
            text-align: right;
            text-transform: uppercase;
        }

        .restPriceInfoWrap .restPriceInfo {
            display: inline-block;
            margin-right: 22px;
        }

        .restPriceInfoWrap .restPriceInfo p {
            margin: 0;
            text-align: right;
            line-height: 11px;
            margin-bottom: 3px;
            font-size: 13px;
        }

        .pageTabsWrap .nav-tabs {
            margin-bottom: 0;
            border: 0;
        }

        .pageTabsWrap .nav-tabs > li {
            margin-right: 7px;
        }

        .pageTabsWrap .nav-tabs > li > a {
            background-color: #FF8300;
            padding: 10px 22px 9px;
            border-color: transparent;
            font-weight: 700;
            font-size: 15px;
            vertical-align: top;
            color: #FFF;
            display: inline-block;
        }

        .pageTabsWrap .nav-tabs > li > a:hover {
            background-color: #ffffff;
            color: #000000;
        }

        .pageTabsWrap .nav-tabs > li.active > a, .pageTabsWrap .nav-tabs > li.active > a:focus, .pageTabsWrap .nav-tabs > li.active > a:hover {
            background-color: #ffffff;
            color: #000000;
        }

        .ratingCount {
            color: #999999;
            font-size: 14px;
            font-style: italic;
            margin-left: 5px;
        }

        #infoHolder > span {
            margin-right: 20px;
            margin-bottom: 0;
            display: inline-block;
            font-size: 22px;
            font-weight: 700;
        }

        #reviewContainer .reviewBody {
            margin-top: 20px;
            font-size: 14px;
            text-align: justify;
        }

        .category {
            color: #fff;
            text-align: left;
            margin-top: 5px;
            font-size: 20px;
            text-transform: uppercase;
        }
        ul.rating { display: inline-block; }
        .breadcrumb-container { padding: 50px 0; min-height: inherit; }
    </style>

@stop
@section('content')
    <section id="pageTitleBox" class="paralax breadcrumb-container img_background"
             style="background-image: url('{{asset('/image/paralax/6.jpg')}}'); padding-bottom: 0;">
        <div class="overlay"></div>
        <div class="container relative">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <h3 style="color: #fff; margin-bottom: 30px;">@lang('messages.search_result')</h3>
                </div>

            </div><!-- row -->

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="pageTabsWrap">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#product" aria-controls="product" role="tab"
                                                                      data-toggle="tab">@lang('messages.stores.details.product')</a>
                            </li>
                            <li role="presentation"><a href="#service" aria-controls="service" role="tab" data-toggle="tab">@lang('messages.service')</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div><!-- row -->
        </div>
    </section>
    <section id="pageContent" class="page-content category-type_list">
        <div class="container">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="product">
                    <div class="row">
                        <div class="sidebar col-lg-3 col-md-3 col-sm-3 col-xs-12 clearfix">
                            <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                                data-wow-delay="0.3s">@lang('messages.categories')</h3>

                            <div class="sidebar-slider sidebar-slider_btm_padding wow fadeInUp">
                                {!! $product_category_menu !!}
                            </div>
                            <div class="sidebar-slider sidebar-slider_btm_padding wow fadeInUp" data-wow-delay="0.3s">
                                <?php
                                $product_sorting = '';
                                if(!empty(Request::get('product_sorting'))) $product_sorting = Request::get('product_sorting');
                                ?>
                                {!! Form::open(['url' => url()->current(), 'method' => 'get','id'=>'price_filter']) !!}
                                <div class="product-price-slider-range slider-range" data-min="{{$product_search_min_price}}" data-max="{{$product_search_max_price}}" data-default-min="{{$product_p_min}}" data-default-max="{{$product_p_max}}" data-range="true"
                                     data-value-container-id="priceAmount"></div>
                                <div class="filter-container">
                                    <div class="slider-range-value pull-left" style="width: 100%;">
                                        <label class="font-main font-weight-normal"
                                               for="priceAmount">@lang('messages.price')</label>
                                        <input class="font-main font-weight-normal" type="text" id="priceAmount" readonly>
                                    </div>
                                    @if(!empty($product_sorting))
                                        <input type="hidden" name="sorting" value="{{Request::get('product_sorting')}}">
                                    @endif
                                    <input type="hidden" name="min-price" id="min_price" value="{{$product_search_min_price}}">
                                    <input type="hidden" name="max-price" id="max_price" value="{{$product_search_max_price}}">

                                    <!-- <button onclick="this.form.submit()" class="btn button-border font-additional font-weight-bold hvr-rectangle-out hover-focus-bg hover-focus-border before-bg pull-right">@lang('messages.filter')</button> -->
                                </div>
                                {!! Form::close() !!}
                                <input type="hidden" name="data-min-price" id="data-min-price" value="{{$product_p_min}}">
                                <input type="hidden" name="data-max-price" id="data-max-price" value="{{$product_p_max}}">
                            </div>
                            <?php
                                //$cid = App\Model\ProductCategory::find($seller_category->getCategory->id);
                                //$feartured_products = \App\Model\Product::getSimilarFeaturedProducts($cid);
                            ?>

                            @if(isset($feartured_products[0]))
                                <div class="hidden-xs">
                                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                                        data-wow-delay="0.3s">@lang('messages.featured_products')</h3>
                                    <ul class="sidebar-popular-product wow fadeInUp" data-wow-delay="0.3s">
                                        @foreach($feartured_products as $fp)
                                            <?php $discountArray = $fp->getDealDiscountHTMLArray(); ?>

                                            <li>
                                                <a class="popular-product-item"
                                                   href="{{url('/product/details').'/'.$fp->id}}">
                                                    <?php $media = $fp->getMedia;?>
                                                    <img class="img_background"
                                                         src="<?=Image::url(asset('/image/default.jpg'), 80, 75, ['crop'])?>"
                                                         @if(isset($media[0])) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 80, 75, ['crop'])?>"
                                                         @else data-src="<?=Image::url(asset('/image/no-media.jpg'), 80, 75, ['crop'])?>"
                                                            @endif>
                                                    <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{str_limit($fp->name, 30)}}@else{{str_limit($fp->ar_name, 30)}}@endif</span>
                                                    <span class="product-item_price font-additional font-weight-normal customColor">{!! $discountArray[0] !!}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 clearfix">
                            <div class="content-box">
                                <div class="category-filter clearfix wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="select pull-right">
                                        <div class="search-facet-horizontal-form">
                                            <div class="inline-select-wrapper">
                                                <div class="-border-top-none -border-radius-none inline-select">
                                                    {!! Form::open(['url' => url()->current(), 'method' => 'get']) !!}
                                                    <select name="sorting" class="js-search-facet-sort-by" onchange="this.form.submit()">
                                                        <option value="new" @if($product_sorting=="new") selected @endif>@lang('messages.sort_by') @lang('messages.newest_items')</option>
                                                        <option value="rating"  @if($product_sorting=="rating") selected @endif>@lang('messages.best_rated')</option>
                                                        <option value="p_asc"  @if($product_sorting=="p_asc") selected @endif>@lang('messages.price_low_to_high')</option>
                                                        <option value="p_desc"  @if($product_sorting=="p_desc") selected @endif>@lang('messages.price_high_to_low')</option>
                                                    </select>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @if(isset($products))
                                    <div class="products-cat clearfix">
                                        <div class="loading" style="display: none">
                                            <img width="100" src="{{asset('/image/pageloader.gif')}}" alt="">
                                        </div>
                                        <ul class="products-list" id="list">
                                            @foreach($products as $product)
                                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                                        <div class="product-list_item row">
                                                            <div class="product-list_item_img col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                                                                <ul class="category-images">
                                                                    <li class="grid">
                                                                        <figure class="effect-bubba wow fadeInRight"
                                                                                data-wow-delay="0.3s">

                                                                            <?php $media = $product->getMedia;?>
                                                                            <img class="img_background"
                                                                                 src="<?=Image::url(asset('/image/default.jpg'), 270, 285, ['crop'])?>"
                                                                                 @if(isset($media[0])) data-src="<?=Image::url(asset('uploads/media/' . $media[0]->file_in_disk), 270, 285, ['crop'])?>"
                                                                                 @else href="<?=Image::url(asset("/images/no-media.jpg"), 270, 285, ['crop'])?>"
                                                                                 @endif alt="Category">
                                                                            <figcaption>
                                                                                <div class="category-images_content">
                                                                                    <h2 class="font-third font-weight-light text-uppercase color-main">
                                                                                        @if(\App\UtilityFunction::getLocal()== "en"){{$product->getCategory->name}}@else{{$product->getCategory->ar_name}}@endif
                                                                                    </h2>

                                                                                    @if($product->getReview->count() != 0)
                                                                                        <p class="font-additional font-weight-bold text-uppercase color-main line-text line-text_white">
                                                                                            {{$product->getReview->count()}}
                                                                                            @if(\App\UtilityFunction::getLocal()== "en")
                                                                                                review<?php if ($product->getReview->count() > 1) echo 's';?>
                                                                                            @else
                                                                                                @if($product->getReview->count() > 1)
                                                                                                    استعراض
                                                                                                @else إعادة النظر
                                                                                                @endif
                                                                                            @endif
                                                                                        </p>
                                                                                    @endif
                                                                                </div>
                                                                                <a href="{{url('/product/details/'.$product->id)}}">@lang('messages.view_more')</a>
                                                                            </figcaption>
                                                                        </figure>
                                                                    </li>
                                                                </ul>
                                                            </div>


                                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 clearfix">
                                                                <div class="product-list-info">
                                                                    <div class="product-list_item_title">
                                                                        <h3 class="font-additional font-weight-bold text-uppercase">
                                                                            <a href="{{url('/product/details/'.$product->id)}}">
                                                                                @if(\App\UtilityFunction::getLocal()== "en") {{str_limit($product->name,35)}} @else {{str_limit($product->ar_name,35)}} @endif
                                                                            </a>
                                                                        </h3>
                                                                        <div class="clearfix"></div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="product-item_price font-additional font-weight-normal customColor">
                                                                                {!! $product->getDealDiscountHTMLArray()[0] !!}
                                                                            </div>
                                                                            {!! \App\UtilityFunction::createReviewRateHtml($product->getReview->avg('review_rating')) !!}
                                                                        </div>
                                                                    </div>
                                                                    <div class="product-list_item_desc font-main font-weight-normal color-third">@if(\App\UtilityFunction::getLocal()== "en") {{ str_limit($product->description,180) }} @else {{ str_limit($product->ar_description,180) }} @endif</div>
                                                                    @if($product->quantity >0)
                                                                        <a href="javascript:;"
                                                                           data-id="{{Crypt::encrypt($product->id)}}"
                                                                           class="add_to_cart btn button-additional font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">
                                                                        <span class="icon-basket"
                                                                              aria-hidden="true"></span>
                                                                            @lang('messages.add_to_cart')
                                                                        </a>
                                                                    @else
                                                                        <span class="btn btn-default"
                                                                              style="color: red; cursor: auto; font-weight: 700;  float: left;">@lang('messages.buyer.product_not_available')</span>
                                                                    @endif
                                                                    <?php
                                                                    $favorite_product = '';
                                                                    if(Auth::user() != null && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER) $favorite_product =  \App\Model\Product::getUserFavoriteProduct($product->id,Auth::user()->getBuyer->id);
                                                                    ?>
                                                                    <a href="javascript:;" style="border-width: 2px; @if(!empty($favorite_product)) background-color: #FF8300 !important; color: #fff; border-color: #FF8300; @endif"
                                                                       data-id="{{Crypt::encrypt($product->id)}}"
                                                                       class="add_to_wish_list btn button-border font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-border hover-focus-bg before-bg">
                                                                        <span class="icon-heart"
                                                                              aria-hidden="true"></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div id="pagination">
                                        @include('frontend.widget.pagination',['paginator'=>$products,'appends'=>['product_sorting'=>$product_sorting,'filter'=>Request::get('filter'),'search'=>Request::get('search'),'min-price'=>$product_search_min_price,'max-price'=>$product_search_max_price]])
                                    </div>
                                @else
                                    @lang('messages.error_message.no_product_found')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="service">
                    <div class="row">
                        <div class="sidebar col-lg-3 col-md-3 col-sm-3 col-xs-12 clearfix">
                            <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                                data-wow-delay="0.3s">@lang('messages.categories')</h3>

                            <div class="sidebar-slider sidebar-slider_btm_padding wow fadeInUp">
                                {!! $service_category_menu !!}
                            </div>
                            <div class="sidebar-slider sidebar-slider_btm_padding wow fadeInUp" data-wow-delay="0.3s">
                                <?php
                                $service_sorting = '';
                                if(!empty(Request::get('service_sorting'))) $service_sorting = Request::get('service_sorting');
                                ?>
                                {!! Form::open(['url' => url()->current(), 'method' => 'get','id'=>'price_filter']) !!}
                                <div class="service-price-slider-range slider-range" data-min="{{$service_search_min_price}}" data-max="{{$service_search_max_price}}" data-default-min="{{$service_p_min}}" data-default-max="{{$service_p_max}}" data-range="true"
                                     data-value-container-id="priceAmount"></div>
                                <div class="filter-container">
                                    <div class="slider-range-value pull-left" style="width: 100%;">
                                        <label class="font-main font-weight-normal"
                                               for="priceAmount">@lang('messages.price')</label>
                                        <input class="font-main font-weight-normal" type="text" id="priceAmount" readonly>
                                    </div>
                                    @if(!empty($service_sorting))
                                        <input type="hidden" name="sorting" value="{{Request::get('service_sorting')}}">
                                    @endif
                                    <input type="hidden" name="min-price" id="min_price" value="{{$service_search_min_price}}">
                                    <input type="hidden" name="max-price" id="max_price" value="{{$service_search_max_price}}">

                                    <!-- <button onclick="this.form.submit()" class="btn button-border font-additional font-weight-bold hvr-rectangle-out hover-focus-bg hover-focus-border before-bg pull-right">@lang('messages.filter')</button> -->
                                </div>
                                {!! Form::close() !!}
                                <input type="hidden" name="data-min-price" id="data-min-price" value="{{$service_p_min}}">
                                <input type="hidden" name="data-max-price" id="data-max-price" value="{{$service_p_max}}">
                            </div>
                            <?php
                            //$cid = App\Model\ProductCategory::find($seller_category->getCategory->id);
                            //$feartured_products = \App\Model\Product::getSimilarFeaturedProducts($cid);
                            ?>

                            @if(isset($feartured_products[0]))
                                <div class="hidden-xs">
                                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                                        data-wow-delay="0.3s">@lang('messages.featured_products')</h3>
                                    <ul class="sidebar-popular-product wow fadeInUp" data-wow-delay="0.3s">
                                        @foreach($feartured_products as $fp)
                                            <?php $discountArray = $fp->getDealDiscountHTMLArray(); ?>

                                            <li>
                                                <a class="popular-product-item"
                                                   href="{{url('/product/details').'/'.$fp->id}}">
                                                    <?php $media = $fp->getMedia;?>
                                                    <img class="img_background"
                                                         src="<?=Image::url(asset('/image/default.jpg'), 80, 75, ['crop'])?>"
                                                         @if(isset($media[0])) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 80, 75, ['crop'])?>"
                                                         @else data-src="<?=Image::url(asset('/image/no-media.jpg'), 80, 75, ['crop'])?>"
                                                            @endif>
                                                    <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{str_limit($fp->name, 30)}}@else{{str_limit($fp->ar_name, 30)}}@endif</span>
                                                    <span class="product-item_price font-additional font-weight-normal customColor">{!! $discountArray[0] !!}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 clearfix">
                            <div class="content-box">
                                <div class="category-filter clearfix wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="select pull-right">
                                        <div class="search-facet-horizontal-form">
                                            <div class="inline-select-wrapper">
                                                <div class="-border-top-none -border-radius-none inline-select">
                                                    {!! Form::open(['url' => url()->current(), 'method' => 'get']) !!}
                                                    <select name="service_sorting" class="js-search-facet-sort-by" onchange="this.form.submit()">
                                                        <option value="new" @if($service_sorting=="new") selected @endif>@lang('messages.sort_by') @lang('messages.newest_items')</option>
                                                        <option value="rating"  @if($service_sorting=="rating") selected @endif>@lang('messages.best_rated')</option>
                                                        <option value="p_asc"  @if($service_sorting=="p_asc") selected @endif>@lang('messages.price_low_to_high')</option>
                                                        <option value="p_desc"  @if($service_sorting=="p_desc") selected @endif>@lang('messages.price_high_to_low')</option>
                                                    </select>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @if(isset($services))
                                    <div class="products-cat clearfix">
                                        <div class="loading" style="display: none">
                                            <img width="100" src="{{asset('/image/pageloader.gif')}}" alt="">
                                        </div>
                                        <ul class="products-list" id="list">
                                            @foreach($services as $product)
                                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                                    <div class="product-list_item row">
                                                        <div class="product-list_item_img col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                                                            <ul class="category-images">
                                                                <li class="grid">
                                                                    <figure class="effect-bubba wow fadeInRight"
                                                                            data-wow-delay="0.3s">
                                                                        <?php $media = $product->getMedia; //dd($media) ?>
                                                                        <img class="img_background"
                                                                             src="<?=Image::url(asset('/image/default.jpg'), 270, 285, ['crop'])?>"
                                                                             @if(isset($media[0])) data-src="<?=Image::url(asset('uploads/media/' . $media[0]->file_in_disk), 270, 285, ['crop'])?>"
                                                                             @else data-src="<?=Image::url(asset('image/no-media.jpg'), 270, 285, ['crop'])?>"
                                                                             @endif alt="Category">
                                                                        <figcaption>
                                                                            <div class="category-images_content">
                                                                                <h2 class="font-third font-weight-light text-uppercase color-main">
                                                                                    @if(\App\UtilityFunction::getLocal()== "en"){{$product->getCategory->name}}@else{{$product->getCategory->ar_name}}@endif</h2>

                                                                                @if($product->getReview->count() != 0)
                                                                                    <p class="font-additional font-weight-bold text-uppercase color-main line-text line-text_white">
                                                                                        {{$product->getReview->count()}}
                                                                                        @if(\App\UtilityFunction::getLocal()== "en")
                                                                                            review<?php if ($product->getReview->count() > 1) echo 's';?>
                                                                                        @else
                                                                                            @if($product->getReview->count() > 1)
                                                                                                استعراض
                                                                                            @else إعادة النظر
                                                                                            @endif
                                                                                        @endif
                                                                                    </p>
                                                                                @endif
                                                                            </div>
                                                                            <a href="{{url('/service/details/'.$product->id)}}">@lang('messages.view_more')</a>
                                                                        </figcaption>
                                                                    </figure>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 clearfix">
                                                            <div class="product-list-info">
                                                                <div class="product-list_item_title">
                                                                    <a href="{{url('/service/details/'.$product->id)}}" style="color: #333;">
                                                                        <h3 class="font-additional font-weight-bold text-uppercase">
                                                                            @if(\App\UtilityFunction::getLocal()== "en") {{str_limit($product->name,35)}} @else {{str_limit($product->name,35)}} @endif</h3></a>
                                                                    <br>

                                                                    <div class="clearfix"></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="product-item_price font-additional font-weight-normal customColor">
                                                                            {!! $product->getDealDiscountHTMLArray()[0] !!}
                                                                        </div>
                                                                        {!! \App\UtilityFunction::createReviewRateHtml($product->getReview->avg('review_rating')) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="product-list_item_desc font-main font-weight-normal color-third">@if(\App\UtilityFunction::getLocal()== "en") {{ str_limit($product->description,150) }} @else {{ str_limit($product->ar_description,150) }} @endif</div>
                                                                <a href="{{url('/seller/details/'.$product->getSeller->store_name)}}"
                                                                   class="btn button-additional font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">
                                                                    {{--<span class="icon-user-following" aria-hidden="true"></span>--}}
                                                                    @lang('messages.view_profile')
                                                                </a>


                                                                <a style="margin-left: 5px" data-toggle="modal" href=".enquiry_modal" data-id="{{Crypt::encrypt($product->getSeller->id)}}" data-service="{{$product->id}}"
                                                                   class="enquiry_btn btn btn-primary font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">
                                                                    {{--<span class="icon-phone" aria-hidden="true"></span>--}}
                                                                    @lang('messages.send_enquiry')
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div id="pagination">
                                        @include('frontend.widget.pagination',['paginator'=>$products,'appends'=>['sorting'=>$service_sorting,'filter'=>Request::get('filter'),'search'=>Request::get('search'),'min-price'=>$service_search_min_price,'max-price'=>$service_search_max_price]])
                                    </div>
                                @else
                                    @lang('messages.error_message.no_product_found')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade enquiry_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang('messages.send_enquiry')</h4>
                </div>
                <div class="text-center" id="enquiry_loading"></div>
                <form class="" action="{{url('/send-enquiry')}}" method="post" id="enquiry_form">
                    {{csrf_field()}}
                    <div class="modal-body" id="enquiry_html">

                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">@lang('messages.close')</button>
                        <input type="hidden" name="skip" id="skip" value="0">
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src={{asset('/js/validator.min.js')}}></script>
    <script type="text/javascript">
        function validationFunc() {
            $('#enquiry_form').validator().on('submit', function (e) {
                if (e.isDefaultPrevented()) {
                    // handle the invalid form...
                    console.log("validation failed");
                } else {
                    // everything looks good!
                    if(grecaptcha.getResponse() == '') {
                        e.preventDefault();
                        alert('Recaptcha is not checked.');
                    }
                    console.log("validation success");
                }
            });
        }
    </script>
    <script type="text/javascript">
        $(document.body).on('click','.enquiry_btn',function () {
            var id = $(this).data('id');
            var service_id = $(this).data('service');
            $('#skip').val(1);
            $('#enquiry_html').empty();
            $('#enquiry_loading').html('<img src="{{asset('/image/pageloader.gif')}}" width="100">');
            var recaptchaSiteKey = '{{env('GOOGLE_RECAPTCHA_KEY')}}';

            $.ajax({
                type: "POST",
                dataType: "json",
                url: $('#enquiry_form').attr('action') + '?id=' + id + '&service_id='+service_id,
                data: $('#enquiry_form').serialize(),
                success: function (data) {
                    $('#enquiry_loading').empty();
                    $('#enquiry_html').html(data.data_generate);
                    $('#skip').val(0);
                    grecaptcha.render('g-recaptcha', {
                        sitekey: recaptchaSiteKey,
                        callback: function(response) {
                            console.log(response);
                        }
                    });
                    validationFunc();
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });
        });
    </script>

    <script>
        $(function () {
            $(".product-price-slider-range").each(function (i) {
                var minAmount = $(this).data("min");
                var minDefaultAmount = $(this).data("default-min");
                var maxAmount = $(this).data("max");
                var maxDefaultAmount = $(this).data("default-max");
                var rangeData = $(this).data("range");
                var valueContainerId = $(this).data("value-container-id");

                $(this).slider({
                    range: rangeData,
                    min: minDefaultAmount,
                    max: maxDefaultAmount,
                    values: [minAmount, maxAmount],
                    change: function(event, ui){
                        $('#min_price').val(ui.values[0]);
                        $('#max_price').val(ui.values[1]);

                        $("#"+valueContainerId).val("{{env('CURRENCY_SYMBOL')}} "+ui.values[0] + " - " + "{{env('CURRENCY_SYMBOL')}} "+ui.values[1]);
                        $('#price_filter').submit();
                    }
                });
                $("#"+valueContainerId).val("{{env('CURRENCY_SYMBOL')}} "+$(this).slider("values", 0) + " - " + "{{env('CURRENCY_SYMBOL')}} "+$(this).slider("values", 1));
            });

            $(".service-price-slider-range").each(function (i) {
                var minAmount = $(this).data("min");
                var minDefaultAmount = $(this).data("default-min");
                var maxAmount = $(this).data("max");
                var maxDefaultAmount = $(this).data("default-max");
                var rangeData = $(this).data("range");
                var valueContainerId = $(this).data("value-container-id");

                $(this).slider({
                    range: rangeData,
                    min: minDefaultAmount,
                    max: maxDefaultAmount,
                    values: [minAmount, maxAmount],
                    change: function(event, ui){
                        $('#min_price').val(ui.values[0]);
                        $('#max_price').val(ui.values[1]);

                        $("#"+valueContainerId).val("{{env('CURRENCY_SYMBOL')}} "+ui.values[0] + " - " + "{{env('CURRENCY_SYMBOL')}} "+ui.values[1]);
                        $('#price_filter').submit();
                    }
                });
                $("#"+valueContainerId).val("{{env('CURRENCY_SYMBOL')}} "+$(this).slider("values", 0) + " - " + "{{env('CURRENCY_SYMBOL')}} "+$(this).slider("values", 1));
            });
        });


        $(document.body).on('click', '.favorite_store', function (e) {
            var seller_id = $(this).data('id');
            toastr.clear();
            $.ajax({
                type: "GET",
                url: '{{url('/add-to-favorite-store')}}?seller_id=' + seller_id,
                dataType: 'json',
                context: this,
                success: function (data) {
                    if (data.success == true) {
                        if (data.exists == 1) {
                            $(this).removeClass('fa-heart-o');
                            $(this).addClass('fa-heart');
                            toastr.success('@lang('messages.error_message.store_added_successfully')');
                        } else {
                            toastr.warning('@lang('messages.error_message.this_store_is_already_in_your_favorite_list')');
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
    <script src="{{asset('/js/starwars.js')}}"></script>
    <script>
        $(' .rate_row ').starwarsjs({
            stars: 5,
            range: [1, 6],
            default_stars: 1,
            on_select: function (data) {
            }
        });
    </script>
@stop
