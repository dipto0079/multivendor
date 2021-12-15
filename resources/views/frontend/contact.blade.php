@extends('frontend.master')
@section('title',__('messages.page_title.contact_us'))
@section('stylesheet')
<style media="screen">
  .form-control { padding: 10px; border-radius: 4px !important; -moz-border-radius: 4px !important; -webkit-border-radius: 4px !important;
      height: inherit; }
      textarea.form-control { resize: none; }
  form.form { margin-top: 35px; }
</style>
@stop

@section('content')
<div id="main" class="site-main">
<!-- ~~~=| Header END |=~~~ -->
<!-- ~~~=| Map area START |=~~~ -->
<!-- <div class="contact_message_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div id="googleMapHas" style="width:100%;height:470px;"></div>
            </div>
        </div>
    </div>
</div> -->
<!-- ~~~=| Map area END |=~~~ -->

<!-- ~~~=| Contact Message START |=~~~ -->
<section class="contact_message_area">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- ~~~=| form START |=~~~ -->
                <div class="contact-page-form">
                    <h2>@lang('messages.contact_us.send_us_an_email')</h2>
                    <div class="comments_form">
                        <form action="{{url('/contact-us-save')}}" class="form" method="post" id="contact_form">
                          {{csrf_field()}}
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input type="text" name="name" class="form-control" value="" placeholder="@lang('messages.contact_us.enter_your_name')" required>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input type="email" name="email" class="form-control" value="" placeholder="@lang('messages.contact_us.enter_your_email')" required>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-group">
                                <textarea name="message" rows="10" class="form-control" placeholder="@lang('messages.contact_us.enter_your_message')" required></textarea>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="g-recaptcha" data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}" style="display: inline-block;"></div>
                              <button type="submit" class="btn btn-primary pull-right" name="button">@lang('messages.contact_us.send_email')</button>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
                <!-- ~~~=| form END |=~~~ -->
            </div>
            <!--div class="col-md-6 col-sm-6 col-xs-12">
                <div class="contact_address">
                    <h2>Visit Us</h2>
                    <p>Hash is one of the excellent magazine in the world.Hash magazine reached many a
                        the readers very soon by his unique stories the magazine.Hash is known for his  to
                        excellent magazine.</p>
                    <div class="visit_us_contact">
                        <ul>
                            <li><a href="">
                                    <div class="visit_img"><img src="{{asset('hash')}}/images/map_ico.png" alt="map ico" /></div><div class="visit_text">Washington Square Park, New York, NY,
                                        <br>United States</div></a></li>
                            <li><a href="">
                                    <div class="visit_img"><img src="{{asset('hash')}}/images/ph_ico.png" alt="map ico" /></div><div class="visit_text">Telephone : +1 000 123 54657</div></a></li>
                            <li><a href="tell:+100012354657">
                                    <div class="visit_img"><img src="{{asset('hash')}}/images/msg_ico.png" alt="map ico" /></div><div class="visit_text">E-mail : Info@News.com</div></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- ~~~=| Fashion area END |=~~~ -->
        </div>
    </div>
</section>
<!-- ~~~=| Footer START |=~~~ -->
</div>
@stop

@section('script')
  {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
<script src='https://www.google.com/recaptcha/api.js'></script>

    <script src={{asset('/js/validator.min.js')}}></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#contact_form').validator().on('submit', function (e) {
          if (e.isDefaultPrevented()) {
            // handle the invalid form...
            console.log("validation failed");
          } else {
            // everything looks good!
            if(grecaptcha.getResponse() == '') {
              e.preventDefault();
              alert('Recaptcha is not checked.');
            }
            console.log("validation success");
          }
        });
    });


    </script>
@stop
