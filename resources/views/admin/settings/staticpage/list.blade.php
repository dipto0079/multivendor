@extends('admin.master')
@section('title','Static Page List')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('/build/css/lib/summernote/summernote.css')}}"/>
    <link rel="stylesheet" href="{{asset('/build/css/separate/pages/editor.min.css')}}">
@stop

@section('content')
    @include('admin.settings.submenu',array('page'=>"static_page"))

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Static Page List</div>
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
                        {{--<div class="tbl-cell tbl-cell-action-bordered">--}}
                            {{--<a href="#form_modal" data-toggle="modal" class="btn" id="add_btn" data-id="add">Add New Static Page</a>--}}
                        {{--</div>--}}
                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>English Title</th>
                    <th>Arabic Title</th>
                    <th class="text-center"  width="120">Action</th>
                </thead>
                <tbody>

                @if(!empty($static_pages[0]))
                    @foreach($static_pages as $static_page)
                        <tr>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$static_page->title}}</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$static_page->ar_title}}</span></td>
                            <td style="white-space: nowrap; width: 1%;">

                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit_btn" data-toggle="modal" href="#form_modal" data-id="{{$static_page->id}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
                                        {{--<a href="#delete_{{$static_page->id}}" data-toggle="modal" class="dropdown-item  hover-red"><span class="glyphicon glyphicon-trash"></span> Delete</a>--}}
                                    </div>
                                </div>
                                <!-- Modal -->
                                {{--<div class="tabledit-toolbar btn-toolbar" style="text-align: left;">--}}
                                    {{--<div class="modal fade bs-modal-sm"--}}
                                         {{--id="delete_{{$static_page->id}}" tabindex="-1"--}}
                                         {{--role="dialog" aria-hidden="true">--}}
                                        {{--<div class="modal-dialog modal-sm" style="margin-top: 200px;">--}}
                                            {{--<div class="modal-content">--}}
                                                {{--<div class="modal-header">--}}
                                                    {{--<button type="button" class="close"--}}
                                                            {{--data-dismiss="modal" aria-label="Close">--}}
                                                        {{--<span aria-hidden="true">&times;</span></button>--}}
                                                    {{--<h4 style="text-align: left;" class="modal-title"--}}
                                                        {{--id="myModalLabel">{{trans('Delete')}}</h4>--}}
                                                {{--</div>--}}
                                                {{--<div class="modal-body text-center">--}}
                                                    {{--<p>Would You Like to Delete?</p>--}}
                                                    {{--<button type="button" class="btn btn-inline btn-default"--}}
                                                            {{--data-dismiss="modal">--}}
                                                        {{--No--}}
                                                    {{--</button><a href="{{url('/admin/static-page/delete/'.$static_page->id)}}" class="btn btn-inline btn-danger">Yes</a>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                            {{--<!-- /.modal-content -->--}}
                                        {{--</div>--}}
                                        {{--<!-- /.modal-dialog -->--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6"><div class="alert alert-danger alert-icon alert-close alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <i class="font-icon font-icon-warning"></i>
                                Static Page Not Available.
                            </div></td>
                    </tr>
                @endif
                </tbody>
            </table>

            <br>
        </div><!--.container-fluid-->
    </div>
    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/settings/static-page/save','id'=>'modal_form','files'=>true)) !!}
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
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src="{{asset('/build/js/lib/summernote/summernote.min.js')}}"></script>
    <script>
        function getEditor(){
            $(document).ready(function() {
                $('.editor_s').summernote({
                    height: 200,                 // set editor height
                    minHeight: null,             // set minimum height of editor
                    maxHeight: null,             // set maximum height of editor
                    focus: true                  // set focus to editable area after initializing summernote
                });
            });
        }
    </script>    <script>
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
                        $('.modal-title').html('Static Page Add');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        getEditor();
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
                        $('.modal-title').html('Static Page Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        getEditor();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
@stop