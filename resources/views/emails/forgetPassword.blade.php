Hello {{$name}}

@if($type == App\Http\Controllers\Enum\UserTypeEnum::USER)
<a href="{{url('/password-reset/'.$token.'::'.App\Http\Controllers\Enum\UserTypeEnum::USER)}}">Click Here</a>, To Change Your Password.
@endif

@if($type == App\Http\Controllers\Enum\UserTypeEnum::SELLER && $status == App\Http\Controllers\Enum\SellerStatusEnum::APPROVED)
<a href="{{url('/password-reset/'.$token.'::'.App\Http\Controllers\Enum\UserTypeEnum::SELLER)}}">Click Here</a>, To Change Your Password.

@elseif($type == App\Http\Controllers\Enum\UserTypeEnum::SELLER && $status == App\Http\Controllers\Enum\SellerStatusEnum::PENDING)
<p>Your information not verified yet.</p>
@endif
