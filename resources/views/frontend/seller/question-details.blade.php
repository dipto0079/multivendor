@extends('frontend.master',['menu'=>'products'])
@section('title','Edit Profile')
@section('stylesheet')
    <style>
        .comments-container {
            /*margin: 60px auto 15px;*/
            /*width: 768px;*/
            width: 100%;
        }

        .comments-container h1 {
            font-size: 36px;
            color: #283035;
            font-weight: 400;
        }

        .comments-container h1 a {
            font-size: 18px;
            font-weight: 700;
        }

        .comments-list {
            margin-top: 30px;
            position: relative;
        }

        /**
         * Lineas / Detalles
         -----------------------*/
        .comments-list:before {
            content: '';
            width: 2px;
            height: 100%;
            background: #c7cacb;
            position: absolute;
            left: 32px;
            top: 0;
        }

        .comments-list:after {
            content: '';
            position: absolute;
            background: #c7cacb;
            bottom: 0;
            left: 27px;
            width: 7px;
            height: 7px;
            border: 3px solid #dee1e3;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
        }

        .reply-list:before, .reply-list:after {display: none;}
        .reply-list li:before {
            content: '';
            width: 60px;
            height: 2px;
            background: #c7cacb;
            position: absolute;
            top: 25px;
            left: -55px;
        }


        .comments-list li {
            margin-bottom: 15px;
            display: block;
            position: relative;
        }

        .comments-list li:after {
            content: '';
            display: block;
            clear: both;
            height: 0;
            width: 0;
        }

        .reply-list {
            padding-left: 88px;
            clear: both;
            margin-top: 15px;
        }
        /**
         * Avatar
         ---------------------------*/
        .comments-list .comment-avatar {
            width: 65px;
            height: 65px;
            position: relative;
            z-index: 99;
            float: left;
            border: 3px solid #FFF;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            -moz-box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            overflow: hidden;
            margin-right: 15px;
            background-color: #fff;
        }

        .comments-list .comment-avatar img {
            width: 100%;
            height: 100%;
        }

        .reply-list .comment-avatar {
            width: 50px;
            height: 50px;
        }

        .comment-main-level:after {
            content: '';
            width: 0;
            height: 0;
            display: block;
            clear: both;
        }
        /**
         * Caja del Comentario
         ---------------------------*/
        .comments-list .comment-box {
            width: 90%;
            float: right;
            position: relative;
            -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.15);
            -moz-box-shadow: 0 1px 1px rgba(0,0,0,0.15);
            box-shadow: 0 1px 1px rgba(0,0,0,0.15);
        }

        .comments-list .comment-box:before, .comments-list .comment-box:after {
            content: '';
            height: 0;
            width: 0;
            position: absolute;
            display: block;
            border-width: 10px 12px 10px 0;
            border-style: solid;
            border-color: transparent #FCFCFC;
            top: 8px;
            left: -11px;
        }

        .comments-list .comment-box:before {
            border-width: 11px 13px 11px 0;
            border-color: transparent rgba(0,0,0,0.05);
            left: -12px;
        }

        .reply-list .comment-box {
            width: 90%;
        }
        .comment-box .comment-head {
            background: #FCFCFC;
            padding: 10px 12px;
            border-bottom: 1px solid #E5E5E5;
            overflow: hidden;
            -webkit-border-radius: 4px 4px 0 0;
            -moz-border-radius: 4px 4px 0 0;
            border-radius: 4px 4px 0 0;
        }

        .comment-box .comment-head i {
            float: right;
            margin-left: 14px;
            position: relative;
            top: 2px;
            color: #A6A6A6;
            cursor: pointer;
            -webkit-transition: color 0.3s ease;
            -o-transition: color 0.3s ease;
            transition: color 0.3s ease;
        }

        .comment-box .comment-head i:hover {
            color: #03658c;
        }

        .comment-box .comment-name {
            color: #283035;
            font-size: 14px;
            font-weight: 700;
            float: left;
            margin-right: 10px;
        }

        .comment-box .comment-name a {
            color: #283035;
        }

        .comment-box .comment-head span {
            float: left;
            color: #999;
            font-size: 13px;
            position: relative;
            top: 1px;
        }

        .comment-box .comment-content {
            background: #FFF;
            padding: 12px;
            font-size: 15px;
            color: #595959;
            -webkit-border-radius: 0 0 4px 4px;
            -moz-border-radius: 0 0 4px 4px;
            border-radius: 0 0 4px 4px;
            word-wrap: break-word;
        }

        .comment-box .comment-name.by-author, .comment-box .comment-name.by-author a {color: #03658c;}
        .comment-box .comment-name.by-author:after {
            content: 'autor';
            background: #03658c;
            color: #FFF;
            font-size: 12px;
            padding: 3px 5px;
            font-weight: 700;
            margin-left: 10px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
        }

        /** =====================
         * Responsive
         ========================*/
        @media only screen and (max-width: 766px) {
            .comments-container {
                width: 480px;
            }

            .comments-list .comment-box {
                width: 390px;
            }

            .reply-list .comment-box {
                width: 320px;
            }
        }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'question'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.seller.question_page.question_details')
                        </div>
                        <div class="panel-body">
                            @if(!empty($question))
                            <div class="comments-container">
                                <ul id="comments-list" class="comments-list">
                                    <li>
                                        <div class="comment-main-level">
                                            <!-- Avatar -->
                                            <div class="comment-avatar">
                                              <img class="img_background" src="{{asset('/image/default.jpg')}}"
                                            @if(!empty($question->getUser->photo))
                                             data-src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $question->getUser->photo), 50, 50, ['crop'])?>"
                                             @else
                                               data-src="{{asset('image/default_author.png')}}"
                                             @endif alt=""></div>
                                            <!-- Contenedor del Comentario -->
                                            <div class="comment-box">
                                                <div class="comment-head">
                                                    <h6 class="comment-name by-author">{{$question->getUser->username}}</h6>
                                                    <span>{{Carbon\Carbon::createFromTimeStamp(strtotime($question->created_at))->diffForHumans()}}</span>
                                                </div>
                                                <div class="comment-content">{{$question->title}}</div>
                                            </div>
                                        </div>
                                        <?php $question_answers = $question->getQuestionAnswer; ?>
                                        <!-- Respuestas de los comentarios -->
                                        @if(!empty($question_answers[0]))
                                        <ul class="comments-list reply-list">
                                            @foreach($question_answers as $question_answer)
                                                <li>
                                                    <!-- Avatar -->
                                                    <div class="comment-avatar">
                                                      <img class="img_background" src="{{asset('/image/default.jpg')}}"
                                                      @if(!empty($question_answer->getUser->photo))
                                                       data-src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $question_answer->getUser->photo), 50, 50, ['crop'])?>"
                                                       @else
                                                       data-src="{{asset('image/default_author.png')}}"
                                                       @endif alt="">
                                                     </div>
                                                    <!-- Contenedor del Comentario -->
                                                    <div class="comment-box">
                                                        <div class="comment-head">
                                                            <h6 class="comment-name">{{$question_answer->getUser->username}}</h6>
                                                            <span>{{Carbon\Carbon::createFromTimeStamp(strtotime($question_answer->created_at))->diffForHumans()}}</span>
                                                        </div>
                                                        <div class="comment-content">{{$question_answer->answer}}</div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>

                                        @endif
                                    </li>
                                </ul>
                            </div>
                            @endif
                            <hr>
                            {!! Form::open(['url'=>'/seller/question/reply/save']) !!}
                            <div class="form-group">
                                <textarea name="answer" maxlength="512" class="form-control" id="" rows="4" required></textarea>
                                <p class="charsRemaining text-right">
                                    @if(\App\UtilityFunction::getLocal()== "en")
                                        You have 512 characters remaining
                                    @else
                                        لديك 512 الأحرف المتبقية
                                    @endif
                                </p>
                                <br>
                                <button class="btn btn-primary pull-right" type="submit">@lang('messages.seller.question_page.send')</button>
                                <input type="hidden" name="q_skip" value="{{Crypt::encrypt($question->id)}}">
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script type="text/javascript">
      $(document).ready(function(){
          $('textarea[maxlength]').keyup(function(){
              var max = parseInt($(this).attr('maxlength'));
              if($(this).val().length > max){
                  $(this).val($(this).val().substr(0, $(this).attr('maxlength')));
              }

              $(this).parent().find('.charsRemaining').html('You have ' + (max - $(this).val().length) + ' characters remaining');
          });
        });
    </script>
@stop
