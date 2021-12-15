@extends('admin.master',['user_name'=>App\User::getUserName($user_id)])
@section('title','Service List')
@section('stylesheet')
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

    @include('admin.seller.submenu',['page'=>'service','seller_id'=>$seller_id])


    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Service List</div>
                                <div class="col-sm-8">
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
                            <a href="#form_modal" data-toggle="modal" class="btn" id="add_btn"
                               data-seller="{{\Illuminate\Support\Facades\Crypt::encrypt($seller_id)}}" data-id="add">Add
                                New Product</a>
                        </div>
                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Categories</th>
                    <th width="120">No. of Media</th>
                    <th width="100" style="text-align: center">Is Editor's Choice?</th>
                    <th width="100" style="text-align: center">Is Featured?</th>
                    <th class="text-center" width="120">Action</th>
                </thead>
                <tbody>

                @if(!empty($products[0]))
                    @foreach($products as $product)
                        <tr>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$product->name}}</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">
                                    <span class="label label-custom label-pill label-default">@if(isset($product->getCategory)){{$product->getCategory->name}}@endif</span>
                                </span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">
                                    <span class="label label-pill @if(!empty($product->getMedia->count())) label-danger @else label-default @endif media_count_{{$product->id}}">{{$product->getMedia->count()}}</span>
                                    <a class="media_modal_popup" data-toggle="modal" href="#media_form_modal"
                                       data-id="{{$product->id}}">Add</a>
                                </span></td>
                            <td class="tabledit-view-mode">
                                <span class="tabledit-span">
                                <span class="checkbox-toggle pull-right" style="margin-right: 15px">
                                    {!! Form::open(array('url'=>'/admin/service/seller/service/is-editors-choice','files'=>true)) !!}
                                    <input type="checkbox" onchange="this.form.submit()"
                                           id="check-toggle-editor-{{$product->id}}"
                                           @if($product->is_editors_choice==1) checked @endif />
                                        <label for="check-toggle-editor-{{$product->id}}"></label>
                                        <input type="hidden" name="product_id_for_editor_choice"
                                               value="{{$product->id}}">
                                    {!! Form::close() !!}
                                </span>
                                </span>
                            </td>
                            <td class="tabledit-view-mode">
                                <span class="tabledit-span">
                                <span class="checkbox-toggle pull-right" style="margin-right: 15px">
                                    {!! Form::open(array('url'=>'/admin/service/seller/service/is-featured','files'=>true)) !!}
                                    <input type="checkbox" onchange="this.form.submit()"
                                           id="check-toggle-featured-{{$product->id}}"
                                           @if($product->is_featured==1) checked @endif />
                                        <label for="check-toggle-featured-{{$product->id}}"></label>
                                        <input type="hidden" name="product_id_for_featured" value="{{$product->id}}">
                                    {!! Form::close() !!}
                                </span>
                                </span>
                            </td>
                            <td style="white-space: nowrap; width: 1%;">

                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit_btn" data-toggle="modal" href="#form_modal"
                                           data-seller="{{\Illuminate\Support\Facades\Crypt::encrypt($seller_id)}}"
                                           data-id="{{$product->id}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
                                        <a class="dropdown-item media_modal_popup" data-toggle="modal"
                                           href="#media_form_modal" data-id="{{$product->id}}"><span></span> Add
                                            Media</a>
                                        <a href="#delete_{{$product->id}}" data-toggle="modal"
                                           class="dropdown-item  hover-red"><span
                                                    class="glyphicon glyphicon-trash"></span> Delete</a>
                                    </div>
                                </div>

                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                    <div class="modal fade bs-modal-sm"
                                         id="delete_{{$product->id}}" tabindex="-1"
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
                                                    <a href="{{url('/admin/product/seller/product/delete/'.$product->id)}}"
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
                        <td colspan="6">
                            <div class="alert alert-danger alert-icon alert-close alert-dismissible fade in"
                                 role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <i class="font-icon font-icon-warning"></i>
                                Service Not Available.
                            </div>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            @include('admin.pagination',['paginator'=>$products])
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
                {!! Form::open(array('url'=>'/admin/service/seller/service/save','id'=>'modal_form','files'=>true)) !!}
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
    <div class="modal fade bd-example-modal-lg" id="media_form_modal" data-backdrop="true" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(array('url'=>'/admin/service/seller/service/media/save','id'=>'media_modal_form','files'=>true)) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Upload Media
                        <span class="btn btn-rounded btn-file pull-right">
                            <span>Add Media</span>
                            <input accept="image/*" type="file" class="media_upload" name="photo[]" multiple value="">
                        </span>
                    </h4>
                </div>

                <div class="modal-body" id="media_modal_form_generate"
                     style="height: 440px; overflow-x: initial; width: 100%; overflow-y: scroll;">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="media_progress" style="display:none;">
                                    <progress class="progress" value="0" max="100">
                                        <div class="progress">
                                            <span class="progress-bar" id="progress-bar" style="width: 0%;">0%</span>
                                        </div>
                                    </progress>
                                    <div class="uploading-list-item-progress">0%</div>
                                </div>
                                <div class="gallery-grid" id="add_ajax_product_id">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="product_id" id="product_id_media" value="1">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" onclick="this.form.submit()" class="btn btn-primary" id="submit_btn">Save
                    </button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>

    <form action="{{url('/email-exists-checking')}}" id="email_checking">{{csrf_field()}}</form>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script>
        function getGridFunc() {
            $(function () {
                $('.gallery-item').matchHeight({
                    target: $('.gallery-item .gallery-picture')
                });
            });
        }
        $(document.body).on('click', '.delete_image', function () {
            var media_image_id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: '{{url('/admin/service/seller/service/media/delete')}}' + '?media_image_id=' + media_image_id,
                dataType: "json",
                success: function (data) {
                    $('.media_' + media_image_id).remove();
                    toastr.success(data.message);
                    $('.media_count_' + data.product_id).html(data.media_count);
                    if (data.media_count == 0) {
                        $('#add_ajax_product_id').html('<span class="text-center text-danger">No Media File Exist!!!</span>');
                        $('.media_count_' + data.product_id).removeClass('label-danger');
                        $('.media_count_' + data.product_id).addClass('label-default');
                    }
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });
        });

        $(document.body).on('click', '.media_modal_popup', function () {
            $('#product_id_media').val('')
            var product_id = $(this).data('id');
            $('#product_id_media').val(product_id);
            $.ajax({
                type: "GET",
                url: '{{url('/admin/service/seller/service/media/save')}}' + '?edit_id=edit_id&product_id=' + product_id,
                dataType: "json",
                success: function (data) {
                    $('#add_ajax_product_id').empty();
                    $('#add_ajax_product_id').append(data.data_generate);
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });
        });
        $(document).ready(function () {
            $(document.body).on('submit', '#media_modal_form', function (e) {
                if ($('#skip').val() == 0) {
                    $("#media_progress").show();
                    $(".progress").val(0);
                    $('#add_ajax_product_id').empty();
                    $('#add_ajax_product_id').append('<div class="text-center"><img src="{{asset('/build/img/ring-alt.gif')}}" height="100" ></div>');
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: $('#media_modal_form').attr('action') + '?add_id=add_id',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        xhr: function () {
                            //upload Progress
                            var xhr = $.ajaxSettings.xhr();
                            if (xhr.upload) {
                                xhr.upload.addEventListener('progress', function (event) {
                                    var percent = 0;
                                    var position = event.loaded || event.position;
                                    var total = event.total;
                                    if (event.lengthComputable) {
                                        percent = Math.ceil(position / total * 100);
                                    }
                                    //update progressbar

                                    $(".progress").val(percent);
                                    $("#progress-bar").css("width", +percent + "%");
                                    $(".uploading-list-item-progress").html(percent + "%");
                                }, true);
                            }
                            return xhr;
                        },
                        mimeType: "multipart/form-data"
                    }).done(function (data) { //
                        // $(my_form_id)[0].reset(); //reset form
                        // $(result_output).html(res); //output response from server
                        var data_arr = JSON.parse(data);
                        getGridFunc();
                        $('#add_ajax_product_id').empty();
                        $('#add_ajax_product_id').append(data_arr.data_generate);
                        $('.media_count_' + data_arr.product_id).html(data_arr.media_count);
                        $('.media_count_' + data_arr.product_id).removeClass('label-default');
                        $('.media_count_' + data_arr.product_id).addClass('label-danger');
                        $("#media_progress").hide();
                    });
                }

            });
            $(document.body).on('change', '.media_upload', function () {
                $('#skip').val(0);
                $('#media_modal_form').submit();
            });
        });
        //        $(document).ready(function(){
        //           $(document.body).on('submit','#media_modal_form',function(e){
        //             e.preventDefault();
        //               $.ajax({
        //                   type: "POST",
        //                   url: $('#media_modal_form').attr('action')+'?add_id=add_id',
        //                   data: new FormData(this),
        //                   contentType: false,
        //                   cache: false,
        //                   processData:false,
        //                   dataType: "json",
        //                   success: function (data) {
        //
        //                        $('#add_ajax_product_id').empty();
        //                        $('#add_ajax_product_id').append(data.data_generate);
        //                   }
        //               }).fail(function (data) {
        //                   var errors = data.responseJSON;
        //                   console.log(errors);
        //               });
        //           });
        //            $(document.body).on('change','.media_upload',function(){
        //                $('#media_modal_form').submit();
        //            });
        //        });

        $(document).ready(function () {
            $(document.body).on('change', '#category_type_id', function () {
                var product_type_id = $('#category_type_id').val();
                if (product_type_id == 1) {
                    $('#product_type').show();
                    $('#service_type').hide();
                    $("#service_type_id").prop('required', false);
                    $("#product_type_id").prop('required', true);
                }
                if (product_type_id == 2) {
                    $('#product_type').hide();
                    $('#service_type').show();
                    $("input").prop('required', true);
                    $("#service_type_id").prop('required', true);
                    $("#product_type_id").prop('required', false);
                }
            });
        });
        $(document).ready(function () {
            $("#add_btn").click(function () {
                var id = $(this).data('id');
                var seller_id = $(this).data('seller');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?add_id=' + id + '&seller_id=' + seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Product Add');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        //$('.category_type').select2();

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
                    url: $('#modal_form').attr('action') + '?edit_id=' + id + '&seller_id=' + seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Product Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        // $('.category_type').select2();

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
