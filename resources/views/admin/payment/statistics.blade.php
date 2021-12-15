@extends('admin.master')
@section('title','Payment List')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('/build')}}/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/pages/gallery.min.css">
    <style type="text/css">
        .show { display: block; }
        .btn-file { margin-right: 20px; padding: 4px 10px; }
        .gallery-item { height: 150px; }
        .tab-content {
            height: 500px;
            overflow-y: scroll;
        }
    </style>
@stop

@section('content')

 @include('admin.payment.submenu',['page'=>'statistics'])

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Statistics</div>
                                <div class="col-sm-8 text-right">
                                    {{--{!! Form::open(['url'=>'/admin/payment','method'=>'get','class'=>'form-inline']) !!}--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="" style="display: inline-block;">Date Filter</label>--}}
                                        {{--<div class="input-group date" style="width: 45%">--}}
                                            {{--<input type="text" name="month" @if(!empty(Request::get('month'))) value="{{Request::get('month')}}" @else value="{{date('m-Y')}}" @endif class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>--}}
                                        {{--</div>--}}
                                        {{--<button type="submit" class="btn btn-primary">Search</button>--}}
                                    {{--</div>--}}
                                    {{--{!! Form::close() !!}--}}
                                </div>
                            </h3>
                        </div>
                    </div>
                </header>
            </section><!--.box-typical-->

            <h3>Coming Soon</h3>


            <br>
        </div><!--.container-fluid-->
    </div>

@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src="{{asset('/build')}}/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.input-group.date').datepicker({
            format: "mm-yyyy",
            minViewMode: 1,
            orientation: "bottom left",
            autoclose: true
        });
    </script>
@stop
