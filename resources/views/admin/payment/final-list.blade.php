@extends('admin.master')
@section('title','Payment Final List')
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

        .form-inline {
            display: inline-block;
        }
    </style>
@stop

@section('content')

    @include('admin.payment.submenu',['page'=>'payment_final'])

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">

                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-2">Final Payment List</div>
                                <div class="col-sm-10 text-right">
                                    {!! Form::open(['url'=>'/admin/payment/final','method'=>'get','class'=>'form-inline']) !!}


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

                                    <select class="form-control input-sm" name="status" onchange="this.form.submit()"
                                            style="width: 200px;">
                                        <option value="{{App\Http\Controllers\Enum\PaymentStatusEnum::PENDING}}"
                                                @if($status == App\Http\Controllers\Enum\PaymentStatusEnum::PENDING) selected @endif>
                                            Pending
                                        </option>
                                        <option value="{{App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED}}"
                                                @if($status == App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED) selected @endif>
                                            Completed
                                        </option>
                                        <option value="{{App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED}}"
                                                @if($status == App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED) selected @endif>
                                            Rejected
                                        </option>

                                    </select>

                                    {!! Form::close() !!}
                                </div>
                            </h3>
                        </div>

                        <div class="tbl-cell tbl-cell-action-bordered">
                            {!! Form::open(['url'=>'/admin/payment/export','class'=>'form-inline']) !!}
                            <div class="form-group">
                                <button type="submit" name="export"
                                        @if(!empty(Request::get('month'))) value="{{Request::get('month')}}"
                                        @else value="{{date('m-Y')}}" @endif class="btn btn-primary">Export
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Orders No.</th>
                    <th width="250">Seller</th>
                    <th width="150">Commission</th>
                    <th width="150">Payable Amount</th>
                    {{--<th width="80" class="text-center">No. of Sub Orders</th>--}}
                    {{--<th width="250">Comment</th>--}}
                    <th width="100" class="text-center">Status</th>
                    <th class="text-center" width="80">Action</th>
                </thead>
                <tbody>
                @if(!empty($payments[0]))
                    @foreach($payments as $payment)
                        <?php
                        $orders = '';
                        $first = 0;
                        $sub_orders = $payment->getPaymentSubOrder;



                        foreach ($sub_orders as $sub_order) {
                            if ($first == 0) {
                                $orders = '<a class="detail_btn" data-toggle="modal" href="#form_modal" data-seller-id="' . $sub_order->seller_id . '" data-id="' . $sub_order->order_id . '">' . $sub_order->order_id . '</a>';
                                $first = 1;
                            } else {
                                $orders .= ', ' . '<a class="detail_btn" data-toggle="modal" href="#form_modal" data-seller-id="' . $sub_order->seller_id . '" data-id="' . $sub_order->order_id . '">' . $sub_order->order_id . '</a>';
                            }
                        }
                        ?>
                        <tr>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{!! $orders !!}</span></td>
                            <td class="tabledit-view-mode">
                              <span class="tabledit-span">
                                <a href="{{url('admin/product/seller/'.$payment->getSeller->id.'/product/list')}}">{{$payment->getSeller->getUser->username}}</a>
                              </span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{env('CURRENCY_SYMBOL').number_format($payment->commission_charged,2)}}</span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{env('CURRENCY_SYMBOL').number_format($payment->amount,2)}}</span>
                            </td>
                            {{--<td class="tabledit-view-mode text-center"><span--}}
                            {{--class="tabledit-span">{{$payment->getPaymentSubOrder->count()}}</span></td>--}}
                            {{--<td class="tabledit-view-mode"><span class="tabledit-span">{{$payment->comment}}</span></td>--}}
                            <td class="tabledit-view-mode text-center">
                                <span class="tabledit-span">
                                    @if($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::PENDING) <span
                                            class="label label-custom label-pill label-info">Pending</span>
                                    @elseif($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED)
                                        <span class="label label-custom label-pill label-success">Completed</span>
                                    @elseif($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED)
                                        <span class="label label-custom label-pill label-danger">Rejected</span>
                                    @endif
                                </span>
                            </td>
                            <td class="tabledit-view-mode text-center">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#payment_modal_{{$payment->id}}">
                                    Action
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="payment_modal_{{$payment->id}}" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title text-left" id="myModalLabel">Payment Status</h4>
                                            </div>
                                            {!! Form::open(['url'=>'/admin/payment/final/status']) !!}
                                            <div class="modal-body text-left">
                                                <div class="form-group">
                                                    <label for="">Status</label>
                                                    <select @if($payment->status != \App\Http\Controllers\Enum\PaymentStatusEnum::PENDING) readonly=""
                                                            @endif name="status" id="" onchange="paymentStatus(this)"
                                                            class="form-control input-sm">

                                                        @if($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::PENDING)

                                                            <option @if($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED) selected
                                                                    @endif value="{{\App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED}}">
                                                                Completed
                                                            </option>

                                                            <option @if($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED) selected
                                                                    @endif value="{{\App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED}}">
                                                                Rejected
                                                            </option>

                                                        @else
                                                            <?php
                                                            $txt = "";
                                                            $val = "";
                                                            if ($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::PENDING) {
                                                                $txt = "Pending";
                                                                $val = \App\Http\Controllers\Enum\PaymentStatusEnum::PENDING;
                                                            } elseif ($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED) {
                                                                $txt = "Completed";
                                                                $val = \App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED;
                                                            } elseif ($payment->status == \App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED) {
                                                                $txt = "Rejected";
                                                                $val = \App\Http\Controllers\Enum\PaymentStatusEnum::REJECTED;
                                                            }
                                                            ?>

                                                            <option selected value="{{$val}}">{{$txt}}</option>
                                                        @endif


                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Comment</label>
                                                    <textarea name="comment" class="form-control" id=""
                                                              rows="6">{{$payment->comment}}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                            <input type="hidden" name="payment_id" value="{{$payment->id}}">
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8">
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
    <script>$('.notifyjs-corner').empty();</script>
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
