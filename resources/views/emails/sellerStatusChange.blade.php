<p>Dear <b>{{$name}}</b>,</p>
	@if($status == App\Http\Controllers\Enum\SellerStatusEnum::APPROVED) <p>Your registration request is approved.</p>
	@elseif($status == App\Http\Controllers\Enum\SellerStatusEnum::REJECTED) <p>Your account is rejected by the administrator. If you have any query please contact with the administrator.</p>
	@elseif($status == App\Http\Controllers\Enum\SellerStatusEnum::BLOCKED) <p>Your account is blocked by the administrator. If you have any query please contact with the administrator.</p>
	@endif

@if(!empty($password) && $status == App\Http\Controllers\Enum\SellerStatusEnum::APPROVED)
	<br/>
	<p><b>Login url:</b> {{url('/seller/login')}}</p>
	<p><b>Email:</b> {{$email}}</p>
	<p><b>Password:</b> {{$password}}</p>
@endif

<div>
	<br/>
	<p style="margin:0">Best Regards,</p>
	<p style="margin:0"><a heref="{{env('APP_NAME_URL_1')}}">{{env('APP_NAME_EN')}}</a> Team</p>
</div>