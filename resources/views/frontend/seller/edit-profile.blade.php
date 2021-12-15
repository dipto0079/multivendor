@extends('frontend.master',['menu'=>'edit-profile'])
@section('title','Edit Profile')
@section('stylesheet')
<style media="screen">
.form_icon { position: relative; }
.form_icon_msg { position: absolute; top: 5px; right: 5px; }
</style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'edit-profile'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.seller.menu.edit_profile')
                        </div>
                        <div class="panel-body">
                            {!! Form::open(array('url'=>'/seller/edit-profile-save','id'=>'profile_form','files'=>true, 'class'=>"form-horizontal")) !!}

                            <?php
                            $seller = Auth::user()->getSeller;
                            ?>

                                <div class="col-md-12">
                                    <h4>@lang('messages.seller.edit_profile.business_info')</h4>
                                    <hr>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.business_info')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" required name="company_name"  value="{{$seller->company_name}}" class="form-control"
                                                   placeholder="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.store_name')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" readonly required name="store_name"  value="{{$seller->store_name}}"  class="form-control"
                                                   placeholder="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.business_email')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                          <div class="form_icon">
                                            <input type="email" required name="business_email" disabled autocomplete="off" id="business_email"  value="{{$seller->business_email}}" class="form-control"
                                                   placeholder="">
                                             <div class="form_icon_msg" id="business_email_icon"></div>
                                          </div>

                                           <p class="business_email_msg error_msg"></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.business_address')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <textarea name="business_address" id="" cols="30" rows="4"
                                                      class="form-control">{!! $seller->business_address !!}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.business_website')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="url" name="business_website"  value="{{$seller->business_website}}"  class="form-control"
                                                   placeholder="http://">
                                        </div>
                                    </div>


                                    <h4>@lang('messages.seller.edit_profile.contact_info')</h4>
                                    <hr>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.name')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" required class="form-control" name="full_name"
                                                   value="{{Auth::user()->username}}" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.email') <span class="nobr"><i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="top" title="" style="font-size: 16px;margin-top: 4px;" data-original-title="If you want to change the email please contract with the administrator."></i></span></label>
                                        </label>

                                        <div class="col-md-6 col-sm-8 ">
                                           <div class="form_icon">
                                             <input type="email" disabled autocomplete="off" value="{{Auth::user()->email}}" class="form-control"
                                                    placeholder="">
                                              <div class="form_icon_msg" id="email_id_icon"></div>
                                           </div>

                                            <p class="email_id_msg error_msg"></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.phone')</label>

                                        <div class="col-md-6 col-sm-8">
                                          <input type="text" size="5" name="country_code" required class="form-control numeric" placeholder="Code" value="{{Auth::user()->country_code}}" style="display: inline-block; width: auto;">
                                          <input style="display: inline-block; width: auto;" required size="15" type="tel" name="phone" id="phone" class="form-control numeric" value="{{Auth::user()->phone}}" placeholder="">
                                        </div>
                                    </div>

                                    <h4>@lang('messages.seller.edit_profile.address')</h4>
                                    <hr>

                                    <div class="form-group @if(!empty(Session::get('TOASTR_MESSAGE')) && empty($seller->country)) has-error @endif">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.country')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <select name="country" id="country_id" required class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($countries as $c)
                                                    <option @if($c->id == $seller->country) selected @endif value="{{$c->id}}">@if(\App\UtilityFunction::getLocal()=='en'){{$c->name}} @else {{$c->ar_name}}@endif</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <?php
                                    $cities_by_country = '';
                                    if(!empty($seller->country))  $cities_by_country = App\Model\City::where('country_id',$seller->country)->get();
                                    ?>
                                    <div class="form-group @if(!empty(Session::get('TOASTR_MESSAGE')) && empty($seller->city)) has-error @endif">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.city')</label>
                                        <div class="col-md-6 col-sm-8 ">
                                            <select name="city" id="city_id" class="form-control" required style="position: static;">
                                                <option value="">Select City</option>
                                                @if(!empty($seller->city) || !empty($cities_by_country[0]))
                                                    @foreach($cities_by_country as $city)
                                                        <option @if($city->id == $seller->city) selected @endif value="{{$city->id}}">@if(\App\UtilityFunction::getLocal()=='en') {{$city->name}} @else {{$city->ar_name}} @endif</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.street')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" class="form-control" required name="street"
                                                   value="{{$seller->street}}">
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.state')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" class="form-control" name="state"
                                                   value="{{$seller->state}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.zip')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="text" class="form-control" name="zip" value="{{$seller->zip}}">
                                        </div>
                                    </div>



                                    <h4>@lang('messages.seller.edit_profile.about_me')</h4>
                                    <hr>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.about_me')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <textarea name="about_me" id="" cols="30" rows="8"
                                                      class="form-control">{!! $seller->about_me !!}</textarea>
                                        </div>
                                    </div>

                                    <h4>@lang('messages.seller.edit_profile.photo')</h4>
                                    <hr>

                                    <div class="form-group">
                                        <label for="" class="col-md-3 col-sm-4 control-label">@lang('messages.seller.edit_profile.photo')</label>

                                        <div class="col-md-6 col-sm-8 ">
                                            <input type="file" name="profile_image" class="form-control">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary font-additional pull-right">
                                            @lang('messages.seller.edit_profile.update')
                                        </button>
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
    <script>
        setTimeout(function(){ $('#profile_form button[type="submit"]').removeClass('disabled'); }, 500);
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
                console.log(data);
            })
        });
    </script>

    <script type="text/javascript">
      $(document).ready(function () {
        var email_exist = false;

        function checkingBusinessEmail() {
          var Email = document.getElementById('business_email').value;

          $('#skip').val(1);
          $.ajax({
            type: "POST",
            url: $('#profile_form').attr('action') + '?email=' + Email + '&email_type=business',
            data: $('#profile_form').serialize(),
            dataType: "json",
            success: function (data) {
              email_exist = data.exists;

              if(email_exist == true){
                $(".business_email_msg").html('');
                $("#business_email_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
                $(".business_email_msg").html('@lang('messages.seller_registration.email_already_exists')');
                $("#business_email").focus();
                $('.font-additional').prop('disabled',true);
              }
              else {
                $("#business_email_icon").html('');
                $("#business_email_icon").html("");
                $("#business_email_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
                $('.font-additional').prop('disabled',false);
              }

              $('#skip').val(0);
            }
          }).fail(function (data) {
            var errors = data.responseJSON;
            console.log(errors);
          });
        }

        $(document.body).on('input','#business_email',function () {
          var Email = document.getElementById('business_email').value;
          var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

          var testEmail = regexEmail.test(Email);

          if (testEmail == true) {
            $(".business_email_msg").html('');
            $("#business_email_icon").html('');
            $("#business_email_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
            $('.font-additional').prop('disabled',false);
          }else {
            checkingBusinessEmail();
            $("#business_email_icon").html('');
            $("#business_email_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
            $(".business_email_msg").html("@lang('messages.seller_registration.please_email')");
            document.getElementById('business_email').focus();
            $('.font-additional').prop('disabled',true);
          }
        });

        function checkingPersonalEmail() {
          var Email = document.getElementById('email_id').value;

          $('#skip').val(1);
          $.ajax({
            type: "POST",
            url: $('#profile_form').attr('action') + '?email=' + Email + '&email_type=personal',
            data: $('#profile_form').serialize(),
            dataType: "json",
            success: function (data) {
              email_exist = data.exists;

              if(email_exist == true){
                $(".email_id_msg").html('');
                $("#email_id_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
                $(".email_id_msg").html('@lang('messages.seller_registration.email_already_exists')');
                $("#email_id").focus();
                $('.font-additional').prop('disabled',true);
              }
              else {
                $("#email_id_icon").html('');
                $(".email_id_msg").html("");
                $("#email_id_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
                $('.font-additional').prop('disabled',false);
              }

              $('#skip').val(0);
            }
          }).fail(function (data) {
            var errors = data.responseJSON;
            console.log(errors);
          });
        }

        $(document.body).on('input','#email_id',function () {
          var Email = document.getElementById('email_id').value;
          var regexEmail = /^[a-zA-Z]([a-zA-Z0-9_\-])+([\.][a-zA-Z0-9_]+)*\@((([a-zA-Z0-9\-])+\.){1,2})([a-zA-Z0-9]{2,40})$/;

          var testEmail = regexEmail.test(Email);

          if (testEmail == true) {
            checkingPersonalEmail();
            $(".email_id_msg").html('');
            $("#email_id_icon").html('');
            $("#email_id_icon").html("<i class='fa fa-check'></i>").css('color', '#ff8300');
            $('.font-additional').prop('disabled',false);
          }
          else {
            $("#email_id_icon").html('');
            $("#email_id_icon").html("<i class='fa fa-times'></i>").css('color', '#ff0000');
            $(".email_id_msg").html("@lang('messages.seller_registration.please_email')");
            document.getElementById('email_id').focus();
            $('.font-additional').prop('disabled',true);
          }
        });


        $(document.body).on('submit','#profile_form',function(){
          // checkingBusinessEmail();
          // checkingPersonalEmail();
        })

      });

    </script>
@stop
