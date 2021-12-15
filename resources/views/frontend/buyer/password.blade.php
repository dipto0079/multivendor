@extends('frontend.master',['menu'=>'password'])
@section('title',__('messages.page_title.password'))
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'password'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.password')
                        </div>
                        <div class="panel-body">
                            {!! Form::open(array('url'=>'/buyer/password','id'=>'password_change_form', 'class'=>"form-horizontal")) !!}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.password_current_password')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input required type="password" name="current_password" class="form-control" placeholder="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.password_new_password')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input required type="password" name="password" class="form-control" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.password_new_confirm_password')</label>
                                            <div class="col-md-6 col-sm-8 ">
                                            <input required type="password" name="confirm_password" class="form-control" placeholder="">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary font-additional pull-right">@lang('messages.buyer.password_update')</button>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
@stop
