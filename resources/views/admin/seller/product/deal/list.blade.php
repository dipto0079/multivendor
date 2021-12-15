@extends('admin.master',['user_name'=>App\User::getUserName($user_id)])
@section('title','Deal List')
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

    @include('admin.seller.submenu',['page'=>'product','open'=>'deal','seller_id'=>$seller_id])

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        {!! Form::open(['url'=>'/admin/product/seller/'.$seller_id.'/deal/list','class'=>'form-inline','id'=>'search_form']) !!}
                        <div class="tbl-cell tbl-cell-title">
                            <h3>Deal List</h3>
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered  text-right"
                             style="width: 60%; text-align: right">

                            <div class="form-group">
                                <label for="" style="display: inline-block;">Date Filter </label>

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
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                            <select name="status" class="form-control" id="" style="width: 135px;" onchange="formsubmit()">
                                <option @if($status == \App\Http\Controllers\Enum\DealStatusEnum::PENDING) selected @endif value="{{\App\Http\Controllers\Enum\DealStatusEnum::PENDING}}">Pending</option>
                                <option @if($status == \App\Http\Controllers\Enum\DealStatusEnum::APPROVED) selected @endif value="{{\App\Http\Controllers\Enum\DealStatusEnum::APPROVED}}">Accepted</option>
                            </select>
                            <script>
                                function formsubmit() {
                                    document.getElementById('search_form').submit();
                                }
                            </script>
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                            <a href="#form_modal" data-toggle="modal" class="btn" id="add_btn" data-seller="{{\Illuminate\Support\Facades\Crypt::encrypt($seller_id)}}" data-id="add">Add New Deal</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Deal Title</th>
                        <th>Discount</th>
                        <th>Discount Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-center" width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($deals[0]))
                    @foreach($deals as $deal)
                        <tr>
                            <td class="tabledit-view-mode"><a href="{{url('/admin/product/seller/'.$seller_id.'/product/list')}}">{{str_limit($deal->product_name,30)}}</a></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{str_limit($deal->title,40)}}</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$deal->discount}}</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">
                                    @if(\App\Http\Controllers\Enum\DiscountTypeEnum::FIXED == $deal->discount_type) <span class="label label-custom label-pill label-danger">Fixed</span>
                                    @else <span class="label label-custom label-pill label-success">Percentage</span>
                                    @endif
                                </span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{date('d F, Y',strtotime($deal->from_date))}}</span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{date('d F, Y',strtotime($deal->to_date))}}</span>
                            </td>

                                <td class="tabledit-view-mode"><span class="tabledit-span">
                                         <span class="checkbox-toggle pull-right" style="margin-right: 15px">
                                        {!! Form::open(array('url'=>'/admin/product/seller/deal/change-status')) !!}
                                             <input type="checkbox" @if($status == \App\Http\Controllers\Enum\DealStatusEnum::APPROVED) checked @endif  onchange="this.form.submit()" id="check-toggle-editor-{{$deal->id}}" />
                                            <label for="check-toggle-editor-{{$deal->id}}"></label>
                                            <input type="hidden" name="deal_id" value="{{$deal->id}}">
                                         {!! Form::close() !!}
                                </span>
                                    </span>
                                </td>

                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{date('d F, Y h:m a',strtotime($deal->created_at))}}</span>
                            </td>
                            <td style="white-space: nowrap; width: 1%;">

                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit_btn" data-toggle="modal" href="#form_modal"
                                           data-id="{{$deal->id}}" data-seller="{{\Illuminate\Support\Facades\Crypt::encrypt($seller_id)}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
                                        <a href="#delete_{{$deal->id}}" data-toggle="modal"
                                           class="dropdown-item  hover-red"><span class="fa fa-ban"></span> Delete</a>
                                    </div>
                                </div>
                                    <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                        <div class="modal fade bs-modal-sm"
                                             id="delete_{{$deal->id}}" tabindex="-1"
                                             role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-sm" style="margin-top: 200px;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close"
                                                                data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                        <h4 style="text-align: left;" class="modal-title"
                                                            id="myModalLabel">Delete</h4>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <p>Would You Like to Delete?</p>
                                                        <button type="button" class="btn btn-inline btn-default"
                                                                data-dismiss="modal">
                                                            No
                                                        </button>
                                                        <a href="{{url('/admin/product/seller/deal/delete/'.$deal->id)}}"
                                                           class="btn btn-inline btn-danger">Yes</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
            @include('admin.pagination',['paginator'=>$deals])
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
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/product/seller/deal/save','id'=>'modal_form')) !!}
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
        getDatePickerRange();
        function getDatePickerRange(){
            $('.input-daterange').datepicker({
                format: "dd-mm-yyyy",
                daysOfWeekHighlighted: "5,6",
                autoclose: true,
                todayHighlight: true
            });
        }

        $(document).ready(function () {
            $("#add_btn").click(function () {
                var id = $(this).data('id');
                var seller_id = $(this).data('seller');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?add_id=' + id+'&seller_id='+seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Deal Add');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        $('.category_type').select2();

                        getDatePickerRange();

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
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
            $(".edit_btn").click(function () {
                var id = $(this).data('id');
                var seller_id = $(this).data('seller');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?edit_id=' + id+'&seller_id='+seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Deal Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        $('.category_type').select2();

                        getDatePickerRange();

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
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });

    </script>



@stop
