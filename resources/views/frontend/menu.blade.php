<div class="container hidden-xs">
    <div class="row">
        <div class="col-sm-3">
            <div class="logo"><a href="{{url('/')}}"><img alt="Discount Deals, Offers at savetaka" src="{{asset('')}}/image/logo.png"/></a></div>
        </div>
        <div class="col-sm-9">
            <div class="navbar navbar-default pull-right" style="margin-top: 18px;margin-right: -12px;">
                <ul class="nav navbar-nav">
                    
                    <li @if(!empty($menu) && $menu == 'home') class="active" @endif><a href="{{url('/')}}">@lang('messages.menu.home')</a></li>
                    <li @if(!empty($menu) && $menu == 'product') class="active" @endif><a href="{{url('/products')}}">@lang('messages.menu.products')</a></li>
                    <li @if(!empty($menu) && $menu == 'services') class="active" @endif><a href="{{url('/service')}}">@lang('messages.menu.services')</a></li>
                    <li @if(!empty($menu) && $menu == 'deals') class="active" @endif><a href="{{url('/deals')}}">@lang('messages.menu.deals')</a></li>
                    <li @if(!empty($menu) && $menu == 'stores') class="active" @endif><a href="{{url('/stores')}}">@lang('messages.menu.stores')</a></li>

                    {{--<li><a href="#">Help</a></li>--}}
                    {{--<li><a style="color:#ff8300;" href="#">Promotions <i class="fa fa-arrow-right " aria-hidden="true"></i></a></li>--}}
                </ul>
            </div>
        </div>
    </div>
</div>