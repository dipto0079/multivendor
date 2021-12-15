@extends('frontend.master',['menu'=>'services'])
@section('title',__('messages.page_title.service_details'))
@section('stylesheet')
    <style>
        .pdetail-img {
            border: 1px solid #eeeeee;
        }
    </style>
@stop
@section('content')

    <?php
        $cid = (App\Model\ProductCategory::find($product->category_id));
        $cid = $cid->getParentCategory;
        $first_child_cid = App\Model\ProductCategory::find($product->category_id);
    ?>
    <section id="pageTitleBox" class="paralax breadcrumb-container"
             @if(isset($cid->banner_image))style="background-image: url('{{asset(env('CATEGORY_PHOTO_PATH').'/'.$cid->banner_image)}}');"
             @else style="background-image: url('{{asset('/image').'/e-commerce.jpg'}}');" @endif>
        <div class="overlay"></div>
        <div class="container relative">
            <h1 class="title font-additional font-weight-normal color-main text-uppercase wow zoomIn"
                data-wow-delay="0.3s">@lang('messages.services')</h1>
            <ul class="breadcrumb-list wow zoomIn" data-wow-delay="0.3s">
              <li>
                  <a href="{{url('/')}}" class="font-additional font-weight-normal color-main text-uppercase">@lang('messages.home')</a>
                  <span>/</span>
              </li>

              @if(isset($cid))
                  <li><a href="{{url('/service/category')}}"
                         class="font-additional font-weight-normal color-main text-uppercase">@lang('messages.categories')</a><span>/</span>
                  </li>
              @else
                  <li class="font-additional font-weight-normal color-main text-uppercase">@lang('messages.categories')</li>
              @endif

              @if(isset($cid))
                  @if(isset($first_child_cid))
                      <li><a href="{{url('/service/category')}}/{{$cid->id}}"
                             class="font-additional font-weight-normal color-main text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$cid->name}}@else{{$cid->ar_name}}@endif</a><span>/</span>
                      </li>
                  @else
                      <li class="font-additional font-weight-normal color-main text-uppercase">@if(\App\UtilityFunction::getLocal()== "en"){{$cid->name}}@else{{$cid->ar_name}}@endif</li>
                  @endif
              @endif

              @if(isset($first_child_cid))
                  @if(isset($second_child_cid))
                      <li><a href="{{url('/service/category')}}/{{$cid->id}}/{{$first_child_cid->id}}"
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
            <div class="row">
                <div class="product-gallery col-lg-4 col-md-4 col-sm-7 col-xs-12 clearfix">
                    <ul class="bxslider" data-mode="fade" data-slide-margin="0" data-min-slides="1" data-move-slides="1"
                        data-pager="true" data-pager-custom="#bx-pager" data-controls="false">

                        <?php $all_media = $product->getMedia; ?>

                        @if(isset($all_media[0]))
                            @foreach($all_media as $media)
                                <li>
                                    <img class="pdetail-img img_background" src="<?=Image::url(asset("/image/default.jpg"), 500, 530, ['crop'])?>"
                                         @if(isset($media)) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media->file_in_disk), 500, 530, ['crop'])?>"
                                         @else src="<?=Image::url(asset("/image/no-media.jpg"), 500, 530, ['crop'])?>"
                                            @endif >
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

                                    <img class="pdetail-img img_background" src="<?=Image::url(asset("/image/default.jpg"), 500, 500, ['crop'])?>"
                                         @if(isset($media)) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media->file_in_disk), 500, 500, ['crop'])?>"
                                         @else src="<?=Image::url(asset('image/no-media.jpg'), 500, 500, ['crop'])?>"
                                            @endif >

                                </a>
                                <?php $count++ ?>
                            @endforeach
                        @else
                            <a data-slide-index="{{$count}}" href="#"><img class="pdetail-img"
                                                                           src="{{url(asset('/image/no-media.jpg'))}}"/></a>
                        @endif
                    </div>
                </div>

                <div class="product-brand col-lg-3 col-md-3 col-sm-5 col-xs-12 hidden-xs clearfix">
                    <?php $similar_products = $product->getSimilarProducts();?>
                    @if(count($similar_products)>0)
                        <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                            data-wow-delay="0.3s">@lang('messages.similar_services')</h3>

                        <div class="product-sidebar-slider product-brand-container vertical-slider slide-controls-top wow fadeInUp"
                             data-wow-delay="0.3s">
                            <ul class="bxslider" data-mode="vertical" data-slide-margin="26" data-min-slides="2"
                                data-move-slides="1" data-pager="false" data-pager-custom="null" data-controls="true">

                                @foreach($similar_products as $sp)
                                    <?php $m = $sp->getMedia; ?>
                                    <li>
                                        <a class="product-brand_item"
                                           href="{{url('/service/details/')}}/{{$sp->id}}">
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

                    <?php $feartured_products = \App\Model\Product::getSimilarFeaturedProducts($product->getCategory);?>

                    @if(isset($feartured_products[0]))
                        <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                            data-wow-delay="0.3s">@lang('messages.featured_services')</h3>

                        <div class="product-sidebar-slider vertical-slider slide-controls-top wow fadeInUp"
                             data-wow-delay="0.3s">
                            <ul class="bxslider" data-mode="vertical" data-slide-margin="26" data-min-slides="3"
                                data-move-slides="1" data-pager="false" data-pager-custom="null" data-controls="true">
                                @foreach($feartured_products as $fp)
                                    <?php $discountArray = $fp->getDealDiscountHTMLArray(); ?>
                                    <li>
                                        <a class="popular-product-item" href="{{url('/service/details').'/'.$fp->id}}">
                                            <?php $media = $fp->getMedia;?>
                                            <img @if(isset($media[0])) src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 80, 75, ['crop'])?>"
                                                 @else src="{{asset('/image/80x75/2.jpg')}}"
                                                    @endif>
                                            <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">@if(\App\UtilityFunction::getLocal()=="en") {{str_limit($fp->name, 30)}} @else {{str_limit($fp->ar_name, 30)}} @endif</span>
                                            <span class="popular-product-item_price font-additional customColor">{!! $discountArray[0] !!}</span>
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
                        <h4 class="font-additional font-weight-bold text-uppercase pull-right" style="padding: 10px 0 0;">
                          {{-- @lang('messages.store') --}}
                          <a class="font-main color-third hover-focus-color"
                                 href="{{url('/store')}}/{{$product->getSeller->store_name}}">{{$product->getSeller->store_name}}</a>
                        </h4>
                    </div>
                    @if(!empty($product->description))
                    <div style="overflow: hidden;" class="product-options_body clearfix wow fadeInUp" data-wow-delay="0.3s">
                        <h4 class="font-additional font-weight-bold text-uppercase">@lang('messages.service_description')</h4>

                        <div class="product-options_desc font-main color-third">@if(\App\UtilityFunction::getLocal()== "en"){!! $product->description !!}@else{!! $product->ar_description !!}@endif</div>
                    </div>
                    @endif
                    <div class="product-options_cart clearfix wow fadeInUp" data-wow-delay="0.3s">
                        <div class="product-options_row">

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
                        <div class="product-options_row">
                            <ul class="product-links">
                                {{--<li>--}}
                                    {{--<a href="javascript:;" data-id="{{Crypt::encrypt($product->id)}}"--}}
                                       {{--class="add_to_wish_list font-additional font-weight-normal hover-focus-color">--}}
                                        {{--<span aria-hidden="true" class="icon-heart"></span>--}}
                                        {{--add to wishlist--}}
                                    {{--</a>--}}
                                {{--</li>--}}
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
                                <li><a href="{{url('/service/category/'.$product->category_id)}}"
                                       class="font-main color-third hover-focus-color">@if(isset($product->getCategory))
                                       @if(\App\UtilityFunction::getLocal()== "en") {{$product->getCategory->name}} @else {{$product->getCategory->ar_name}} @endif
                                       @endif</a>
                                </li>
                            </ul>

                            <h4 class="font-additional font-weight-bold text-uppercase">@lang('messages.store')</h4>

                            <ul class="tags-list">
                                <li><a class="font-main color-third hover-focus-color"
                                       href="{{url('/store')}}/{{$product->getSeller->store_name}}">{{$product->getSeller->store_name}}</a>
                                </li>
                            </ul>

                            <h4 class="font-additional font-weight-bold text-uppercase">@lang('messages.share_this_service')</h4>
                            <ul class="social-list">
                                <li>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{url('/details/'.$product->id)}}"
                                       class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span
                                                class="social_facebook" aria-hidden="true"></span></a></li>
                                <li><a href="https://twitter.com/home?status={{url('/details/'.$product->id)}}"
                                       class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span
                                                class="social_twitter" aria-hidden="true"></span></a></li>
                                <li><a href="https://plus.google.com/share?url={{url('/details/'.$product->id)}}"
                                       class="hover-focus-border hover-focus-bg hvr-rectangle-out before-bg"><span
                                                class="social_googleplus" aria-hidden="true"></span></a></li>
                                <li>
                                    <a href="https://pinterest.com/pin/create/button/?url={{url('/details/'.$product->id)}}&media=&description="
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
                           href="#reviews" aria-controls="profile" role="tab" data-toggle="tab">@lang('messages.reviews') (2)</a>
                    </li>
                    {{--<li role="presentation">--}}
                        {{--<a class="hover-focus-border hover-focus-bg font-additional font-weight-normal hvr-rectangle-out before-bg"--}}
                           {{--href="#delivery-returns" aria-controls="messages" role="tab" data-toggle="tab">Delivery &--}}
                            {{--returns</a>--}}
                    {{--</li>--}}
                </ul>
                <div class="tab-content wow fadeInUp" data-wow-delay="0.3s">

                    <div id="reviews" class="tab-pane fade in active" role="tabpanel">
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
                        <p>Accusantium doloremque laudantium totam rem aperiam eaque ipsa:</p>
                    </div>
                    {{--<div id="delivery-returns" class="tab-pane fade" role="tabpanel">--}}
                        {{--<p>Accusantium doloremque laudantium totam rem aperiam eaque ipsa:</p>--}}

                        {{--<p>Proin est elentesque risus magna vulputate vitae sodales uam morbi non sem lacus porta--}}
                            {{--mollis. Nunc condime ntum metus eud In molestie sed consect etu Lorem ipsum dolor sit amet--}}
                            {{--conse adipisicing elit sed do incididunt ut labore et dolore magna. Ut enim ad minim veniam--}}
                            {{--quis nostrud exercita tion ullamco laboris nisi aliquip exa commodo consequat. Duis aute--}}
                            {{--irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla--}}
                            {{--pariatur.</p>--}}
                        {{--<ul class="bullet-list">--}}
                            {{--<li>Excepteur sint occaecat cupidatat non</li>--}}
                            {{--<li>Proident sunt in culpa qui deserunt</li>--}}
                            {{--<li>Mollit anim id est laborum</li>--}}
                            {{--<li>Sed ut perspiciatis unde omnis iste natus</li>--}}
                            {{--<li>Quae ab illo inventore veritatis quas</li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </section>

    <?php $new_arrivals = $product->getNewArrivalProducts() ?>

    @if(isset($new_arrivals[0]))
        <section id="slider" class="slider-container slider-top-pagination">
            <div class="container">
                <h2 class="title font-additional font-weight-bold text-uppercase wow zoomIn" data-wow-delay="0.3s">@lang('messages.recently_added')</h2>
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

                                        <img class="product-item_image"
                                             @if(count($m)>0) src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $m[0]->file_in_disk), 262, 179, ['crop'])?>"
                                             @else src="<?=Image::url(asset('/image/no-media.jpg'), 262, 179, ['crop'])?>"
                                             @endif
                                             @if(\App\UtilityFunction::getLocal()=="en") alt="{{$na->name}}"
                                             title="{{$na->name}}" @else alt="{{$na->ar_name}}"
                                             title="{{$na->name}}" @endif>

                                        <a href="{{url('/service/details')}}/{{$na->id}}" class="product-item_link">
                                            {!! $discountArray[1] !!}
                                        </a>
                                        <ul class="product-item_info transition">
                                            {{--<li>--}}
                                                {{--<a href="#">--}}
                                                    {{--<span aria-hidden="true" class="icon-bag"></span>--}}

                                                    {{--<div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">--}}
                                                        {{--@lang('messages.add_to_cart')--}}
                                                    {{--</div>--}}
                                                {{--</a>--}}
                                            {{--</li>--}}
                                            <li>
                                                <a href="{{url('/service/details')}}/{{$na->id}}">
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
                                    <a class="product-item_footer" href="{{url('/service/details')}}/{{$na->id}}">
                                        <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">
                                          @if(\App\UtilityFunction::getLocal()=="en") {{$na->name}} @else {{$na->ar_name}} @endif
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
@stop
