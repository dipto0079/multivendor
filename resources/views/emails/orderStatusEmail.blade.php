<strong>Hello {{$name}}</strong>

@if($status == App\Http\Controllers\Enum\OrderStatusEnum::DELIVERED)
  <p>Your order id {{$order_number}} is now on delivery.</p>
@elseif($status == App\Http\Controllers\Enum\OrderStatusEnum::REJECTED)
  <p>Your order id {{$order_number}} is rejected. Please contact with admin.</p>
@endif
