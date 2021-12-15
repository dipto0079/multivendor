@extends('frontend.master',['menu'=>'order_history'])
@section('title',__('messages.page_title.order_history'))
@section('stylesheet')
    <style>
        .nav-tabs { margin: 0; }
        .tab-content { padding: 15px; border: 1px solid #ddd; border-top: 0; }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'order_history'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.order_history')
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($order_histories[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-name"><span class="nobr">@lang('messages.buyer.order_order_no')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.buyer.order_date')</span></th>
                                            <th class="product-stock-stauts"><span class="nobr">@lang('messages.buyer.order_payment_amount')</span></th>
                                            <th class="product-add-to-cart">@lang('messages.buyer.order_status')</th>
                                            <th class="product-add-to-cart" width="100"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($order_histories as $order_history)
                                          <?php
                                            $order_amount = $order_history->sub_total_price - $order_history->discount + $order_history->vat_amount + $order_history->shipping_rate;
                                          ?>
                                            <tr>
                                                <td class="product-name">
                                                    {{$order_history->id}}
                                                </td>
                                                <td class="product-name">{{date('d-m-Y',strtotime($order_history->created_at))}}</td>
                                                <td class="product-price">
                                                    <span>{{env('CURRENCY_SYMBOL').number_format($order_amount,2)}}</span>
                                                </td>
                                                <td class="product-add-to-cart">
                                                    @if($order_history->status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING) @lang('messages.status.pending')
                                                    @elseif($order_history->status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED) <span style="background-color: #46c35f;" class="label label-custom label-pill label-success">@lang('messages.status.accepted') </span>
                                                    @elseif($order_history->status == \App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED) @lang('messages.status.delivered')
                                                    @elseif($order_history->status == \App\Http\Controllers\Enum\OrderStatusEnum::REJECTED) @lang('messages.status.rejected')
                                                    @elseif($order_history->status == \App\Http\Controllers\Enum\OrderStatusEnum::FINALIZED) @lang('messages.status.finalized')
                                                    @endif
                                                </td>
                                                <td class="product-add-to-cart">
                                                    <a href="#order_details" data-id="{{Crypt::encrypt($order_history->id)}}" data-toggle="modal" class="order_view btn btn-primary">@lang('messages.view')</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div id="pagination">
                                        @include('frontend.widget.pagination',['paginator'=>$order_histories])
                                    </div>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.buyer.order_no_order_available')</h4></div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script>
        $(document.body).on('click','.order_view', function (e) {
            var order_id = $(this).data('id');
            $('#data_place').empty();
            $('#data_place').append('<p class="text-center"><img src="{{asset('image/pageloader.gif')}}" width="50"></p>');
            $.ajax({
                tyep: 'GET',
                url: '{{url('/buyer/order-history-details')}}?order_id='+order_id,
                dataType: 'json',
                success: function(data){
                    if(data.success == true){
                        $('#data_place').empty();
                        $('#data_place').append(data.data_generate);
                    }else{
                        $('#data_place').empty();
                        $('#data_place').append('<p class="text-danger">'+data.data_generate+'</p>');
                    }
                }
            }).fail(function (data) {
                var errors = data.responsceJSON;
                console.log(errors);
            })
        });
       $(document.body).on('click','.details',function(){
           var id = $(this).data('id');

           $(this).closest('tbody').find('tr').removeClass('tr_show');
           $('#show_order_'+id).addClass('tr_show');
           if($(this).closest('tr').hasClass('tr_show')){
               $('#show_order_'+id).toggleClass('tr_show');
           }
       });
    </script>
@stop
