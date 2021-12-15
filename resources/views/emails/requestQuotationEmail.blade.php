<h4>Hello {{$seller->getUser->username}},</h4>
<h4>A buyer is requested a quatation for your service that are listed in the 70doors.com store list.<br>
Following are the details:</h4>

--------------------------------------------------------------------
<h4>Requested service: <a href="{{url('/store/'.$seller->store_name)}}">{{$seller->store_name}}</a> >
  <a href="{{url('/service/details/'.$service_product->id)}}">{{$service_product->name}}</a></h4>
@if(!empty($auth_info))
<h4>Buyer: {{$auth_info->username}}</h4>
<h4>Buyer Email: {{$auth_info->email}}</h4>
<h4>Buyer Phone: {{$auth_info->phone}}</h4>
@else
<h4>Buyer Email/Phone: {{$quotation_info['phone_email']}}</h4>
@endif
@if(!empty($quotation_info['quotation_message'])) <h4>Buyer Message: {{$quotation_info['quotation_message']}}</h4> @endif
<h4>Requested on: {{date('d/m/Y h:i a')}}</h4>
--------------------------------------------------------------------<br>
<h4>Give your feedback as early as possible. We hope your business growth.</h4>
<h4>All the best</h4>
<h4>70doors.com Team</h4>
