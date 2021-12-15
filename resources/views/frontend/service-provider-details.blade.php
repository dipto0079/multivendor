@extends('frontend.master',['menu'=>'services'])
@section('title',__('messages.page_title.provider_details'))
@section('stylesheet')
    <link rel="stylesheet" type="text/css" href="{{asset('/fancybox/jquery.fancybox.css')}}?v=2.1.5" media="screen"/>
    <link rel="stylesheet" href="{{asset('/css/starwars.css')}}">
    <style media="screen">
        .panel-default > .panel-heading {
            background-color: #fff;
            color: #000;
            text-align: left;
            padding: 10px 15px;
            line-height: normal;
        }

        .panel-body {
            padding: 20px;
        }

        .morecontent span {
            display: none;
        }

        .morecontent span {
            display: none;
        }

        .affix-bottom {
            position: absolute;
        }

        .affix {
            top: 10px;
        }

        #pricing_div {
            max-width: 370px;
        }

        ul.breadcrumb-list {
            text-align: left;
            margin-bottom: 10px;
            padding: 0;
        }

        ul.breadcrumb-list li, ul.breadcrumb-list li a, ul.breadcrumb-list li span {
            color: #ff8300;
        }
        .fancybox { width: 33.33333%; padding: 15px; display: inline-block; float: left; }
        .fancybox img { max-width: 100%; display: block; }
    </style>
@stop

@section('content')
    <?php
        $reviews = $seller->getReview;
        $services = $seller->getProducts;
    ?>
    <div id="main" class="site-main">
        <div class="container provider_information">
            <div class="row">
                <div class="col-md-8 col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-3 text-center">
                                    <img class="img-circle user_img img_background"
                                         src="<?=Image::url(asset('/image/default.jpg'), 200, 175, ['crop'])?>"
                                         @if(!empty($seller->getUser->photo)) data-src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $seller->getUser->photo), 200, 175, ['crop'])?>"
                                         @else src="<?=Image::url(asset('image/no-media.jpg'), 200, 175, ['crop'])?>"
                                         alt=""
                                         @endif alt="">
                                </div>
                                <div class="col-sm-9">
                                    <h3 class="name">{{$seller->getUser->username}}
                                        @if(!empty($reviews[0]))
                                            <span class="review">
                              <label class="label label-success">{{$seller->getReview->sum('review_rating')/$seller->getReview->count()}}
                                  <i class="fa fa-star"></i></label> {{$reviews->count()}} reviews
                            </span>
                                        @endif
                                    </h3>

                                    <p class="address">{{$seller->city.', '.$seller->state.', '.$seller->zip.', '.$seller->getCountry->name}}</p>

                                    <div class="clearfix"></div>
                                    <div class="status">
                                        <div class="hired" style="border: 0;">
                                            <a class="label label-success" href="{{url('/store/'.$seller->store_name)}}"
                                               style="font-size: 13px; padding-bottom: 3px;">{{$seller->store_name}}</a>
                                        </div>
                                        {{--<div class="verified">--}}
                                        {{--<img src="{{asset('image/120.png')}}" alt="">--}}
                                        {{--</div>--}}
                                    </div>
                                </div>
                            </div>
                            <div class="about_me">
                                <h4>@lang('messages.seller.details.about_me')</h4>

                                <p class="gray_color more">{{$seller->about_me}}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Mobile pricing div -->
                    <div class="panel panel-default visible-xs visible-sm">
                        <div class="panel-heading">
                            @lang('messages.seller.details.pricing')
                        </div>
                        <div class="panel-body">
                            <h4 style="margin:0;">@lang('messages.seller.details.changes')</h4>

                            <p>
                                <small>@lang('messages.seller.details.customised_packages')</small>
                            </p>
                            <ul class="list-group">
                                @if(!empty($services[0]))
                                    @foreach($services as $service)
                                        <li class="list-group-item">
                                            <span class="pull-right">{{env('CURRENCY_SYMBOL').number_format($service->price,2)}}
                                                +</span>
                                            <strong>@if(\App\UtilityFunction::getLocal()== "en"){{$service->name}}@else{{$service->ar_name}}@endif</strong>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>


                            <button data-toggle="modal" data-target=".request_quotation" type="button"
                               class="btn btn-primary btn-block">@lang('messages.seller.details.request_quotation')</button>

                            <p>
                                <small>@lang('messages.seller.details.quotation_txt')</small>
                            </p>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.seller.details.work_photos_projects')
                        </div>
                        <?php $products = $seller->getProducts; ?>
                        <div class="panel-body" style="padding-top: 0px;">
                            <div class="row">
                                <?php $total_media_image_count = 0; ?>

                                @if(!empty($products[0]))
                                    {{--<ul class="work_photo">--}}
                                    <?php
                                        $medias = \App\Model\Seller::getTotalServiceMedia($seller->id);
                                        $total_media_image_count = count($medias);
                                        $media_count = 0;
                                    ?>

                                    @if(!empty($medias[0]))
                                        @foreach($medias as $media)
                                            <a data-title-id="title-{{$media->id}}"
                                               @if(isset($media)) href="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media->file_in_disk), 1024, 680, ['crop'])?>"
                                               @else href="<?=Image::url(asset("/images/no-media.jpg"), 1024, 680, ['crop'])?>"
                                               @endif class="fancybox">
                                                <img class="img_background"
                                                     src="<?=Image::url(asset('image/no-media.jpg'), 250, 150, ['crop'])?>"
                                                     @if(isset($media)) data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media->file_in_disk), 250, 150, ['crop'])?>"
                                                     @else src="<?=Image::url(asset("/images/no-media.jpg"), 250, 150, ['crop'])?>"
                                                     @endif alt=""></a>
                                            <?php
                                                $media_count++;
                                                if($media_count == 6) break;
                                            ?>
                                        @endforeach
                                    @endif
                                    {{--</ul>--}}
                                @endif
                                <div class="clearfix"></div>
                                <h3 class="text-center"><a href="{{url('/seller/media?seller_id='.Crypt::encrypt($seller->id))}}" id="total" data-fancybox-type="ajax" style="font-size: 18px;">@lang('messages.seller.details.See_all_images')
                                        ({{$total_media_image_count}})</a></h3>
                                <div id="ajax_fancybox" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.seller.details.reviews')
                        </div>
                        <div class="panel-body">
                            @if(!empty($reviews[0]))
                                <div class="review_rating">
                                    <div class="overall_rating visible-xs">
                                        <h4>@lang('messages.seller.details.reviews')</h4>
                                        <label class="label label-success">{{$seller->getReview->sum('review_rating')/$seller->getReview->count()}}
                                            <i class="fa fa-star"></i></label>

                                        @lang('messages.seller.details.based_on') {{$seller->getReview->count()}}
                                        @if(\App\UtilityFunction::getLocal()== "en")
                                            @lang('messages.seller.details.review')<?php if ($seller->getReview->count() > 0) echo "s"; ?>
                                        @else
                                            @if($seller->getReview->count() > 0)
                                                @lang('messages.seller.details.review')
                                            @else
                                                @lang('messages.seller.details.s_reviews')
                                            @endif
                                        @endif
                                    </div>
                                    <div class="bar_rating">
                                        <div class="bar_rating_status">
                                            <p>@lang('messages.seller.details.excellent')</p>

                                            <p>@lang('messages.seller.details.good')</p>

                                            <p>@lang('messages.seller.details.satisfactory')</p>

                                            <p>@lang('messages.seller.details.below_average')</p>

                                            <p>@lang('messages.seller.details.poor')</p>
                                        </div>
                                        <div class="progress_bar">
                                            @for($i=5;$i>=1;$i--)
                                                <?php
                                                $review_percentage = 0;
                                                $review_rating_avg = $seller->getReview->where('review_rating', $i)->avg('review_rating');
                                                if ($review_rating_avg != 0) {
                                                    $review_percentage = ($seller->getReview->where('review_rating', $i)->avg('review_rating') / $seller->getReview->where('review_rating', $i)->count()) * 20;
                                                }
                                                ?>
                                                <div class="bar">
                                                    <div class="inner" style="width: {{$review_percentage}}%;"></div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="overall_rating hidden-xs">
                                        <h4>@lang('messages.seller.details.overall_rating')</h4>
                                        @lang('messages.seller.details.based_on') {{$seller->getReview->count()}}
                                        @if(\App\UtilityFunction::getLocal()== "en")
                                            @lang('messages.seller.details.review')<?php if ($seller->getReview->count() > 0) echo "s"; ?>
                                        @else
                                            @if($seller->getReview->count() > 0)
                                                @lang('messages.seller.details.review')
                                            @else
                                                @lang('messages.seller.details.s_reviews')
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="review_list">
                                    <h4>@lang('messages.seller.details.reviews')</h4>
                                    <ul class="reviews">
                                        @foreach($reviews->take(3) as $review)
                                            <li>
                                                <div class="customer_info">
                                                    <div class="name_short">
                                                        {{App\Model\ProductReview::buyerShortName($review->getBuyer->getUser->username)}}
                                                    </div>
                                                    <div class="info">
                                                        <h3 class="name">{{$review->getBuyer->getUser->username}}</h3>
                                                        <label class="label label-success">{{env('CURRENCY_SYMBOL').number_format($review->review_rating,1)}}
                                                            <i class="fa fa-star"></i></label>

                                                        <p class="date">{{date('F Y',strtotime($review->created_at))}}</p>
                                                    </div>
                                                </div>
                                                <p class="review_comment more">{{$review->review_comment}}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <h2 class="text-center" style="font-size: 16px; color: #ff0000;">@lang('messages.seller.details.not_rated_yet')</h2>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-5 hidden-xs hidden-sm">
                    <div id="pricing_div">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                @lang('messages.seller.details.pricing')
                            </div>
                            <div class="panel-body">
                                <h4 style="margin:0;">@lang('messages.seller.details.changes')</h4>

                                <p>
                                    <small>@lang('messages.seller.details.customised_packages')</small>
                                </p>

                                <ul class="list-group">
                                    @if(!empty($services[0]))
                                        @foreach($services as $service)
                                            <li class="list-group-item">
                                                <span class="pull-right">{{env('CURRENCY_SYMBOL').number_format($service->price,2)}}</span>
                                                <strong>@if(\App\UtilityFunction::getLocal()== "en"){{$service->name}}@else{{$service->ar_name}}@endif</strong>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>

                                <button data-toggle="modal" data-target=".request_quotation" type="button"
                                   class="btn btn-primary btn-block">@lang('messages.seller.details.request_quotation')</button>

                                <p>
                                    <small>@lang('messages.seller.details.quotation_txt')</small>
                                </p>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                @lang('messages.seller.details.share_this_profile')
                                <a href="https://www.facebook.com/sharer/sharer.php?app_id=1705134666453068&sdk=joey&u={{url('/seller/details/'.$seller->getUser->username)}}&display=popup&ref=plugin&src=share_button"
                                onclick="return !window.open(this.href, 'Facebook', 'width=640,height=580')" class="label label-default label-icon"><i class="fa fa-facebook"></i></a>
                                <a   href="https://plus.google.com/share?url={{url('/seller/details/'.$seller->getUser->username)}}"
                                 onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="label label-default label-icon"><i class="fa fa-link"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row" id="similar_profes">
                <div class="col-sm-12">
                    <h3>@lang('messages.seller.details.similar_professionals')</h3>
                    <br>
                </div>
                <?php
                    $similar_sellers = App\Model\Seller::join('subscriptions','sellers.id','=','subscriptions.seller_id')
                        ->where('sellers.category_id', $seller->category_id)
                        ->where('sellers.id', '!=', $seller->id)
                        ->where('subscriptions.to_date', '>=', date('Y-m-d 00:00:00'))
                        ->groupBy('sellers.id')
                        ->take(3)->get();
                ?>
                @if(!empty($similar_sellers[0]))
                    @foreach($similar_sellers as $similar_seller)
                        <div class="col-sm-4">
                            <div class="panel panel-default similar_profes">
                                <div class="panel-body">
                                    <a href="{{url('/seller/details/'.$similar_seller->store_name)}}" class="">
                                        <div class="provider_img">
                                            <img class="img-circle img_background"
                                                 src="<?=Image::url(asset(env('USER_PHOTO_PATH').$similar_seller->getUser->photo),200,175,['crop'])?>"
                                                 @if(!empty($similar_seller->getUser->photo)) data-src="<?=Image::url(asset(env('USER_PHOTO_PATH').$similar_seller->getUser->photo),200,175,['crop'])?>"
                                                 @else src="<?=Image::url(asset('image/no-media.jpg'),200,175,['crop'])?>"
                                                 alt=""
                                                 @endif alt="">
                                        </div>
                                        <div class="provider_info">
                                            <h3 class="ellipsis name">{{$similar_seller->getUser->username}}</h3>
                                            {!! \App\UtilityFunction::createReviewRateHtml($similar_seller->getReview->avg('review_rating')) !!}
                                            <div class="clearfix"></div>
                                            <p class="ellipsis">{{$similar_seller->city.', '.$similar_seller->state.', '.$similar_seller->zip.', '.$similar_seller->getCountry->name}}</p>

                                            <p class="price">
                                                <span>Rs.12000</span> @lang('messages.seller.details.per_event')
                                            </p>
                                        </div>
                                    </a>

                                    <div class="clearfix"></div>
                                    <?php $recent_review = $similar_seller->getReview()->orderBy('created_at','desc')->first(); ?>
                                    @if(!empty($recent_review))
                                        <div class="provider_recent_review">
                                            <p>
                                                <strong>@lang('messages.seller.details.recent_review_by') {{$recent_review->getBuyer->getUser->username}}</strong>
                                            </p>

                                            <p>{{str_limit($recent_review->review_comment,80)}}
                                                <a href="#revice_comment_d_{{$recent_review->id}}"
                                                   data-toggle="modal">@lang('messages.seller.details.read_more')</a>
                                            </p>
                                            <a href="#"
                                               class="link">@lang('messages.seller.details.request_quotation')</a>
                                        </div>
                                        <div class="modal fade" id="revice_comment_d_{{$recent_review->id}}"
                                             tabindex="-1"
                                             role="dialog" aria-labelledby="myLargeModalLabel">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close"><span
                                                                    aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h4 class="modal-title" style="margin: 0;"
                                                            id="myModalLabel">@lang('messages.seller.details.recent_review_by') {{$recent_review->getBuyer->getUser->username}}</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{$recent_review->review_comment}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

<?php $seller_services = $seller->getProducts; ?>
    <div class="modal fade request_quotation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">@lang('messages.seller.details.request_quotation')</h4>
          </div>
          <div class="modal-body">
            <form class="" action="{{url('/request-quotation')}}" method="post" id="request_form">
              {{csrf_field()}}
              <div class="form-group">
                <label for="" class="required">@lang('messages.seller.details.your_phone_email')</label>
                <input type="text" class="form-control" name="phone_email" @if(!empty(Auth::user())) value="{{Auth::user()->email}}" @endif required>
              </div>
              <div class="form-group">
                <label for="" class="required">@lang('messages.seller.details.request_for_the_service')</label>
                <select class="form-control" name="service_product" required>
                  <option value="">@lang('messages.select')</option>
                  @if(!empty($seller_services[0]))
                    @foreach($seller_services as $seller_service)
                      <option value="{{$seller_service->id}}">@if(\App\UtilityFunction::getLocal() == 'en'){{$seller_service->name}}@else{{$seller_service->ar_name}}@endif</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="form-group">
                <label for="">@lang('messages.message')</label>
                <textarea name="quotation_message" class="form-control" rows="6"></textarea>
              </div>
              <div class="form-group">
                <div class="g-recaptcha" data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}" style="display: inline-block;"></div>
                <button type="submit" class="btn btn-primary pull-right">@lang('messages.send')</button>
              </div>
              <input type="hidden" name="skip" value="{{Crypt::encrypt($seller->id)}}">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
          </div>
        </div>
      </div>
    </div>

@stop

@section('script')
    <script type="text/javascript" src="{{asset('/fancybox/jquery.fancybox.js')}}?v=2.1.5"></script>
    <script src="{{asset('/js/jquery-migrate-1.4.1.min.js')}}"></script>
    <script>
    $(document).ready(function () {
        $(document.body).on("click",'#total', function (e) {
            e.preventDefault(); // avoids calling preview.php
            var nzData = this.href;
             $('#ajax_fancybox').load(nzData, function(html) {
               json_parse = JSON.parse(html);
                fancyBoxContent();
               $('#ajax_fancybox').html(json_parse);
               $('#ajax_fancybox a:first-child').click();
             });
        }); // on
    }); // ready
        function fancyBoxContent(){
            jQuery(document).ready(function ($) {
                $('.fancybox').attr('rel', 'gallery')
                    .fancybox({
                        beforeLoad: function () {
                            var el, id = $(this.element).data('title-id');

                            if (id) {
                                el = $('#' + id);

                                if (el.length) {
                                    this.title = el.html();
                                }
                            }
                        },
                        helpers: {
                            title: {
                                type: 'inside'
                            },
                            overlay: {
                                speedOut: 0
                            }
                        }
                    });
            });
        }
        fancyBoxContent();
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
    <script type="text/javascript">
        $('#pricing_div').affix({
            offset: {
                top: 170,
                bottom: 920,
            }
        })
    </script>
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src='https://www.google.com/recaptcha/api.js'></script>

      <script src={{asset('/js/validator.min.js')}}></script>
      <script type="text/javascript">
      $('#request_form button').removeClass('disabled');
      $(document).ready(function(){
          $('#request_form').validator().on('submit', function (e) {
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
      });


      </script>
@stop
