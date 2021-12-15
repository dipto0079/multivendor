@extends('frontend.master')

<?php
$title = "";
if (\App\UtilityFunction::getLocal() == 'en')
    $title = $item->title;
else
    $title = $item->ar_title
?>

@section('title',env('APP_NAME_'.\App\UtilityFunction::getLocal()).' '.$title)
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <h3> {{$title}}</h3>
            <hr>
            <p> @if (\App\UtilityFunction::getLocal() == 'en'){!! $item->description !!} @else {!! $item->ar_description !!} @endif</p>
        </div>
    </div>
@stop

@section('script')
@stop