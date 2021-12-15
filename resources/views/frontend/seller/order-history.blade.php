@extends('frontend.master',['menu'=>'order_history'])
@section('title',__('messages.page_title.order_history'))
@section('stylesheet')
    <style>
        .nav-tabs {
            margin: 0;
        }

        .tab-content {
            padding: 15px;
            border: 1px solid #ddd;
            border-top: 0;
        }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'order_history'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.order_history')
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($sub_order_list[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-name"><span
                                                        class="nobr">@lang('messages.buyer.order_order_no')</span></th>
                                            <th class="product-price"><span
                                                        class="nobr">@lang('messages.buyer.order_date')</span></th>
                                            <th class="product-stock-stauts"><span
                                                        class="nobr">@lang('messages.buyer.order_payment_amount')</span>
                                            </th>
                                            <th class="product-add-to-cart text-center">@lang('messages.buyer.order_status')</th>
                                            <th class="product-add-to-cart text-center">@lang('messages.seller.action')</th>
                                            <th class="product-add-to-cart" width="100"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($sub_order_list as $so)
                                            <?php
                                                $details = \App\Model\SubOrder::getSubOrderSummaryById($so->id);
                                                $order_total = $details['sub_total'];
                                                $admin_commission = $details['admin_commission'];
                                                $total = $order_total - $admin_commission + $so->tax + $so->shipping_cost;
                                            ?>

                                            <tr>
                                                <td class="product-name">
                                                    {{$so->order_id}}
                                                </td>
                                                <td class="product-name">{{date('d-m-Y',strtotime($so->order_date))}}</td>
                                                <td class="product-price">
                                                    <span>{{env('CURRENCY_SYMBOL').number_format($total,2)}}</span>
                                                </td>
                                                <td class="product-add-to-cart  text-center">
                                                    @if($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING) @lang('messages.status.pending')
                                                    @elseif($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED) @lang('messages.status.accepted')
                                                    @elseif($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED) @lang('messages.status.delivered')
                                                    @elseif($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::CLAIMED) @lang('messages.status.claimed')
                                                    @elseif($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::FINALIZED) @lang('messages.status.finalized')
                                                    @elseif($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::REJECTED) @lang('messages.status.rejected')
                                                    @endif
                                                    @if(!empty($so->getPayment))
                                                        {{'/'}}
                                                        @if($so->getPayment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::PENDING) @lang('messages.status.pending')
                                                        @elseif($so->getPayment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED) @lang('messages.status.rejected')
                                                        @elseif($so->getPayment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED) @lang('messages.status.completed')
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="product-add-to-cart text-center">
                                                    @if($so->status != \App\Http\Controllers\Enum\OrderStatusEnum::REJECTED
                                                    && $so->status != \App\Http\Controllers\Enum\OrderStatusEnum::CLAIMED
                                                    && $so->status != \App\Http\Controllers\Enum\OrderStatusEnum::FINALIZED)
                                                        {!! Form::open(['url'=>'/seller/payment/claim']) !!}
                                                        <button type="submit" name="order_id"
                                                                value="{{Crypt::encrypt($so->id)}}"
                                                                class="btn btn-primary">@lang('messages.seller.claimed_payment')</button>
                                                        {!! Form::close() !!}
                                                    @elseif($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::CLAIMED)
                                                        @lang('messages.status.claimed')
                                                    @endif
                                                </td>
                                                <td class="product-add-to-cart">
                                                    <a href="#order_details" data-id="{{Crypt::encrypt($so->id)}}"
                                                       data-toggle="modal"
                                                       class="order_view btn btn-primary">@lang('messages.view')</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.buyer.order_no_order_available')</h4>
                                    </div>
                                @endif
                            </div>
                            <div id="pagination">
                                @include('frontend.widget.pagination',['paginator'=>$sub_order_list])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang('messages.seller.menu.order_list')</h4>
                </div>
                <div class="modal-body" id="data_place">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('messages.seller.product.close')</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script>
        $(document.body).on('click', '.order_view', function (e) {
            var order_id = $(this).data('id');
            $('#data_place').empty();
            $.ajax({
                tyep: 'GET',
                url: '{{url('/seller/order-list-details')}}?order_id=' + order_id,
                dataType: 'json',
                success: function (data) {
                    $('#data_place').append('<p class="text-center"><img src="{{asset('image/pageloader.gif')}}" width="50">');
                    if (data.success == true) {
                        $('#data_place').empty();
                        $('#data_place').append(data.data_generate);
                    } else {
                        $('#data_place').empty();
                        $('#data_place').append('<p class="text-danger">' + data.data_generate + '</p>');
                    }
                }
            }).fail(function (data) {
                var errors = data.responsceJSON;
                console.log(errors);
            })
        })
    </script>
@stop
