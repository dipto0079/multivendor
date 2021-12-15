@extends('frontend.master',['menu'=>'favourite_store'])
@section('title',__('messages.page_title.favourite_store'))
@section('stylesheet')
    <style>
        .like_icon:hover .fa-heart:before {
            content: "\f08a";
            z-index: 1000;
        }

        .thumbnail:hover {
            border: 1px solid #ff8300;
        }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'favourite_store'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.favourite_store')
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                <div class="row store_list">
                                    @if(!empty($stores[0]))
                                        @foreach($stores as $store)
                                            <div class="col-xs-6 col-sm-4 col-md-3">
                                                <div class="thumbnail">
                                                    <div class="like_icon"><a
                                                                href="{{url('/buyer/remove-favorite-store/'.$store->id)}}" title="Remove From Favorite"><i
                                                                    class="fa fa-heart"></i></a></div>
                                                    <div class="image">
                                                        <a href="{{url('/store').'/'.$store->getSeller->store_name}}">
                                                          <img class="img_background" src="{{asset('/image/default.jpg')}}"
                                                                    @if(!empty($store->getSeller->getUser->photo)) data-src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $store->getSeller->getUser->photo), 200, 178, ['crop'])?>"
                                                                    @else src="<?=Image::url(asset('image/no-media.jpg'),200,175,['crop'])?>" alt=""
                                                                    alt=""
                                                                    @endif alt="{{$store->getSeller->store_name}}"></a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-md-12"><h4>@lang('messages.buyer.favorite_store_no_added')</h4>
                                        </div>
                                    @endif
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
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
@stop
