@extends('frontend.master',['menu'=>'my_dashboard'])
@section('title',__('messages.page_title.my_dashboard'))
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'my_dashboard'])
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
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
@stop