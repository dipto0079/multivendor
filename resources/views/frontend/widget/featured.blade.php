<?php
    $three_latest_news = App\Model\News::getFeaturedNews(3);
?>
<div class="hp_banner_box">
    <div class="hp_banner_left">
        @foreach($three_latest_news as $news)
            <?php $ids[] = $news->id?>
            <div class="bl_single_news">
                @if($news->media_type == 2)
                    <a href="{{url('/news/'.$news->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
                @endif
                <img src="{{asset(Image::url($news->images[0]->location,276,450,array('crop')))}}" alt="" />
                <div class="bl_single_text"> <a href="{{url('/news/'.$news->id)}}" target="_blank">
                        <h4>{{$news->title}}</h4>
                    </a> <span><i class="fa fa-clock-o"></i> {{$news->created_at}}</span> </div>
            </div>
        @endforeach
    </div>

<?php
    $two_latest_news = App\Model\News::getFeaturedNews(2,$ids);
?>
    <div class="hp_banner_right">
        @foreach($two_latest_news as $news)
        <div class="br_single_news">
            @if($news->media_type == 2)
                <a href="{{url('/news/'.$news->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
            @endif
            <img src="{{asset(Image::url($news->images[0]->location,332,223,array('crop')))}}" alt="" />
            <div class="br_single_text"> <span class="green_hp_span">{{$news->category_name}}</span> <a href="{{url('/news/'.$news->id)}}" target="_blank">
                    <h4>{{$news->title}}</h4>
                </a> </div>
            <div class="br_cam"> <a href="{{url('/news-image-view/'.$news->images[0]->id)}}" target="_blank" class="fa fa-camera"></a> </div>
        </div>
        @endforeach
    </div>
</div>