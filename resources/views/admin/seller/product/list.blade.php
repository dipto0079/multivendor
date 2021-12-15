@extends('admin.master')
@section('title','Seller List')
@section('stylesheet')
    <style type="text/css">
        .show {
            display: block;
        }

        hr {
            margin: 1em 0;
        }
    </style>
@stop

@section('content')
    <div class="page-content" style="padding-left: 115px;">
        <div class="container-fluid">
            {!! Form::open(['url'=>'/admin/product/seller']) !!}
            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-6">Product Seller List</div>
                                <div class="col-sm-6">
                                    @if(!empty(Session::get('message')))
                                        <div class="alert alert-danger alert-icon alert-close alert-dismissible fade in"
                                             role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                            <i class="font-icon font-icon-warning"></i>
                                            {{Session::get('message')}}
                                        </div>
                                    @endif
                                </div>
                            </h3>
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                          {!! Form::open(['url'=>'/admin/product/seller']) !!}
                          <div class="form-control-wrapper form-control-icon-right" style="width: 300px;">
                            <input type="text" name="search" class="form-control" @if(!empty($search)) value="{{$search}}" @endif placeholder="Search">
                            @if(!empty($search))
                            <i class="font-icon font-icon-close-2 color-red" style="cursor: pointer;"
                             onclick="location.replace('{{url('/admin/product/seller')}}')"></i>
                            @else
                            <i class="font-icon font-icon-search"></i>
                            @endif
                          </div>
                          {!! Form::close() !!}
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                            <select class="form-control input-sm" name="status" onchange="this.form.submit()"
                                    style="width: 200px;">
                                {{--<option value="all" @if($status == 'all') selected @endif>Status</option>--}}
                                <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::PENDING}}"
                                        @if($status == App\Http\Controllers\Enum\SellerStatusEnum::PENDING) selected @endif>
                                    Pending
                                </option>
                                <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::APPROVED}}"
                                        @if($status == App\Http\Controllers\Enum\SellerStatusEnum::APPROVED) selected @endif>
                                    Approved
                                </option>
                                <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::REJECTED}}"
                                        @if($status == App\Http\Controllers\Enum\SellerStatusEnum::REJECTED) selected @endif>
                                    Rejected
                                </option>
                                <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::BLOCKED}}"
                                        @if($status == App\Http\Controllers\Enum\SellerStatusEnum::BLOCKED) selected @endif>
                                    Blocked
                                </option>
                                <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::ARCHIVE}}"
                                        @if($status == App\Http\Controllers\Enum\SellerStatusEnum::ARCHIVE) selected @endif>
                                    Archive
                                </option>
                            </select>
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                            <a href="#form_modal" data-toggle="modal" class="btn" id="add_btn" data-id="add">Add New
                                Product Seller</a>
                        </div>
                    </div>

                </header>
            </section><!--.box-typical-->
            {!! Form::close() !!}
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th></th>
                    <th>Business Name</th>
                    <th>Store Name</th>
                    <th>Category</th>
                    <th>Commission</th>
                    {{--<th>Stores</th>--}}
                    <th>Products</th>
                    <th width="80" class="text-center">Status</th>
                    <th>Created</th>
                    <th class="text-center" width="120">Action</th>
                </thead>
                <tbody>

                @if(!empty($sellers[0]))
                    @foreach($sellers as $seller)
                        <tr>
                            <td class="tabledit-view-mode" width="50">
                                <span class="tabledit-span"><img class="admin-list-img"
                                            @if(!empty($seller->getUser->photo))
                                            src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $seller->getUser->photo), 50, 50, ['crop'])?>"
                                            @else
                                            src="{{asset('image/default_author.png')}}"
                                            @endif alt=""></span>
                            </td>
                            <td class="tabledit-view-mode"><span class="tabledit-span"><a
                                            href="{{url('admin/product/seller/'.$seller->id.'/product/list')}}">{{$seller->company_name}}</a></span>
                            </td>
                            <td class="tabledit-view-mode"><span class="tabledit-span"><a
                                            href="{{url('store/'.$seller->store_name)}}">{{$seller->store_name}}</a></span>
                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">@if(!empty($seller->getCategory->name))<span class="label label-custom label-pill label-default">{{$seller->getCategory->name}}</span>@endif</span>
                            </td>
                            <td class="tabledit-view-mode"><span class="label label-custom label-pill label-success">{{$seller->commission}}%</span>
                                @if(!empty($seller->getSellerCommission->where('status',\App\Http\Controllers\Enum\PaymentStatusEnum::COMPLETED)->sum('commission_charged')))
                                    {{env('CURRENCY_SYMBOL').number_format($seller->getSellerCommission->sum('commission_charged'),2)}}
                                @endif
                            </td>
                            {{--                            <td class="tabledit-view-mode"><span class="tabledit-span">@if($seller->getStores->count()>0){{$seller->getStores->count()}}@endif</span></td>--}}
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">@if($seller->getProducts->count()>0){{$seller->getProducts->count()}}@endif</span>
                            </td>
                            <td>
                                {!! Form::open(['url'=>'/admin/product/seller/status-change','class'=>'form-inline']) !!}
                                <select class="form-control input-sm" name="status" onchange="this.form.submit()">
                                    <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::PENDING}}"
                                            @if($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::PENDING) selected @endif>
                                        Pending
                                    </option>
                                    <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::APPROVED}}"
                                            @if($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::APPROVED) selected @endif>
                                        Approved
                                    </option>
                                    <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::REJECTED}}"
                                            @if($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::REJECTED) selected @endif>
                                        Rejected
                                    </option>
                                    <option value="{{App\Http\Controllers\Enum\SellerStatusEnum::BLOCKED}}"
                                            @if($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::BLOCKED) selected @endif>
                                        Blocked
                                    </option>
                                </select>
                                <input type="hidden" name="seller_id" value="{{$seller->id}}">
                                {!! Form::close() !!}

                            </td>
                            <td class="tabledit-view-mode"><span
                                        class="tabledit-span">{{date('d F, Y',strtotime($seller->created_at))}}</span>
                            </td>
                            <td style="white-space: nowrap; width: 1%;">

                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit_btn"
                                           href="{{url('admin/product/seller/'.$seller->id.'/product/list')}}"><span
                                                    class="glyphicon glyphicon-tags"></span> View Details
                                        </a>
                                        <a class="dropdown-item edit_btn" data-toggle="modal" href="#form_modal"
                                           data-id="{{$seller->id}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
                                        @if($seller->status != App\Http\Controllers\Enum\SellerStatusEnum::ARCHIVE)
                                          <a href="#delete_{{$seller->id}}" data-toggle="modal"
                                             class="dropdown-item  hover-red"><span
                                                      class="glyphicon glyphicon-trash"></span> Delete</a>
                                        @endif
                                    </div>
                                </div>
                                <!-- Modal -->
                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                    <div class="modal fade bs-modal-sm"
                                         id="delete_{{$seller->id}}" tabindex="-1"
                                         role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-sm" style="margin-top: 200px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 style="text-align: left;" class="modal-title"
                                                        id="myModalLabel">{{trans('Delete')}}</h4>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p>Would You Like to Delete?</p>
                                                    <button type="button" class="btn btn-inline btn-default"
                                                            data-dismiss="modal">
                                                        No
                                                    </button>

                                                    <a href="{{url('/admin/product/seller/delete/'.$seller->id)}}"
                                                       class="btn btn-inline btn-danger">Yes</a>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10">
                            <div class="alert alert-danger alert-icon alert-close alert-dismissible fade in"
                                 role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <i class="font-icon font-icon-warning"></i>
                                Product Seller Not Available.
                            </div>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            @include('admin.pagination',['paginator'=>$sellers,'appends'=>['status'=>$status]])
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
                {!! Form::open(array('url'=>'/admin/product/seller/save','id'=>'modal_form','files'=>true)) !!}
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
                <input type="hidden" name="skip" value="0" id="skip">
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <form action="{{url('/email-exists-checking')}}" id="email_checking">{{csrf_field()}}</form>
    <form action="{{url('/seller-email-exists-checking')}}" id="seller_email_checking">{{csrf_field()}}</form>
    <form action="{{url('/admin/store-exists')}}" id="store_checking">{{csrf_field()}}</form>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script>
        function emailCheck() {
            var email_exist = false;
            $('#business_email').keyup(function () {
                var Email = document.getElementById('business_email').value;
                var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

                var testEmail = regexEmail.test(Email);

                $.ajax({
                    type: "POST",
                    url: $('#seller_email_checking').attr('action') + '?email=' + Email,
                    data: $('#seller_email_checking').serialize(),
                    dataType: "json",
                    success: function (data) {
                        email_exist = data.exists;

                        if (regexEmail.test(Email) && email_exist == false) {
                            $(".business_email_msg").html('');
                            $('#submit_btn').attr('disabled', false);
                            $("#business_email").focus();
                        }
                        else if (email_exist == true) {
                            $(".business_email_msg").html('');
                            $('#submit_btn').attr('disabled', true);
                            $(".business_email_msg").html('This email already exists.');
                        }
                        else {
                            $('#submit_btn').attr('disabled', true);
                            $(".business_email_msg").html("Please write your correct email.");
                            document.getElementById('business_email').focus();
                        }
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });


            });
            $('#email_id').keyup(function () {
                var Email = document.getElementById('email_id').value;
                var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

                var testEmailID = regexEmail.test(Email);

                $.ajax({
                    type: "POST",
                    url: $('#email_checking').attr('action') + '?email=' + Email,
                    data: $('#email_checking').serialize(),
                    dataType: "json",
                    success: function (data) {
                        email_exist = data.exists;
                        if (regexEmail.test(Email)) {
                            $(".email_id_msg").html('');
                            $('#submit_btn').attr('disabled', false);
                            $("#email_id").focus();
                        }
                        else if (email_exist == true) {
                            $(".email_id_msg").html('');
                            $('#submit_btn').attr('disabled', true);
                            $(".email_id_msg").html('This email already exists.');
                        }
                        else {
                            $('#submit_btn').attr('disabled', true);
                            $(".email_id_msg").html("Please write your correct email.");
                            document.getElementById('email_id').focus();
                        }
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });


            });
        }

        function hasWhiteSpace(s) {
            return /\s/g.test(s);
        }

        function storeExists() {
            var store_exist = false;
            $('#store_name').keyup(function () {
                var store_name = $(this).val();
                var seller_id = $(this).closest('div.modal-body').find('#edit_id_div input').val();
                if (seller_id == undefined) {
                    seller_id = 0;
                }
                $.ajax({
                    type: "POST",
                    url: $('#store_checking').attr('action') + '?store_name=' + store_name + '&seller_id=' + seller_id,
                    data: $('#store_checking').serialize(),
                    dataType: "json",
                    success: function (data) {
                        store_exist = data.exists;
                        if (store_exist == true) {
                            $(".store_name_msg").html('');
                            $('#submit_btn').attr('disabled', true);
                            $(".store_name_msg").html('This store name already exists.');
                            $(this).focus();
                        }
                        else if (hasWhiteSpace(store_name) == true) {
                            $(".store_name_msg").html('');
                            $('#submit_btn').attr('disabled', true);
                            $(".store_name_msg").html('Please remove space from store name.');
                            $(this).focus();
                        }
                        else {
                            $(".store_name_msg").html('');
                            $('#submit_btn').attr('disabled', false);
                        }
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        }

        function getCities(){
            $(document.body).on('change','#country_id',function(){
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: '{{url('/admin/city-by-country')}}?country='+id,
                    dataType: "json",
                    success: function (data) {
                        $("#city_id").empty();
                        $("#city_id").html(data.cities_html);

                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            })
        }

        $(document).ready(function () {
            $("#add_btn").click(function () {
                var id = $(this).data('id');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?add_id=' + id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Product Seller Add');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();

                        storeExists();
                        emailCheck();
                        getCities();
                        $(function() {
                          autosize($('textarea[data-autosize]'));
                        });
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
            $(".edit_btn").click(function () {
                var id = $(this).data('id');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?edit_id=' + id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Product Seller Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();

                        storeExists();
                        emailCheck();
                        getCities();
                        $(function() {
                          autosize($('textarea[data-autosize]'));
                        });
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
    <script src="{{asset('/build/js/lib/autosize/autosize.min.js')}}"></script>
    <script src="{{asset('/build/js/lib/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
    <script src="{{asset('/build/js/lib/bootstrap-maxlength/bootstrap-maxlength-init.js')}}"></script>
    <script>
      $(function() {
        autosize($('textarea[data-autosize]'));
      });
    </script>
@stop
