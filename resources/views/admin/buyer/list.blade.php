@extends('admin.master')
@section('title','Buyer List')
@section('stylesheet')
    <style type="text/css">
        .show { display: block; }
        hr { margin: 1em 0; }
    </style>
@stop

@section('content')
    <div class="page-content" style="padding-left: 115px;">
        <div class="container-fluid">
            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Buyer List</div>
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
                        <div class="tbl-cell tbl-cell-action-bordered" style="width: 30%;">
                            {!! Form::open(['url'=>'admin/buyer']) !!}
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="typeahead-container">
                                        <div class="typeahead-field">
									<span class="typeahead-query">
										<input type="text" name="buyer_search" class="form-control" value="{{$search_token}}" placeholder="Enter To Search..">
									</span>
                                            <span class="typeahead-button"><button type="submit"><span class="font-icon-search"></span></button></span>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                            {!! Form::open(['url'=>'/admin/buyer']) !!}
                            <select class="form-control input-sm" name="status" onchange="this.form.submit()"
                                    style="width: 200px;">
                                <option value="{{App\Http\Controllers\Enum\BuyerStatusEnum::APPROVED}}"
                                        @if($status == App\Http\Controllers\Enum\BuyerStatusEnum::APPROVED) selected @endif>
                                    Approved
                                </option>
                                <option value="{{App\Http\Controllers\Enum\BuyerStatusEnum::BLOCKED}}"
                                        @if($status == App\Http\Controllers\Enum\BuyerStatusEnum::BLOCKED) selected @endif>
                                    Blocked
                                </option>
                                <option value="{{App\Http\Controllers\Enum\BuyerStatusEnum::ARCHIVE}}"
                                        @if($status == App\Http\Controllers\Enum\BuyerStatusEnum::ARCHIVE) selected @endif>
                                    Archive
                                </option>
                            </select>
                            {!! Form::close() !!}
                        </div>
                    </div>

                </header>
            </section><!--.box-typical-->
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="text-center" width="150">Mobile</th>
                    <th class="text-center" width="150">Registration Date</th>
                    <th class="text-center" width="100">Is Blocked?</th>
                    <th class="text-center" width="120">Action</th>
                </thead>
                <tbody>

                @if(!empty($buyers[0]))
                    @foreach($buyers as $buyer)
                        <tr>
                            <td class="tabledit-view-mode" width="50">
                                <span class="tabledit-span">
                                <img alt="" class="admin-list-img"
                                     @if(!empty($buyer->photo) && stripos($buyer->photo,'https://')!== false)
                                     src="{{$buyer->photo}}"
                                     @elseif(!empty($buyer->photo))
                                     src="<?=Image::url(asset(env('USER_PHOTO_PATH').$buyer->photo),50,50,['crop'])?>"
                                     @else
                                     src="{{asset('image/default_author.png')}}"
                                        @endif
                                >

                                </span>
                            </td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">
                                    <a href="{{url('admin/buyer/'.$buyer->buyer_id.'/notification/list')}}">{{$buyer->username}}</a>
                                </span></td>
                            <td class="tabledit-view-mode"><span class="tabledit-span">{{$buyer->email}}</span></td>
                            <td class="tabledit-view-mode text-center"><span class="tabledit-span">{{$buyer->phone}}</span></td>
                            <td class="tabledit-view-mode text-center"><span class="tabledit-span">{{date('F j , Y',strtotime($buyer->created_at))}}</span></td>
                            <td class="tabledit-view-mode text-center">
                                <span class="tabledit-span">
                                    <span class="checkbox-toggle pull-right" style="margin-right: 15px">
                                    {!! Form::open(array('url'=>'/admin/buyer/block','files'=>true)) !!}
                                    <input type="checkbox" onchange="this.form.submit()" id="check-toggle-featured-{{$buyer->id}}"
                                           @if($buyer->status == App\Http\Controllers\Enum\BuyerStatusEnum::BLOCKED) checked @endif />
                                        <label for="check-toggle-featured-{{$buyer->id}}"></label>
                                        <input type="hidden" name="buyer_id_for_featured" value="{{$buyer->id}}">
                                    {!! Form::close() !!}
                                </span>
                                </span>
                            </td>

                            <td style="white-space: nowrap; width: 1%;">

                                <div class="dropdown dropdown-status">

                                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit_btn"  href="{{url('admin/buyer/'.$buyer->buyer_id.'/notification/list')}}"><span
                                                    class="glyphicon glyphicon-tags"></span> View Details
                                        </a>
                                        <a class="dropdown-item edit_btn" data-toggle="modal" href="#form_modal" data-id="{{$buyer->id}}"><span
                                                    class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
                                        <a href="#delete_{{$buyer->id}}" data-toggle="modal" class="dropdown-item  hover-red"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                    </div>
                                </div>
                                <!-- Modal -->
                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                    <div class="modal fade bs-modal-sm"
                                         id="delete_{{$buyer->id}}" tabindex="-1"
                                         role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-sm" style="margin-top: 200px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 style="text-align: left;" class="modal-title"
                                                        id="myModalLabel">{{trans('Delete')}}</h4>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p>Would You Like to Delete?</p>
                                                    <button type="button" class="btn btn-inline btn-default"
                                                            data-dismiss="modal">
                                                        No
                                                    </button>
                                                    <a href="{{url('/admin/buyer/delete/'.$buyer->buyer_id)}}" class="btn btn-inline btn-danger">Yes</a>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10"><div class="alert alert-danger alert-icon alert-close alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <i class="font-icon font-icon-warning"></i>
                                Buyer Not Available.
                            </div></td>
                    </tr>
                @endif
                </tbody>
            </table>
            @include('admin.pagination',['paginator'=>$buyers])
            <br>
        </div><!--.container-fluid-->
    </div>
    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" data-backdrop="true" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                {!! Form::open(array('url'=>'/admin/buyer/edit-profile','id'=>'modal_form','files'=>true)) !!}
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center load_image" style="margin-top: 23px;">
                        <img src="{{asset('build/img/ring-alt.gif')}}" style="width:50px;"
                             alt="">

                        <div>Loading</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit_btn">Save</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <form action="{{url('/email-exists-checking')}}" id="email_checking">{{csrf_field()}}</form>
    <form action="{{url('/seller-email-exists-checking')}}" id="seller_email_checking">{{csrf_field()}}</form>
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script>

        $(document).ready(function () {

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
                        $('.modal-title').html('Buyer Edit');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();

                        emailCheck();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
@stop
