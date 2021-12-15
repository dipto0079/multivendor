@extends('admin.master')
@section('title','Payment List')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('/build')}}/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/pages/gallery.min.css">
    <style type="text/css">
        .show {
            display: block;
        }

        .btn-file {
            margin-right: 20px;
            padding: 4px 10px;
        }

        .gallery-item {
            height: 150px;
        }

        .tab-content {
            height: 500px;
            overflow-y: scroll;
        }
    </style>
@stop

@section('content')

    @include('admin.payment.submenu',['page'=>'payment'])

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Payment List</div>
                                <div class="col-sm-8 text-right">
                                    {!! Form::open(['url'=>'/admin/payment','method'=>'get','class'=>'form-inline']) !!}
                                    <div class="form-group">
                                        <label for="" style="display: inline-block;">Date Filter</label>
                                        <div class="input-group date" style="width: 45%">
                                            <input type="text" name="month"
                                                   @if(!empty(Request::get('month'))) value="{{Request::get('month')}}"
                                                   @else value="{{date('m-Y')}}" @endif class="form-control"><span
                                                    class="input-group-addon"><i
                                                        class="glyphicon glyphicon-th"></i></span>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </h3>
                        </div>
                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="200">Order No.</th>
                    <th>Seller</th>
                    {{--<th width="150">No. of Sub Orders</th>--}}
                    <th width="300" class="text-right">Amount</th>
                    <th class="text-center" width="130">Action</th>
                </thead>
                <tbody>
                @if(!empty($payments[0]))
                    @foreach($payments as $payment)
                        <tr>
                            <?php
                            $orders = '';
                            $first = 0;
                            $sub_orders = explode(',', $payment->sub_order_id);
                            foreach ($sub_orders as $sub_order) {
                                $sub_order = \App\Model\SubOrder::find($sub_order);


                                if ($first == 0) {
                                    $orders = '<a class="detail_btn" data-toggle="modal" href="#form_modal" data-seller-id="' . $sub_order->seller_id . '" data-id="' . $sub_order->order_id . '">' . $sub_order->order_id . '</a>';
                                    $first = 1;
                                } else {
                                    $orders .= ', ' . '<a class="detail_btn" data-toggle="modal" href="#form_modal" data-seller-id="' . $sub_order->seller_id . '" data-id="' . $sub_order->order_id . '">' . $sub_order->order_id . '</a>';
                                }
                            }



                            ?>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{!! $orders !!}</span></td>


                            <td class="tabledit-view-mode">
                              <span class="tabledit-span">
                                <a href="{{url('admin/product/seller/'.$payment->getSeller->id.'/product/list')}}">{{$payment->getSeller->getUser->username}}</a>
                              </span>
                            </td>
                            <td class="tabledit-view-mode text-right">
                                <span class="tabledit-span">

                                    <?php
                                    $total_sub_order = 0;
                                    $total_shipping_cost = 0;
                                    $total_vat = 0;
                                    $total_admin_commission = 0;

                                    foreach (explode(',', $payment->sub_order_id) as $val) {
                                        $sub_order = \App\Model\SubOrder::find($val);
                                        $details = \App\Model\SubOrder::getSubOrderSummaryById($val);

                                        $total_sub_order = $total_sub_order + $details['sub_total'];
                                        $total_admin_commission = $total_admin_commission + $details['admin_commission'];
                                        $total_vat = $total_vat + $sub_order->tax;
                                        $total_shipping_cost = $total_shipping_cost + $sub_order->shipping_cost;
                                    }

                                    $total = $total_sub_order+$total_vat+$total_shipping_cost-$total_admin_commission;

                                    ?>
                                    <small style="color:red">Sub Total</small> {{env('CURRENCY_SYMBOL').number_format($total_sub_order,2)}}
                                        <br>
                                    <small style="color:red">+ VAT </small> {{env('CURRENCY_SYMBOL').number_format($total_vat,2)}}
                                    <br>
                                    <small style="color:red">+ Shipping Charge </small> {{env('CURRENCY_SYMBOL').number_format($total_shipping_cost,2)}}
                                    <br>
                                    {{--@if($order->coupon)--}}
                                    {{--<hr style="margin: 0;">--}}
                                    {{--<small style="color:green">- Coupon Discount (<a--}}
                                    {{--href="{{url('/admin/settings/coupon')}}">{{$order->coupon}}</a>) </small> {{number_format($order->discount,2)}}--}}
                                    {{--<br>@endif--}}
                                    {{--<hr style="margin: 0;">--}}
                                    {{--<b><small>Sub Total </small> {{env('CURRENCY_SYMBOL').number_format($total+$vat + $shipping_rate,2)}}</b><br>--}}


                                    <small style="color:green">- Commission</small> {{env('CURRENCY_SYMBOL').number_format($total_admin_commission,2)}}
                                    <br>
                                    <hr style="margin: 0;">
                                    <b><small>TOTAL </small>{{env('CURRENCY_SYMBOL').number_format($total,2)}}</b>
                                </span>
                            </td>
                            <td class="tabledit-view-mode text-center">
                                <span class="tabledit-span">
                                    {!! Form::open(['url'=>'/admin/payment/finalized']) !!}
                                    <button type="submit" name="sub_orders" value="{{encrypt($payment->sub_order_id)}}"
                                            class="btn btn-primary btn-sm">Pay The Bill</button>
                                        <input type="hidden" name="amount_paid" value="{{encrypt($total)}}">
                                        <input type="hidden" name="seller_id" value="{{encrypt($payment->seller_id)}}">
                                        <input type="hidden" name="commission" value="{{encrypt($total_admin_commission)}}">
                                        <input type="hidden" name="payment_month"
                                               @if(!empty(Request::get('month'))) value="{{encrypt(Request::get('month'))}}"
                                               @else value="{{encrypt(date('m-Y'))}}" @endif>
                                    {!! Form::close() !!}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">
                            <div class="alert alert-danger alert-icon alert-close alert-dismissible fade in"
                                 role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <i class="font-icon font-icon-warning"></i>
                                No Data Available.
                            </div>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {{--            @include('admin.pagination',['paginator'=>$payments,'appends'=>['from'=>Request::get('from'),'to'=>Request::get('to')]])--}}
            <br>
        </div><!--.container-fluid-->
    </div>
    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Order Details</h4>
                </div>
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center load_image" style="margin-top: 23px;">
                        <img src="{{asset('build/img/ring-alt.gif')}}" style="width:50px;"
                             alt="">

                        <div>Loading</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src="{{asset('/build')}}/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.input-group.date').datepicker({
            format: "mm-yyyy",
            minViewMode: 1,
            orientation: "bottom left",
            autoclose: true
        });

        $(document).ready(function () {
            $(".detail_btn").click(function () {
                var id = $(this).data('id');
                var seller_id = $(this).data('seller-id');
                var url_string = '{{url('/admin/order/details/')}}'
                url_string = url_string + '/' + id + "/" + seller_id;

                $("#model_body").empty();
                $('#modal_form_generate .load_image').show();
                $.ajax({
                    type: "GET",
                    url: url_string,
                    dataType: "json",
                    success: function (data) {
                        $("#modal_form_generate").html(data.data_generate);
                        $('#modal_form_generate .load_image').hide();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
@stop
