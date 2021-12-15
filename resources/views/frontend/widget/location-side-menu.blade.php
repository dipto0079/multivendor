<?php $cities = App\Model\City::getCity(); ?>

@if(isset($cities[0]))
<ul class="categories-tree wow fadeInUp" data-wow-delay="0.3s">
    @foreach($cities as $city)
        <?php $locations = $city->getLocationByCity ?>
    <li>
        <a href="#{{$city->id}}" data-toggle="collapse" class="city_location font-additional font-weight-normal hover-focus-color color-third text-uppercase">
            <span class="pull-left">{{$city->name}}</span>
            <span class="pull-right">(80)</span>
            @if(isset($locations[0]))<span class="icon fa fa-caret-right"></span>@endif
        </a>
        @if(isset($locations[0]))
        <ul class="collapse" id="{{$city->id}}">
            @foreach($locations as $location)
                <li><a href="javascript:void(0)">{{$location->name}} <span class="pull-right">(2)</span></a></li>
            @endforeach
        </ul>
        @endif
    </li>
    @endforeach
</ul>
@endif