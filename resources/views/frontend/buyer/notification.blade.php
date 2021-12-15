@extends('frontend.master',['menu'=>'newsletter'])
@section('title',__('messages.page_title.notification_list'))
@section('stylesheet')
    <style>
        .morecontent span {
            display: none;
        }
        .morelink {
            display: block;
        }
        .btn-group-sm>.btn, .btn-sm  { padding: 5px 10px !important; font-size: 12px !important; }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.buyer.sidebar-nav',['menu'=>'notification'])
                <div class="col-md-9 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('messages.page_title.notification_list')
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($notifications[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th class="product-remove" width="50"></th>
                                            <th class="product-name"><span class="nobr">@lang('messages.buyer.description')</span><a href="{{url('/buyer/notification-clear-all')}}" class="btn btn-primary btn-sm pull-right">@lang('messages.buyer.clear_all')</a></th>
                                            <th class="" width="70"><span class="nobr">@lang('messages.buyer.order_status')</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($notifications as $notification)
                                            <tr>
                                                <td class="product-remove">
                                                    <a href="{{url('/buyer/remove-notification/'.$notification->id)}}" class="remove remove_from_wishlist">×</a>
                                                </td>
                                                <td class="product-name">
                                                    <span class="more @if($notification->is_viewed == 1) read @endif" data-id="{{Crypt::encrypt($notification->id)}}">{!! $notification->description !!}</span>
                                                </td>
                                                <td class="status">
                                                    @if($notification->is_viewed == 0) <span style="color: red;">@lang('messages.unread')</span>
                                                    @else <span style="color: green;">@lang('messages.read')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.buyer.no_product_added')</h4></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script>
        $(document).ready(function() {
            // Configure/customize these variables.
            $(document).ready(function() {
                $('.more').each(function(event){ /* select all divs with the item class */
                    var max_length = 150; /* set the max content length before a read more link will be added */
                    if($(this).html().length > max_length){ /* check for content length */
                        var short_content 	= $(this).html().substr(0,max_length); /* split the content in two parts */
                        var long_content	= $(this).html().substr(max_length);

                        $(this).html(short_content+
                                ' <a href="javascript:;" class="read_more">...read more</a>'+
                                '<span class="more_text" style="display:none;">'+long_content+'</span>'); /* Alter the html to allow the read more functionality */
                        $(this).find('a.read_more').click(function(event){ /* find the a.read_more element within the new html and bind the following code to it */
                            event.preventDefault(); /* prevent the a from changing the url */
                            $(this).hide(); /* hide the read more button */
                            $(this).parents('.more').find('.more_text').show(); /* show the .more_text span */
                        });
                    }
                });
            });

            $(document.body).on('click','.more',function(e){

                var id = $(this).data('id');
                if(!$(this).hasClass("read")) {
                    $.ajax({
                        type: 'GET',
                        url: '{{url('/buyer/notification-read/')}}/'+id,
                        dataType: 'json',
                        context: this,
                        success: function(data){
                            if(data.success == true){
                                $(this).addClass('read');
                                $(this).closest('tr').find('.status').html('<span style="color: green;">@lang('messages.read')</span>');
                            }
                        }
                    }).fail(function(data){
                        var errors = data.responseJSON;
                    });
                }
            });
        });
    </script>
@stop
