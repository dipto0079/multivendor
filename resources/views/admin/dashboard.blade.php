@extends('admin.master')
@section('title','Dashboard')
@section('stylesheet')
    {{--<link href="{{asset('/build')}}/css/components-md.css" id="style_components" rel="stylesheet" type="text/css"/>--}}
    <style>
        .dashboard-stat {
            display: block;
            margin-bottom: 25px;
            overflow: hidden;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            -ms-border-radius: 2px;
            -o-border-radius: 2px;
            border-radius: 2px;
        }

        .dashboard-stat:before, .dashboard-stat:after {
            content: " ";
            display: table;
        }

        .dashboard-stat:after {
            clear: both;
        }

        .portlet .dashboard-stat:last-child {
            margin-bottom: 0;
        }

        .dashboard-stat .visual {
            width: 80px;
            height: 80px;
            display: block;
            float: left;
            padding-top: 10px;
            padding-left: 15px;
            margin-bottom: 15px;
            font-size: 35px;
            line-height: 35px;
        }

        .dashboard-stat .visual > i {
            margin-left: -35px;
            font-size: 110px;
            line-height: 110px;
        }

        .dashboard-stat .details {
            position: absolute;
            right: 15px;
            padding-right: 15px;
        }

        .dashboard-stat .details .number {
            padding-top: 25px;
            text-align: right;
            font-size: 34px;
            line-height: 36px;
            letter-spacing: -1px;
            margin-bottom: 0px;
            font-weight: 300;
        }

        .dashboard-stat .details .desc {
            text-align: right;
            font-size: 16px;
            letter-spacing: 0px;
            font-weight: 300;
        }

        .dashboard-stat .more {
            clear: both;
            display: block;
            padding: 6px 10px 6px 10px;
            position: relative;
            text-transform: uppercase;
            font-weight: 300;
            font-size: 11px;
            opacity: 0.7;
            filter: alpha(opacity=70);
        }

        .dashboard-stat .more:hover {
            text-decoration: none;
            opacity: 0.9;
            filter: alpha(opacity=90);
        }

        .dashboard-stat .more > i {
            display: inline-block;
            margin-top: 1px;
            float: right;
        }

        .dashboard-stat-light {
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .dashboard-stat-light .details {
            margin-bottom: 5px;
        }

        .dashboard-stat-light .details .number {
            font-weight: 300;
            margin-bottom: 0px;
        }

        /* Statistic Block */
        .dashboard-stat.blue-madison {
            background-color: #578ebe;
        }

        .dashboard-stat.blue-madison.dashboard-stat-light:hover {
            background-color: #4884b8;
        }

        .dashboard-stat.blue-madison .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.blue-madison .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.blue-madison .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.blue-madison .more {
            color: #FFFFFF;
            background-color: #4884b8;
        }

        /* Statistic Block */
        .dashboard-stat.blue-chambray {
            background-color: #2C3E50;
        }

        .dashboard-stat.blue-chambray.dashboard-stat-light:hover {
            background-color: #253443;
        }

        .dashboard-stat.blue-chambray .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.blue-chambray .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.blue-chambray .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.blue-chambray .more {
            color: #FFFFFF;
            background-color: #253443;
        }

        /* Statistic Block */
        .dashboard-stat.blue-ebonyclay {
            background-color: #22313F;
        }

        .dashboard-stat.blue-ebonyclay.dashboard-stat-light:hover {
            background-color: #1b2732;
        }

        .dashboard-stat.blue-ebonyclay .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.blue-ebonyclay .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.blue-ebonyclay .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.blue-ebonyclay .more {
            color: #FFFFFF;
            background-color: #1b2732;
        }

        /* Statistic Block */
        .dashboard-stat.blue-hoki {
            background-color: #67809F;
        }

        .dashboard-stat.blue-hoki.dashboard-stat-light:hover {
            background-color: #5e7694;
        }

        .dashboard-stat.blue-hoki .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.blue-hoki .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.blue-hoki .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.blue-hoki .more {
            color: #FFFFFF;
            background-color: #5e7694;
        }

        /* Statistic Block */
        .dashboard-stat.blue-steel {
            background-color: #4B77BE;
        }

        .dashboard-stat.blue-steel.dashboard-stat-light:hover {
            background-color: #416db4;
        }

        .dashboard-stat.blue-steel .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.blue-steel .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.blue-steel .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.blue-steel .more {
            color: #FFFFFF;
            background-color: #416db4;
        }

        /* Statistic Block */
        .dashboard-stat.green-meadow {
            background-color: #1BBC9B;
        }

        .dashboard-stat.green-meadow.dashboard-stat-light:hover {
            background-color: #18aa8c;
        }

        .dashboard-stat.green-meadow .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.green-meadow .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.green-meadow .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.green-meadow .more {
            color: #FFFFFF;
            background-color: #18aa8c;
        }

        /* Statistic Block */
        .dashboard-stat.green-haze {
            background-color: #44b6ae;
        }

        .dashboard-stat.green-haze.dashboard-stat-light:hover {
            background-color: #3ea7a0;
        }

        .dashboard-stat.green-haze .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.green-haze .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.green-haze .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.green-haze .more {
            color: #FFFFFF;
            background-color: #3ea7a0;
        }

        /* Statistic Block */
        .dashboard-stat.grey-mint {
            background-color: #9eacb4;
        }

        .dashboard-stat.grey-mint.dashboard-stat-light:hover {
            background-color: #92a2ab;
        }

        .dashboard-stat.grey-mint .visual > i {
            color: #FAFCFB;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.grey-mint .details .number {
            color: #FAFCFB;
        }

        .dashboard-stat.grey-mint .details .desc {
            color: #FAFCFB;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.grey-mint .more {
            color: #FAFCFB;
            background-color: #92a2ab;
        }

        /* Statistic Block */
        .dashboard-stat.red-pink {
            background-color: #E08283;
        }

        .dashboard-stat.red-pink.dashboard-stat-light:hover {
            background-color: #dc7273;
        }

        .dashboard-stat.red-pink .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.red-pink .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.red-pink .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.red-pink .more {
            color: #FFFFFF;
            background-color: #dc7273;
        }

        /* Statistic Block */
        .dashboard-stat.purple-plum {
            background-color: #8775a7;
        }

        .dashboard-stat.purple-plum.dashboard-stat-light:hover {
            background-color: #7c699f;
        }

        .dashboard-stat.purple-plum .visual > i {
            color: #FFFFFF;
            opacity: 0.1;
            filter: alpha(opacity=10);
        }

        .dashboard-stat.purple-plum .details .number {
            color: #FFFFFF;
        }

        .dashboard-stat.purple-plum .details .desc {
            color: #FFFFFF;
            opacity: 1;
            filter: alpha(opacity=100);
        }

        .dashboard-stat.purple-plum .more {
            color: #FFFFFF;
            background-color: #7c699f;
        }
    </style>
@stop

@section('content')
    <div class="page-content" style="padding-left: 115px;">

        <h3 class="page-title">
            Impression
        </h3>
        <!-- END PAGE HEADER-->
        <!-- BEGIN DASHBOARD STATS -->
        <div class="row">
            <div id="loading_img" style="position: absolute;z-index: 100;left: 46%;">
                <div class="text-center"><img src="{{asset('image/pageloader.gif')}}"></div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat blue-madison">
                    <div class="visual">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="pending_order">
                            {{$pending_order}}
                        </div>
                        <div class="desc">
                            Pending Order
                        </div>
                    </div>
                    <a class="more" href="{{url('/admin/order/pending')}}">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat green-meadow">
                    <div class="visual">
                        <i class="fa fa-certificate"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="total_service_seller">
                            {{$total_service_seller}}
                        </div>
                        <div class="desc">
                            Total Service Seller
                        </div>
                    </div>
                    <a class="more" href="{{url('/admin/service/seller')}}">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat green-haze">
                    <div class="visual">
                        <i class="fa fa-certificate"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="total_product_seller">
                            {{$total_product_seller}}
                        </div>
                        <div class="desc">
                            Total Product Seller
                        </div>
                    </div>
                    <a class="more" href="{{url('/admin/product/seller')}}">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat grey-mint">
                    <div class="visual">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="total_buyer">
                            {{$total_buyer}}
                        </div>
                        <div class="desc">
                            Total Buyer
                        </div>
                    </div>
                    <a class="more" href="{{url('/admin/buyer')}}">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat red-pink">
                    <div class="visual">
                        <i class="fa fa-gift"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="total_deals">
                            {{$total_deals}}
                        </div>
                        <div class="desc">
                            Total Deals
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat purple-plum">
                    <div class="visual">
                        <i class="fa fa-tags"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="sales_today">
                            {{env('CURRENCY_SYMBOL').' '.number_format($sales_today,2)}}
                        </div>
                        <div class="desc">
                            Sales Today
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat green-meadow">
                    <div class="visual">
                        <i class="fa fa-tags"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="total_product">
                            {{$product_count}}
                        </div>
                        <div class="desc">
                            Total Product
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END DASHBOARD STATS -->
        <div class="clearfix">
        </div>

    </div>
@stop

@section('script')

@stop