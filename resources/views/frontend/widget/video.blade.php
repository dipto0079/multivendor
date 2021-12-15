<?php
    $video_news = App\Model\News::getPopularNews(4,1);
?>
<div class="header_fasion gadgets_heading">
    <div class="left_fashion main_nav_box">
        <ul>
            <li class="nav_video_post"><a href="">Video</a></li>
        </ul>
    </div>
    <div class="fasion_right"> <a href=""><img src="{{asset('hash')}}/images/hor_dot.png" alt="" /></a> </div>
</div>
<div class="fashion_area_box video_area_box">
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="all_news_right">
                @for($i=1;$i<count($video_news);$i++)
                    <div class="fs_news_right <?php if($i == count($video_news)-1) echo 'last_right';?>">
                        <div class="single_fs_news_img">
                            <a href="{{url('/news/'.$video_news[$i]->id)}}" target="_blank" style="margin: 0;"><div class="play-button small"></div></a>
                            <img src="{{url(Image::url($video_news[$i]->images[0]->location,96,98,array('crop')))}}" alt="Single News" />
                            <div class="br_cam"> <a class="fa fa-caret-right" href="{{url('/news-image-view/'.$video_news[$i]->images[0]->id)}}" target="_blank"></a> </div>
                        </div>
                        <div class="single_fs_news_right_text">
                            <h4><a href="{{url('/news/'.$video_news[$i]->id)}}">{{$video_news[$i]->title}}</a></h4>
                            <p> <a class="video_f" href="{{url('/category/'.$video_news[$i]->category_main_id.'/'.strtolower($video_news[$i]->category_name))}}">{{$video_news[$i]->category_name}} </a>| <i class="fa fa-clock-o"></i> {{$video_news[$i]->created_at}} </p>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="fs_news_left fs_news_vid_right">
                <div class="single_fs_news_left_text">
                    <div class="fs_news_left_img">
                        <img src="{{url(Image::url($video_news[0]->images[0]->location,399,270,array('crop')))}}" alt="video" />
                        <a href="{{url('/news/'.$video_news[0]->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
                        <div class="br_cam br_vid_big"> <a class="fa fa-caret-right" href="{{url('/news-image-view/'.$video_news[0]->images[0]->id)}}" target="_blank"></a> </div>
                    </div>
                    <h4><a href="{{url('/news/'.$video_news[0]->id)}}">{{$video_news[0]->title}}</a></h4>
                </div>
            </div>
        </div>
    </div>
</div>
