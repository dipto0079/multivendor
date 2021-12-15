<section id="subscribe" class="subscribe-row background-container">
    <div class="container">
        <div class="subscribe-container clearfix wow fadeInUp" data-wow-delay="0.3s">
            <div class="subscribe-desc font-additional font-weight-bold">@lang('messages.footer.newsletter')</div>
            <div id="mc_embed_signup" class="subscribe-form">
                <form action="{{url('/email-subscribe')}}"
                      method="post" id="subscribe_form" name="mc-embedded-subscribe-form"
                      class="validate">
                    {{csrf_field()}}
                    <div id="mc_embed_signup_scroll">
                        <div class="mc-field-group subscribe-field">
                            <input type="text" value="" name="email" class="required email font-main color-third"
                                   id="email_subscribe">
                            <p id="email_subscribe_msg" style="height: 20px; margin-bottom: 0;"></p>
                        </div>
                        <div id="mce-responses" class="clear">
                            <div class="response" id="mce-error-response" style="display:none"></div>
                            <div class="response" id="mce-success-response" style="display:none"></div>
                        </div>
                        <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                        <div class="subscribe-button">
                            <button type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe"
                                    class="btn btn-primary font-additional hvr-wobble-bottom"> @lang('messages.footer.subscribe')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<footer>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <div class="footer-logo">
                        <img alt="Footer-logo"
                             src="{{asset('')}}\image\logo.png"/>
                    </div>


                    <div class="footer-desc">
                        <?php $about_us = \App\Model\StaticPage::where('key_word',\App\Http\Controllers\Enum\StaticPageEnum::ABOUT_US)->first(); ?>
                        <p>@if(\App\UtilityFunction::getLocal()== "en")
                                {!! str_limit($about_us->description,200,'...') !!}
                           @else
                                {!! str_limit($about_us->ar_description,500,'...') !!}
                            @endif
                        </p>
                    </div>


                    <div class="">
                        <a target="_blank" rel="nofollow" href="https://www.facebook.com/whitebazar"
                           class="icon-link  facebook fill"><i class="fa fa-facebook"></i></a>
                        {{--<a target="_blank" rel="nofollow" href="#"--}}
                           {{--class="icon-link linkedin fill"><i class="fa fa-linkedin"></i></a>--}}
                        <a href="https://twitter.com/_whitebazar" target="_blank" class="icon-link  twitter fill"><i
                                    class="fa fa-twitter"></i></a>
                        {{--<a target="_blank" rel="nofollow" href="#"--}}
                           {{--class="icon-link google-plus fill"><i class="fa fa-google-plus"></i></a>--}}

                        <a target="_blank" rel="nofollow" href="https://www.instagram.com/_whitebazar/"
                           class="icon-link instagram fill"><i class="fa fa-instagram"></i></a>

                        <a target="_blank" rel="nofollow" href="https://www.pinterest.com/whitebazaar/"
                           class="icon-link pinterest fill"><i class="fa fa-pinterest-p"></i></a>


                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="footer-menu">
                        <h3>{{env('APP_NAME_'.\App\UtilityFunction::getLocal())}}</h3>
                        <ul>
                            <li style="padding-top: 10px;"><a href="{{url('/about-us')}}">@lang('messages.footer.about_us')</a></li>
                            <li><a href="{{url('/contact-us')}}">@lang('messages.footer.contact_us')</a></li>
{{--                            <li><a href="{{url('/press')}}">@lang('messages.footer.press')</a></li>--}}
                            <li><a href="{{url('/support')}}">@lang('messages.footer.support')</a></li>
                            <li><a href="{{url('/privacy-policy')}}">@lang('messages.footer.pp')</a></li>
                            <li><a href="{{url('/term-and-condition')}}">@lang('messages.footer.tc')</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="footer-menu">
                        <h3>@lang('messages.footer.seller')</h3>
                        <ul>

                            <li style="padding-top: 10px;"><a href="{{url('/seller/login')}}">@lang('messages.footer.seller_signin')</a></li>
                            <li><a href="{{url('/seller-registration')}}">@lang('messages.footer.seller_signup')</a></li>
                        </ul>
                    </div>
                    <div class="footer-menu">
                        <h3 style="padding-top: 10px;">@lang('messages.footer.buyer')</h3>
                        <ul>
                            <li style="padding-top: 10px;"><a href="{{url('/buyer/login')}}">@lang('messages.footer.buyer_signin')</a></li>
                            <li><a href="{{url('/buyer-registration')}}">@lang('messages.footer.buyer_signup')</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="apps">
                        <a href="#"><img class="img-responsive" alt=""
                                         src="{{asset('')}}\image\pstore.png"/></a>
                    </div>
                    <div class="copyright">
                        {{--<p>Copyright &copy;&nbsp; 2017 &nbsp;<a href="{{url('')}}" rel="nofollow">{{env('APP_NAME_'.\App\UtilityFunction::getLocal())}}</a> Ltd. All Rights Reserved.</p>--}}
                        <p>@lang('messages.footer.copyright')</p>
                    </div>
                    <div class="payment_method">
                        <span>@lang('messages.footer.payment')</span>
                        <img class="img-responsive" alt=""
                             src="{{asset('')}}\image\pg.png"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
