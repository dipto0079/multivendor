@extends('frontend.master')
@section('title',__('messages.page_title.seller_registration'))
@section('stylesheet')
    <style type="text/css">
        .board {
            /*min-height: 500px; */
            background: #fff;
        }

        .board .nav-tabs {
            position: relative;
            margin: -15px auto;
            margin-bottom: 0;
            box-sizing: border-box;
            border-bottom-width: 0;
        }

        .board > div.board-inner {

        }

        .liner {
            height: 2px;
            background: #ddd;
            position: absolute;
            width: 80%;
            margin: 0 auto;
            left: 0;
            right: 0;
            top: 50%;
            z-index: 1;
        }

        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
            color: #555555;
            cursor: default;
            border: 0;
            border-bottom-color: transparent;
        }

        span.round-tabs {
            width: 70px;
            height: 70px;
            line-height: 70px;
            display: inline-block;
            border-radius: 100px;
            background: white;
            z-index: 2;
            position: absolute;
            left: 0;
            text-align: center;
            font-size: 25px;
        }

        span.round-tabs.one {
            color: rgb(34, 194, 34);
            border: 2px solid rgb(34, 194, 34);
        }

        li.active span.round-tabs.one {
            background: #fff !important;
            border: 2px solid #ddd;
            color: rgb(34, 194, 34);
        }

        .nav-tabs > li.active > a span.round-tabs {
            background: #fafafa;
        }

        .nav-tabs > li {
            width: 25%;
            text-align: center;
        }

        .nav-tabs > li a {
            padding: 0;
        }

        .nav-tabs > li a:hover {
            background: transparent;
        }

        /*.tab-content > .tab-pane { display: block; }*/
        .tab-pane {
            position: relative;
            padding-top: 25px;
            padding-left: 15px;
            padding-right: 15px;
        }

        .tab-content .head {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 25px;
            text-transform: uppercase;
            padding-bottom: 10px;
        }

        .form-tabs_steps {
            background: #fff;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: inline-block;
            color: #fff;
            position: relative;
            z-index: 9;
            font-size: 12px;
            padding: 4px;
            margin-bottom: 12px;
            border: solid 2px #e0e0e0;
            color: transparent;
            box-shadow: 0 0 0 6px #fff;
            margin-top: 30px;
        }

        .nav-tabs li.done {
            color: #ff8300;
        }

        .nav-tabs li.active .form-tabs_steps, .nav-tabs li.done .form-tabs_steps {
            background-color: #ff8300;
            border: solid 2px #ff8300;
            box-shadow: 0 0 0 6px #fff;
            width: 28px;
            height: 28px;
        }

        .nav-tabs li.done .form-tabs_steps {
            background-color: #ff8300;
            background-image: url('{{asset('/image/')}}/checkmark.png');
            background-size: 60%;
            background-repeat: no-repeat;
            background-position: center;
            border-color: #ff8300;
        }

        .partner_banner {
            background-color: #ff8300;
            padding: 34px 0 114px;
            color: #fff;
            background-image: url('{{asset('/store/')}}/media/PartnerWithUs-BG-Image.png');
            background-position: center bottom;
            background-repeat: no-repeat;
            margin-top: -25px;
        }

        .partner_content {
            background-color: #fff;
            min-height: 350px;
            margin-top: -83px;
            border-radius: 4px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-bottom: 30px;
        }

        input[type="radio"] {
            width: 20px;
            height: 20px;
        }

        .form-h4 {
            font-size: 15px;
            color: #999;
            font-weight: 600 !important;
            margin: 20px 0;
        }

        .tab-content label {
            line-height: 25px;
        }

        .category-list {
            list-style: none;
            margin-top: 35px;
            overflow: hidden;
        }

        .category-list li {
            float: left;
            list-style: none;
            width: 24.5%;
            text-align: center;
        }

        .nb-radio {
            float: left;
            margin: 0 auto;
            position: relative;
            cursor: pointer;
            padding: 34px 0;
            overflow: hidden;
            height: 200px;
            width: 100%;
            border-bottom: solid 1px #e0e0e0;
            border-right: solid 1px #e0e0e0;
        }

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

        .nb-radio input:checked + img {
            display: none;
        }

        .category-list li img {
            display: block;
            margin: 0 auto;
            margin-bottom: 12px;
            width: 90px;
        }

        .nb-radio input:checked + img + img {
            display: block;
        }

        .nb-radio__bg {
            display: block;
            width: 18px;
            height: 18px;
            border: 2px solid #757575;
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
            background-image: url('{{asset('/store/')}}/media/RedBoxBig.png');
        }

        .nb-radio input:checked + img + img + .nb-radio__bg + .nb-radio__icon {
            opacity: 1;
        }

        .nb-radio input:checked + img + img + .nb-radio__bg + .nb-radio__icon + .border-category {
            display: block;
        }

        .nb-radio input:checked + img + img {
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
            border: solid 2px #ff8300;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        .nb-radio input:checked + img + img + .nb-radio__bg + .nb-radio__icon + .border-category + .nb-radio__text {
            color: #ff8300;
        }

        .nb-radio__text {
            font-weight: 600;
            font-size: 14px;
            color: #666;
            padding: 0 25px;
            text-align: center;
        }

        .category-list li:nth-child(4n) .nb-radio {
            border-right: 0;
        }

        .error_msg {
            font-size: 13px;
            color: #eb5131 !important;
            padding: 0;
            margin: 20px 0 0 0 !important;
        }

        .form-group select,
        .form-group textarea,
        .form-group input[type="text"],
        .form-group input[type="url"] {
            box-shadow: none;
            width: 100%;
            border: 0;
            border-bottom: solid 1px #666;
            padding: 10px 20px 8px 0;
            font-size: 18px;
            color: #333;
        }

        .form-group textarea {
            border: 1px solid #ddd;
            padding: 15px;
        }

        .form-group select {
            height: initial;
        }

        .form-group textarea:focus,
        .form-group input[type="text"]:focus,
        .form-group input[type="url"]:focus {
            border-bottom-width: 2px;
        }

        .form-group label, .form-group + h4, .form-group h4 {
            font-size: 15px;
            font-weight: 600;
            color: #666;
            border-color: #666 !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            -moz-box-shadow: none !important;
        }

        .form-group {
            margin-bottom: 35px;
        }

        .form_icon {
            position: relative;
            width: 100%;
            float: left;
        }

        .form_icon_msg {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        .tab-pane p {
            margin-bottom: 0 !important;
        }

        @media (max-width: 767px) {
            .category-list li {
                width: 50%;
            }

            .category-list li:nth-child(4n) .nb-radio {
                border-right-width: 1px;
            }

            .category-list li:nth-child(2n) .nb-radio {
                border-right-width: 0;
            }

            .partner_content {
                padding: 15px;
            }

            .nav-tabs > li {
                float: left !important;
            }

            .form-tabs_steps {
                margin-top: 40px;
            }
        }

        @if (\App\UtilityFunction::getLocal() == 'ar') .margin-top-xl {
            float: right;
        } @endif
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="partner_banner">
            <div class="container">
                <h2>@lang('messages.seller_registration.grow_your_business')</h2>
                <p>@lang('messages.seller_registration.fill_your_details')</p>
            </div>
        </div>
        <div class="container">
            <div class="clearfix"></div>
            <div class="board partner_content">
                @if(!empty(Session::get('message')) || !empty(Session::get('error_message')))
                    <div class="text-center">
                        @if(!empty(Session::get('message')))
                            <i class="fa fa-check fa-5x" style="color: #ff8300;"></i>
                            <h4>{{Session::get('message')}}</h4><br>
                            <a href="{{url('/')}}"
                               class="btn btn-primary font-additional">@lang('messages.seller_registration.go_back_to_homepage')</a>
                        @elseif(!empty(Session::get('error_message')))
                            <i class="fa fa-times fa-5x" style="color: #00ff00;"></i>
                            <h4>{{Session::get('error_message')}}</h4><br>
                            <p>Or</p>
                            <a href="{{url('/')}}"
                               class="btn btn-primary font-additional">@lang('messages.seller_registration.try_again')</a>
                        @endif
                    </div>
                @else
                    <div class="board-inner">
                        <ul class="nav nav-tabs" role="tablist">
                            <div class="liner"></div>
                            <li class="active">
                                <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab"></a>
                                <span class="form-tabs_steps">1</span>
                                <div>@lang('messages.seller_registration.category_selection')</div>
                            </li>
                            <li class="disabled">
                                <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab"></a>
                                <span class="form-tabs_steps">2</span>
                                <div>@lang('messages.seller_registration.business_info')</div>
                            </li>
                            <li class="disabled">
                                <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab"></a>
                                <span class="form-tabs_steps">2</span>
                                <div>@lang('messages.seller_registration.contact_info')</div>
                            </li>
                            <li class="disabled">
                                <a href="#step4" data-toggle="tab" aria-controls="step4" role="tab"></a>
                                <span class="form-tabs_steps">2</span>
                                <div>@lang('messages.seller_registration.about_me')</div>
                            </li>
                        </ul>
                    </div>
                    {!! Form::open(array('url' => '/seller-registration/save', 'id'=>'reg_form','files'=>true)) !!}
                    <div class="tab-content">
                        <div class="tab-pane active" role="tabpanel" id="step1">
                            <h3 class="margin-bottom-xl">@lang('messages.seller_registration.tell_business')</h3>
                            <h4 class="form-h4 required">@lang('messages.seller_registration.seller_type')</h4>
                            <div id="seller_type">
                                <label class="radio-inline">
                                    <input type="radio" name="seller_type" id="p_s" checked
                                           value="1">@lang('messages.seller_registration.product_seller')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="seller_type" id="s_s"
                                           value="2">@lang('messages.seller_registration.service_seller')
                                </label>
                                <p class="error_msg seller_msg"></p>
                                <input type="hidden" id="seller_type_hidden" value="1">
                            </div>

                            <div id="product_category">
                                <h4 class="form-h4 required">@lang('messages.seller_registration.select_category')</h4>
                                <ul class="category-list">
                                    @foreach($product_categories as $product_category)
                                        <li>
                                            <label class="nb-radio">
                                                <input type="radio" name="categoryName"
                                                       value="{{$product_category->id}}">
                                                <img class="img_background" src="{{asset('image/default.jpg')}}"
                                                     data-src="{{asset('/uploads/category/'.$product_category->image)}}">
                                                <img class="activeIcon"
                                                     src="{{asset('/uploads/category/'.$product_category->image)}}">
                                                <span class="nb-radio__bg"></span><span
                                                        class="nb-radio__icon rippler rippler-default">
		                       			</span>
                                                <div class="border-category"></div>
                                                <p class="nb-radio__text">@if(\App\UtilityFunction::getLocal()=="en"){{$product_category->name}} @else {{$product_category->ar_name}} @endif</p>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div id="service_category" style="display: none;">
                                <h4 class="form-h4 required">@lang('messages.seller_registration.select_category')</h4>
                                <ul class="category-list">
                                    @foreach($service_categories as $service_category)
                                        <li>
                                            <label class="nb-radio">
                                                <input type="radio" name="categoryName"
                                                       value="{{$service_category->id}}">
                                                <img src="{{asset('/uploads/category/'.$service_category->image)}}">
                                                <img class="activeIcon"
                                                     src="{{asset('/uploads/category/'.$service_category->image)}}">
                                                <span class="nb-radio__bg"></span><span
                                                        class="nb-radio__icon rippler rippler-default">
		                       			</span>
                                                <div class="border-category"></div>
                                                <p class="nb-radio__text">@if(\App\UtilityFunction::getLocal()=="en"){{$service_category->name}} @else {{$service_category->ar_name}} @endif</p>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <input type="hidden" id="category_hidden">
                            <p class="error_msg seller_category_msg"></p>
                            <div class="margin-top-xl">
                                <button type="button"
                                        class="btn btn-primary font-additional first-step">@lang('messages.seller_registration.next')</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="step2">
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.business_name')</label>
                                <input type="text" class="form-control" name="business_name" id="business_name"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <p class="business_name_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.store_name')
                                    <i class="glyphicon glyphicon-question-sign" data-toggle="tooltip"
                                       data-placement="top" title="For store name use number and or letters."
                                       style="font-size: 16px;margin-top: 4px;"></i>
                                </label>
                                <div class="form_icon">
                                    <input type="text" class="form-control" name="store_name" id="store_name"
                                           placeholder="@lang('messages.seller_registration.your_answer')">
                                    <div class="form_icon_msg" id="store_name_msg_icon"></div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="store_name_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.business_email')</label>
                                <div class="form_icon">
                                    <input type="text" class="form-control" name="business_email" autocomplete="off"
                                           id="business_email"
                                           placeholder="@lang('messages.seller_registration.your_answer')">
                                    <div class="form_icon_msg" id="business_email_icon"></div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="business_email_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.business_address')</label>
                                <input type="text" class="form-control" name="business_address" id="business_address"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <div class="clearfix"></div>
                                <p class="business_address_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label class="">@lang('messages.seller_registration.website')</label>
                                <input type="text" class="form-control" name="business_website" id="business_website"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <div class="clearfix"></div>
                                <p class="business_website_msg error_msg"></p>
                            </div>
                            <div class="margin-top-xl">
                                <button type="button"
                                        class="btn btn-default font-additional prev-step-f-step">@lang('messages.seller_registration.back')</button>
                                <button type="button"
                                        class="btn btn-primary font-additional second-step">@lang('messages.seller_registration.next')</button>
                            </div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="step3">
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.name')</label>
                                <input type="text" class="form-control" name="name" id="name"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <div class="clearfix"></div>
                                <p class="name_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.phone')</label>
                                <input type="text" class="form-control" name="phone_number" id="phone_number"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <div class="clearfix"></div>
                                <p class="phone_number_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.email')</label>
                                <div class="email form_icon">
                                    <input type="text" class="form-control" name="email_id" autocomplete="off"
                                           id="email_id"
                                           placeholder="@lang('messages.seller_registration.your_answer')">
                                    <div class="form_icon_msg" id="email_id_icon"></div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="email_id_msg error_msg"></p>
                            </div>
                            <?php $sa_id = 0; ?>

                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.country')</label>
                                <select name="country" id="country" class="form-control">
                                    <option value="">@lang('messages.seller_registration.select-country')</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}"
                                                @if($country->short_name == 'sa') selected @endif>{{$country->name}}</option>

                                        <?php
                                        if($country->short_name == 'sa')
                                            $sa_id = $country->id
                                        ?>

                                    @endforeach
                                </select>
                                <div class="clearfix"></div>
                                <p class="country_msg error_msg"></p>
                            </div>

                            <?php
                            $cities_by_country = '';
                            if(!empty($sa_id))  $cities_by_country = App\Model\City::where('country_id',$sa_id)->get();
                            ?>

                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.city')</label>

                                <select name="city" id="city" class="form-control" required style="position: static;">
                                    {{--<option value="">Select City</option>--}}
                                    @if(!empty($cities_by_country[0]))
                                        @foreach($cities_by_country as $city)
                                            <option value="{{$city->id}}">@if(\App\UtilityFunction::getLocal()=='en') {{$city->name}} @else {{$city->ar_name}} @endif</option>
                                        @endforeach
                                    @endif
                                </select>

                                {{--<input type="text" class="form-control" name="city" id="city"--}}
                                       {{--placeholder="@lang('messages.seller_registration.your_answer')">--}}
                                <div class="clearfix"></div>
                                <p class="city_msg error_msg"></p>
                            </div>

                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.street')</label>
                                <input type="text" class="form-control" name="street" id="street"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <div class="clearfix"></div>
                                <p class="street_msg error_msg"></p>
                            </div>

                            <div class="form-group">
                                <label>@lang('messages.seller_registration.state')</label>
                                <input type="text" class="form-control" name="state" id="state"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <div class="clearfix"></div>
                                <p class="state_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label>@lang('messages.seller_registration.zip')</label>
                                <input type="text" class="form-control" name="zip" id="zip"
                                       placeholder="@lang('messages.seller_registration.your_answer')">
                                <div class="clearfix"></div>
                                <p class="zip_msg error_msg"></p>
                            </div>

                            <div class="margin-top-xl">
                                <button type="button"
                                        class="btn btn-default font-additional prev-step-f-step">@lang('messages.seller_registration.back')</button>
                                <button type="button"
                                        class="btn btn-primary font-additional third-step">@lang('messages.seller_registration.next')</button>
                            </div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="step4">
                            <div class="form-group">
                                <label class="required">@lang('messages.seller_registration.about_me')</label>
                                <textarea class="form-control" name="about_me" id="about_me" rows="5"></textarea>
                                <div class="clearfix"></div>
                                <p class="about_me_msg error_msg"></p>
                            </div>
                            <div class="form-group">
                                <label>@lang('messages.seller_registration.profile_image')</label>
                                <input type="file" id="profile_image"
                                       accept="image/x-png,image/gif,image/jpeg,image/jpg" name="profile_image"
                                       class="form-control">
                                <div class="clearfix"></div>
                            </div>
                            <div class="margin-top-xl">
                                <button type="button"
                                        class="btn btn-default font-additional prev-step-f-step">@lang('messages.seller_registration.back')</button>
                                <button type="submit"
                                        class="btn btn-primary font-additional final-step">@lang('messages.seller_registration.submit')</button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {!! Form::close() !!}
                @endif
            </div>
        </div>
    </div>
    <form action="{{url('/email-exists-checking')}}" id="email_checking">{{csrf_field()}}</form>
    <form action="{{url('/seller-email-exists-checking')}}" id="seller_email_checking">{{csrf_field()}}</form>
    <form action="{{url('/store-exists-checking')}}" id="store_checking">{{csrf_field()}}</form>
@stop

@section('script')
    <script type="text/javascript" src="{{asset('js/registration.step.js')}}"></script>

    <script>
        $(document.body).on('change','#country',function(){
            var country = $(this).val();

            $('#city').addClass('input_loader');
            $.ajax({
                type: 'POST',
                url: '{{url("/seller-registration/city-by-country")}}?country='+country,
                data: $('#reg_form').serialize(),
                dataType: 'json',
                success: function(data){


                    $('#city').removeClass('input_loader');
                    $('#city').empty();

                    {{--$('#city').append('<option value="">@lang('messages.select')</option>');--}}
                    $('#city').append(data.cities_html);
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(data);
            })
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            var email_exist = false;

            // $(document.body).on('input','#business_email',function () {
            //   var Email = document.getElementById('business_email').value;
            //   var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;
            //
            // 	var testEmail = regexEmail.test(Email);
            //
            // 	$.ajax({
            // 		type: "POST",
            // 		url: $('#seller_email_checking').attr('action') + '?email=' + Email,
            // 		data: $('#seller_email_checking').serialize(),
            // 		dataType: "json",
            // 		success: function (data) {
            // 			email_exist = data.exists;
            //
            // 			if (regexEmail.test(Email) && email_exist == false) {
            // 				$(".business_email_msg").html('');
            // 				$("#business_email_icon").html('');
            // 				$("#business_email_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
            // 			}
            // 			else if(email_exist == true){
            // 				$(".business_email_msg").html('');
            // 				$("#business_email_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
            // 				$(".business_email_msg").html('@lang('messages.seller_registration.email_already_exists')');
            // 				$("#business_email").focus();
            // 			}
            // 			else {
            // 				$("#business_email_icon").html('');
            // 				$("#business_email_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
            // 				$(".business_email_msg").html("@lang('messages.seller_registration.please_email')");
            // 				document.getElementById('business_email').focus();
            // 			}
            // 		}
            // 	}).fail(function (data) {
            // 		var errors = data.responseJSON;
            // 		console.log(errors);
            // 	});
            // });
            //
            // $(document.body).on('input','#email_id',function () {
            //   var Email = document.getElementById('email_id').value;
            // 	var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;
            //
            // 	$.ajax({
            // 	    type: "POST",
            // 	    url: $('#email_checking').attr('action') + '?email=' + Email,
            // 	    data: $('#email_checking').serialize(),
            // 	    dataType: "json",
            // 	    success: function (data) {
            // 	    	email_exist = data.exists;
            //
            // 				if (regexEmail.test(Email) && email_exist == false) {
            // 					$(".business_email_msg").html('');
            // 					$("#business_email_icon").html('');
            // 					$("#business_email_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
            // 				}
            // 				else if(email_exist == true){
            // 					$(".email_id_msg").html('');
            // 					$("#email_id_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
            // 					$(".email_id_msg").html('@lang('messages.seller_registration.email_already_exists')');
            // 					$("#email_id").focus();
            // 				}
            // 				else {
            // 					$("#email_id_icon").html('');
            // 					$("#email_id_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
            // 					$(".email_id_msg").html("@lang('messages.seller_registration.please_email')");
            // 					document.getElementById('email_id').focus();
            // 				}
            // 	    }
            // 	}).fail(function (data) {
            // 	    var errors = data.responseJSON;
            // 	    console.log(errors);
            // 	});
            //     });

            function checkingBusinessEmail() {
                var Email = document.getElementById('business_email').value;

                $('#skip').val(1);
                $.ajax({
                    type: "POST",
                    url: $('#seller_email_checking').attr('action') + '?email=' + Email,
                    data: $('#seller_email_checking').serialize(),
                    dataType: "json",
                    success: function (data) {
                        email_exist = data.exists;

                        if (email_exist == true) {
                            $(".business_email_msg").html('');
                            $("#business_email_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
                            $(".business_email_msg").html('@lang('messages.seller_registration.email_already_exists')');
                            $("#business_email").focus();
                        }
                        else {
                            $("#business_email_icon").html('');
                            $("#business_email_icon").html("");
                            $("#business_email_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
                        }

                        $('#skip').val(0);
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            }

            $(document.body).on('input', '#business_email', function () {
                var Email = document.getElementById('business_email').value;
                var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

                var testEmail = regexEmail.test(Email);

                if (testEmail == true) {
                    $(".business_email_msg").html('');
                    $("#business_email_icon").html('');
                    $("#business_email_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
                    checkingBusinessEmail();
                } else {
                    $("#business_email_icon").html('');
                    $("#business_email_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
                    $(".business_email_msg").html("@lang('messages.seller_registration.please_email')");
                    document.getElementById('business_email').focus();
                }
            });

            function checkingPersonalEmail() {
                var Email = document.getElementById('email_id').value;

                $.ajax({
                    type: "POST",
                    url: $('#email_checking').attr('action') + '?email=' + Email,
                    data: $('#email_checking').serialize(),
                    dataType: "json",
                    success: function (data) {
                        email_exist = data.exists;

                        if (email_exist == true) {
                            $(".email_id_msg").html('');
                            $("#email_id_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
                            $(".email_id_msg").html('@lang('messages.seller_registration.email_already_exists')');
                            $("#email_id").focus();
                        }
                        else {
                            $("#email_id_icon").html('');
                            $(".email_id_msg").html("");
                            $("#email_id_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
                        }

                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            }

            $(document.body).on('input', '#email_id', function () {
                var Email = document.getElementById('email_id').value;
                var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

                var testEmail = regexEmail.test(Email);

                if (testEmail == true) {
                    checkingPersonalEmail();
                    $(".email_id_msg").html('');
                    $("#email_id_icon").html('');
                    $("#email_id_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
                }
                else {
                    $("#email_id_icon").html('');
                    $("#email_id_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
                    $(".email_id_msg").html("@lang('messages.seller_registration.please_email')");
                    document.getElementById('email_id').focus();
                }
            });

            function isUrlValid(userInput) {
                var res = userInput.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
                if (res == null)
                    return false;
                else
                    return true;
            }

            var store_exist = false;
            var store_func = false;
            $('#store_name').keypress(function (e) {
                if (!/[0-9a-zA-Z-]/.test(String.fromCharCode(e.which)))
                    return false;
            });

            function storeNameAjax() {
                var store = document.getElementById('store_name').value;
                $.ajax({
                    type: "POST",
                    url: $('#store_checking').attr('action') + '?store=' + store,
                    data: $('#store_checking').serialize(),
                    dataType: "json",
                    success: function (data) {
                        store_exist = data.store_exists;

                        if (store_exist == true) {
                            $(".store_name_msg").html('');
                            $("#store_name_msg_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
                            $(".store_name_msg").html('@lang('messages.seller_registration.store_already_exists')');
                        }
                        else {
                            $(".store_name_msg").html('');
                            $("#store_name_msg_icon").html('');
                            $("#store_name_msg_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
                        }

                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            }

            $(document.body).on('input', '#store_name', function () {
                $('#store_name_msg_icon').html('');
                $('#store_name_msg').html('');
                storeNameAjax();
            });


            $(document.body).on('click', '#seller_type input[type="radio"]', function () {
                var seller_type = $(this).val();
                if (seller_type == 1) {
                    $('#product_category').show();
                    $('#service_category').hide();
                }
                if (seller_type == 2) {
                    $('#product_category').hide();
                    $('#service_category').show();
                }
                $('#seller_type_hidden').val(seller_type);
                $('.seller_msg').html('');
            });
            $(document.body).on('click', '#product_category .nb-radio, #service_category .nb-radio', function () {
                var category_type = $(this).find('input[type="radio"]').val();
                $('#category_hidden').val(category_type);
                $('.seller_category_msg').html('');
            });


            $(".first-step").click(function (e) {
                if ($('#seller_type_hidden').val() == '') {
                    $('.seller_msg').html('Please Select Seller Type.');
                }
                else if ($('#category_hidden').val() == '') {
                    $('.seller_category_msg').html('@lang('messages.seller_registration.please_select_category')');
                }

                else {
                    $('.error_msg').html('');
                    var $active = $('.board .nav-tabs li.active');
                    $active.addClass('done');
                    $active.next().removeClass('disabled');
                    nextTab($active);
                    $('html,body').animate({scrollTop: $(".board-inner").offset().top - 65}, 'slow');
                }
            });
            $(".second-step").click(function (e) {

                console.log(isUrlValid($('#business_website').val()));

                //alert($('#business_website').val() != '' && validateURL($('#business_website').val()));

                var Email = document.getElementById('business_email').value;
                var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

                var testEmail = regexEmail.test(Email);
                var store = document.getElementById('store_name').value;


                $('.business_name_msg').html('');
                //$('.store_name_msg').html('');
                //$('.business_email_msg').html('');
                $('.business_address_msg').html('');
                $('.business_website_msg').html('');

                if ($('#business_name').val() == '') {
                    $('.business_name_msg').html('@lang('messages.seller_registration.please_fill_up')');
                    $("#business_name").focus();
                }
                else if ($('#store_name').val() == '') {
                    $('.store_name_msg').html('@lang('messages.seller_registration.please_fill_up')');
                    $("#store_name").focus();
                }
                else if (store_exist == true) {
                    $('.store_name_msg').html('@lang('messages.seller_registration.store_already_exists')');
                    $("#store_name").focus();
                }
                else if ($('#business_email').val() == '') {
                    $('.business_email_msg').html('@lang('messages.seller_registration.please_fill_up')');
                    $("#business_email").focus();
                }
                else if (testEmail == false) {
                    $('.business_email_msg').html('@lang('messages.seller_registration.please_email')');
                    $("#business_email").focus();
                }
                else if (email_exist == true) {
                    $(".business_email_msg").html('@lang('messages.seller_registration.email_already_exists')');
                    $("#business_email").focus();
                }
                else if ($('#business_address').val() == '') {
                    $('.business_address_msg').html('@lang('messages.seller_registration.please_fill_up')');
                    $("#business_address").focus();
                }
                else if ($('#business_website').val() != '' && isUrlValid($('#business_website').val()) == false) {
                    $('.business_website_msg').html('@lang('messages.seller_registration.this_is_not_valid_url')');
                    $("#business_website").focus();
                }

                else {
                    $('.error_msg').html('');
                    var $active = $('.board .nav-tabs li.active');
                    $active.addClass('done');
                    $active.next().removeClass('disabled');
                    nextTab($active);
                    $('html,body').animate({scrollTop: $(".board-inner").offset().top - 65}, 'slow');
                }

            });
            $(".third-step").click(function (e) {
                var Email = document.getElementById('email_id').value;
                var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

                var testEmailID = regexEmail.test(Email);

                $('.name_msg').html('');
                $('.phone_number_msg').html('');
                $('.email_id_msg').html('');
                $('.street_msg').html('');
                $('.city_msg').html('');
                $('.state_msg').html('');
                $('.zip_msg').html('');
                $('.country_msg').html('');

                if ($('#name').val() == '') {
                    $('.name_msg').html('@lang('messages.seller_registration.please_fill_up') ');
                    $('#name').focus();
                }
                else if ($('#phone_number').val() == '') {
                    $('.phone_number_msg').html('@lang('messages.seller_registration.please_fill_up') ');
                    $('#phone_number').focus();
                }
                else if ($('#email_id').val() == '') {
                    $('.email_id_msg').html('@lang('messages.seller_registration.please_fill_up') ');
                    $('#email_id').focus();
                }
                else if (testEmailID == false) {
                    $('.email_id_msg').html('@lang('messages.seller_registration.please_email')');
                    $('#email_id').focus();
                }
                else if (email_exist == true) {
                    $('.email_id_msg').html('@lang('messages.seller_registration.store_already_exists')');
                    $("#email_id").focus();
                }
                else if ($('#street').val() == '') {
                    $('.street_msg').html('@lang('messages.seller_registration.please_fill_up') ');
                    $('#street').focus();
                }
                else if ($('#city').val() == '') {
                    $('.city_msg').html('@lang('messages.seller_registration.please_fill_up') ');
                    $('#city').focus();
                }
                {{--else if ($('#state').val() == '') {--}}
                        {{--$('.state_msg').html('@lang('messages.seller_registration.please_fill_up') ');--}}
                        {{--$('#state').focus();--}}
                        {{--}--}}
                        {{--else if ($('#zip').val() == '') {--}}
                        {{--$('.zip_msg').html('@lang('messages.seller_registration.please_fill_up') ');--}}
                        {{--$('#zip').focus();--}}
                        {{--}--}}
                else if ($('#country').val() == '') {
                    $('.country_msg').html('@lang('messages.seller_registration.please_fill_up') ');
                    $('#country').focus();
                }
                else {
                    $('.error_msg').html('');
                    var $active = $('.board .nav-tabs li.active');
                    $active.addClass('done');
                    $active.next().removeClass('disabled');
                    nextTab($active);
                    $('html,body').animate({scrollTop: $(".board-inner").offset().top - 65}, 'slow');
                }
            });
            $(".final-step").click(function (e) {
                if ($('#about_me').val() == '') {
                    $('.about_me_msg').html('@lang('messages.seller_registration.please_fill_up') ');
                    $("#about_me").focus();
                    return false;
                }
                else {
                    $('.error_msg').html('');
                    var $active = $('.board .nav-tabs li.active');
                    $active.addClass('done');
                    return true;
                    $(".final-step").attr('disabled', true);
                }
            });

            $(".prev-step-f-step").click(function (e) {
                $('#panelHeadingBlue').empty();
                $('#panelHeadingBlue').append('dd');
                var $active = $('.board .nav-tabs li.active');
                prevTab($active);
                $('html,body').animate({scrollTop: $(".board-inner").offset().top - 65}, 'slow');
            });
        });
    </script>
@stop
