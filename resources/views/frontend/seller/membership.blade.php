@extends('frontend.master',['menu'=>'wishlist'])
@section('title','Wish list')
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'membership'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.seller.menu.membership')
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                              <?php $membership_fees = App\Model\Setting::where('key',App\Http\Controllers\Enum\SettingsEnum::MEMBERSHIP_FEE)->first(); ?>

                              <h3 style="margin-bottom: 20px;">
                                  @lang('messages.seller.membership_fees') = {{env('CURRENCY_SYMBOL').number_format($membership_fees->value,2)}}
                                {!! Form::open(['url'=>'/seller/membership/save','class'=>'form-inline pull-right']) !!}
                                  <button type="submit" class="btn btn-primary">
                                    @if(empty($memberships[0])) @lang('messages.seller.subscribe')
                                    @else @lang('messages.seller.extend_subscription')
                                    @endif
                                  </button>
                                {!! Form::close() !!}
                                <div class="clearfix"></div>
                              </h3>

                              @if(!empty($memberships[0]))
                                  <table class="shop_table cart wishlist_table">
                                      <thead>
                                      <tr>
                                          <th class="product-price"><span class="nobr">@lang('messages.seller.from_to')</span></th>
                                          <th class="product-add-to-cart" width="150">@lang('messages.seller.created_at')</th>
                                          <th class="product-add-to-cart" width="120">@lang('messages.seller.fees')</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      @foreach($memberships as $membership)
                                          <tr>
                                            <td class="product-price"><span class="nobr">
                                              {{date('d-m-Y',strtotime($membership->from_date)) .' - '. date('d-m-Y',strtotime($membership->to_date))}}
                                            </span></td>
                                            <td class="product-add-to-cart" width="150">{{date('Y-m-d h:i a',strtotime($membership->created_at))}}</td>
                                            <td class="product-add-to-cart" width="120">{{env('CURRENCY_SYMBOL').number_format($membership->fees,2)}}</td>
                                          </tr>
                                      @endforeach
                                      </tbody>
                                  </table>
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
