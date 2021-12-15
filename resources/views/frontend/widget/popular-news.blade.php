<?php
    $featured_popular_news = App\Model\News::getPopularNews(4);
?>

<!-- ~~~=| Fashion area START |=~~~ -->
<div class="fashion_area">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="header_fasion">
                <div class="left_fashion main_nav_box">
                    <ul>
                        <li class="nav_fashion"><a>Popular News</a></li>
                    </ul>
                </div>
                {{--<div class="fasion_right">--}}
                    {{--<ul>--}}
                        {{--<li><a href="">Style</a></li>--}}
                        {{--<li><a href="">smart living</a></li>--}}
                        {{--<li><a href="">fashion week</a></li>--}}
                        {{--<li class="last_item"><a href="">...</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}
            </div>
            <div class="fashion_area_box">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="fs_news_left">
                                <div class="single_fs_news_left_text">
                                    <div class="fs_news_left_img">
                                        @if($featured_popular_news[0]->media_type == 2)
                                            <a href="{{url('/news/'.$featured_popular_news[0]->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
                                        @endif
                                        <img src="{{asset(Image::url($featured_popular_news[0]->images[0]->location,399,270,array('crop')))}}" alt="" />
                                        <div class="br_cam br_vid_big_s"> <a class="fa fa-camera" href="{{url('/news-image-view/'.$featured_popular_news[0]->images[0]->id)}}" target="_blank"></a> </div>
                                    </div>
                                    <h4><a href="{{url('/news/'.$featured_popular_news[0]->id)}}" target="_blank">{{$featured_popular_news[0]->title}}</a></h4>
                                    <p> <i class="fa fa-clock-o"></i> {{$featured_popular_news[0]->created_at}} <i class="fa fa-thumbs-up"></i> {{$featured_popular_news[0]->likes}} </p>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="all_news_right">
                            @for($i=1;$i<count($featured_popular_news);$i++)
                            <div class="fs_news_right <?php if($i == count($featured_popular_news)-1) echo 'last_right';?>">
                                <div class="single_fs_news_img">
                                    @if($featured_popular_news[$i]->media_type == 2)
                                        <a href="{{url('/news/'.$featured_popular_news[$i]->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
                                    @endif
                                    <img src="{{url(Image::url($featured_popular_news[$i]->images[0]->location,96,98,array('crop')))}}" alt="Single News" /> </div>
                                <div class="single_fs_news_right_text">
                                    <h4><a href="{{url('/news/'.$featured_popular_news[$i]->id)}}" target="_blank">{{$featured_popular_news[$i]->title}}</a></h4>
                                    <p> <a href="{{url('/category/'.$featured_popular_news[$i]->category_main_id.'/'.strtolower($featured_popular_news[$i]->category_name))}}">{{$featured_popular_news[$i]->category_name}} </a>| <i class="fa fa-clock-o"></i> {{$featured_popular_news[$i]->created_at}} </p>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            {{----}}
            {{--<br>--}}
            {{--<div class="home_add_box"> <img src="{{asset('hash')}}/images/ht-add.jpg" alt="add" /> </div>--}}

            {{--<div class="news_pagination">--}}
                {{--<ul class="news_pagi">--}}
                    {{--<li><a href="">1</a></li>--}}
                    {{--<li><a href="">2</a></li>--}}
                    {{--<li><a href="">3</a></li>--}}
                    {{--<li class="dotlia"><a href="">. . .</a></li>--}}
                    {{--<li class="nextlia"><a href="">Next</a></li>--}}
                {{--</ul>--}}
            {{--</div>--}}
        </div>
    </div>
</div>
<!-- ~~~=| Fashion area END |=~~~ -->