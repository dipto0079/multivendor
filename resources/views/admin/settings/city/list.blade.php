@extends('admin.master')
@section('title','City List')
@section('stylesheet')
@stop

@section('content')
@include('admin.settings.submenu',array('page'=>'city'))

    <div class="page-content" >
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">City List</div>
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
                        <!-- <div class="tbl-cell tbl-cell-action-bordered">
                            <form action="{{url('/admin/settings/city/list')}}" methon="POST">
                            {{csrf_field()}}
                            <?php $countries = App\Model\Country::orderBy('name','asc')->get(); ?>
                            <select name="country" id="" style="width: 150px" onchange="this.form.submit()" class="form-control">
                                <option value="">Select Country</option>
                                @if(isset($countries[0]))
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}"  @if($country == $country->sr) selected @endif>{{$country->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            </form>
                        </div> -->
                        <div class="tbl-cell tbl-cell-action-bordered">
                            <a href="#form_modal" data-toggle="modal" class="btn" id="add_btn" data-id="add">Add New City</a>
                        </div>
                    </div>
                </header>
            </section><!--.box-typical-->

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Country</th>
                    <th>English Name</th>
                    <th>Arabic Name</th>
                    <th style="width: 130px;">Shipping Status</th>
                    <th class="text-center"  width="120">Action</th>
                </thead>
                <tbody>

                @if(!empty($cities[0]))
                    @foreach($cities as $city)
                        <tr>
                            <td class="tabledit-view-mode"><span class="tabledit-span">@if(isset($city->getCountry)){{$city->getCountry->name}}@endif</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$city->name}}</span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$city->ar_name}}</span></td>
                            <td class="tabledit-view-mode">
                                <span class="tabledit-span">
                                <span class="checkbox-toggle pull-right" style="margin-right: 15px">
                                    {!! Form::open(array('url'=>'/admin/settings/city/status')) !!}
                                    <input type="checkbox" onchange="this.form.submit()" id="check-toggle-editor-{{$city->id}}"
                                           @if($city->shipping_status == 1) checked @endif
                                    />
                                        <label for="check-toggle-editor-{{$city->id}}"></label>
                                        <input type="hidden" name="city_id" value="{{$city->id}}">
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
                                        <a class="dropdown-item edit_btn" data-toggle="modal" href="#form_modal" data-id="{{$city->id}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
                                        <a href="#delete_{{$city->id}}" data-toggle="modal" class="dropdown-item  hover-red"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                    </div>
                                </div>
                                <!-- Modal -->
                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                    <div class="modal fade bs-modal-sm"
                                         id="delete_{{$city->id}}" tabindex="-1"
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
                                                    </button><a href="{{url('/admin/settings/city/delete/'.$city->id)}}" class="btn btn-inline btn-danger">Yes</a>
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
                        <td colspan="6"><div class="alert alert-danger alert-icon alert-close alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <i class="font-icon font-icon-warning"></i>
                                City Not Available.
                            </div></td>
                    </tr>
                @endif
                </tbody>
            </table>
            @include('admin.pagination',['paginator'=>$cities])
            <br>
        </div><!--.container-fluid-->
    </div>
    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/settings/city/save','id'=>'modal_form','files'=>true)) !!}
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
                        $('.modal-title').html('City Add');
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
                    url: $('#modal_form').attr('action') + '?edit_id=' + id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('City Edit');
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