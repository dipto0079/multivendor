<nav class="side-menu-addl">
    <header class="side-menu-addl-title">
        <div class="caption"></div>
    </header>
    <ul class="side-menu-addl-list">

      {{--<li class="header @if($page == 'statistics') opened @endif">--}}
          {{--<a href="{{url('admin/statistics')}}">--}}
              {{--<span class="tbl-row">--}}
                  {{--<span class="tbl-cell tbl-cell-caption">Statistics</span>--}}
              {{--</span>--}}
          {{--</a>--}}
      {{--</li>--}}

        <li class="header @if($page == 'payment') opened @endif">
            <a href="{{url('admin/payment')}}">
                <span class="tbl-row">
                    <span class="tbl-cell tbl-cell-caption">Payment</span>
                </span>
            </a>
        </li>

        <li class="header @if($page == 'payment_final') opened @endif">
            <a href="{{url('admin/payment/final')}}">
                <span class="tbl-row">
                    <span class="tbl-cell tbl-cell-caption">Payment Final<span class="label label-custom label-pill label-default pull-right"></span></span>
                </span>
            </a>
        </li>


    </ul>
</nav>
