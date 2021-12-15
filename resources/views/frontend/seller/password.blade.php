@extends('frontend.master',['menu'=>'password'])
@section('title','Wish list')
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.account.sidebar-nav',['menu'=>'password'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Password
                        </div>
                        <div class="panel-body">

                            <form action="{{url('/edit-profile')}}" method="post" class="form-horizontal">
                                {{csrf_field()}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3 control-label required">New Password</label>
                                        <div class="col-sm-6">
                                            <input type="password" name="password" class="form-control" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-sm-3 control-label required">New Password Confirm</label>
                                            <div class="col-sm-6">
                                            <input type="password" name="confirm_password" class="form-control" placeholder="">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary font-additional pull-right">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
@stop