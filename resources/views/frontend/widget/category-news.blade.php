<?php
    $categories = App\Model\News::getHomePageCategories(2);
?>

<div class="header_fasion gadgets_heading">
    <div class="left_fashion main_nav_box">
        <ul>
            <li class="nav_gadgets"><a href="">GADGETS</a></li>
        </ul>
    </div>
    <div class="fasion_right"> <a href=""><img src="{{asset('hash')}}/images/hor_dot.png" alt="" /></a> </div>
</div>
<div class="gadgets_area_box">
    <div class="row">
        @foreach($categories as $category)
        <?php
            $category_wise_news = App\Model\News::getCategoryWiseNews(4,$category->category_main_id);
            //$second_category_wise_news = App\Model\News::getCategoryWiseNews(4,$category_wise_news[0]->category_main_id);
        ?>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="fs_news_left fs_gadgets_news_left">
                    <div class="fs_news_left_img_g">
                        @if($category_wise_news[0]->media_type == 2)
                            <a href="{{url('/news/'.$category_wise_news[0]->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
                        @endif
                        <img src="{{url(Image::url($category_wise_news[0]->images[0]->location,399,280,array('crop')))}}" alt="" />
                        <div class="br_cam br_vid_big"> <a class="fa fa-caret-right" href=""></a> </div>
                    </div>
                    <div class="single_fs_news_left_text">
                        <h4><a href="{{url('/news/'.$category_wise_news[0]->id)}}" target="_blank">{{$category_wise_news[0]->title}}</a></h4>
                        <p> <i class="fa fa-clock-o"></i> {{$category_wise_news[0]->created_at}} <i class="fa fa-thumbs-up"></i> {{$category_wise_news[0]->likes}} </p>
                    </div>
                    <div class="all_news_right">
                        @for($i=1;$i<count($category_wise_news);$i++)
                            <div class="fs_news_right <?php if($i == count($category_wise_news)-1) echo 'last_right';?>">
                                <div class="single_fs_news_img">
                                    @if($category_wise_news[$i]->media_type == 2)
                                        <a href="{{url('/news/'.$category_wise_news[$i]->id)}}" target="_blank" style="margin: 0;"><div class="play-button large"></div></a>
                                    @endif
                                    <img src="{{url(Image::url($category_wise_news[$i]->images[0]->location,96,98,array('crop')))}}" alt="Single News" /> </div>
                                <div class="single_fs_news_right_text">
                                    <h4><a href="{{url('/news/'.$category_wise_news[$i]->id)}}" target="_blank">{{$category_wise_news[$i]->title}}</a></h4>
                                    <p> <a class="gad_color" href="{{url('/category/'.$category_wise_news[$i]->category_main_id.'/'.strtolower($category_wise_news[$i]->category_name))}}">{{$category_wise_news[$i]->category_name}} </a>| <i class="fa fa-clock-o"></i> {{$category_wise_news[$i]->created_at}} </p>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>