@extends('admin.master')
@section('title','Static Page List')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('/build/css/lib/summernote/summernote.css')}}"/>
    <link rel="stylesheet" href="{{asset('/build/css/separate/pages/editor.min.css')}}">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/pages/activity.min.css">
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/pages/chat.min.css">
    <style>
        .chat-area-header { height: auto; }
        .chat-list-item .chat-list-item-name .name, .chat-list-item .chat-list-item-txt {
            overflow: hidden; -o-text-overflow: initial; text-overflow: initial; white-space: initial;
        }
        .chat-message-txt { word-wrap: break-word; }
    </style>
@stop

@section('content')
    @include('admin.settings.submenu',array('page'=>"question"))

    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Question Details</div>
                                <div class="col-sm-8">
                                    @if(!empty(Session::get('message')))
                                        <div class="alert alert-danger alert-icon alert-close alert-dismissible fade in" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                            <i class="font-icon font-icon-warning"></i>
                                            {{Session::get('message')}}
                                        </div>
                                    @endif
                                </div>
                            </h3>
                        </div>
                        {{--<div class="tbl-cell tbl-cell-action-bordered">--}}
                            {{--<a href="#form_modal" data-toggle="modal" class="btn" id="add_btn" data-id="add">Add New Static Page</a>--}}
                        {{--</div>--}}
                    </div>
                </header>
            </section><!--.box-typical-->

            <div class="box-typical chat-container">

                <section class="chat-area">

                    <div class="chat-area-header">
                        <div class="chat-list-item">
                            <div class="chat-list-item-photo">
                                <img
                                     @if(!empty($question->getUser->photo))
                                     src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $question->getUser->photo), 50, 50, ['crop'])?>"
                                     @else
                                     src="{{asset('image/default_author.png')}}"
                                @endif alt="">
                            </div>
                            <div class="chat-list-item-name">
                                <span class="name">{{$question->getUser->username}}</span>
                            </div>
                            <div class="chat-list-item-txt writing">{{$question->title}}</div>
                        </div>

                    </div>

                    <?php $question_answers = $question->getQuestionAnswer; ?>
                    <div class="chat-dialog-area scrollable-block">
                        @if(isset($question_answers[0]))
                            @foreach($question_answers as $question_answer)
                                <div class="chat-message">
                                    <div class="chat-message-photo">
                                        <img
                                                @if(!empty($question_answer->getUser->photo))
                                                src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $question_answer->getUser->photo), 50, 50, ['crop'])?>"
                                                @else
                                                src="{{asset('image/default_author.png')}}"
                                        @endif alt="">
                                    </div>
                                    <div class="chat-message-header">
                                        <div class="tbl-row">
                                            <div class="tbl-cell tbl-cell-name">{{$question_answer->getUser->username}}</div>
                                            <div class="tbl-cell tbl-cell-date">{{date('F j, Y',strtotime($question_answer->created_at))}}</div>
                                        </div>
                                    </div>
                                    <div class="chat-message-content">
                                        <div class="chat-message-txt">{{$question_answer->answer}}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif





                    </div>

                    <div class="chat-area-bottom">
                        {!! Form::open(['url'=>'admin/settings/question/reply','class'=>'write-message','id'=>'reply_question']) !!}
                        <div class="avatar">
                            <img @if(!empty(Auth::user()->photo))
                                 src="<?=Image::url(asset(env('USER_PHOTO_PATH') . Auth::user()->photo), 50, 50, ['crop'])?>"
                                 @else
                                 src="{{asset('image/default_author.png')}}"
                                 @endif
                                    alt="">
                        </div>
                        <div class="form-group">
                            <textarea rows="4" name="answer" class="form-control maxlength-simple" maxlength="512" placeholder="Type your Answer"></textarea>
                        </div>
                        <input type="hidden" name="question_id" value="{{$question->id}}">
                        <button type="submit" class="btn btn-rounded float-left">Send</button>
                        {!! Form::close() !!}
                    </div>

                </section>
            </div>

            <br>
        </div><!--.container-fluid-->
    </div>
    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/settings/static-page/save','id'=>'modal_form','files'=>true)) !!}
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center load_image" style="margin-top: 23px;">
                        <img src="{{asset('build/img/ring-alt.gif')}}" style="width:50px;"
                             alt="">

                        <div>Loading</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src="{{asset('/build/js/lib/summernote/summernote.min.js')}}"></script>
    <script>
        function getEditor(){
            $(document).ready(function() {
                $('.editor_s').summernote({
                    height: 200,                 // set editor height
                    minHeight: null,             // set minimum height of editor
                    maxHeight: null,             // set maximum height of editor
                    focus: true                  // set focus to editable area after initializing summernote
                });
            });
        }
    </script>
    <script>
        $(document).ready(function () {
            $("#add_btn").click(function () {
                var id = $(this).data('id');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?add_id=' + id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Static Page Add');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        getEditor();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
            $(".edit_btn").click(function () {
                var id = $(this).data('id');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?edit_id=' + id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.modal-title').html('Static Page Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();
                        getEditor();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
    <script src="{{asset('/build/js/lib/autosize/autosize.min.js')}}"></script>
  	<script src="{{asset('/build/js/lib/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
  	<script src="{{asset('/build/js/lib/bootstrap-maxlength/bootstrap-maxlength-init.js')}}"></script>
    <script>
  		$(function() {
  			autosize($('textarea[data-autosize]'));
  		});
  	</script>
@stop
