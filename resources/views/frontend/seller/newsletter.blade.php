@extends('frontend.master',['menu'=>'newsletter'])
@section('title','Wish list')
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.account.sidebar-nav',['menu'=>'newsletter'])
                <div class="col-md-9 col-sm-8">
                    <div class="shop">
                        Coming soon...
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
@stop