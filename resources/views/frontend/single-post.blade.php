@extends('frontend.master')
@section('title','Single Post')
@section('stylesheet')
@stop

@section('content')
<!-- ~~~=| Header END |=~~~ -->

<section class="main_news_wrapper cc_single_post_wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-sm-9 col-xs-12">
                <!-- ~~~=| Fashion area START |=~~~ -->
                <div class="cc_single_post">
                    <div class="bsp_img">
                        <img src="{{asset('hash')}}/images/blog-s-p1.jpg" alt="blog single post" />
                    </div>
                    <div class="sp_details">
                        <a href="">Sports</a>
                        <h2>52 Disney Animated Movie’s about Locations Mapped</h2>
                        <div class="post_meta">
                            <ul>
                                <li><a href=""><i class="fa fa-user"></i>By luck Walker</a></li>
                                <li><a href=""><i class="fa fa-eye"></i>500</a></li>
                                <li><a href=""><i class="fa fa-comment-o"></i>45</a></li>
                            </ul>
                        </div>
                        <div class="post_text">
                            <p>News is one of the excellent magazine in the world.News  magazine reached many readers very soon by his unique stories in the magazine."But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the  a system, at expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, are avoids pleasure i
                                tself, because sure rationally encounter consequences theare extremely painful. </p>
                            <div class="post_inner">
                                <p>News is one of the excellent magazine in the world.News  magazine reached many readers very soon by his unique stories in the magazine."But I must to explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete are the master-builder of  the
                                    human happiness. </p>
                            </div>
                            <p>News is one of the excellent magazine in the world.News  magazine reached many readers very soon by his unique stories in the magazine."But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the  a system, at expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, are avoids pleasure i
                                tself, because sure rationally encounter consequences theare extremely painful.</p>
                        </div>
                        <div class="social_tags">
                            <div class="social_tags_left">
                                <p>Tags :</p>
                                <ul>
                                    <li><a href="">Photography</a></li>
                                    <li><a href="">Content</a></li>
                                    <li><a href="">News</a></li>
                                </ul>
                            </div>
                            <div class="social_tags_right">
                                <ul>
                                    <li class="facebook"><a class="fa fa-facebook" href=""></a></li>
                                    <li class="twitter"><a class="fa fa-twitter" href=""></a></li>
                                    <li class="google-plus"><a class="fa fa-google-plus" href=""></a></li>
                                    <li class="linkedin"><a class="fa fa-linkedin" href=""></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="sp-next-prev">
                            <div class="sp-prev">
                                <a href=""><i class="fa fa-angle-double-left"></i>Previous post</a>
                            </div>
                            <div class="sp-next">
                                <a href="">Previous post<i class="fa fa-angle-double-right"></i></a>
                                <div class="sp-next-post">
                                    <a href="">What Do I Need To Make It In <br>Business?</a>
                                </div>
                            </div>
                        </div>
                        <div class="sp-comments-box">
                            <h2>Comments</h2>
                            <div class="single_comment">
                                <div class="single_comment_pic">
                                    <img src="{{asset('hash')}}/images/comment-pic1.png" alt="" />
                                </div>
                                <div class="single_comment_text">
                                    <div class="sp_title">
                                        <a href=""><h4>Chris Hemsworth</h4></a>
                                        <p>10 Min ago</p>
                                    </div>
                                    <p>They call him Flipper Flipper faster than lightning. No one you see is smarter than he. They call him Flipper Flipper the faster than lightning. No one you see is smarter than he</p>
                                    <a href=""><i class="fa fa-reply"></i>Reply</a>
                                </div>
                            </div>
                            <div class="single_comment single_comment_middle">
                                <div class="single_comment_pic">
                                    <img src="{{asset('hash')}}/images/comment-pic2.png" alt="" />
                                </div>
                                <div class="single_comment_text">
                                    <div class="sp_title">
                                        <a href=""><h4>Chris Hemsworth</h4></a>
                                        <p>10 Min ago</p>
                                    </div>
                                    <p>They call him Flipper Flipper faster than lightning. No one you see is smarter than he. They call him Flipper Flipper the faster than lightning. No one you see is smarter than he</p>
                                    <a href=""><i class="fa fa-reply"></i>Reply</a>
                                </div>
                            </div>
                            <div class="single_comment single_comment_last">
                                <div class="single_comment_pic">
                                    <img src="{{asset('hash')}}/images/comment-pic3.png" alt="" />
                                </div>
                                <div class="single_comment_text">
                                    <div class="sp_title">
                                        <a href=""><h4>Chris Hemsworth</h4></a>
                                        <p>10 Min ago</p>
                                    </div>
                                    <p>They call him Flipper Flipper faster than lightning. No one you see is smarter than he. They call him Flipper Flipper the faster than lightning. No one you see is smarter than he</p>
                                    <a href=""><i class="fa fa-reply"></i>Reply</a>
                                </div>
                            </div>
                        </div>
                        <div class="comment-form">
                            <h2>leave your comments</h2>
                            <div class="comments_form">
                                <form>
                                    <div class="inp_name">
                                        <input id="c_name" type="text" placeholder="Your Name" required/>
                                        <input type="text" placeholder="Your Name" required/>
                                    </div>
                                    <textarea cols="30" rows="10" placeholder="Message"></textarea>
                                    <input type="submit" value="Send Message"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ~~~=| Fashion area END |=~~~ -->
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="home_sidebar">
                    @include('frontend.widget.advertise')
                    @include('frontend.widget.follow-us')
                    @include('frontend.widget.latest-news')
                    @include('frontend.widget.news-slider')
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ~~~=| Footer START |=~~~ -->
@stop

@section('script')
@stop