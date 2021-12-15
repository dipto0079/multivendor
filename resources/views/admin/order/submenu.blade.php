<?php
    $order = \App\Model\Order::all();
    $pending_order_count = $order->where('status',\App\Http\Controllers\Enum\OrderStatusEnum::PENDING)->count();
    $accepted_order_count = $order->where('status',\App\Http\Controllers\Enum\OrderStatusEnum::ACCEPTED)->count();
    $rejected_order_count = $order->where('status',\App\Http\Controllers\Enum\OrderStatusEnum::REJECTED)->count();
?>
<nav class="side-menu-addl">
    <header class="side-menu-addl-title">
        <div class="caption"></div>
    </header>
    <ul class="side-menu-addl-list">

        <li class="header @if($page == 'order_pending') opened @endif">
            <a href="{{url('admin/order/pending')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Pending @if($pending_order_count != 0)<span class="label label-custom label-pill label-danger pull-right">{{$pending_order_count}}</span>@endif</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'order_accepted') opened @endif">
            <a href="{{url('admin/order/accepted')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Accepted<span class="label label-custom label-pill label-default pull-right">{{$accepted_order_count}}</span></span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'order_rejected') opened @endif">
            <a href="{{url('admin/order/rejected')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Rejected<span class="label label-custom label-pill label-default pull-right">{{$rejected_order_count}}</span></span>
	                </span>
            </a>
        </li>

    </ul>
</nav>