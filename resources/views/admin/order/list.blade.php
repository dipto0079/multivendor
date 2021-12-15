@extends('admin.master')
@section('title','Pending Order List')
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
    @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING) @include('admin.order.submenu',['page'=>'order_pending']) <?php $status_url = 'pending'; ?>
    @elseif(!empty($status) && $status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED) @include('admin.order.submenu',['page'=>'order_accepted']) <?php $status_url = 'accepted'; ?>
    @elseif(!empty($status) && $status == \App\Http\Controllers\Enum\OrderStatusEnum::REJECTED) @include('admin.order.submenu',['page'=>'order_rejected']) <?php $status_url = 'rejected'; ?>
    @endif

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">
                                    @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING) Pending
                                    @elseif(!empty($status) && $status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED)
                                        Accepted
                                    @elseif(!empty($status) && $status == \App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED)
                                        Delivered
                                    @elseif(!empty($status) && $status == \App\Http\Controllers\Enum\OrderStatusEnum::REJECTED)
                                        Rejected
                                    @endif
                                    Order List
                                </div>
                                <div class="col-sm-8 text-right">
                                    {!! Form::open(['url'=>'/admin/order/'.$status_url,'method'=>'get','class'=>'form-inline']) !!}
                                    <div class="form-group">
                                        <label for="" style="display: inline-block;">Date Filter</label>
                                        <div class="input-daterange input-group" id="datepicker" style="width: 65%">
                                            <input type="text" class="input-sm form-control" name="from"
                                                   @if(!empty(Request::get('from'))){
                                                   value="{{date('d-m-Y',strtotime(Request::get('from')))}}"
                                                    @endif
                                            />
                                            <span class="input-group-addon">to</span>
                                            <input type="text" class="input-sm form-control" name="to"
                                                   @if(!empty(Request::get('to'))){
                                                   value="{{date('d-m-Y',strtotime(Request::get('to')))}}"
                                                    @endif
                                            />
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
                    <th>Order#</th>
                    <th>Total Sub Orders</th>
                    <th>Buyer</th>
                    <th>Date</th>
                    <th>Amount</th>

                    @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)
                        <th width="100" style="text-align: center">Accept</th> @endif
                    <th class="text-center" width="120">Action</th>
                </tr>
                </thead>
                <tbody>

                @if(!empty($orders[0]))
                    @foreach($orders as $order)
                        <?php



                        $details = \App\Model\Order::getOrderSummaryById($order->id);
                        $order_total = $details['sub_total'];
                        $admin_commission = $details['admin_commission'];

                        $order_amount = $order_total + $order->vat_amount + $order->shipping_rate - $order->discount;

                        ?>
                        <tr>
                            <td class="tabledit-view-mode">
                              <span class="tabledit-span"><a class="dropdown-item detail_btn" data-toggle="modal"
                                                             href="#form_modal" data-id="{{$order->id}}"><span
                                              class="fa fa-eye"></span> {{$order->id}}
                                    </a>
                              </span>
                            </td>
                            <td class="tabledit-view-mode">
                              <span class="tabledit-span">
                                @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED)
                                      <a href="#sub_order_status" class="sub_order_status" data-id="{{$order->id}}"
                                         data-toggle="modal"> @endif
                                          {{$order->getSubOrders->count()}}
                                          @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED) </a> @endif

                                  @if($order->getSubOrders->count() == $order->getSubOrders->where('status',\App\Http\Controllers\Enum\OrderStatusEnum::FINALIZED)->count())
                                      All Complete @endif

                                  @if($order->getSubOrders->where('status',\App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED)->count())
                                      {{$order->getSubOrders->where('status',\App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED)->count()}}
                                      Delivered
                                  @endif
                              </span>
                            </td>
                            <td class="tabledit-view-mode"><span class="tabledit-span"><a
                                            href="{{url('/admin/buyer/'.$order->getBuyer->id.'/notification/list')}}">{{$order->getBuyer->getUser->username}}</a></span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{date('d F, Y h:m a',strtotime($order->created_at))}}</span>
                            </td>
                            <td class="tabledit-view-mode text-right"><span class="tabledit-span">
                                    {{env('CURRENCY_SYMBOL').number_format($order->sub_total_price,2)}}<br>
                                    <small style="color:red">+ VAT </small> {{env('CURRENCY_SYMBOL').number_format($order->vat_amount,2)}}<br>
                                    <small style="color:red">+ Shipping Charge </small> {{env('CURRENCY_SYMBOL').number_format($order->shipping_rate,2)}}<br>
                                    @if($order->coupon)<hr style="margin: 0;"><small style="color:green">- Coupon Discount (<a href="{{url('/admin/settings/coupon')}}">{{$order->coupon}}</a>) </small> {{number_format($order->discount,2)}}<br>@endif
                                    {{--<small style="color:green">- Commission</small> {{number_format($admin_commission,2)}}<br>--}}
                                    <hr style="margin: 0;">
                                    <b><small style="color:green">Amount Paid</small> {{env('CURRENCY_SYMBOL').number_format($order_amount,2)}}</b>
                                </span>
                            </td>


                            @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)
                                <td class="tabledit-view-mode">
                                <span class="tabledit-span">
                                <span class="checkbox-toggle pull-right" style="margin-right: 15px">
                                    {!! Form::open(array('url'=>'/admin/order/publish-order')) !!}
                                    <input type="checkbox" onchange="this.form.submit()"
                                           id="check-toggle-editor-{{$order->id}}"/>
                                        <label for="check-toggle-editor-{{$order->id}}"></label>
                                        <input type="hidden" name="order_id" value="{{$order->id}}">
                                    {!! Form::close() !!}
                                </span>
                                </span>
                                </td>
                            @endif

                            <td style="white-space: nowrap; width: 1%;">
                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item detail_btn" data-toggle="modal" href="#form_modal"
                                           data-id="{{$order->id}}"><span
                                                    class="fa fa-eye"></span> Details
                                        </a>
                                        @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)
                                            <a href="#delete_{{$order->id}}" data-toggle="modal"
                                               class="dropdown-item  hover-red"><span class="fa fa-ban"></span>
                                                Reject</a>
                                        @endif
                                    </div>
                                </div>
                                @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)
                                    <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                        <div class="modal fade bs-modal-sm"
                                             id="delete_{{$order->id}}" tabindex="-1"
                                             role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-sm" style="margin-top: 200px;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close"
                                                                data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                        <h4 style="text-align: left;" class="modal-title"
                                                            id="myModalLabel">Reject</h4>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <p>Would You Like to Reject?</p>
                                                        <button type="button" class="btn btn-inline btn-default"
                                                                data-dismiss="modal">
                                                            No
                                                        </button>
                                                        <a href="{{url('/admin/order/reject/'.$order->id)}}"
                                                           class="btn btn-inline btn-danger">Yes</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="12">
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
            @include('admin.pagination',['paginator'=>$orders,'appends'=>['from'=>Request::get('from'),'to'=>Request::get('to')]])
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
    <div class="modal fade" id="sub_order_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Sub Order Status</h4>
                </div>
                <div class="modal-body" id="status_generate">
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
        $('.input-daterange').datepicker({
            format: "dd-mm-yyyy",
            daysOfWeekHighlighted: "5,6",
            autoclose: true,
            todayHighlight: true
        });
        $(document).ready(function () {
            $(".detail_btn").click(function () {
                var id = $(this).data('id');
                $("#model_body").empty();
                $('#modal_form_generate .load_image').show();
                $.ajax({
                    type: "GET",
                    url: '{{url('/admin/order/details/')}}/' + id+"/0/0",
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

            $(".sub_order_status").click(function () {
                var id = $(this).data('id');
                $("#model_body").empty();
                $('#status_generate .load_image').show();
                $.ajax({
                    type: "GET",
                    url: '{{url('/admin/order/sub_order/status/')}}/' + id,
                    dataType: "json",
                    success: function (data) {
                        $("#status_generate").html(data.data_generate);
                        $('#status_generate .load_image').hide();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
@stop
