@extends('admin.master',['user_name'=>App\User::getUserName($user_id)])
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
    </style>
@stop

@section('content')

    @include('admin.seller.submenu',['page'=>'product','open'=>'order','seller_id'=>$seller_id])

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                Pending Order List
                            </h3>
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered  text-right"
                             style="width: 80%; text-align: right">
                            {!! Form::open(['url'=>'/admin/product/seller/'.$seller_id.'/order/list','class'=>'form-inline','id'=>'search_form']) !!}
                            <div class="form-group">
                                <label for="" style="display: inline-block;">Date Filter</label>
                                <div class="input-daterange input-group" id="datepicker" style="width: 55%">
                                    <input type="text" class="input-sm form-control" name="from" id="from"
                                           @if(!empty(Request::get('from'))){
                                           value="{{date('d-m-Y',strtotime(Request::get('from')))}}"
                                            @endif
                                    />
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="input-sm form-control" name="to" id="to"
                                           @if(!empty(Request::get('to'))){
                                           value="{{date('d-m-Y',strtotime(Request::get('to')))}}"
                                            @endif
                                    />
                                </div>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>

                            <select name="status" class="form-control" id="" style="width: 135px;"
                                    onchange="formsubmit()">
                                <option @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING) selected
                                        @endif value="{{\App\Http\Controllers\Enum\OrderStatusEnum::PENDING}}">Pending
                                </option>
                                <option @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED) selected
                                        @endif value="{{\App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED}}">Accepted
                                </option>
                                <option @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED) selected
                                        @endif value="{{\App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED}}">
                                    Delivered
                                </option>
                                <option @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::FINALIZED) selected
                                        @endif value="{{\App\Http\Controllers\Enum\OrderStatusEnum::FINALIZED}}">
                                    Finalized
                                </option>
                                <option @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::REJECTED) selected
                                        @endif value="{{\App\Http\Controllers\Enum\OrderStatusEnum::REJECTED}}">Rejected
                                </option>
                            </select>
                            <script>
                                function formsubmit() {
                                    document.getElementById('search_form').submit();
                                }
                            </script>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Order#</th>
                    <th>Buyer</th>
                    <th>Date</th>
                    <th>Sub Total</th>
                    <th>Vat</th>
                    <th>Shipping</th>
                    <th>Admin Commission</th>
                    <th>Amount Paid</th>
                    <th class="text-center" width="120">Action</th>
                </thead>
                <tbody>


                @if(!empty($subOrders[0]))
                    @foreach($subOrders as $so)
                        <?php


                        $details = \App\Model\SubOrder::getSubOrderSummaryById($so->id);


                        $order_total = $details['sub_total'];
                        $admin_commission = $details['admin_commission'];
                        $total = $order_total + $so->tax + $so->shipping_cost - $admin_commission;// - $so->getOrder->discount;

                        ?>

                        <tr>
                            <td class="tabledit-view-mode"><span class="tabledit-span">
                                    <a class="dropdown-item details_btn" data-toggle="modal" href="#form_modal"
                                       data-id="{{$so->order_id}}" data-seller="{{$seller_id}}"><span
                                                class="fa fa-eye"></span> {{$so->order_id}}
                                        </a>
                                    </span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span"><a
                                            href="{{url('/admin/buyer/'.$so->getOrder->getBuyer->id.'/order/list')}}">{{$so->getOrder->getBuyer->getUser->username}}</a></span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{date('d F, Y h:m a',strtotime($so->created_at))}}</span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{number_format($order_total,2)}}</span></td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{number_format($so->tax,2)}}</span></td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{number_format($so->shipping_cost,2)}}</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{number_format($admin_commission,2)}}
                                    ({{$so->admin_commission}}%)</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{number_format($total,2)}}</span></td>

                            <td style="white-space: nowrap; width: 1%;">

                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item details_btn" data-toggle="modal" href="#form_modal"
                                           data-id="{{$so->order_id}}" data-seller="{{$seller_id}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Details
                                        </a>
                                        @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)
                                            <a href="#delete_{{$so->id}}" data-toggle="modal"
                                               class="dropdown-item  hover-red"><span class="fa fa-ban"></span>
                                                Reject</a>
                                        @endif
                                    </div>
                                </div>
                                @if($status == \App\Http\Controllers\Enum\OrderStatusEnum::PENDING)
                                    <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                        <div class="modal fade bs-modal-sm"
                                             id="delete_{{$so->id}}" tabindex="-1"
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
                                                        <a href="{{url('/admin/order/reject/'.$so->id)}}"
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
                        <td colspan="9">
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
            @include('admin.pagination',['paginator'=>$subOrders])
            <br>
        </div><!--.container-fluid-->
    </div>
    <!-- Button trigger modal -->

    <!-- Modal -->


    <div class="modal fade bd-example-modal-lg" id="form_modal" data-backdrop="true" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Order Details</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/product/seller/'.$seller_id.'/order/details','id'=>'modal_form','files'=>true)) !!}
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center load_image" style="margin-top: 23px;">
                        <img src="{{asset('build/img/ring-alt.gif')}}" style="width:50px;"
                             alt="">

                        <div>Loading</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit_btn">Save</button>
                </div>
                {!! Form::close() !!}

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
        function numericNumber() {
            $('.numeric').attr('min', 0);

            $('.numeric').keypress(function (e) {
                var regex = /^[0-9\b]+$/;
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        }
        $(document).ready(function () {
            $(".details_btn").click(function () {
                var order_id = $(this).data('id');
                var seller_id = $(this).data('seller');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "GET",
                    url: '{{url('/admin/order/details/')}}' + '/' + order_id + '/' + seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Order Details');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        //$('.category_type').select2();

                        numericNumber();
                    }
                }).fail(function (data) {
//                    var errors = data.responseJSON;
                    console.log(data.responseText);
                });
            });
        });
    </script>


@stop
