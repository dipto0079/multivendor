@extends('frontend.master',['menu'=>'product'])
@section('title',__('messages.page_title.products'))
@section('stylesheet')
    <link href="{{asset(url('css/slider.css'))}}" media="screen" rel="stylesheet" type="text/css">
    @if (\App\UtilityFunction::getLocal() == 'ar')
        <style>
          
        </style>
    @endif
@stop


@section('content')
    <div id="main" class="site-main">
        <div class="container">


            <div class="row">
                <div class="col-sm-12 banner-slider">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="welcome-msg-ctr" style="margin:0 0 20px 0 ">
                                <div class="welcome-ribbon">@lang('messages.welcome')</div>
                                <div class="welcome-triangle"></div>
                                <div class="welcome-msg">@lang('messages.new_to')
                                    <a class="font-additional font-weight-normal customColor"
                                       href="{{url('/buyer-registration')}}">@lang('messages.signup_and_save')</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4 hidden-xs" @if (\App\UtilityFunction::getLocal() == 'ar') style="float: right;" @endif>
                            <div class="row">
                                <div class="col-lg-12 res-catagories">
                                    @if(isset($product_categories[0]))
                                        @foreach($product_categories as $product_category)
                                            <a class="cata-box"
                                               href="{{url('/products/category/'.$product_category->id)}}">
                                                <span>
                                                    <img class="img_background" src="{{asset('/image/default.jpg')}}" data-src="{{asset('uploads/category/'.$product_category->image)}}"
                                                          alt="">@if(\App\UtilityFunction::getLocal() == "en") {{$product_category->name}} @else {{$product_category->ar_name}} @endif
                                                </span>
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="col-md-9 col-sm-8">
                            <?php $advertisement_top = \App\Model\Advertisement::getAdvertisement(\App\Http\Controllers\Enum\AdvertisementTypeEnum::PRODUCT_PAGE_TOP); ?>
                            <div class="container_12">
                                <div class="grid_12">
                                    @if(count($advertisement_top)>1)
                                        <div class="slidprev"><span>Prev</span></div>
                                        <div class="slidnext"><span>Next</span></div>@endif
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
                                                <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 870, 340, array('crop'))?>" data-src="<?=Image::url(asset('image/products.jpg'), 870, 340, array('crop'))?>" alt="">
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

            @if(isset($popular_stores[0]))
            <section id="slider" class="slider-container slider-top-pagination">
                <div class="container">
                    <h2 data-wow-delay="0.3s">@lang('messages.popular_stores')</h2>

                    <div class="starSeparatorBox2 clearfix">
                        <div id="owl-product-slider"
                             class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                             data-wow-delay="0.7s" data-navigation="true" data-pagination="false"
                             data-single-item="false"
                             data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                             data-min600="2" data-min800="3" data-min1200="4">
                            @foreach($popular_stores as $popular_store)
                                <div class="item">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body product-border">
                                          <a title="" href="{{url('store/'.$popular_store->store_name)}}">
                                              <img class="img_background product-item_image"
                                                   src="<?=Image::url(asset('/image/default.jpg'),200,175,['crop'])?>"
                                                   @if(!empty($popular_store->getUser->photo)) data-src="<?=Image::url(asset(env('USER_PHOTO_PATH').$popular_store->getUser->photo),200,175,['crop'])?>"
                                                   @else src="<?=Image::url(asset('image/no-media.jpg'),200,175,['crop'])?>" alt=""
                                                    @endif >
                                          </a>
                                        </div>
                                        <a class="product-item_footer"  href="{{url('/store/'.$popular_store->store_name)}}">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                                {{$popular_store->store_name}}
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

            @if(isset($recent_products[0]))
                <section id="collection">
                    <div class="relative">
                        <div class="isotopeBox noTitle">
                            <div class="starSeparatorBox clearfix">
                                <ul id="filter" class="product-filter clearfix">
                                    <li>
                                        <a href="#" data-id="best-seller" onclick="best_seller(this)"
                                           class="current btn best_seller font-additional font-weight-normal text-uppercase hover-focus-bg"
                                           data-filter=".bestsellers">@lang('messages.best_sellers')</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="new_product(this)" data-id="new-products"
                                           class="new_product btn font-additional font-weight-normal text-uppercase hover-focus-bg"
                                           data-filter=".newproducts">@lang('messages.new_products')</a>
                                    </li>
                                    <li>
                                        <a href="#" data-id="featured" onclick="featured(this)"
                                           class="btn featured font-additional font-weight-normal text-uppercase hover-focus-bg"
                                           data-filter=".specials">@lang('messages.specials')</a>
                                    </li>
                                </ul>
                                <div class="isotope-frame wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="isotope-filter">
                                      @foreach($top_seller_products as $tsp)
                                          <div class="isotope-item  bestsellers">
                                              <div class="product-item hvr-underline-from-center">
                                                  <div class="product-item_body">
                                                      <?php $discountArray = $tsp->getDealDiscountHTMLArray(); ?>
                                                      <?php $media = $tsp->getMedia; ?>
                                                      <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 262, 179, ['crop'])?>"
                                                               @if(isset($media[0]))  data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 262, 179, ['crop'])?>"
                                                               @else  data-src="<?=Image::url(asset('/image/no-media.jpg'), 262, 179, ['crop'])?>"
                                                               @endif alt="Category">

                                                      <a class="product-item_link" href="{{url('/product/details/').'/'.$tsp->id}}">
                                                          {!! $discountArray[1] !!}
                                                      </a>
                                                      <ul class="product-item_info transition">
                                                          <li>
                                                              <a href="javascript:;" class="add_to_cart"
                                                                 data-id="{{Crypt::encrypt($tsp->id)}}">
                                                                  <span class="icon-bag" aria-hidden="true"></span>

                                                                  <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                      @lang('messages.add_to_cart')
                                                                  </div>
                                                              </a>
                                                          </li>

                                                          <li>
                                                              <a href="{{url('/product/details/').'/'.$tsp->id}}">
                                                                  <span class="icon-eye" aria-hidden="true"></span>

                                                                  <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                      @lang('messages.view')
                                                                  </div>
                                                              </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;" class="add_to_wish_list"
                                                                 data-id="{{Crypt::encrypt($tsp->id)}}">
                                                                  <span class="icon-heart" aria-hidden="true"></span>

                                                                  <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                      @lang('messages.add_to_favorites')
                                                                  </div>
                                                              </a>
                                                          </li>
                                                      </ul>
                                                  </div>
                                                  <a href="{{url('/product/details/').'/'.$tsp->id}}" class="product-item_footer">
                                                      <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                                          @if(\App\UtilityFunction::getLocal() == 'en'){{str_limit($tsp->name,30)}}@else{{str_limit($tsp->ar_name,30)}}@endif
                                                      </div>
                                                      <div class="product-item_price font-additional font-weight-normal customColor">
                                                          {!! $discountArray[0] !!}
                                                      </div>
                                                  </a>
                                              </div>
                                          </div>
                                      @endforeach
                                        @foreach($recent_products as $rp)
                                            <div class="isotope-item newproducts ">
                                                <div class="product-item hvr-underline-from-center">
                                                    <div class="product-item_body">

                                                        <?php $discountArray = $rp->getDealDiscountHTMLArray(); ?>

                                                        <?php $media = $rp->getMedia; ?>
                                                        <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 262, 179, ['crop'])?>"
                                                             @if(isset($media[0]))  data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 262, 179, ['crop'])?>"
                                                             @else data-src="<?=Image::url(asset('/image/no-media.jpg'), 262, 179, ['crop'])?>"
                                                             @endif alt="Category">

                                                        <a class="product-item_link" href="{{url('/product/details/').'/'.$rp->id}}">
                                                            {!! $discountArray[1] !!}
                                                        </a>
                                                        <ul class="product-item_info transition">
                                                            <li>
                                                                <a href="javascript:;" class="add_to_cart"
                                                                   data-id="{{Crypt::encrypt($rp->id)}}">
                                                                    <span class="icon-bag" aria-hidden="true"></span>

                                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                        @lang('messages.add_to_cart')
                                                                    </div>
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="{{url('/product/details/').'/'.$rp->id}}">
                                                                    <span class="icon-eye" aria-hidden="true"></span>

                                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                        @lang('messages.view')
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;" class="add_to_wish_list"
                                                                   data-id="{{Crypt::encrypt($rp->id)}}">
                                                                    <span class="icon-heart" aria-hidden="true"></span>

                                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                        @lang('messages.add_to_favorites')
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <a href="{{url('/product/details/').'/'.$rp->id}}" class="product-item_footer">
                                                        <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                                            @if(\App\UtilityFunction::getLocal() == 'en'){{str_limit($rp->name,30)}}@else{{str_limit($rp->ar_name,30)}}@endif
                                                        </div>
                                                        <div class="product-item_price font-additional font-weight-normal customColor">
                                                            {!! $discountArray[0] !!}
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                        @foreach($featured_products as $fp)
                                            <div class="isotope-item  specials">
                                                <div class="product-item hvr-underline-from-center">
                                                    <div class="product-item_body">
                                                        <?php $discountArray = $fp->getDealDiscountHTMLArray(); ?>
                                                        <?php $media = $fp->getMedia; ?>
                                                            <img class="img_background" src="<?=Image::url(asset('/image/default.jpg'), 262, 179, ['crop'])?>"
                                                                 @if(isset($media[0]))  data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 262, 179, ['crop'])?>"
                                                                 @else data-src="<?=Image::url(asset('/image/no-media.jpg'), 262, 179, ['crop'])?>"
                                                                 @endif alt="Category">

                                                        <a class="product-item_link"
                                                           href="{{url('/product/details/').'/'.$fp->id}}">
                                                            {!! $discountArray[1] !!}
                                                        </a>
                                                        <ul class="product-item_info transition">
                                                            <li>
                                                                <a href="javascript:;" class="add_to_cart"
                                                                   data-id="{{Crypt::encrypt($fp->id)}}">
                                                                    <span class="icon-bag" aria-hidden="true"></span>

                                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                        @lang('messages.add_to_cart')
                                                                    </div>
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="{{url('/product/details/').'/'.$fp->id}}">
                                                                    <span class="icon-eye" aria-hidden="true"></span>

                                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                        @lang('messages.view')
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;" class="add_to_wish_list"
                                                                   data-id="{{Crypt::encrypt($fp->id)}}">
                                                                    <span class="icon-heart" aria-hidden="true"></span>

                                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                                        @lang('messages.add_to_favorites')
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <a href="{{url('/product/details/').'/'.$fp->id}}" class="product-item_footer">
                                                        <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                                            @if(\App\UtilityFunction::getLocal() == 'en'){{str_limit($fp->name,30)}}@else{{str_limit($fp->ar_name,30)}}@endif
                                                        </div>
                                                        <div class="product-item_price font-additional font-weight-normal customColor">

                                                            {!! $discountArray[0] !!}

                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-center">
                                        <a href="{{url('/products/category?filter=new-products')}}"
                                           class="btn view_all_url  discount-info_link button-border font-additional font-weight-bold customBorderColor text-uppercase hvr-rectangle-out before-bg wow fadeInRight hover-focus-bg">
                                            @lang('messages.view_all')</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <?php $advertisements = \App\Model\Advertisement::getAdvertisement(\App\Http\Controllers\Enum\AdvertisementTypeEnum::PRODUCT_PAGE_MIDDLE); ?>
            @if(!empty($advertisements))

                <?php $advertisement = $advertisements[rand(0, count($advertisements) - 1)];?>

                <section id="discount" class="discount background-container">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 clearfix">
                                <img class="discount-image wow fadeInLeft img_background" data-wow-delay="0.3s"
                                     src="<?=Image::url(asset('uploads/advertisement') . '/' . $advertisement->image, 328, 297, array('crop'))?>"
                                     data-src="<?=Image::url(asset('uploads/advertisement') . '/' . $advertisement->image, 328, 297, array('crop'))?>"
                                     alt="Discounts"
                                     style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInLeft;">
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 clearfix">
                                <div class="discount-info">
                                    <span class="discount-info_small_txt font-third font-weight-bold text-uppercase wow fadeInRight"
                                          data-wow-delay="0.3s"
                                          style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInRight;">@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_one}} @else {{$advertisement->ar_sub_title_one}} @endif</span>

                            <span class="discount-info_shadow_txt text-shadow font-additional font-weight-bold text-uppercase customColor wow fadeInRight"
                                  data-wow-delay="0.3s"
                                  style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInRight;">@if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->main_title}} @else {{$advertisement->ar_main_title}} @endif</span>

                                    <span class="discount-info_right_txt text-left font-additional font-weight-bold text-uppercase wow fadeInLeft"
                                          data-wow-delay="0.3s"
                                          style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInLeft;">
                                    @if(!empty($advertisement->sub_title_two))<span class="arrow_right"
                                                                                    aria-hidden="true"></span>@endif
                                        @if(\App\UtilityFunction::getLocal() == 'en'){{$advertisement->sub_title_two}} @else {{$advertisement->ar_sub_title_two}} @endif
                                </span>
                                    @if(!empty($advertisement->link))<a href="{{url($advertisement->link)}}"
                                                                        class="discount-info_link button-border font-additional font-weight-bold customBorderColor text-uppercase hvr-rectangle-out before-bg wow fadeInRight hover-focus-bg pull-left"
                                                                        data-wow-delay="0.3s"
                                                                        style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInRight;">@lang('messages.view')</a>@endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <section id="previewInfo" class="borderTopSeparator">
                <div class="relative blog-preview">
                    <div class="smallLogo"></div>
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 clearfix">
                            <h3 class="title-primary font-additional font-weight-bold text-uppercase wow zoomIn"
                                data-wow-delay="0.3s"
                                style="visibility: visible; animation-delay: 0.3s; animation-name: zoomIn;">@lang('messages.recent_comments')</h3>

                            <div class="starSeparator wow zoomIn" data-wow-delay="0.3s"
                                 style="visibility: visible; animation-delay: 0.3s; animation-name: zoomIn;">
                                <span class="icon-star" aria-hidden="true"></span>
                            </div>
                            <?php $recent_comments = App\Model\ProductReview::orderBy('created_at','desc')->groupBy('id')->take(3)->get(); ?>
                            @if(!empty(count($recent_comments)))
                              @foreach($recent_comments as $recent_comment)
                                <div class="blog-preview_item wow fadeInUp" data-wow-delay="0.3s"
                                   style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInUp;">
                                  <span class="blog-preview_image" style="border: 1px solid #ddd; padding: 3px;">
                                      <div style="height: 175px;  background-repeat: no-repeat;
                                      @if(!empty($recent_comment->getBuyer->getUser->photo) && (isset($recent_comment->getBuyer->getUser->facebook_id) && isset($recent_comment->getBuyer->getUser->google_id)))
                                              background-size: contain;
                                              background-position: top center;
                                              background-image: url('{{asset(env('USER_PHOTO_PATH') . $recent_comment->getBuyer->getUser->photo)}}');
                                      @elseif(!empty($recent_comment->getBuyer->getUser->photo) && (!isset($recent_comment->getBuyer->getUser->facebook_id) || !isset($recent_comment->getBuyer->getUser->google_id)))
                                              background-size: contain;
                                              background-position: top center;
                                              background-image: url('{{$recent_comment->getBuyer->getUser->photo}}');
                                      @else
                                              background-position: center center;
                                              background-image: url('{{asset('/image/default.jpg')}}');
                                      @endif
                                              "></div>

                                      <div class="blog-preview_posted">
                                          <span class="blog-preview_date font-additional font-weight-bold text-uppercase">{{date('d F',strtotime($recent_comment->created_at))}}</span>
                                          <span class="blog-preview_comments font-additional font-weight-normal text-uppercase">{{$recent_comment->getProduct->getReview->count()}} COMMENT<?php if($recent_comment->getProduct->getReview->count() > 1) echo "s"; ?></span>
                                      </div>
                                  </span>

                                  <div class="blog-preview_info">
                                      <h4 class="blog-preview_title font-additional font-weight-bold text-uppercase">{{$recent_comment->getBuyer->getUser->username}}</h4>

                                      <div class="blog-preview_desc font-main font-weight-normal">{{str_limit($recent_comment->review_comment,150)}}</div>
                                      <!-- <a class="blog-preview_btn button-border font-additional font-weight-normal before-bg text-uppercase hvr-rectangle-out hover-focus-bg"
                                         href="blog-post.html">@lang('messages.more')</a> -->
                                  </div>
                                </div>
                              @endforeach
                            @endif
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 clearfix">
                            <h3 class="title-primary font-additional font-weight-bold text-uppercase wow zoomIn"
                                data-wow-delay="0.3s"
                                style="visibility: visible; animation-delay: 0.3s; animation-name: zoomIn;">@lang('messages.customer_says')</h3>

                            <div class="starSeparator wow zoomIn" data-wow-delay="0.3s"
                                 style="visibility: visible; animation-delay: 0.3s; animation-name: zoomIn;">
                                <span class="icon-star" aria-hidden="true"></span>
                            </div>
                            <div class="mentions-slider vertical-slider wow fadeInUp" data-wow-delay="0.3s"
                                 style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInUp;">
                                <div class="bx-wrapper" style="max-width: 100%; margin: 0px auto;">
                                    <div class="bx-viewport"
                                         style="width: 100%; overflow: hidden; position: relative; height: 519px;">
                                        <ul class="bxslider" data-mode="vertical" data-slide-margin="51"
                                            data-min-slides="3"
                                            data-move-slides="1" data-pager="false" data-pager-custom="null"
                                            data-controls="true"
                                            style="width: auto; position: relative; transition-duration: 0s; transform: translate3d(0px, -760px, 0px);">
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;"
                                                class="bx-clone">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/4.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Karla Anderson
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;"
                                                class="bx-clone">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/5.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Sheela Khan
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;"
                                                class="bx-clone">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/3.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Smith John
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/3.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Smith John
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/4.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Karla Anderson
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/5.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Sheela Khan
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/3.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Smith John
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;"
                                                class="bx-clone">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/3.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Smith John
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;"
                                                class="bx-clone">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/4.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Karla Anderson
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li style="float: none; list-style: none; position: relative; width: 370px; margin-bottom: 51px;"
                                                class="bx-clone">
                                                <div class="clients-comment clearfix">
                                                    <div class="clients-comment_ava">
                                                        <img src="{{asset('')}}/image/70x70/5.jpg" alt="Client">
                                                    </div>
                                                    <div class="clients-comment_body font-main color-third">
                                                        <i class="fa fa-quote-left customColor"></i>
                                                        Vtae sodales aliq uam morbi non sem lacus port mollis. Nunc
                                                        condime tum
                                                        metus eud molest sed consectetuer.
                                                    </div>
                                                    <div class="clients-comment_author font-additional font-weight-normal">
                                                        Sheela Khan
                                                        <span>Happy Customer</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="bx-controls bx-has-controls-direction">
                                        <div class="bx-controls-direction"><a class="bx-prev" href=""><i
                                                        class="fa fa-angle-left"></i></a><a class="bx-next" href=""><i
                                                        class="fa fa-angle-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="clearfix"></div>
    <section id="freeShpping" class="borderTopSeparator">
        <div class="container freeshpping-container">
            <div class="row">
                <div class="freeshpping col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                    <div class="freeshpping-item font-additional wow fadeInLeft" data-wow-delay="0.3s">
                        <span class="icon-globe-alt customColor" aria-hidden="true"></span>
                        @lang('messages.free_shipping')
                    </div>
                </div>
                <div class="freeshpping col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                    <div class="freeshpping-item font-additional wow fadeInUp" data-wow-delay="0.3s">
                        <span class="icon-support customColor" aria-hidden="true"></span>
                        @lang('messages.customer_support')
                    </div>
                </div>
                <div class="freeshpping col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                    <div class="freeshpping-item font-additional wow fadeInRight" data-wow-delay="0.3s">
                        <span class="icon-handbag customColor" aria-hidden="true"></span>
                        @lang('messages.returns_and_exchanges')
                    </div>
                </div>
            </div>
        </div>
    </section>


@stop
@section('script')
    <script>
        function new_product() {
            $('.view_all_url').attr('href', '{{url('products/category?filter=new-products')}}');
        }
        function best_seller() {
            $('.view_all_url').attr('href', '{{url('products/category?filter=best-seller')}}');
        }
        function featured() {
            $('.view_all_url').attr('href', '{{url('products/category?filter=featured')}}');
        }

    </script>

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
