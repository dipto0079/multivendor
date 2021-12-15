
<?php $latest_news = App\Model\News::getLatestNews(); $i=0; ?>
<div class="follow_us_side">
    <h2>Latest News</h2>
    <div class="all_news_right">
        @foreach($latest_news as $news)
            <div class="fs_news_right <?php if($i == count($latest_news)-1) echo 'last_right';?>">
                <div class="single_fs_news_img">
                    @if($news->media_type == 2)
                        <a href="{{url('/news/'.$latest_news[$i]->id)}}" target="_blank" style="margin: 0;"><div class="play-button small"></div></a>
                    @endif
                    <img alt="Single News" src="{{url(Image::url($news->images[0]->location,80,80,array('crop')))}}">
                </div>
                <div class="single_fs_news_right_text">
                    <h4><a href="{{url('/news/'.$news->id)}}" target="_blank">{{$news->title}}</a></h4>
                    <p><a href="{{url('/category/'.$news->category_main_id.'/'.strtolower($news->category_name))}}">{{$news->category_name}}</a> | <i class="fa fa-clock-o"></i> {{$news->created_at}}
                    </p>
                </div>
            </div>
            <?php $i++; ?>
        @endforeach
        {{--<div class="fs_news_right">--}}
            {{--<div class="single_fs_news_img">--}}
                {{--<img alt="Single News" src="{{asset('hash')}}/images/side2.jpg">--}}
            {{--</div>--}}
            {{--<div class="single_fs_news_right_text">--}}
                {{--<h4><a href="">Thoughts on remaining casually chic</a></h4>--}}
                {{--<p>--}}
                    {{--<a href="">Fashion | </a>--}}
                    {{--<i class="fa fa-clock-o"></i>--}}
                    {{--1 hour ago--}}
                {{--</p>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="fs_news_right last_right">--}}
            {{--<div class="single_fs_news_img">--}}
                {{--<img alt="Single News" src="{{asset('hash')}}/images/side3.jpg">--}}
            {{--</div>--}}
            {{--<div class="single_fs_news_right_text">--}}
                {{--<h4><a href="">Thoughts on remaining casually chic</a></h4>--}}
                {{--<p>--}}
                    {{--<a href="">Fashion | </a>--}}
                    {{--<i class="fa fa-clock-o"></i>--}}
                    {{--1 hour ago--}}
                {{--</p>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="fs_news_right last_right">--}}
            {{--<div class="single_fs_news_img">--}}
                {{--<img alt="Single News" src="{{asset('hash')}}/images/side4.jpg">--}}
            {{--</div>--}}
            {{--<div class="single_fs_news_right_text">--}}
                {{--<h4><a href="">Thoughts on remaining casually chic</a></h4>--}}
                {{--<p>--}}
                    {{--<a href="">Fashion | </a>--}}
                    {{--<i class="fa fa-clock-o"></i>--}}
                    {{--1 hour ago--}}
                {{--</p>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="fs_news_right last_right">--}}
            {{--<div class="single_fs_news_img">--}}
                {{--<img alt="Single News" src="{{asset('hash')}}/images/side5.jpg">--}}
            {{--</div>--}}
            {{--<div class="single_fs_news_right_text">--}}
                {{--<h4><a href="">Thoughts on remaining casually chic</a></h4>--}}
                {{--<p>--}}
                    {{--<a href="">Fashion | </a>--}}
                    {{--<i class="fa fa-clock-o"></i>--}}
                    {{--1 hour ago--}}
                {{--</p>--}}
            {{--</div>--}}
        {{--</div>--}}
    </div>
</div>

