@extends('frontend.master',['menu'=>'edit_profile'])
@section('title',__('messages.page_title.edit_profile'))
@section('stylesheet')
<!-- <link rel="stylesheet" href="{{asset('/css/intlTelInput.css')}}"> -->
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'edit_profile'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.edit_profile')
                        </div>
                        <div class="panel-body">
                            {!! Form::open(array('url'=>'/buyer/edit-profile','id'=>'profile_form','files'=>true, 'class'=>"form-horizontal")) !!}
                                <div class="col-md-12">
                                    <h4>@lang('messages.buyer.user_information')</h4>
                                    <hr>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.name')</label>
                                        <div class="col-md-6 col-sm-8">
                                            <input type="text" class="form-control" required name="full_name" value="{{Auth::user()->username}}" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.email') <span class="nobr"> <i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="top" title="" style="font-size: 16px;margin-top: 4px;" data-original-title="If you want to change the email please contract with the administrator."></i></span></label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="email" name="email" disabled class="form-control" required  value="{{Auth::user()->email}}" placeholder="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.mobile')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" size="5" name="country_code" required class="form-control numeric" placeholder="Code" value="{{Auth::user()->country_code}}" style="display: inline-block; width: auto;"> 
                                            <input style="display: inline-block; width: auto;" size="15" type="tel" name="phone" id="phone" class="form-control numeric" value="{{Auth::user()->phone}}" placeholder="" required>
                                        </div>
                                    </div>

                                    <?php
                                        $buyer = Auth::user()->getBuyer;
                                    ?>


                                    <h4>@lang('messages.buyer.address')</h4>
                                    <hr>

                                    <div class="form-group @if(!empty(Session::get('TOASTR_MESSAGE')) && empty($buyer->country)) has-error @endif">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.country')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <select name="country" id="country_id" required class="form-control">
                                              <option value="">@lang('messages.select')</option>
                                                @foreach($countries as $c)
                                                    <option @if($c->id == $buyer->country) selected @endif value="{{$c->id}}">@if(\App\UtilityFunction::getLocal()=='en'){{$c->name}} @else {{$c->ar_name}}@endif</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <?php 
                                        $cities_by_country = '';
                                        if(!empty($buyer->country))  $cities_by_country = App\Model\City::where('country_id',$buyer->country)->get(); 
                                    ?>
                                    <div class="form-group @if(!empty(Session::get('TOASTR_MESSAGE')) && empty($buyer->city)) has-error @endif">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.city')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <select name="city" id="city_id" class="form-control" required style="position: static;">
                                                <option value="">Select City</option>
                                                @if(!empty($buyer->city) || !empty($cities_by_country[0]))
                                                    @foreach($cities_by_country as $city)
                                                        <option @if($city->id == $buyer->city) selected @endif value="{{$city->id}}">@if(\App\UtilityFunction::getLocal()=='en') {{$city->name}} @else {{$city->ar_name}} @endif</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            {{--  <input type="text" class="form-control" required name="city" value="{{$buyer->city}}">  --}}
                                        </div>
                                    </div>

                                    <div class="form-group @if(!empty(Session::get('TOASTR_MESSAGE')) && empty($buyer->street)) has-error @endif">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.street')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" class="form-control" required name="street" value="{{$buyer->street}}" required>
                                        </div>
                                    </div>

                                    <div class="form-group @if(!empty(Session::get('TOASTR_MESSAGE')) && empty($buyer->state)) has-error @endif">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.state')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" class="form-control" name="state" value="{{$buyer->state}}" required>
                                        </div>
                                    </div>

                                    <div class="form-group @if(!empty(Session::get('TOASTR_MESSAGE')) && empty($buyer->zip)) has-error @endif">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.zip')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" class="form-control" name="zip" value="{{$buyer->zip}}" required>
                                        </div>
                                    </div>

                                    <h4>@lang('messages.buyer.photo')</h4>
                                    <hr>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.buyer.photo')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="file" name="profile_image" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary font-additional pull-right">@lang('messages.buyer.update')</button>
                                    </div>
                                </div>
                                <input type="hidden" name="skip" id="skip" value="0">
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script type="text/javascript">
      $(document.body).bind('.form-group input,.form-group select',function() {
        $(this).closest('.form-group').removeClass('has-error');
      });
    </script>
    <script src="{{asset('/js/validator.min.js')}}"></script>
    <script>
        setTimeout(function(){ $('#profile_form button[type="submit"]').removeClass('disabled'); }, 500);
        $('#profile_form').validator().on('submit', function (e) {
            if (e.isDefaultPrevented()) {
            } else {
            }
        });
    </script>
    <script type="text/javascript">
    $(document).on("input", ".numeric", function() {
      this.value = this.value.replace(/[^\d\.\-]/g,'');
    });
    </script>

    <script>
        $(document.body).on('change','#country_id',function(){
            var country_id = $(this).val();
            $('#skip').val(1);
            $('#city_id').addClass('input_loader');
            $.ajax({
                type: 'POST',
                url: $('#profile_form').attr('action')+'?country_id='+country_id,
                data: $('#profile_form').serialize(),
                dataType: 'json',
                success: function(data){
                    $('#city_id').removeClass('input_loader');
                    $('#skip').val(0);
                    $('#city_id').empty();

                    $('#city_id').append('<option value="">@lang('messages.select')</option>');
                    $('#city_id').append(data.cities_html);
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            })
        });
    </script>
@stop
