@extends('admin.master',['user_name'=>App\User::getUserName($seller->user_id)])
@section('title','Notification List')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('/build')}}/css/separate/pages/gallery.min.css">
    <style type="text/css">
        .show { display: block; }
        .btn-file { margin-right: 20px; padding: 4px 10px; }
        .gallery-item { height: 150px; }
        p { margin-bottom: 5px;}
    </style>
@stop

@section('content')

    @include('admin.seller.submenu',['page'=>'product','open'=>'details'])


    <div class="page-content">
        <div class="container-fluid">

            <section class="box-typical">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>
                                <div class="col-sm-4">Seller Details</div>
                            </h3>
                        </div>
                    </div>
                </header>
            </section><!--.box-typical-->

            <section class="card">
      				<div class="card-block">
                <img @if(!empty($seller->getUser->photo)) src="<?=Image::url(asset(env('USER_PHOTO_PATH').$seller->getUser->photo),200,175,['crop'])?>"
                @else src="<?=Image::url(asset('image/no-media.jpg'),200,175,['crop'])?>" alt=""
                @endif alt="" width="200" style="float: left; margin-right: 15px;">
                <p><strong>Name:</strong> {{$seller->getUser->username}}</p>
                <p><strong>Company Name:</strong> {{$seller->company_name}}</p>
                <p><strong>Personal Email:</strong> {{$seller->getUser->email}}</p>
                <p><strong>Business Email:</strong> {{$seller->business_email}}</p>
                <p><strong>Store Name:</strong> {{$seller->store_name}}</p>
                <p><strong>Category:</strong> <span class="label label-custom label-pill label-default">{{$seller->getCategory->name}}</span></p>
                <p><strong>Commission:</strong> {{$seller->commission}}%</p>
                <p><strong>Status:</strong>
                  @if($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::PENDING) Pending
                  @elseif($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::APPROVED) Approved
                  @elseif($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::REJECTED) Rejected
                  @elseif($seller->status == App\Http\Controllers\Enum\SellerStatusEnum::BLOCKED) Blocked
                  @endif
                </p>
                <p><strong>Joined:</strong> {{date('d F, Y',strtotime($seller->created_at))}}</p><br>
                <p><strong>About Him/Her: </strong>{{$seller->about_me}}</p>
                <p><strong>No. of Product:</strong> <a href="{{url('/admin/product/seller/'.$seller->id.'/product/list')}}" class="label label-pill label-danger">@if($seller->getProducts->count()>0){{$seller->getProducts->count()}}@endif</a></p>
      				</div>
    			</section>
        </div><!--.container-fluid-->
    </div>
@stop

@section('script')

@stop
