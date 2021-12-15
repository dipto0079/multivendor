@extends('frontend.master',['menu'=>'order_list'])
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
                @include('frontend.seller.sidebar-nav',['menu'=>'order_list'])
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
                            @lang('messages.seller.menu.order_list')
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
                                            <th class="product-stock-stauts"><span
                                                        class="nobr">@lang('messages.buyer.cart.delivery_schedule')</span>
                                            </th>
                                            <th class="product-add-to-cart text-center" width="120">@lang('messages.shipping_tax.status')</th>
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
                                                <td class="product-name">{{date('d-m-Y',strtotime($so->getOrder->created_at))}}</td>
                                                <td class="product-price">
                                                    <span>{{env('CURRENCY_SYMBOL').number_format($total,2)}}</span>
                                                </td>
                                                <td class="product-name">
                                                    @if(!empty($so->getOrder->delivery_schedule))
                                                        {{date('d-m-Y h:i a',strtotime($so->getOrder->delivery_schedule))}}
                                                    @endif
                                                </td>
                                                <td class="product-add-to-cart  text-center">

                                                    @if($so->order_status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED)

                                                        {!! Form::open(['url'=>'/seller/order-change-status']) !!}
                                                        <select name="change_status" class="form-control change_status"
                                                                id="" style="width: 100px;">
                                                            <option @if($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING) selected
                                                                    @endif value="">@lang('messages.status.pending')</option>
                                                            <option @if($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED) selected
                                                                    @endif value="{{Crypt::encrypt(\App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED)}}">@lang('messages.status.delivered')</option>
                                                            <option @if($so->status == \App\Http\Controllers\Enum\OrderStatusEnum::REJECTED) selected
                                                                    @endif value="{{Crypt::encrypt(\App\Http\Controllers\Enum\OrderStatusEnum::REJECTED)}}">@lang('messages.status.rejected')</option>
                                                        </select>
                                                        <input type="hidden" name="order_id"
                                                               value="{{Crypt::encrypt($so->id)}}">
                                                        {!! Form::close() !!}
                                                    @else
                                                        <span style="font-size: 12px;background-color: #5cb85c;" class="label label-custom label-pill label-success">@lang('messages.status.waiting')</span>

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
                    <h4 class="modal-title" id="myModalLabel">@lang('messages.buyer.order_history.order_list')</h4>
                </div>
                <div class="modal-body" id="data_place">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script type="text/javascript">
        $(document.body).on('change', '.change_status', function (e) {
            var r = confirm("Do you want this action?");
            var status = $(this).val();
            if (r == true && status != '') {
                $(this).parent('form').submit();
            } else {
                $(this).val('');
                return false;
            }
        });
    </script>
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
