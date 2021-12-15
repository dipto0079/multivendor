@extends('frontend.master',['menu'=>'payments'])
@section('title',__('messages.page_title.shipping_tex'))
@section('stylesheet')
<link rel="stylesheet" href="{{asset('css/bootstrap-chosen.css')}}">
<style>
    .category-list {
		    list-style: none;
		    margin-top: 10px;
		    overflow: hidden;
		}
		.category-list li {
		    float: left;
			list-style: none;
			width: 19%;
			text-align: center;
			margin-right: 1%;
		}
		.category-list li:last-child { margin-right: 0; }
		.nb-radio {
		    float: left;
		    margin: 0 auto;
		    position: relative;
		    cursor: pointer;
		    padding: 20px 0;
		    overflow: hidden;
		    height: 150px;
		    width: 100%;
			border: solid 1px #ff8300;
			border-radius: 6px;
			-webkit-border-radius: 6px;
			-moz-border-radius: 6px;
		}
		.nb-radio i.fa { font-size: 65px; color: #FF8300; }
		.nb-radio input {
		    opacity: 0;
		    width: 0;
		    height: 0;
		    outline: 0;
		    border: 0;
		    position: absolute;
		}
		.nb-checkbox input, .nb-radio input {
		    position: absolute;
		    left: 0;
		    top: 0;
		    width: 100%;
		    height: 100%;
		    opacity: 0;
		    z-index: 2;
		    cursor: pointer;
		    margin: 0;
		    color: #666;
		}
		.nb-radio input:checked+img {
		    display: none;
		}
		.category-list li img {
			display: block;
			margin: 0 auto;
			margin-bottom: 12px;
			width: 60px;
		}
		.nb-radio input:checked+img+img {
		    display: block;
		}
		.nb-radio__bg {
		    display: block;
		    width: 18px;
		    height: 18px;
		    /*border: 1px solid #757575;*/
		    border-radius: 50%;
		    display: inline-block;
		    margin-right: 12px;
		    vertical-align: middle;
		    display: none;
		}
		.nb-checkbox__bg, .nb-radio__bg {
		    border: 1px solid #999;
		    width: 14px;
		    height: 14px;
		    border-radius: 2px;
		    float: left;
		    margin-right: 6px;
		    margin-top: 2px;
		    opacity: 1;
		}
		.nb-radio__icon {
		    position: absolute;
		    right: 0;
		    top: 0;
		    opacity: 0;
		    width: 38px;
		    height: 38px;
		    transition: all .3s ease;
		}
		.nb-radio input:checked+i+.nb-radio__bg+.nb-radio__icon {
		    opacity: 1;
		}
		.nb-radio input:checked+i+.nb-radio__bg+.nb-radio__icon+.border-category {
		    display: block;
		}
		.nb-radio input:checked+img+img {
		    display: block;
		}
		.category-list li:hover img {
		    display: none;
		}
		.category-list li:hover img.activeIcon {
		    display: block;
		}
		.nb-radio .activeIcon {
		    display: none;
		}
		.border-category {
		    height: 100%;
		    width: 100%;
		    border: solid 1px #ff8300;
		    display: none;
		    position: absolute;
		    top: 0;
		    left: 0;
			border-radius: 4px;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px; 
		}
		.nb-radio input:checked+img+img+.nb-radio__bg+.nb-radio__icon+.border-category+.nb-radio__text {
		    color: #ff8300;
		}
		.nb-radio__text {
		    font-weight: 600;
		    font-size: 12px;
		    color: #666;
		    padding: 0 25px;
			text-align: center;
		}
		.range_input { width: 50%; float: left; margin-right: -1px; position: relative; }
		.range_input input.form-control { width: 100%; padding-right: 30px; }
		.range_input:after { content: 'KG'; position: absolute; right: 10px; top:6px; }
		#order_range_div .range_input:after { content: ''; }
		#order_range_div .range_input input.form-control { padding-right: 10px; }
</style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'shipping_tex'])
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
                            @lang('messages.seller.menu.shipping_tex')
                        </div>
						<?php 
							$shipping_type = \App\UtilityFunction::getShippingType();

						?>
                        <div class="panel-body">
                            <div class="shop">
								{!! Form::open(['url'=>'/seller/shipping-and-tax-save','id' =>'shipping_form']) !!}
								<div class="form-group">
									<label for="" class="required">@lang('messages.shipping_tax.country')</label>
									<select name="country_id" id="country_id" class="form-control" required>
										<option value="">@lang('messages.shipping_tax.select_country')</option>
{{--										<option value="-1">@lang('messages.shipping_tax.all_country')</option>--}}
										@if(!empty($countries[0]))
											@foreach($countries as $country)
												<option value="{{$country->id}}">@if (\App\UtilityFunction::getLocal() == 'en'){{$country->name}}@else{{$country->ar_name}}@endif</option>
											@endforeach
										@endif
									</select>
								</div>
								<div class="form-group">
									<label for="" class="">@lang('messages.shipping_tax.city')</label>
									<select data-placeholder="@lang('messages.shipping_tax.select_cities')" name="city_id[]" id="city_id" class="form-control chosen-select" multiple>										
									</select>
								</div>
								<div class="form-group">
									<div id="product_category">
										<label class="required">@lang('messages.shipping_tax.select_shipping_type')</label>
										<div class="clearfix"></div>
										<ul class="category-list" id="shipping_type">
											@foreach($shipping_type as $key => $value)
											<li>
												<label class="nb-radio">
												<input type="radio" name="shipping_type" value="{{$key}}" @if($key == \App\Http\Controllers\Enum\ShippingTypeEnum::FREE_SHIPPING) checked @endif>
												<!-- <i class="fa fa-truck fa-5x activeIcon"></i>  -->
												{{--<img src="{{asset('image/default.jpg')}}">--}}
												{{--<img class="activeIcon" src="{{asset('image/default.jpg')}}">--}}
													{!! $value['icon'] !!}
												<span class="nb-radio__bg"></span><span class="nb-radio__icon rippler rippler-default">
												</span>
												<div class="border-category"></div>
												<p class="nb-radio__text">{{$value['name']}}</p>
												</label>
											</li>
											@endforeach
										</ul>
									</div>
								</div>

								<div id="free_shipping">
								</div>
								<div style="display: none;" id="flat_rate">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.rate')</label>
												<input type="number" name="flat_rate_rate" id="flat_rate_rate" class="form-control">
											</div>
										</div>
									</div>
								</div>
								{{--<div style="display: none;" id="rate_by_weight">--}}
									{{--<div class="row">--}}
										{{--<div class="col-sm-4">--}}
											{{--<div class="form-group">--}}
												{{--<label for="">@lang('messages.shipping_tax.est_delivery_time_optional')</label>--}}
												{{--<input type="text" class="form-control" name="weight_est_delivery_time" id="weight_est_delivery_time" placeholder="@lang('messages.shipping_tax.eg_3_5_business_days')">--}}
											{{--</div>--}}
										{{--</div>--}}
										{{--<div class="col-sm-8">--}}
											{{--<div class="row">--}}
												{{--<div class="col-sm-6">--}}
													{{--<label for="">@lang('messages.shipping_tax.weight_range')</label>--}}
												{{--</div>--}}
												{{--<div class="col-sm-6">--}}
													{{--<label for="">@lang('messages.shipping_tax.rate')</label>--}}
												{{--</div>--}}
											{{--</div>--}}
											{{--<div class="row">--}}
												{{--<div id="weight_range_div">--}}
													{{--<div class="weight-form-group">--}}
														{{--<div class="col-sm-6">--}}
															{{--<div class="range_input">--}}
																{{--<div class="form-group">--}}
																	{{--<input type="number" class="form-control" name="weight_range_start[]" value="0">--}}
																{{--</div>--}}
															{{--</div>--}}
															{{--<div class="range_input">--}}
																{{--<div class="form-group">--}}
																	{{--<input type="number" class="form-control" name="weight_range_end[]">--}}
																{{--</div>--}}
															{{--</div>--}}
														{{--</div>--}}
														{{--<div class="col-sm-6">--}}
															{{--<div class="form-group">--}}
																{{--<input type="number" name="weight_rate[]" class="form-control">--}}
															{{--</div>--}}
														{{--</div>--}}
														{{--<div class="clearfix"></div>--}}
													{{--</div>--}}
												{{--</div>--}}
												{{--<div class="col-md-12">--}}
													{{--<a href="javascript:;" onclick="addWeightRangeFun()" id="add_range">@lang('messages.shipping_tax.add_range')</a>--}}
													{{--<input type="hidden" name="weight_counter" id="weight_counter" value="1">--}}
												{{--</div>--}}
											{{--</div>--}}
										{{--</div>--}}
									{{--</div>--}}
								{{--</div>--}}
								<div style="display: none;" id="rate_by_order_price">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.est_delivery_time_optional')</label>
												<input type="text" class="form-control" name="order_est_delivery_time" id="order_est_delivery_time" placeholder="@lang('messages.shipping_tax.eg_3_5_business_days')">
											</div>
										</div>
										<div class="col-sm-8">
											<div class="row">
												<div class="col-sm-6">
													<label for="">@lang('messages.shipping_tax.price_range')</label>
												</div>
												<div class="col-sm-6">
													<label for="">@lang('messages.shipping_tax.rate')</label>
												</div>
											</div>
											<div class="row">
												<div id="order_range_div">
													<div class="order-form-group">
														<div class="col-sm-6">
															<div class="range_input">
																<div class="form-group">
																	<input type="number" class="form-control" name="order_range_start[]" value="0">
																</div>
															</div>
															<div class="range_input">
																<div class="form-group">
																	<input type="number" class="form-control" name="order_range_end[]">
																</div>
															</div>
														</div>
														<div class="col-sm-6">
															<div class="form-group">
																<input type="number" name="order_rate[]" class="form-control">
															</div>
														</div>
														<div class="clearfix"></div>
													</div>
												</div>
												<div class="col-md-12">
													<a href="javascript:;" onclick="addOrderRangeFun()" id="add_range">@lang('messages.shipping_tax.add_range')</a>
													<input type="hidden" name="order_counter" id="order_counter" value="1">
												</div>
											</div>
										</div>
									</div>
								</div>
								<div style="display: none;" id="alloe_store_pickup_only">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.pickup_title')</label>
												<input type="text" name="pickup_title" id="pickup_title" class="form-control" placeholder="@lang('messages.shipping_tax.eg_store_pickup')">
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-sm-6">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.pickup_address')</label>
												<input type="text" name="pickup_address" id="pickup_address" class="form-control" placeholder="@lang('messages.shipping_tax.enter_your_street_address')">
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-sm-3">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.country')</label>
												<select name="pickup_country" id="pickup_country" class="form-control">
													<option value=""></option>
													@foreach($countries as $country)
														<option value="{{$country->id}}">@if (\App\UtilityFunction::getLocal() == 'en'){{$country->name}}@else{{$country->ar_name}}@endif</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.city')</label>
												<input type="text" class="form-control" name="pickup_city" id="pickup_city" placeholder="@lang('messages.shipping_tax.enter_your_city')">
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-sm-3">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.state')</label>
												<input type="text" name="pickup_state" id="pickup_state" class="form-control">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label for="">@lang('messages.shipping_tax.zip_code')</label>
												<input type="text" class="form-control" name="pickup_zip_code" id="pickup_zip_code" placeholder="@lang('messages.shipping_tax.enter_number')">
											</div>
										</div>
									</div>
								</div>
								<div class="form-group" id="is_tax">
									<label for="">@lang('messages.shipping_tax.tax')</label>
									<div class="radio">
										<label>
											<input type="radio" name="is_tax" id="" value="0" checked required>
											@lang('messages.shipping_tax.no_tax')
										</label>
									</div>
									<div class="radio">
										<label 
										@if (\App\UtilityFunction::getLocal() == 'ar')											
												style="float: right; margin-left: 20px; margin-top: 6px;">
											@else
												style="float: left; margin-right: 20px; margin-top: 6px;">
											@endif											
											<input type="radio" name="is_tax" id="" value="1" required>
											@lang('messages.shipping_tax.tax_rate')
										</label>
										<div class="input-group" 
											@if (\App\UtilityFunction::getLocal() == 'ar')
											style="width: 100px; float: right;">
											@else
											style="width: 100px; float: left;">
											@endif
											<input type="number" min="0" max="100" name="tax_percentage" id="tax_percentage" required value="0" class="form-control input-sm">
											<span class="input-group-addon">%</span>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<input type="hidden" name="skip" id="skip" value="0">
								<button class="btn btn-primary pull-right">@lang('messages.seller.product.save')</button>
								{!! Form::close() !!}
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
<script>
	$(document.body).on('click','#is_tax input[name="is_tax"]',function(){
		var is_tax = $(this).val();
		if(is_tax == 1){
			$('#tax_percentage').attr('required',true);
		}else{
			$('#tax_percentage').attr('required',false);
		}
	});

	function getWeightHtml(){
		var range_append = '<div class="weight-form-group"><div class="col-sm-6">' +
				'<div class="range_input"><div class="form-group">' +
				'<input type="number" class="form-control" name="weight_range_start[]" value="0">' +
				'</div></div>' +
				'<div class="range_input"><div class="form-group">' +
				'<input type="number" class="form-control" name="weight_range_end[]">' +
				'</div></div></div>' +
				'<div class="col-sm-6"><div class="form-group">' +
				'<input type="number" name="weight_rate[]" class="form-control">' +
				'</div></div>' +
				'<div class="clearfix"></div></div>';

		$('#weight_range_div').empty();
		$('#weight_range_div').html(range_append);
	}

	function getOrderHtml(){
		var range_append = '<div class="order-form-group"><div class="col-sm-6">'+
				'<div class="range_input"><div class="form-group">' +
				'<input type="number" class="form-control" name="order_range_start[]" value="0">'+
				'</div></div>'+
				'<div class="range_input"><div class="form-group">'+
				'<input type="number" class="form-control" name="order_range_end[]" placeholder="" value="">'+
				'</div></div></div>'+
				'<div class="col-sm-6"><div class="form-group"><input type="number" name="order_rate[]" class="form-control">'+
				'</div></div><div class="clearfix"></div></div>';

		$('#order_range_div').empty();
		$('#order_range_div').html(range_append);
	}

	$(document.body).on('click','#shipping_type input[type="radio"]',function(){
		var shipping_type_checked = $(this).val();

		if(shipping_type_checked == {{App\Http\Controllers\Enum\ShippingTypeEnum::FLAT_RATE}}){
			$('#flat_rate_rate').attr('required',true);
			getWeightHtml();
			getOrderHtml();
			$('#weight_range_div input[name="weight_range_start[]"]').attr('required',false);
			$('#weight_range_div input[name="weight_rate[]"]').attr('required',false);
			$('#order_range_div input[name="order_range_start[]"]').attr('required',false);
			$('#alloe_store_pickup_only input').attr('required',false);
			$('#alloe_store_pickup_only select').attr('required',false);
		}
		else if(shipping_type_checked == {{App\Http\Controllers\Enum\ShippingTypeEnum::RATE_BY_WEIGHT}}){
			$('#flat_rate_rate').attr('required',false);
			$('#weight_range_div input[name="weight_range_start[]"]').attr('required',true);
			$('#weight_range_div input[name="weight_rate[]"]').attr('required',true);
			getOrderHtml();
			$('#order_range_div input[name="order_range_start[]"]').attr('required',false);
			$('#alloe_store_pickup_only input').attr('required',false);
			$('#alloe_store_pickup_only select').attr('required',false);
		}
		else if(shipping_type_checked == {{App\Http\Controllers\Enum\ShippingTypeEnum::RATE_BY_ORDER_PRICE}}){
			$('#flat_rate_rate').attr('required',false);
			$('#order_range_div input[name="order_range_start[]"]').attr('required',true);
			getWeightHtml();
			$('#weight_range_div input[name="weight_range_start[]"]').attr('required',false);
			$('#weight_range_div input[name="weight_rate[]"]').attr('required',false);
			$('#alloe_store_pickup_only input').attr('required',false);
			$('#alloe_store_pickup_only select').attr('required',false);
		}
		else if(shipping_type_checked == {{App\Http\Controllers\Enum\ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY}}){
			$('#flat_rate_rate').attr('required',false);
			getWeightHtml();
			getOrderHtml();
			$('#weight_range_div input[name="weight_range_start[]"]').attr('required',false);
			$('#weight_range_div input[name="weight_rate[]"]').attr('required',false);
			$('#order_range_div input[name="order_range_start[]"]').attr('required',false);
			$('#alloe_store_pickup_only input').attr('required',true);
			$('#alloe_store_pickup_only select').attr('required',true);
		}
		else{
			$('#flat_rate_rate').attr('required',false);
			getWeightHtml();
			getOrderHtml();
			$('#alloe_store_pickup_only input').attr('required',false);
			$('#alloe_store_pickup_only select').attr('required',false);
		}
	});
</script>
<script src="{{asset('/js/validator.min.js')}}"></script>
<script>
	$('#shipping_form').validator().on('submit', function (e) {
		if (e.isDefaultPrevented()) {
			// handle the invalid form...
			
			
		} else {
			// everything looks good!
		

		}
	});
</script>
<script>
$(document.body).on('change','#country_id',function(){
	var country_id = $(this).val();
	$('#skip').val(1);
 	$.ajax({
		type: 'POST',
		url: $('#shipping_form').attr('action')+'?country_id='+country_id,
		data: $('#shipping_form').serialize(),
		dataType: 'json',
		success: function(data){
			$('#city_id').removeClass('.chosen-select');
			$('#skip').val(0);
			$('#city_id').empty();

//			$('#city_id').append('<option value="">Select City</option>');
			$('#city_id').append(data.cities_html);
			$('#city_id').trigger('chosen:updated');
			getChosen();
		}
		}).fail(function (data) {
			var errors = data.responseJSON;
			console.log(errors);
		})
    });
</script>
<script src="{{asset('/js')}}/chosen.jquery.js"></script>
<script>
	function getChosen(){
		$('#city_id').chosen();
		$('.chosen-select-deselect').chosen({ allow_single_deselect: true });
	}
	getChosen();
</script>
<script>
$(document.body).on('click','#shipping_type input[type="radio"]',function(){
	var shipping_type = $(this).val();

	if(shipping_type == {{App\http\Controllers\Enum\ShippingTypeEnum::FREE_SHIPPING}}){
		$('#free_shipping').show();
		$('#flat_rate').hide();
		$('#rate_by_weight').hide();
		$('#rate_by_order_price').hide();
		$('#alloe_store_pickup_only').hide();
	}
	else if(shipping_type == {{App\http\Controllers\Enum\ShippingTypeEnum::FLAT_RATE}}){
		$('#free_shipping').hide();
		$('#flat_rate').show();
		$('#rate_by_weight').hide();
		$('#rate_by_order_price').hide();
		$('#alloe_store_pickup_only').hide();
	}
	else if(shipping_type == {{App\http\Controllers\Enum\ShippingTypeEnum::RATE_BY_WEIGHT}}){
		$('#free_shipping').hide();
		$('#flat_rate').hide();
		$('#rate_by_weight').show();
		$('#rate_by_order_price').hide();
		$('#alloe_store_pickup_only').hide();
	}
	else if(shipping_type == {{App\http\Controllers\Enum\ShippingTypeEnum::RATE_BY_ORDER_PRICE}}){
		$('#free_shipping').hide();
		$('#flat_rate').hide();
		$('#rate_by_weight').hide();
		$('#rate_by_order_price').show();
		$('#alloe_store_pickup_only').hide();
	}
	else if(shipping_type == {{App\http\Controllers\Enum\ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY}}){
		$('#free_shipping').hide();
		$('#flat_rate').hide();
		$('#rate_by_weight').hide();
		$('#rate_by_order_price').hide();
		$('#alloe_store_pickup_only').show();
	}
});
function addWeightRangeFun(){
	toastr.clear();
	var last_range_start_input = $('#weight_range_div .weight-form-group:last-child .range_input input[name="weight_range_start[]"]');
	var last_range_end_input = $('#weight_range_div .weight-form-group:last-child .range_input input[name="weight_range_end[]"]');
	var rate_input = $('#weight_range_div .weight-form-group:last-child input[name="weight_rate[]"]');

	var last_range_start = last_range_start_input.val();
	var last_range_end = last_range_end_input.val();
	var rate = rate_input.val();
	var weight_counter = $('#weight_counter').val();

	if(last_range_end && last_range_end != 0){
		if(last_range_start < last_range_end){
			if(rate){
				$('#weight_range_div .form-group input').attr('readonly',true);
				var range_append = '<div class="weight-form-group"><div class="col-sm-6">'+
							'<div class="range_input"><div class="form-group">'+
								'<input type="number" class="form-control" name="weight_range_start[]" readonly value="'+last_range_end+'">'+
							'</div></div>'+
							'<div class="range_input"><div class="form-group">'+
								'<input type="number" class="form-control" name="weight_range_end[]" placeholder="And Up" value="">'+
							'</div></div>'+
						'</div>'+
						'<div class="col-sm-6"><div class="form-group">'+
							'<input type="number" name="weight_rate[]" class="form-control">'+
						'</div></div><div class="clearfix"></div></div>';
	
				$('#weight_range_div').append(range_append);
				$('#weight_counter').val(parseInt(weight_counter)+1);
			}else{
				$(rate_input).focus();
				toastr.warning('Please Add Rate.');
			}
		}
		else {
			$(last_range_start_input).focus();
			toastr.warning('End Range greater Than Start.');
		}
	}
	else {
		$(last_range_end_input).focus();
		toastr.warning('Range must greater then zero.');
	}
}
function addOrderRangeFun(){
	toastr.clear();
	var last_range_start_input = $('#order_range_div .order-form-group:last-child .range_input input[name="order_range_start[]"]');
	var last_range_end_input = $('#order_range_div .order-form-group:last-child .range_input input[name="order_range_end[]"]');
	var rate_input = $('#order_range_div .order-form-group:last-child input[name="order_rate[]"]');

	var last_range_start = last_range_start_input.val();
	var last_range_end = last_range_end_input.val();
	var rate = rate_input.val();
	var order_counter = $('#order_counter').val();

	console.log(last_range_start,last_range_end);

	if(last_range_end && last_range_end != 0){
		if(last_range_start < last_range_end){
			if(rate){
				$('#order_range_div .form-group input').attr('readonly',true);
				var range_append = '<div class="order-form-group"><div class="col-sm-6">'+
								'<div class="range_input"><div class="form-group">'+
									'<input type="number" class="form-control" readonly name="order_range_start[]" value="'+last_range_end+'">'+
								'</div></div>'+
								'<div class="range_input"><div class="form-group">'+
									'<input type="number" class="form-control" name="order_range_end[]" placeholder="And Up" value="">'+
								'</div></div>'+
							'</div>'+
							'<div class="col-sm-6"><div class="form-group">'+
								'<input type="number" name="order_rate[]" class="form-control">'+
							'</div></div><div class="clearfix"></div></div>';
		
				$('#order_range_div').append(range_append);
				$('#order_counter').val(parseInt(order_counter)+1);
			}else{
				$(rate_input).focus();
				toastr.warning('Please Add Rate.');
			}
		}
		else {
			$(last_range_start_input).focus();
			toastr.warning('End Range greater Than Start.');
		}
		
	}
	else {
		$(last_range_end_input).focus();
		toastr.warning('Range must greater then zero.');
	}
}
</script>
@stop
