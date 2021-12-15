@extends('admin.master')
@section('title','Service Category List')
@section('stylesheet')
    <style>
        .dd-handle{
            cursor: default;
        }
        .dd-handle:hover a{
            color: white;
        }
        .cimage
        {
            padding-right: 5px;
        }
    </style>
@stop

@section('content')
@include('admin.settings.submenu',array('page'=>'service'))

    {{--<div class="page-content" style="padding-left: 115px;">--}}
    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Service Category List</div>
                                <div class="col-sm-8">
                                    @if(!empty(Session::get('message')))
                                        <div class="alert alert-danger alert-icon alert-close alert-dismissible fade in" role="alert">
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
                            <a href="#form_modal" data-toggle="modal" class="btn" id="add_btn" data-id="add">Add New Service Category</a>
                        </div>
                    </div>
                </header>
            </section>
            <section class="box-typical box-typical-padding">
                <div class="row">
                    <div class="col-md-12">
                        <div class="dd">

                            <ol class="dd-list">
                                @if(!empty($categories[0]))
                                    @foreach($categories as $category)
                                        <?php $first_step_category = \App\Model\ProductCategory::where('parent_category_id',$category->id)->get() ?>
                                        <li class="dd-item" data-id="1">
                                            <div class="dd-handle">@if(!empty($category->image))<img class="cimage" src="<?=Image::url(asset(env('CATEGORY_PHOTO_PATH')).'/'.$category->image,20,20,array('crop'))?>" alt="">@endif @if(!empty($category->banner_image))<img src="<?=Image::url(asset(env('CATEGORY_PHOTO_PATH')).'/'.$category->banner_image,20,20,array('crop'))?>" alt="">@endif<span class="tabledit-span" style="margin-left: 20px">{{$category->name}}-({{$category->ar_name}})</span>



                                                <a href="#delete_{{$category->id}}" data-toggle="modal" class="pull-right hover-red" style="color: black"> Delete </a>
                                                <a  href="#form_modal" data-toggle="modal" class="pull-right edit_btn" data-id="{{$category->id}}">Edit |  &nbsp;</a>

                                                <span class="checkbox-toggle pull-right" style="margin-right: 15px">
                                                    {!! Form::open(array('url'=>'/admin/settings/category/show-in-public-menu','files'=>true)) !!}
                                                    <input type="checkbox" onchange="this.form.submit()" id="check-toggle-{{$category->id}}" @if($category->show_in_public_menu==1) checked @endif />
                                                    <label for="check-toggle-{{$category->id}}"></label>
                                                    <input type="hidden" name="category_id" value="{{$category->id}}">
                                                    {!! Form::close() !!}
                                                </span>


                                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                    <div class="modal fade bs-modal-sm"
                                                         id="delete_{{$category->id}}" tabindex="-1"
                                                         role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-sm" style="margin-top: 200px;">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span></button>
                                                                    <h4 style="text-align: left;color:black" class="modal-title">{{trans('Delete')}}</h4>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <p style="color:black">Would You Like to Delete?</p>
                                                                    <button type="button" class="btn btn-inline btn-default"
                                                                            data-dismiss="modal">
                                                                        No
                                                                    </button><a href="{{url('/admin/settings/category/delete/'.$category->id)}}" class="btn btn-inline btn-danger">Yes</a>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            @if(isset($first_step_category[0]))
                                                <ol class="dd-list">
                                                    @foreach($first_step_category as $first_step)
                                                        <?php $second_step_category = \App\Model\ProductCategory::where('parent_category_id',$first_step->id)->get() ?>
                                                        <li class="dd-item" data-id="6">
                                                            <div class="dd-handle">@if(!empty($first_step->image))<img src="<?=Image::url(asset(env('CATEGORY_PHOTO_PATH')).'/'.$first_step->image,20,20,array('crop'))?>" alt="">@endif<span class="tabledit-span" style="margin-left: 20px">{{$first_step->name}}-({{$first_step->ar_name}})</span>
                                                                <a href="#delete_{{$first_step->id}}" data-toggle="modal" class="pull-right hover-red" style="color: black"> Delete </a>
                                                                <a href="#form_modal" data-toggle="modal" class="pull-right edit_btn" data-id="{{$first_step->id}}">Edit |  &nbsp;</a>
                                                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                                    <div class="modal fade bs-modal-sm"
                                                                         id="delete_{{$first_step->id}}" tabindex="-1"
                                                                         role="dialog" aria-hidden="true">
                                                                        <div class="modal-dialog modal-sm" style="margin-top: 200px;">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <button type="button" class="close"
                                                                                            data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span></button>
                                                                                    <h4 style="text-align: left;color:black" class="modal-title">{{trans('Delete')}}</h4>
                                                                                </div>
                                                                                <div class="modal-body text-center">
                                                                                    <p style="color:black">Would You Like to Delete?</p>
                                                                                    <button type="button" class="btn btn-inline btn-default"
                                                                                            data-dismiss="modal">
                                                                                        No
                                                                                    </button><a href="{{url('/admin/settings/category/delete/'.$first_step->id)}}" class="btn btn-inline btn-danger">Yes</a>
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        @if(isset($second_step_category[0]))
                                                            <ol class="dd-list">
                                                                @foreach($second_step_category as $second_step)
                                                                    <li class="dd-item" data-id="6">
                                                                        <div class="dd-handle">@if(!empty($second_step->image))<img src="<?=Image::url(asset(env('CATEGORY_PHOTO_PATH')).'/'.$second_step->image,20,20,array('crop'))?>" alt="">@endif<span class="tabledit-span" style="margin-left: 20px">{{$second_step->name}}-({{$second_step->ar_name}})</span>
                                                                            <a href="#delete_{{$second_step->id}}" data-toggle="modal" class="pull-right hover-red" style="color: black"> Delete </a>
                                                                            <a href="#form_modal" data-toggle="modal" class="pull-right edit_btn" data-id="{{$second_step->id}}">Edit |  &nbsp;</a>
                                                                            {{--<span class="checkbox-toggle pull-right"--}}
                                                                                              {{--style="margin-right: 15px">--}}
                                                                                {{--{!! Form::open(['url'=>'/admin/settings/category/show/public/menu','class'=>'form-inline']) !!}--}}
                                                                                {{--<input type="checkbox" onchange="this.form.submit()" id="check-toggle-{{$second_step->id}}" @if($second_step->show_in_public_menu==1) checked @endif />--}}
                                                                                {{--<label for="check-toggle-{{$second_step->id}}"></label>--}}
                                                                                {{--<input type="hidden" name="category_id" value="{{$second_step->id}}">--}}
                                                                                {{--{!! Form::close() !!}--}}
                                                                            {{--</span>--}}
                                                                            <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                                                <div class="modal fade bs-modal-sm"
                                                                                     id="delete_{{$second_step->id}}" tabindex="-1"
                                                                                     role="dialog" aria-hidden="true">
                                                                                    <div class="modal-dialog modal-sm" style="margin-top: 200px;">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <button type="button" class="close"
                                                                                                        data-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">&times;</span></button>
                                                                                                <h4 style="text-align: left; color:black" class="modal-title">{{trans('Delete')}}</h4>
                                                                                            </div>
                                                                                            <div class="modal-body text-center">
                                                                                                <p style="color:black">Would You Like to Delete?</p>
                                                                                                <button type="button" class="btn btn-inline btn-default"
                                                                                                        data-dismiss="modal">
                                                                                                    No
                                                                                                </button><a href="{{url('/admin/settings/category/delete/'.$second_step->id)}}" class="btn btn-inline btn-danger">Yes</a>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ol>
                                                        @endif
                                                    @endforeach
                                                </ol>
                                            @endif
<div class="clearfix"></div>
                                        </li>
                                    @endforeach
                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <br>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/settings/category/save','id'=>'modal_form','files'=>true)) !!}
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center load_image" style="margin-top: 23px;">
                        <img src="{{asset('build/img/ring-alt.gif')}}" style="width:50px;"
                             alt="">

                        <div>Loading</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <input type="hidden" name="product_category_type_id" value="{{\App\Http\Controllers\Enum\ProductTypeEnum::SERVICE}}">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script>
        $(document).ready(function () {
            $("#add_btn").click(function () {
                var id = $(this).data('id');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?add_id=' + id+'&category_type=service',
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Service Category Add');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
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
                    url: $('#modal_form').attr('action') + '?edit_id=' + id+'&category_type=service',
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Service Category Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
@stop