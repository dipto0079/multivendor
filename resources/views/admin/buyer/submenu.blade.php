<nav class="side-menu-addl">
    <header class="side-menu-addl-title">
        <div class="caption"></div>
    </header>
    <ul class="side-menu-addl-list">


        <li class="header @if($page == 'notification') opened @endif">
            <a href="{{url('admin/buyer/'.$buyer_id.'/notification/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Notification</span>
	                </span>
            </a>
        </li>


        <li class="header @if($page == 'buyer_order') opened @endif">
            <a href="{{url('admin/buyer/'.$buyer_id.'/order/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Orders</span>
	                </span>
            </a>
        </li>

    </ul>
</nav>