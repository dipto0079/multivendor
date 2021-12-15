@extends('frontend.master',['menu'=>'product'])
@section('title',__('messages.page_title.product_category'))
@section('stylesheet')
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

        .product-item_price {
            float: none;
            font-size: 12px;
        }

        ul.rating { display: inline-block; }
    </style>
@stop
@section('content')
    <?php
    $cid = App\Model\ProductCategory::find($category_id);
    $first_child_cid = App\Model\ProductCategory::find($first_child_cid);
    $second_child_cid = App\Model\ProductCategory::find($second_child_cid);

    $filter ="";
    if(isset($_GET['filter'])) $filter = $_GET['filter'];

    ?>

    <section id="pageTitleBox" class="paralax breadcrumb-container img_background"
             @if(isset($cid->banner_image))style="background-image: url('{{asset(env('CATEGORY_PHOTO_PATH').'/'.$cid->banner_image)}}');"
             @else style="background-image: url('{{asset('/image').'/e-commerce.jpg'}}');" @endif>
        <div class="overlay"></div>
        <div class="container relative">
            <h1 class="title font-additional font-weight-normal color-main text-uppercase wow zoomIn"
                data-wow-delay="0.3s">@lang('messages.products')</h1>
            <ul class="breadcrumb-list wow zoomIn" data-wow-delay="0.3s">
                <li>
                    <a href="{{url('/')}}" class="font-additional font-weight-normal color-main text-uppercase">@lang('messages.home')</a>
                    <span>/</span>
                </li>

                @if(isset($cid))
                    <li><a href="{{url('/products/category')}}"
                           class="font-additional font-weight-normal color-main text-uppercase">@lang('messages.categories')</a><span>/</span>
                    </li>
                @else
                    <li class="font-additional font-weight-normal color-main text-uppercase">@lang('messages.categories')</li>
                @endif

                @if(isset($cid))
                    @if(isset($first_child_cid))
                        <li><a href="{{url('/products/category')}}/{{$cid->id}}"
                               class="font-additional font-weight-normal color-main text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$cid->name}}@else{{$cid->ar_name}}@endif</a><span>/</span>
                        </li>
                    @else
                        <li class="font-additional font-weight-normal color-main text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$cid->name}}@else{{$cid->ar_name}}@endif</li>
                    @endif
                @endif

                @if(isset($first_child_cid))
                    @if(isset($second_child_cid))
                        <li><a href="{{url('/products/category')}}/{{$cid->id}}/{{$first_child_cid->id}}"
                               class="font-additional font-weight-normal color-main text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$first_child_cid->name}}@else{{$first_child_cid->ar_name}}@endif</a><span>/</span>
                        </li>
                    @else
                        <li class="font-additional font-weight-normal color-main text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$first_child_cid->name}}@else{{$first_child_cid->ar_name}}@endif</li>
                    @endif
                @endif

                @if(isset($second_child_cid))
                    <li class="font-additional font-weight-normal color-main text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$second_child_cid->name}}@else{{$second_child_cid->ar_name}}@endif</li>
                @endif
            </ul>
        </div>
    </section>
    <section id="pageContent" class="page-content category-type_list">
        <div class="container">
            <div class="row">
                <div class="sidebar col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                        data-wow-delay="0.3s">@lang('messages.categories')</h3>

                    <div style="font-size: 14px" class="sidebar-slider sidebar-slider_btm_padding wow fadeInUp">
                        {!! $category_menu !!}
                    </div>

                    {{--<input type="hidden" id="filtered_category_id" value="{{Request::get('category')}}">--}}
{{--                    <input type="hidden" id="filter_string" value="{{Request::get('filter')}}">--}}
                    {{--<input type="hidden" id="filtered_min_price_id" value="{{$p_min}}">--}}
                    {{--<input type="hidden" id="filtered_max_price_id" value="{{$p_max}}">--}}

                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp "
                        data-wow-delay="0.3s">@lang('messages.by_price')</h3>

                    <div class="sidebar-slider sidebar-slider_btm_padding wow fadeInUp" data-wow-delay="0.3s">
                        <?php
                            $sorting = '';
                            if(!empty(Request::get('sorting'))) $sorting = Request::get('sorting');
                        ?>
                        {!! Form::open(['url' => url()->current(), 'method' => 'get','id'=>'price_filter']) !!}
                        <div class="slider-range" data-min="{{$search_min_price}}" data-max="{{$search_max_price}}" data-default-min="{{$p_min}}" data-default-max="{{$p_max}}" data-range="true"
                             data-value-container-id="priceAmount"></div>
                        <div class="filter-container">
                            <div class="slider-range-value pull-left" style="width: 100%;">
                                <label class="font-main font-weight-normal"
                                       for="priceAmount">@lang('messages.price')</label>
                                <input class="font-main font-weight-normal" type="text" id="priceAmount" readonly>
                            </div>
                            @if(!empty($sorting))
                            <input type="hidden" name="sorting" value="{{Request::get('sorting')}}">
                            @endif
                            <input type="hidden" name="min-price" id="min_price" value="{{$search_min_price}}">
                            <input type="hidden" name="max-price" id="max_price" value="{{$search_max_price}}">

                            <!-- <button onclick="this.form.submit()" class="btn button-border font-additional font-weight-bold hvr-rectangle-out hover-focus-bg hover-focus-border before-bg pull-right">@lang('messages.filter')</button> -->
                        </div>
                        {!! Form::close() !!}
                        <input type="hidden" name="data-min-price" id="data-min-price" value="{{$p_min}}">
                        <input type="hidden" name="data-max-price" id="data-max-price" value="{{$p_max}}">
                    </div>

                    <?php $feartured_products = \App\Model\Product::getSimilarFeaturedProducts($cid);?>

                    @if(isset($feartured_products[0]))
                        <div class="hidden-xs">
                            <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                                data-wow-delay="0.3s">@lang('messages.featured_products')</h3>
                            <ul class="sidebar-popular-product wow fadeInUp" data-wow-delay="0.3s">
                                @foreach($feartured_products as $fp)
                                    <?php $discountArray = $fp->getDealDiscountHTMLArray(); ?>

                                    <li>
                                        <a class="popular-product-item" href="{{url('/product/details').'/'.$fp->id}}">
                                            <?php $media = $fp->getMedia;?>
                                            <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 80, 75, ['crop'])?>"
                                                 @if(isset($media[0])) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 80, 75, ['crop'])?>"
                                                 @else data-src="<?=Image::url(asset('/image/80x75/2.jpg'), 80, 75, ['crop'])?>"
                                                    @endif>
                                            <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{str_limit($fp->name, 30)}}@else{{str_limit($fp->ar_name, 60)}}@endif</span>
                                            <span class="product-item_price font-additional font-weight-normal customColor">{!! $discountArray[0] !!}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 clearfix">
                    <div class="content-box">
                        <div class="category-filter clearfix wow fadeInUp" data-wow-delay="0.3s">
                            <div class="select pull-right">
                                <div class="search-facet-horizontal-form">
                                    <div class="inline-select-wrapper">
                                        <div class="-border-top-none -border-radius-none inline-select">
                                            {!! Form::open(['url' => url()->current(), 'method' => 'get']) !!}
                                            <select name="sorting" class="js-search-facet-sort-by" onchange="this.form.submit()">
                                                <option value="new" @if($sorting=="new") selected @endif>@lang('messages.sort_by') @lang('messages.newest_items')</option>
                                                <option value="rating"  @if($sorting=="rating") selected @endif>@lang('messages.best_rated')</option>
                                                <option value="p_asc"  @if($sorting=="p_asc") selected @endif>@lang('messages.price_low_to_high')</option>
                                                <option value="p_desc"  @if($sorting=="p_desc") selected @endif>@lang('messages.price_high_to_low')</option>
                                            </select>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $page = '';
                            $category_url_string = '';
                            $filter_url_string = '';
                            $price_url = '';
                            if (!empty(Request::get('page'))) $string_mark = '&'; else $string_mark = '?';
                            if (!empty(Request::get('page'))) $page = '?page=' . Request::get('page');
                            if (!empty(Request::get('category'))) $category_url_string = $string_mark . 'category=' . Request::get('category');
                            if (!empty(Request::get('filter'))) $filter_url_string = $string_mark . 'filter=' . Request::get('filter');
                            if (!empty(Request::get('min-price')) && !empty(Request::get('min-price'))) $price_url = '&min-price=' . Request::get('min-price') . '&max-price=' . Request::get('max-price');
                            ?>
                        </div>
                        @if(isset($products))
                            <div class="products-cat clearfix">
                                <div class="loading" style="display: none">
                                    <img width="100" src="{{asset('/image/pageloader.gif')}}" alt="">
                                </div>
                                <ul class="products-list" id="list">
                                    @foreach($products as $product)
                                        @if($filter =='deals')
                                            <?php $product = \App\Model\Product::find($product->id);?>
                                        @endif

                                        <li class="wow fadeInUp" data-wow-delay="0.3s">
                                            <div class="product-list_item row">
                                                <div class="product-list_item_img col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                                                    <ul class="category-images">
                                                        <li class="grid">
                                                            <figure class="effect-bubba wow fadeInRight"
                                                                    data-wow-delay="0.3s">
                                                                <?php $media = $product->getMedia;?>
                                                                <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 270, 285, ['crop'])?>"
                                                                     @if(isset($media[0])) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 270, 285, ['crop'])?>"
                                                                     @else href="<?=Image::url(asset("/images/no-media.jpg"), 270, 285, ['crop'])?>"
                                                                     @endif
                                                                     alt="Category">
                                                                <figcaption>
                                                                    <div class="category-images_content">
                                                                        <h2 class="font-third font-weight-light text-uppercase color-main">
                                                                            @if(\App\UtilityFunction::getLocal()== "en"){{$product->getCategory->name}}@else{{$product->getCategory->ar_name}}@endif</h2>


                                                                    @if($product->getReview->count() != 0)
                                                                            <p class="font-additional font-weight-bold text-uppercase color-main line-text line-text_white">
                                                                                {{$product->getReview->count()}} review<?php if($product->getReview->count() > 1) echo 's';?></p>
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
                                                            <a style="color:#333"
                                                               href="{{url('/product/details/'.$product->id)}}"><h3
                                                                        class="font-additional font-weight-bold text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{str_limit($product->name,30)}}@else{{str_limit($product->ar_name,30)}}@endif</h3>
                                                            </a>
                                                        </div>
                                                        <div class="row">
                                                          <div class="col-sm-6">
                                                            <div class="product-item_price font-additional font-weight-normal customColor">
                                                              {!! $product->getDealDiscountHTMLArray()[0] !!}
                                                            </div>
                                                          </div>
                                                          <div class="col-sm-6 text-right">
                                                            {!! \App\UtilityFunction::createReviewRateHtml($product->getReview->avg('review_rating')) !!}
                                                            <div class="clearfix"></div>
                                                            <a href="{{url('/store/'.$product->getSeller->store_name)}}">{{$product->getSeller->store_name}}</a>
                                                          </div>
                                                        </div>

                                                        <div class="product-list_item_desc font-main font-weight-normal color-third">@if(\App\UtilityFunction::getLocal()== "en"){!! str_limit($product->description,100) !!}@else{!! str_limit($product->ar_description,100) !!}@endif</div>
                                                        <?php
                                                            $favorite_product = '';
                                                            if(Auth::user() != null && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER) $favorite_product =  \App\Model\Product::getUserFavoriteProduct($product->id,Auth::user()->getBuyer->id);
                                                        ?>
                                                        <a href="javascript:;"
                                                           data-id="{{Crypt::encrypt($product->id)}}"
                                                           class="add_to_cart btn button-additional font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">
                                                            <span class="icon-basket" aria-hidden="true"></span>
                                                            @lang('messages.add_to_cart')
                                                        </a>

                                                        <a href="javascript:;" style="border-width: 2px; @if(!empty($favorite_product)) background-color: #FF8300 !important; color: #fff; border-color: #FF8300; @endif"
                                                           data-id="{{Crypt::encrypt($product->id)}}"
                                                           class="add_to_wish_list btn button-border font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-border hover-focus-bg before-bg">
                                                            <span class="icon-heart" aria-hidden="true"></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div id="pagination">
                                @include('frontend.widget.pagination',['paginator'=>$products,'appends'=>['sorting'=>$sorting,'filter'=>Request::get('filter'),'min-price'=>$search_min_price,'max-price'=>$search_max_price]])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('script')
    <script>
        $(function () {
            $(".slider-range").each(function (i) {
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

        $(document).ready(function () {

            $(document.body).on('click', 'a.parent,a.child,a.sub_child', function () {
                var category_name = $(this).data('id');
                $('#filtered_category_id').val(category_name);
                $('.loading').show();
                var min_price = $('#filtered_min_price_id').val();
                var max_price = $('#filtered_max_price_id').val();
                var price_url = '';
                if (min_price != '' && max_price != '') {
                    price_url = '&min-price=' + min_price + '&max-price=' + max_price;
                }
                $.ajax({
                    type: "GET",
                    url: '{{url('/products/category')}}/' + category_name + '?ajax=ajax_call' + price_url,
                    dataType: "json",
                    success: function (data) {
                        $('.loading').hide();
                        $('#list').html(data.data_generate);
                        $('#pagination').html(data.pagination_data_generate);
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
                window.history.pushState("object or string", "Title", "{{url('/products/category')}}/" + category_name + '?min-price=' + min_price + '&max-price=' + max_price);
                $('#grid_url').attr("href", "{{url('/products/category-grid')}}/" + category_name + price_url);
                $('#list_url').attr("href", "{{url('/products/category')}}/" + category_name + price_url);
            });
        });
    </script>
@stop
