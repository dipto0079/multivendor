<html>
<head></head>

<body>
<h3>Hello {{$name}}</h3>
<p>Your have a new order. The order details as follows.</p>

<table cellpadding="5" cellspacing="0" border="1">
    <thead>
    <tr>
        <th align="left">Product Name</th>
        <th>Quantity</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($order_lists[0]))
        @foreach($order_lists as $order_list)
            <tr>
                <td>{{$order_list->getProduct->name}}</td>
                <td>{{$order_list->quantity}}</td>
            </tr>
        @endforeach
    @endif
    </tbody>

</table>
<h3>Delivery Details</h3>
<p><strong>Street: </strong>{{$order->delivery_street}}</p>
<p><strong>City: </strong>{{$order->delivery_city}}</p>
<p><strong>State: </strong>{{$order->delivery_state}}</p>
<p><strong>Zip Code: </strong>{{$order->delivery_zip}}</p>
<p><strong>Country: </strong>{{$order->getCountryName->name}}</p>

</body>
</html>
