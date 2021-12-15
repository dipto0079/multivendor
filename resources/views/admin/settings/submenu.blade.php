<nav class="side-menu-addl">
    <header class="side-menu-addl-title">
        <div class="caption"></div>
    </header>
    <ul class="side-menu-addl-list">

        <li class="header @if($page == 'setting') opened @endif">
            <a href="{{url('/admin/settings')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Settings</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'product') opened @endif">
            <a href="{{url('admin/settings/category/product')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Product Categories</span>
	                </span>
            </a>
        </li>
        <li class="header @if($page == 'service') opened @endif">
            <a href="{{url('admin/settings/category/service')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Service Categories</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'city') opened @endif">
            <a href="{{url('admin/settings/city/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">City</span>
	                </span>
            </a>
        </li>
        <li class="header @if($page == 'location') opened @endif">
            <a href="{{url('admin/settings/location/list')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Location</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'static_page') opened @endif">
            <a href="{{url('/admin/settings/static-page')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Static Page</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'admin_user' || $page == 'admin_role') opened @endif">
            <a href="{{url('/admin/settings/admin-user')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Admin User</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'coupon') opened @endif">
            <a href="{{url('/admin/settings/coupon')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Coupon</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'advertisement') opened @endif">
            <a href="{{url('/admin/settings/advertisement')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Advertisement</span>
	                </span>
            </a>
        </li>

        <li class="header @if($page == 'success_story') opened @endif">
            <a href="{{url('/admin/settings/success-story')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Success Story</span>
	                </span>
            </a>
        </li>


        <li class="header @if($page == 'question') opened @endif">
            <a href="{{url('/admin/settings/question')}}">
	                <span class="tbl-row">
	                    <span class="tbl-cell tbl-cell-caption">Question</span>
	                </span>
            </a>
        </li>



        {{--<li class="header @if($page == 'admin_role') opened @endif">--}}
            {{--<a href="{{url('/admin/settings/admin-role')}}">--}}
	                {{--<span class="tbl-row">--}}
	                    {{--<span class="tbl-cell tbl-cell-caption">Admin Role</span>--}}
	                {{--</span>--}}
            {{--</a>--}}
        {{--</li>--}}

    </ul>
</nav>