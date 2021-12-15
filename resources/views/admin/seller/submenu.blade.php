<nav class="side-menu-addl">
    <header class="side-menu-addl-title">
        <div class="caption"></div>
    </header>
    <ul class="side-menu-addl-list">
        @if($page == 'product')
            <li class="header @if(isset($open) && $open == 'details') opened @endif">
                <a href="{{url('admin/product/seller/'.$seller_id.'/details')}}">
                  <span class="tbl-row">
                      <span class="tbl-cell tbl-cell-caption">Details</span>
                  </span>
                </a>
            </li>
            <li class="header @if(!isset($open) && $page == 'product') opened @endif">
                <a href="{{url('admin/'.$page.'/seller/'.$seller_id.'/'.$page.'/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Products</span>
	                </span>
                </a>
            </li>
            <li class="header @if(isset($open) && $open == 'notification') opened @endif">
                <a href="{{url('admin/product/seller/'.$seller_id.'/notification/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Notification</span>
	                </span>
                </a>
            </li>
            <li class="header @if(isset($open) && $open == 'order') opened @endif">
                <a href="{{url('admin/product/seller/'.$seller_id.'/order/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Orders</span>
	                </span>
                </a>
            </li>
            <li class="header @if(isset($open) && $open == 'deal') opened @endif">
                <a href="{{url('admin/product/seller/'.$seller_id.'/deal/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Deals</span>
	                </span>
                </a>
            </li>
        @elseif($page == 'service')
            <li class="header @if(isset($open) && $open == 'details') opened @endif">
                <a href="{{url('admin/service/seller/'.$seller_id.'/details')}}">
                  <span class="tbl-row">
                      <span class="tbl-cell tbl-cell-caption">Details</span>
                  </span>
                </a>
            </li>
            <li class="header @if(!isset($open) && $page == 'service') opened @endif">
                <a href="{{url('admin/service/seller/'.$seller_id.'/service/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Services</span>
	                </span>
                </a>
            </li>
            <li class="header @if(isset($open) && $open == 'notification') opened @endif">
                <a href="{{url('admin/service/seller/'.$seller_id.'/notification/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Notification</span>
	                </span>
                </a>
            </li>
            {{--<li class="header @if(isset($open) && $open == 'order') opened @endif">--}}
                {{--<a href="{{url('admin/service/seller/'.$seller_id.'/order/list')}}">--}}
	                {{--<span class="tbl-row">--}}
	                    {{--<span class="tbl-cell tbl-cell-caption">Orders</span>--}}
	                {{--</span>--}}
                {{--</a>--}}
            {{--</li>--}}
            <li class="header @if(isset($open) && $open == 'deal') opened @endif">
                <a href="{{url('admin/service/seller/'.$seller_id.'/deal/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Deals</span>
	                </span>
                </a>
            </li>
        @endif
    </ul>
</nav>
