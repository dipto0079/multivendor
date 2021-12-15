@extends('frontend.master',['menu'=>'favourite_store'])
@section('title','Wish list')
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.account.sidebar-nav',['menu'=>'favourite_store'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           Favourite Stores
                        </div>
                        <div class="panel-body">
                            {{--<h3>Your favourite online merchants</h3>--}}
                            {{--<p>You have favourited 4 merchants</p>--}}
                            <div class="row store_list">
                                <div class="col-xs-6 col-sm-4 col-md-3">
                                    <div class="thumbnail">
                                        <div class="like_icon"><i class="fa fa-heart"></i></div>
                                        <div class="image">
                                            <img src="{{asset('/image/company/76e-200x100.png')}}" alt="...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-3">
                                    <div class="thumbnail">
                                        <div class="like_icon"><i class="fa fa-heart"></i></div>
                                        <div class="image">
                                            <img src="{{asset('/image/company/133-200x100.png')}}" alt="...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-3">
                                    <div class="thumbnail">
                                        <div class="like_icon"><i class="fa fa-heart"></i></div>
                                        <div class="image">
                                            <img src="{{asset('/image/company/76e-200x100.png')}}" alt="...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-3">
                                    <div class="thumbnail">
                                        <div class="like_icon"><i class="fa fa-heart"></i></div>
                                        <div class="image">
                                            <img src="{{asset('/image/noimage.jpg')}}" alt="...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
@stop