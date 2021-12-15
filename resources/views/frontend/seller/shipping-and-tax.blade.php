@extends('frontend.master',['menu'=>'payments'])
@section('title',__('messages.page_title.shipping_tex'))
@section('stylesheet')
<style>
    .btn-sm {
    padding: 5px 10px;
}
</style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'shipping_tex'])
                <div class="col-md-9 col-sm-8">
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.seller.menu.shipping_tex')
                            <a href="{{url('/seller/add-shipping-and-tax')}}" class="btn btn-primary btn-sm pull-right">@lang('messages.shipping_tax.add_new_rule')</a>
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($shipping_texs[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-price"><span class="nobr">@lang('messages.shipping_tax.region')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.shipping_tax.city')</span></th>
                                            <th class="product-price" width="200"><span class="nobr">@lang('messages.shipping_tax.shipping_method')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.shipping_tax.tax')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.shipping_tax.rate')</span></th>
                                            <th class="product-add-to-cart" width="90"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($shipping_texs as $shipping_tex)
                                            <tr>
                                                <td class="product-price">
                                                    @if($shipping_tex->country_id == -1) @lang('messages.shipping_tax.all_country')
                                                    @else {{$shipping_tex->getCountry->name}} @endif
                                                </td>
                                                <td class="product-price">
                                                    @if(!empty($shipping_tex->city_ids))
                                                        {!! App\Model\Shipping::getCities($shipping_tex->city_ids) !!}
                                                    @else
                                                        @lang('messages.shipping_tax.all_city')
                                                    @endif
                                                </td>
                                                <td class="product-price">{{app('App\UtilityFunction')->getShippingType($shipping_tex->shipping_type)}}</td>
                                                <td class="product-price">
                                                    @if(!empty($shipping_tex->tax)) {{$shipping_tex->tax}}%
                                                    @else @lang('messages.shipping_tax.tax_has_not_been_added')
                                                    @endif
                                                </td>
                                                <td class="product-price">
                                                    @if($shipping_tex->shipping_type == App\Http\Controllers\Enum\ShippingTypeEnum::FREE_SHIPPING) 
                                                    @elseif($shipping_tex->shipping_type == App\Http\Controllers\Enum\ShippingTypeEnum::FLAT_RATE) {{$shipping_tex->rate}}
                                                    @elseif($shipping_tex->shipping_type == App\Http\Controllers\Enum\ShippingTypeEnum::RATE_BY_WEIGHT) 
                                                        <?php $rate_by_weight = $shipping_tex->getShippingRateByWeight; ?>
                                                        @if(!empty($rate_by_weight[0]))
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <th width="33.33333%">@lang('messages.shipping_tax.start')</th>
                                                                    <th width="33.33333%">@lang('messages.shipping_tax.end')</th>
                                                                    <th width="33.33333%">@lang('messages.shipping_tax.rate')</th>
                                                                </tr>
                                                                @foreach($rate_by_weight as $r_w)
                                                                    <tr>
                                                                        <td>{{number_format($r_w->range_start,2)}}KG</td>
                                                                        <td>{{number_format($r_w->range_end,2)}}KG</td>
                                                                        <td>{{number_format($r_w->rate,2)}}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif
                                                    @elseif($shipping_tex->shipping_type == App\Http\Controllers\Enum\ShippingTypeEnum::RATE_BY_ORDER_PRICE) 
                                                    <?php $rate_by_order_price = $shipping_tex->getShippingRateByOrderPrice; ?>
                                                        @if(!empty($rate_by_order_price[0]))
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <th width="33.33333%">@lang('messages.shipping_tax.start')</th>
                                                                    <th width="33.33333%">@lang('messages.shipping_tax.end')</th>
                                                                    <th width="33.33333%">@lang('messages.shipping_tax.rate')</th>
                                                                </tr>
                                                                @foreach($rate_by_order_price as $r_o_p)
                                                                    <tr>
                                                                        <td>{{number_format($r_o_p->range_start,2)}}</td>
                                                                        <td>{{number_format($r_o_p->range_end,2)}}</td>
                                                                        <td>{{number_format($r_o_p->rate,2)}}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif
                                                    @elseif($shipping_tex->shipping_type == App\Http\Controllers\Enum\ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY)
                                                        <?php $store_pickup = $shipping_tex->getShippingPickup; ?>
                                                        <p><strong>@lang('messages.shipping_tax.pickup_title'):</strong> {{$store_pickup->pickup_title}}</p>
                                                        <p><strong>@lang('messages.shipping_tax.pickup_address'):</strong> {{$store_pickup->pickup_address}}</p>
                                                        <p><strong>@lang('messages.shipping_tax.country'):</strong> {{$store_pickup->getCountry->name}}</p>
                                                        <p><strong>@lang('messages.shipping_tax.city'):</strong> {{$store_pickup->city_id}}</p>
                                                        <p><strong>@lang('messages.shipping_tax.state'):</strong> {{$store_pickup->state}}</p>
                                                        <p><strong>@lang('messages.shipping_tax.zip_code'):</strong> {{$store_pickup->zip}}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span data-toggle="tooltip" data-placement="top" title="@lang('messages.seller.product.edit')" data-original-title="Edit">
                                                        <a href="{{url('/seller/edit-shipping-and-tax/'.$shipping_tex->id)}}" class="btn-sm btn btn-primary"><i class="fa fa-pencil-square-o"></i></a>
                                                      </span>
                                                    <span data-toggle="tooltip" data-placement="top" title="@lang('messages.seller.product.delete')" data-original-title="Edit">
                                                        <a href="{{url('/seller/delete-shipping-and-tax/'.$shipping_tex->id)}}" class="btn-sm btn btn-primary"><i class="fa fa-trash"></i></a>
                                                      </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.shipping_tax.no_rule_added')</h4></div>
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
@stop
