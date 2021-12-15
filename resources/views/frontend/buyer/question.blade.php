@extends('frontend.master',['menu'=>'question'])
@section('title','Edit Profile')
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'question'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.buyer.question_page.question_heading')
                            <a href="#question_modal" data-toggle="modal" class="btn btn-primary pull-right">@lang('messages.buyer.question_page.add_new_question')</a>
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($questions[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-remove" width="50"></th>
                                            <th class="product-name"><span class="nobr">@lang('messages.buyer.question_page.title')</span></th>
                                            <th class="product-price" width="100"><span class="nobr">@lang('messages.buyer.question_page.created')</span></th>
                                            <th class="product-add-to-cart" width="100"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($questions as $question)
                                            <tr>
                                                <td class="product-remove">
                                                    <a href="{{url('/buyer/question/delete/'.$question->id)}}"
                                                       class="remove remove_from_wishlist">×</a>
                                                </td>
                                                <td class="product-name">
                                                    {{$question->title}}
                                                </td>
                                                <td class="product-name">{{date('d-m-Y',strtotime($question->created_at))}}</td>
                                                <td class="product-add-to-cart">
                                                    <a href="{{url('/buyer/question/details/'.$question->id)}}" class="order_view btn btn-primary">@lang('messages.view')</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.buyer.order_no_order_available')</h4></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="question_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang('messages.buyer.question_page.add_question')</h4>
                </div>
                {!! Form::open(['url'=>'/buyer/question/save']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">@lang('messages.buyer.question_page.question_title')</label>
                        <textarea name="question" maxlength="512" class="form-control" id="" rows="5" required></textarea>
                        <p class="charsRemaining text-right">
                            @if(\App\UtilityFunction::getLocal()== "en")
                                You have 512 characters remaining
                            @else
                                لديك 512 الأحرف المتبقية
                            @endif
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.seller.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.seller.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
@stop
