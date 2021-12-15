@extends('frontend.master',['menu'=>'product'])
@section('title',__('messages.page_title.product_details'))
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('build/css/separate/vendor/bootstrap-touchspin.min.css')}}">
    <link rel="stylesheet" href="{{asset('/css/starwars.css')}}">
    <link rel="stylesheet" href="{{asset('/css/jqzoom.css')}}">
    <style>
        .pdetail-img {
            border: 1px solid #eeeeee;
        }

        .bootstrap-touchspin .input-group-btn-vertical > .btn {
            padding: 11px 10px;
        }

        .bootstrap-touchspin .form-control {
            height: 46px;
            font-size: 16px;
        }
        /*.product-gallery_preview { width: 30% !important; margin-right: 3%; margin-bottom: 3%; }*/
        /*.product-gallery_preview a { width: 100% !important; border: 1px solid #DDDDDD; margin: 0 !important; }*/
        /*.product-gallery_preview a.zoomThumbActive { opacity: 1; }*/
    </style>
@stop
@section('content')
    <?php
      $second_child_cid = App\Model\ProductCategory::find($product->category_id);
      $first_child_cid = $second_child_cid->getParentCategory;
      $cid = $first_child_cid->getParentCategory;
    ?>
    <section id="pageTitleBox" class="paralax breadcrumb-container"
             @if(isset($cid->banner_image))style="background-image: url('{{asset(env('CATEGORY_PHOTO_PATH').'/'.$cid->banner_image)}}');"
             @else style="background-image: url('{{asset('/image').'/e-commerce.jpg'}}');" @endif>
        <div class="overlay"></div>
        <div class="container relative">
            <h1 class="title font-additional font-weight-normal color-main text-uppercase wow zoomIn"
                data-wow-delay="0.3s">@lang('messages.products')</h1>
            <ul class="breadcrumb-list wow zoomIn" data-wow-delay="0.3s">
                <li>
                    <a href="{{url('/')}}"
                       class="font-additional font-weight-normal color-main text-uppercase">@lang('messages.home')</a>
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
                    @if(isset($second_child_cid) && isset($cid))
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

    <section id="productDetails" class="product-details">
        <div class="container">
            @if(!empty(Session::get('message')))
                <div class="alert alert-success alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    {{Session::get('message')}}
                </div>
            @endif
            @if(!empty(Session::get('error_message')))
                <div class="alert alert-warning alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    {{Session::get('error_message')}}
                </div>
            @endif
            <div class="row">
                <div class="product-gallery col-lg-4 col-md-4 col-sm-7 col-xs-12 clearfix">
                    <ul class="bxslider" data-mode="fade" data-slide-margin="0" data-min-slides="1" data-move-slides="1"
                        data-pager="true" data-pager-custom="#bx-pager" data-controls="false">

                        <?php $all_media = $product->getMedia; ?>

                        @if(isset($all_media[0]))
                            @foreach($all_media as $media)
                                <li class="">
                                    <a href="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media->file_in_disk), 1000, 830, ['crop'])?>" title="" data-spzoom>
                                        <img class=""
                                             src="<?=Image::url(asset('/image/default.jpg'), 500, 530, ['crop'])?>"
                                             @if(isset($media)) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media->file_in_disk), 500, 530, ['crop'])?>"
                                             @else src="<?=Image::url(asset("/images/no-media.jpg"), 500, 530, ['crop'])?>"
                                                @endif >
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><img class="pdetail-img" src="{{url(asset('/image/no-media.jpg'))}}"/></li>
                        @endif
                    </ul>
                    <div id="bx-pager" class="product-gallery_preview">
                        <?php $count = 0; ?>
                        @if(isset($all_media[0]))
                            @foreach($all_media as $media)
                                <a data-slide-index="{{$count}}" href="#">

                                    <img class="pdetail-img img_background"
                                         src="<?=Image::url(asset('/image/default.jpg'), 500, 530, ['crop'])?>"
                                         @if(isset($media)) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media->file_in_disk), 500, 530, ['crop'])?>"
                                         @else data-src="<?=Image::url(asset('image/no-media.jpg'), 500, 530, ['crop'])?>"
                                            @endif >

                                </a>
                                <?php $count++ ?>
                            @endforeach

                        @endif
                    </div>


                    {{--<div class="clearfix"></div>--}}
                    {{--<div class="bzoom_wrap">--}}
                        {{--<ul id="bzoom">--}}
                            {{--@if(isset($all_media[0]))--}}
                                {{--@foreach($all_media as $media)--}}
                                    {{--<li>--}}
                                        {{--<img class="bzoom_thumb_image" src="https://unsplash.it/375/500?image=201" title="first img" />--}}
                                        {{--<img class="bzoom_big_image" src="https://unsplash.it/750/1000?image=201"/>--}}
                                    {{--</li>--}}
                                {{--@endforeach--}}

                            {{--@endif--}}
                        {{--</ul>--}}
                        {{--<div class="clearfix"></div>--}}
                    {{--</div>--}}

                </div>
                <div class="product-brand col-lg-3 col-md-3 col-sm-5 col-xs-12 hidden-xs clearfix">
                    <?php
                        $similar_products = $product->getSimilarProducts();
                        $feartured_products = \App\Model\Product::getSimilarFeaturedProducts($product->getCategory);
                    ?>
                    @if(isset($feartured_products[0]))
                        <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                            data-wow-delay="0.3s">@lang('messages.feartured_products')</h3>

                        <div class="product-sidebar-slider vertical-slider slide-controls-top wow fadeInUp"
                             data-wow-delay="0.3s">
                            <ul class="bxslider" data-mode="vertical" data-slide-margin="26" data-min-slides="3"
                                data-move-slides="1" data-pager="false" data-pager-custom="null" data-controls="true">
                                @foreach($feartured_products as $fp)
                                    <?php $discountArray = $fp->getDealDiscountHTMLArray(); ?>
                                    <li>
                                        <a class="popular-product-item" href="{{url('/product/details').'/'.$fp->id}}">
                                            <?php $media = $fp->getMedia;?>
                                            <img @if(isset($media[0])) src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 80, 75, ['crop'])?>"
                                                 @else src="{{url(asset('/image/no-media.jpg'))}}"
                                                    @endif>
                                            <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">@if(\App\UtilityFunction::getLocal()=="en") {{str_limit($fp->name, 30)}} @else {{str_limit($fp->ar_name, 60)}} @endif</span>
                                            <span class="popular-product-item_price font-additional customColor">{!! $discountArray[0] !!}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(count($similar_products)>0)
                        <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                            data-wow-delay="0.3s">@lang('messages.similar_products')</h3>

                        <div class="product-sidebar-slider product-brand-container vertical-slider slide-controls-top wow fadeInUp"
                             data-wow-delay="0.3s">
                            <ul class="bxslider" data-mode="vertical" data-slide-margin="26" data-min-slides="2"
                                data-move-slides="1" data-pager="false" data-pager-custom="null" data-controls="true">

                                @foreach($similar_products as $sp)
                                    <?php $m = $sp->getMedia; ?>

                                    <li>
                                        <a class="product-brand_item"
                                           href="{{url('/product/details/')}}/{{$sp->id}}">

                                            <img @if(count($m)>0) src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $m[0]->file_in_disk), 250, 150, ['crop'])?>"
                                                 @else src="{{url(asset('/image/no-media.jpg'))}}"
                                                 @endif @if(\App\UtilityFunction::getLocal()=="en") alt="{{$sp->name}}"
                                                 title="{{$sp->name}}" @else alt="{{$sp->ar_name}}"
                                                 title="{{$sp->name}}" @endif>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="product-cart pull-left col-lg-5 col-md-5 col-sm-12 col-xs-12 clearfix">
                    <div class="product-options_header clearfix wow fadeInUp" data-wow-delay="0.3s">
                        <h3 class="font-additional font-weight-bold text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$product->name}}@else{{$product->ar_name}}@endif</h3>

                        <div class="product-item_price font-additional font-weight-normal customColor">
                            {!! $product->getDealDiscountHTMLArray()[0] !!}
                        </div>
                        {!! \App\UtilityFunction::createReviewRateHtml($product->getReview->avg('review_rating')) !!}
                        <div class="clearfix"></div>
                        <h4 class="font-additional font-weight-bold text-uppercase @if(\App\UtilityFunction::getLocal()== "en"){{"pull-right"}}@endif "  style="padding: 10px 0 0;">
                          {{-- @lang('messages.store') --}}
                          <a class="font-main color-third hover-focus-color"
                                 href="{{url('/store')}}/{{$product->getSeller->store_name}}">{{$product->getSeller->store_name}}</a>
                        </h4>
                    </div>
                    @if(!empty($product->description))
                    <div style="overflow: hidden;" class="product-options_body clearfix wow fadeInUp" data-wow-delay="0.3s">
                        <h4 class="font-additional font-weight-bold text-uppercase">@lang('messages.product_description')</h4>

                        <div class="product-options_desc font-main color-third more">@if(\App\UtilityFunction::getLocal()== "en"){!! $product->description !!}@else{!! $product->ar_description !!}@endif</div>
                    </div>
                    @endif
                    <div class="product-options_cart clearfix wow fadeInUp" data-wow-delay="0.3s">
                        <div class="product-options_row" style="padding-top: 0">
                            @if($product->quantity >0)
                            <div class="form-group" style="width: 90px; float: left;">
                                <input type="text" class="quantity_input" data-bts-max="{{$product->quantity}}" id="quantity_input" min="1"
                                       max="{{$product->quantity}}" name="product-qty" value="1">
                            </div>
                            <!-- <div class="product-counter">
                                <input class="font-main font-weight-normal" type="number" name="product-qty"
                                       id="productQuantity" value="1" min="1" max="10" readonly="readonly">

                                <div class="productCounter addQuantity font-main hover-focus-color"
                                     data-counter-step="1" data-counter-type="add"
                                     data-counter-field="#productQuantity">+
                                </div>
                                <div class="productCounter minusQuantity font-main hover-focus-color"
                                     data-counter-step="1" data-counter-type="minus"
                                     data-counter-field="#productQuantity">-
                                </div>
                            </div> -->

                            <a href="javascript:;" data-id="{{Crypt::encrypt($product->id)}}"
                               class="add_to_cart btn button-additional button-big font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">
                                <span class="icon-basket" aria-hidden="true"></span>
                                @lang('messages.add_to_cart')
                            </a>

                            @else
                                <span class="btn btn-default" style="color: red; cursor: auto; font-weight: 700;">@lang('messages.buyer.product_not_available')</span>
                            @endif
                        </div>
                        <div class="product-options_row">
                            <ul class="product-links">
                                <li>
                                    <?php
                                        $favorite_product = '';
                                        if(Auth::user() != null && Auth::user()->user_type == \App\Http\Controllers\Enum\UserTypeEnum::USER) $favorite_product  = \App\Model\Product::getUserFavoriteProduct($product->id,Auth::user()->getBuyer->id);
                                    ?>
                                    <a href="javascript:;" data-id="{{Crypt::encrypt($product->id)}}" style="@if(!empty($favorite_product)) color: #FF8300; @endif"
                                       class="add_to_wish_list font-additional font-weight-normal hover-focus-color">
                                        <span aria-hidden="true" class="icon-heart"></span>
                                        @lang('messages.add_to_wishlist')
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="font-additional font-weight-normal hover-focus-color">
                                        <span class="icon-envelope" aria-hidden="true"></span>
                                        @lang('messages.email_to_friend')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="product-options_footer clearfix wow fadeInUp" data-wow-delay="0.3s">
                        <div class="product-options_row">

                            <h4 class="font-additional font-weight-bold text-uppercase">@lang('messages.category')</h4>

                            <ul class="tags-list">
                                <li><a href="{{url('/products/category/'.$product->category_id)}}"
                                       class="font-main color-third hover-focus-color">@if(isset($product->getCategory))
                                            @if(\App\UtilityFunction::getLocal()== "en"){{$product->getCategory->name}}@else{{$product->getCategory->ar_name}}@endif
                                        @endif</a>
                                </li>
                            </ul>

                            <h4 class="font-additional font-weight-bold text-uppercase">@lang('messages.share_this_product')</h4>
                            <ul class="social-list">
                                <li>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{url('/product/details/'.$product->id)}}"
                                       class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span
                                                class="social_facebook" aria-hidden="true"></span></a></li>
                                <li><a href="https://twitter.com/home?status={{url('/product/details/'.$product->id)}}"
                                       class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span
                                                class="social_twitter" aria-hidden="true"></span></a></li>
                                <li><a href="https://plus.google.com/share?url={{url('/product/details/'.$product->id)}}"
                                       class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span
                                                class="social_googleplus" aria-hidden="true"></span></a></li>
                                <li>
                                    <a href="https://pinterest.com/pin/create/button/?url={{url('/product/details/'.$product->id)}}&media=&description="
                                       class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span
                                                class="social_pinterest" aria-hidden="true"></span></a></li>
                                {{--<li><a href="#" class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span class="social_instagram" aria-hidden="true"></span></a></li>--}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="tabsPanel" class="tabs-container background-container">
        <div class="container">
            <div class="tabs-panel" role="tabpanel">
                <ul class="nav-tabs wow fadeInUp" data-wow-delay="0.3s" role="tablist">

                    <li role="presentation" class="active">
                        <a class="hover-focus-border hover-focus-bg font-additional font-weight-normal hvr-rectangle-out before-bg"
                           href="#reviews" aria-controls="profile" role="tab"
                           data-toggle="tab">@lang('messages.reviews') @if(!empty($product->getReview->count()))
                                ({{$product->getReview->count()}})@endif</a>
                    </li>
                    <li role="presentation">
                        <a class="hover-focus-border hover-focus-bg font-additional font-weight-normal hvr-rectangle-out before-bg"
                           href="#delivery-returns" aria-controls="messages" role="tab"
                           data-toggle="tab">@lang('messages.delivery_returns')</a>
                    </li>
                </ul>
                <div class="tab-content wow fadeInUp" data-wow-delay="0.3s">

                    <div id="reviews" class="tab-pane fade in active" role="tabpanel">
                        <h3>@lang('messages.all_reivews')
                            @if(!empty(Auth::user()->getBuyer) && empty(\App\Model\ProductReview::where('product_id',$product->id)->where('buyer_id',Auth::user()->getBuyer->id)->exists()))
                            <a href="#give_review" data-toggle="modal"
                                                             class="pull-right">@lang('messages.give_review')</a>
                            @endif
                        </h3>
                        @if(!empty(Auth::user()->getBuyer) && empty(\App\Model\ProductReview::where('product_id',$product->id)->where('buyer_id',Auth::user()->getBuyer->id)->exists()))
                            <div class="modal fade" id="give_review" tabindex="-1" role="dialog"
                             aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">@lang('messages.give_review')</h4>
                                    </div>
                                    <div class="modal-body">
                                        {!! Form::open(['url'=>'/product-review','id'=>'review_form']) !!}
                                        <div class="form-group">
                                            <label for="">@lang('messages.rate')</label>

                                            <div class="rate_row"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">@lang('messages.comment')</label>
                                            <textarea name="comment" maxlength="256" class="form-control" id="" rows="6"
                                                      required></textarea>
                                            <p class="charsRemaining">
                                                @if(\App\UtilityFunction::getLocal()== "en")
                                                    You have 256 characters remaining
                                                @else
                                                    لديك 256 الأحرف المتبقية
                                                @endif
                                            </p>
                                        </div>
                                        <button type="submit"
                                                class="btn btn-primary pull-right">@lang('messages.submit_review')</button>
                                        <input type="hidden" name="product_id" value="{{Crypt::encrypt($product->id)}}">
                                        {!! Form::close() !!}
                                        <div class="clearfix"></div>
                                    </div>
                                    {{--<div class="modal-footer">--}}
                                        {{--<button type="button" class="btn btn-default"--}}
                                                {{--data-dismiss="modal">@lang('messages.close')</button>--}}
                                    {{--</div>--}}
                                </div>
                            </div>
                        </div>
                        @endif
                        <?php $reviews = $product->getReview; ?>
                        @if(!empty($reviews[0]))
                            <div class="panel panel-default" style="margin-top: 25px;">
                                <!-- List group -->
                                <ul class="list-group">
                                    @foreach($reviews as $review)
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="text-left col-md-3 col-sm-4">
                                                    {{$review->getBuyer->getUser->username}}<br>
                                                    {{date('d-m-Y',strtotime($review->created_at))}}
                                                </div>
                                                <div class="text-left col-md-9 col-sm-8">
                                                    {!! \App\UtilityFunction::createReviewRateHtml($review->review_rating) !!}
                                                    <br>

                                                    <div class="clearfix"></div>
                                                    <small>{!! strip_tags($review->review_comment) !!}</small>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div id="delivery-returns" class="tab-pane fade" role="tabpanel">
                        <p>Accusantium doloremque laudantium totam rem aperiam eaque ipsa:</p>

                        <p>Proin est elentesque risus magna vulputate vitae sodales uam morbi non sem lacus porta
                            mollis. Nunc condime ntum metus eud In molestie sed consect etu Lorem ipsum dolor sit amet
                            conse adipisicing elit sed do incididunt ut labore et dolore magna. Ut enim ad minim veniam
                            quis nostrud exercita tion ullamco laboris nisi aliquip exa commodo consequat. Duis aute
                            irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla
                            pariatur.</p>
                        <ul class="bullet-list">
                            <li>Excepteur sint occaecat cupidatat non</li>
                            <li>Proident sunt in culpa qui deserunt</li>
                            <li>Mollit anim id est laborum</li>
                            <li>Sed ut perspiciatis unde omnis iste natus</li>
                            <li>Quae ab illo inventore veritatis quas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $new_arrivals = $product->getNewArrivalProducts() ?>

    @if(isset($new_arrivals[0]))
        <section id="slider" class="slider-container slider-top-pagination">
            <div class="container">
                <h2 class="title font-additional font-weight-bold text-uppercase wow zoomIn"
                    data-wow-delay="0.3s">@lang('messages.new_arrivals')</h2>
                {{--<span class="subTitle font-additional font-weight-normal text-uppercase wow zoomIn" data-wow-delay="0.3s">SED FELIS PRAESENT DONEC BLAND</span>--}}

                <div class="starSeparatorBox clearfix">
                    <div class="starSeparator wow zoomIn" data-wow-delay="0.3s">
                        <span aria-hidden="true" class="icon-star"></span>
                    </div>
                    <div id="owl-product-slider"
                         class="enable-owl-carousel owl-product-slider owl-top-pagination owl-carousel owl-theme wow fadeInUp"
                         data-wow-delay="0.7s" data-navigation="true" data-pagination="false" data-single-item="false"
                         data-auto-play="false" data-transition-style="false" data-main-text-animation="false"
                         data-min600="2" data-min800="3" data-min1200="4">

                        @foreach($new_arrivals as $na)
                            <?php $discountArray = $na->getDealDiscountHTMLArray(); ?>
                            <div class="item">
                                <div class="product-item hvr-underline-from-center">
                                    <div class="product-item_body product-border">

                                        <?php $m = $na->getMedia; ?>

                                        <img class="product-item_image img_background"
                                            src="<?=Image::url(asset('/image/no-media.jpg'), 262, 179, ['crop'])?>"
                                             @if(count($m)>0) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $m[0]->file_in_disk), 262, 179, ['crop'])?>"
                                             @else src="<?=Image::url(asset('/image/no-media.jpg'), 262, 179, ['crop'])?>"
                                             @endif @if(\App\UtilityFunction::getLocal()=="en") alt="{{$na->name}}"
                                             title="{{$na->name}}" @else alt="{{$na->ar_name}}"
                                             title="{{$na->name}}" @endif>

                                        <a href="{{url('/product/details')}}/{{$na->id}}" class="product-item_link">
                                            {!! $discountArray[1] !!}
                                        </a>
                                        <ul class="product-item_info transition">
                                            <li>
                                                <a href="javascript:;" data-id="{{Crypt::encrypt($na->id)}}"
                                                   class="add_to_cart">
                                                    <span aria-hidden="true" class="icon-bag"></span>

                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                        @lang('messages.add_to_cart')
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{url('/product/details')}}/{{$na->id}}">
                                                    <span aria-hidden="true" class="icon-eye"></span>

                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                        @lang('messages.view')
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" data-id="{{Crypt::encrypt($na->id)}}"
                                                   class="add_to_wish_list">
                                                    <span aria-hidden="true" class="icon-heart"></span>

                                                    <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">
                                                        @lang('messages.add_to_favorites')
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a class="product-item_footer" href="{{url('/product/details')}}/{{$na->id}}">
                                        <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                            @if(\App\UtilityFunction::getLocal()== "en"){{str_limit($na->name,150)}}@else{{str_limit($na->ar_name,100)}}@endif
                                        </div>
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
@stop
@section('script')
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
    <script src="{{asset('build/js/lib/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
    <script>
        function getTouchSpin() {
            $(".quantity_input").TouchSpin({
                verticalbuttons: true,
                verticalupclass: 'glyphicon glyphicon-plus',
                verticaldownclass: 'glyphicon glyphicon-minus',
                step: 1,
                decimals: 0,
                min: 1
            });
        }
        getTouchSpin();
    </script>
{{--    <script src="{{asset('/js/jquery-migrate-1.4.1.min.js')}}"></script>--}}
    {{--<script src="{{asset('/js/jqzoom.js')}}"></script>--}}
    {{--<script>--}}
        {{--$("#bzoom").zoom({--}}
            {{--zoom_area_width: 300,--}}
            {{--autoplay_interval :3000,--}}
            {{--small_thumbs : 4,--}}
            {{--autoplay : false--}}
        {{--});--}}
    {{--</script>--}}
@stop
