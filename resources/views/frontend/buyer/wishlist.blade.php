@extends('frontend.master',['menu'=>'wishlist'])
@section('title',__('messages.page_title.wishlist'))
@section('stylesheet')
    <style>
        .product-category {
            color: gray;
            font-size: 11px;
        }

        .product-category span {
            font-weight: bold;
        }
        .popover-content img { max-width: inherit !important; }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'wishlist'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.wishlist')
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($wishLists[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-remove"></th>
                                            <th class="product-thumbnail" width="100"></th>
                                            <th class="product-name"><span
                                                        class="nobr">@lang('messages.buyer.product_name')</span></th>
                                            <th class="product-price"><span
                                                        class="nobr">@lang('messages.buyer.unit_price')</span></th>
                                            <th class="product-price"><span class="nobr"></span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($wishLists as $wishList)
                                            <?php
                                                $product_count = 0;
                                                $media = $wishList->getProduct->getMedia;
                                                $product_type = $wishList->getProduct->getSeller->business_type;
                                            ?>
                                            <tr>
                                                <td class="product-remove">
                                                    <a href="{{url('/buyer/remove-wish-list/'.$wishList->id)}}"
                                                       class="remove remove_from_wishlist">×</a>
                                                </td>
                                                <td class="product-thumbnail">
                                                    <a href="{{url('/buyer/remove-wish-list/'.$wishList->id)}}" class="remove remove_from_wishlist pull-right visible-xs visible-sm">×</a>
                                                    @if(!empty($media[0]))
                                                        <span class="img_popover" data-toggle="popover" data-html="true"
                                                              data-trigger="focus" data-content="<img src='<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 350, 350, ['crop'])?>' alt='Product-1'>">
                                                      <img width="150" height="150" class="img_background"
                                                           src="{{asset('/image/default.jpg')}}"
                                                           data-src="<?=Image::url(asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk), 150, 150, ['crop'])?>"
                                                           alt="Product-1">
                                                     </span>
                                                    @else
                                                        <img width="150" height="150" class="img_background"
                                                             src="{{asset('/image/default.jpg')}}"
                                                             data-src="<?=Image::url(asset('/image/default.jpg'), 150, 150, ['crop'])?>"
                                                             alt="Product-1">
                                                    @endif
                                                </td>
                                                <td class="product-name">
                                                  @if($wishList->getProduct->status == App\Http\Controllers\Enum\ProductStatusEnum::SHOWN)
                                                    <a href="{{url('/product/details/'.$wishList->getProduct->id)}}">
                                                  @endif
                                                      @if(\App\UtilityFunction::getLocal()== "en"){{$wishList->getProduct->name}}@else{{$wishList->getProduct->ar_name}}@endif
                                                  @if($wishList->getProduct->status == App\Http\Controllers\Enum\ProductStatusEnum::SHOWN)
                                                    </a>
                                                  @endif

                                                    <div class="product-category">
                                                        <span>@lang('messages.buyer.wish_list_category') </span>@if(\App\UtilityFunction::getLocal()== "en"){{$wishList->getProduct->getSeller->getCategory->name}}@else{{$wishList->getProduct->getSeller->getCategory->ar_name}}@endif
                                                    </div>
                                                </td>
                                                <td class="product-price">
                                                    <span class="amount">
                                                        {{env('CURRENCY_SYMBOL').number_format($wishList->getProduct->price,2)}}</span>
                                                </td>
                                                <td class="product-add-to-cart">
                                                    @if($product_type == \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)
                                                      @if($wishList->getProduct->quantity && $wishList->getProduct->status == App\Http\Controllers\Enum\ProductStatusEnum::SHOWN)
                                                        <a href="javascript:;" class="add_to_cart btn btn-primary"
                                                           data-id="{{Crypt::encrypt($wishList->getProduct->id)}}">@lang('messages.add_to_cart')</a>
                                                      @elseif($wishList->getProduct->quantity && $wishList->getProduct->status == App\Http\Controllers\Enum\ProductStatusEnum::ARCHIVE)
                                                        <strong style="color: red">@lang('messages.buyer.product_not_available')</strong>
                                                        <div class="clearfix"></div>
                                                      @else
                                                          <strong style="color: red">@lang('messages.buyer.product_not_available')</strong>
                                                          <div class="clearfix"></div>
                                                      @endif
                                                    @else
                                                        <strong>@lang('messages.service') @lang('messages.product')</strong>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.buyer.no_product_added')</h4></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script type="text/javascript">
        $('.img_popover').popover({title: "", trigger: "hover"});
    </script>
@stop
