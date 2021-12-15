@extends('frontend.master',['menu'=>'payments'])
@section('title',__('messages.page_title.order_history'))
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'payments'])
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
                            @lang('messages.seller.menu.my_earnings')
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($payments[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-price"><span class="nobr">@lang('messages.seller.my_earnings.orders_id')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.seller.my_earnings.amount')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.seller.my_earnings.admin_commission')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.seller.my_earnings.no_of_order')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.seller.my_earnings.comment')</span></th>
                                            <th class="product-add-to-cart" width="120">@lang('messages.seller.my_earnings.month')</th>
                                            <th class="product-add-to-cart" width="100">@lang('messages.seller.my_earnings.status')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($payments as $payment)
                                            <?php
                                            $orders = '';
                                            $first = 0;
                                            $sub_orders = $payment->getPaymentSubOrder;
                                            foreach($sub_orders as $sub_order){
                                                if($first == 0){
                                                    $orders = $sub_order->order_id;
                                                    $first = 1;
                                                }
                                                else {
                                                    $orders .= ', '.$sub_order->order_id;
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td class="product-price">{{$orders}}</td>
                                                <td class="product-price">{{env('CURRENCY_SYMBOL').number_format($payment->amount,2)}}</td>
                                                <td class="product-price">{{env('CURRENCY_SYMBOL').number_format($payment->commission_charged,2)}}</td>
                                                <td class="product-price">{{$payment->getPaymentSubOrder->count()}}</td>
                                                <td class="product-price">{{$payment->comment}}</td>
                                                <td>{{date('F, Y',strtotime($payment->payment_for_the_month))}}</td>
                                                <td>
                                                    @if($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::PENDING) <strong>@lang('messages.status.pending')</strong>
                                                    @elseif($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED) <strong style="color: red;">@lang('messages.status.rejected')</strong>
                                                    @elseif($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED) <strong style="color: #008E00;">@lang('messages.status.completed')</strong>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.seller.my_earnings.no_payment_occured')</h4></div>
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
