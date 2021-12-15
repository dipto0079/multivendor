@extends('frontend.master')
@section('title',env('APP_NAME_'.\App\UtilityFunction::getLocal()).' '.__('messages.error.not_found'))
@section('stylesheet')
    <style>
        .site-main { margin: 50px 0; }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <h1 style="font-weight: 700; font-size: 48px; margin-bottom: 40px;">@lang('messages.error.not_found')</h1>
            <a href="{{url('/')}}" class="btn btn-primary font-additional">@lang('messages.error.go_home')</a>
        </div>
    </div>

@stop

@section('script')
@stop