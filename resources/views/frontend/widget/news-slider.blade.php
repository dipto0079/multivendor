<?php
    $news_slider = App\Model\News::getNewsSlider(env('WIDGET_SLIDER_IMAGE'));
?>
<div class="follow_us_side">
    <h2>Image Gallery</h2>
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            @for($i=0;$i<count($news_slider);$i++)
                <li data-target="#carousel-example-generic" data-slide-to="{{$i}}" @if($i == 0) class="active" @endif></li>
            @endfor
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            @for($i=0;$i<count($news_slider);$i++)
                <div class="item @if($i == 0) active @endif">
                    <div class="br_single_news">
                        @if($news_slider[$i]->media_type == 2)
                            <a href="{{url('/news/'.$news_slider[$i]->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
                        @endif
                        <img alt="" src="{{url(Image::url($news_slider[$i]->images[0]->location,300,250,array('crop')))}}">
                        <div class="br_cam">
                            <a class="fa fa-camera" href="{{url('/news-image-view/'.$news_slider[$i]->images[0]->id)}}" target="_blank"></a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>