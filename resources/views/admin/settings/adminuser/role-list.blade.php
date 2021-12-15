@extends('admin.master')
@section('title','Static Page List')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('/build/css/lib/summernote/summernote.css')}}"/>
    <link rel="stylesheet" href="{{asset('/build/css/separate/pages/editor.min.css')}}">
@stop

@section('content')
    @include('admin.settings.submenu',array('page'=>"admin_role"))

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Admin Role List</div>
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
                            <a href="#form_modal" data-toggle="modal" class="btn" id="add_btn" data-id="add">Add New Role</a>
                        </div>
                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-center"  width="120">Action</th>
                </thead>
                <tbody>

                @if(!empty($admin_roles[0]))
                    @foreach($admin_roles as $admin_role)
                        <tr>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$admin_role->name}}</span></td>
                            <td style="white-space: nowrap; width: 1%;">

                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item permission_edit_btn" data-toggle="modal" href="#permission_form_modal" data-id="{{$admin_role->id}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                {!! Form::open(array('url'=>'/admin/settings/admin-role/save','id'=>'modal_form','files'=>true)) !!}
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

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="permission_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Admin Role Permission</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/settings/admin-role/permission/save','files'=>true)) !!}
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label for="form_control_1" style="text-align: left;">Name</label>
                                        <input type="text" name="name" id="name" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <label for="form_control_1">All Permission</label>
                                <select style="width: 100%;height: 235px; overflow-x: hidden;" multiple="multiple" class="multi-select" id="my_multi_select1">
                                    @foreach($adminPermissions as $adminPermission)
                                        <option style="padding-left: 8px;" value="{{$adminPermission->id}}">{{$adminPermission->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <div style="margin-bottom: 10px;margin-top: 70px;"><input id="left" type="button" style="width: 100%;font-size: 16px;" value="<"/></div>
                                <div style="margin-bottom: 10px;"><input type="button" id="right" style="width: 100%;font-size: 16px;" value=">"/></div>
                                <div style="margin-bottom: 10px;"><input type="button" style="width: 100%;font-size: 16px;" id="leftall" value="<<"/></div>
                                <div><input type="button" style="width: 100%;font-size: 16px;" id="rightall" value=">>"/></div>
                            </div>

                            <div class="col-sm-5">
                                <label for="form_control_1">Assigned Permission</label>
                                <select style="width: 100%;height: 235px; overflow-x: hidden;" multiple="multiple" class="multi-select" id="my_multi_select2" name="assigned_permissions[]">
                                    @foreach($adminPermissions as $adminPermission)
                                        <option value="{{$adminPermission->id}}">{{$adminPermission->name}}</option>
                                    @endforeach
                                </select>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
                <input type="hidden" name="id" id="post_id_val">
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <form action="{{url('/admin/settings/admin-role/permission')}}" id="permission_modal_form">{{csrf_field()}}</form>
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
    </script>
    <script>
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
                        $('.modal-title').html('Role Add');
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
                        $('.modal-title').html('Role Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        getEditor();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });

//            $(".permission_edit_btn").click(function () {
//                var id = $(this).data('id');
//                $("#model_body").empty();
//                $('.load_image').show();
//                $.ajax({
//                    type: "POST",
//                    url: $('#permission_modal_form').attr('action') + '?role_id=' + id,
//                    data: $('#permission_modal_form').serialize(),
//                    dataType: "json",
//                    success: function (data) {
//                        $('.modal-title').html('Role Edit');
//                        $("#modal_form_generate").html(data.data_generate);
//                        $('.load_image').hide();
//                        getEditor();
//                    }
//                }).fail(function (data) {
//                    var errors = data.responseJSON;
//                    console.log(errors);
//                });
//            });
        });
    </script>
    <script>
        $(function () {
            function moveItems(origin, dest) {
                $(origin).find(':selected').appendTo(dest);
            }

            function moveAllItems(origin, dest) {
                $(origin).children().appendTo(dest);
            }

            $('#left').click(function () {
                moveItems('#my_multi_select2', '#my_multi_select1');
            });

            $('#right').on('click', function () {
                moveItems('#my_multi_select1', '#my_multi_select2');
            });

            $('#leftall').on('click', function () {
                moveAllItems('#my_multi_select2', '#my_multi_select1');
            });

            $('#rightall').on('click', function () {
                moveAllItems('#my_multi_select1', '#my_multi_select2');
            });
        });

    </script>
    <script>
        $(document).ready(function () {
            $('#test_click tr td button.blue').click(function () {
                var edit_id = $(this).val();
                $('#edit_post_id').val(edit_id);
            });
            // Get delete id into the delete route
            $('#test_click tr td button.red').click(function () {
                var delete_id = $(this).val();
                $("#delete_id").attr("href", "" + "/admin/division/delete/" + delete_id + "");
            });

            $('#saveBtn').click(function () {
                selectBox = document.getElementById("my_multi_select2");
                for (var i = 0; i < selectBox.options.length; i++) {
                    selectBox.options[i].selected = true;
                }
            });
        });
    </script>
    <script>
        $("document").ready(function () {
            $('.permission_edit_btn').click(function () {

                var id = $(this).data('id');

                $('#loading_img').show();
                $.ajax({
                    type: "POST",
                    url: $('#permission_modal_form').attr('action') + '?role_id=' + id,
                    data: $('#permission_modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        var json = $.parseJSON(data['rawdata']);

                        $('#my_multi_select1').empty();
                        $('#my_multi_select2').empty();

                        $('#post_id_val').val(json[0].id);
                        $('#name').val(json[0].name);

                        $('#loading_img').remove();
                        $.each(json[1], function (key, value) {
                            $('#my_multi_select1').append("<option style='padding-left: 8px;' value='" + value['id'] + "'>" + value['name'] + "</option>");
                        });

                        $.each(json[2], function (key, value) {
                            $('#my_multi_select2').append("<option style='padding-left: 8px;' value='" + value['permission_id'] + "'>" + value['permission_name'] + "</option>");
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