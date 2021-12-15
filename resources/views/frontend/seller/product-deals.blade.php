@extends('frontend.master',['menu'=>'products'])
@section('title',__('messages.page_title.deals'))
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('/build')}}/css/bootstrap-datepicker.min.css">
    <style>
        .btn-file {
            position: relative;
            overflow: hidden;
            transition: all .2s ease-in-out;
            cursor: pointer;
            margin-right: 20px;
            padding: 4px 10px;
        }
        .btn-file input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            height: 200px;
            width: 1000px;
            opacity: 0;
            cursor: pointer;
        }
        .gallery-grid .gallery-col {
            float: left;
            width: 20%;
            padding: 7px;
        }
        .gallery-item {
            height: 150px;
        }
        .gallery-item {
            position: relative;
            -webkit-border-radius: 5px;
            border-radius: 5px;
            overflow: hidden;
        }
        .gallery-item .remove_from_wishlist { position: absolute; top: 0; right: 15px; font-size: 32px; }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'products'])
                <div class="col-md-9 col-sm-8">
                    @if(!empty(Session::get('message')))
                        <div class="alert alert-success alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                                {{Session::get('message')}}
                        </div>
                    @endif
                    @if(!empty(Session::get('error_message')))
                        <div class="alert alert-warning alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                                {{Session::get('error_message')}}
                        </div>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {{str_limit($product->name,50)}}
                            <a href="#form_modal" data-toggle="modal" id="add_btn" data-product="{{\Illuminate\Support\Facades\Crypt::encrypt($product->id)}}" data-id="add" class="btn btn-primary btn-sm pull-right">@lang('messages.seller.product.deal_page.add_new_deal')</a>
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($deals[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-name"><span class="nobr">@lang('messages.seller.product.deal_page.product')</span></th>
                                            <th class="product-price"><span class="nobr">@lang('messages.seller.product.deal_page.deal_title')</span></th>
                                            <th class="product-stock-stauts"><span class="nobr">@lang('messages.seller.product.deal_page.discount')</span></th>
                                            <th class="product-stock-stauts"><span class="nobr">@lang('messages.seller.product.deal_page.form_to')</span></th>
                                            <th class="product-stock-stauts"><span class="nobr">@lang('messages.seller.product.deal_page.status')</span></th>
                                            <th class="product-stock-stauts"><span class="nobr">@lang('messages.seller.product.deal_page.created')</span></th>
                                            <th class="product-add-to-cart" width="100"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($deals as $deal)
                                            <tr>
                                                <td class="product-name"><a href="{{url('/product/details/'.$deal->getProduct->id)}}">{{$deal->getProduct->name}}</a></td>
                                                <td class="product-name"><a href="{{url('/products/category/'.$deal->getProduct->getCategory->id.'?filter=deals')}}">{{$deal->title}}</a></td>
                                                <td class="product-name text-center">
                                                    {{$deal->discount}}
                                                    @if(\App\Http\Controllers\Enum\DiscountTypeEnum::FIXED == $deal->discount_type) <span class="label label-danger">@lang('messages.seller.product.deal_page.fixed')</span>
                                                    @else <span class="label label-success">@lang('messages.seller.product.deal_page.percentage')</span>
                                                    @endif
                                                </td>
                                                <td class="product-price">
                                                    {{date('d F, Y',strtotime($deal->from_date))}} - {{date('d F, Y',strtotime($deal->to_date))}}
                                                </td>
                                                <td class="product-price">
                                                    @if($deal->status == \App\Http\Controllers\Enum\DealStatusEnum::APPROVED) @lang('messages.status.accepted')
                                                    @elseif($deal->status == \App\Http\Controllers\Enum\DealStatusEnum::PENDING) @lang('messages.status.pending')
                                                    @endif
                                                </td>
                                                <td class="product-price">{{date('d F, Y h:m a',strtotime($deal->created_at))}}</td>
                                                <td class="product-add-to-cart">
                                                    <a data-toggle="modal" href="#form_modal" data-seller="{{\Illuminate\Support\Facades\Crypt::encrypt(Auth::user()->getSeller->id)}}" data-id="{{Crypt::encrypt($deal->id)}}" class="edit_btn btn btn-primary">@lang('messages.seller.product.deal_page.edit')</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.buyer.order_no_order_available')</h4></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" data-backdrop="true" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                {!! Form::open(array('url'=>'/seller/product/deal/save','id'=>'modal_form')) !!}
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center load_image" style="margin-top: 23px;">
                        <img src="{{asset('build/img/ring-alt.gif')}}" style="width:50px;"
                             alt="">

                        <div>Loading</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.seller.product.close')</button>
                    <button type="submit" class="btn btn-primary" id="submit_btn">@lang('messages.seller.product.save')</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src="{{asset('/build')}}/js/bootstrap-datepicker.min.js"></script>
    <script>
        function getDatePickerRange(){
            $('.input-daterange').datepicker({
                format: "dd-mm-yyyy",
                daysOfWeekHighlighted: "5,6",
                autoclose: true,
                todayHighlight: true
            });
        }
        $(document).ready(function () {
            $("#add_btn").click(function () {
                var id = $(this).data('id');
                var product_id = $(this).data('product');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?add_id=' + id+'&product_id='+product_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('@lang('messages.seller.product.deal_page.deal_add')');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();

                        getDatePickerRange();

                        $('.numeric').attr('min', 0);

                        $('.numeric').keypress(function (e) {
                            var regex = /^[0-9\b]+$/;
                            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                            if (regex.test(str)) {
                                return true;
                            }
                            e.preventDefault();
                            return false;
                        });
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
            $(".edit_btn").click(function () {
                var id = $(this).data('id');
                var seller_id = $(this).data('seller');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?edit_id=' + id+'&seller_id='+seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('@lang('messages.seller.product.deal_page.deal_edit')');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();

                        getDatePickerRange();

                        $('.numeric').attr('min', 0);

                        $('.numeric').keypress(function (e) {
                            var regex = /^[0-9\b]+$/;
                            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                            if (regex.test(str)) {
                                return true;
                            }
                            e.preventDefault();
                            return false;
                        });

                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
@stop
