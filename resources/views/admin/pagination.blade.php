<?php
$append_url = '';
if (isset($appends)) {
    foreach ($appends as $key => $value) {
        $append_url = $append_url . '&' . $key . '=' . $value;
    }
}
$last_id = 0;
?>

@if ($paginator->lastPage() > 1)
    <br/>
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>
                        <div class="col-sm-4"><?php
                            $from = ($paginator->currentPage() - 1) * ($paginator->perPage()) + 1;
                            $to = $from + $paginator->count() - 1;
                            echo "Showing " . $from . " - " . $to . " of " . $paginator->total() . " items";
                            ?></div>
                        <div class="col-sm-8">
                        </div>
                    </h3>
                </div>
                <div class="tbl-cell">

                    <div class="dataTables_wrapper form-inline dt-bootstrap">


                        <div class="dataTables_paginate">
                            <ul class="pagination">
                                <li class="paginate_button previous @if($paginator->currentPage() == 1) disabled @endif"
                                    id="example_previous">
                                    <a @if($paginator->currentPage() != 1) href="{{ $paginator->url(1).$append_url }}"
                                       @endif
                                       aria-controls="example" data-dt-idx="0" tabindex="0">Previous</a>
                                </li>
                                @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                                    <li class="paginate_button {{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                                        <a href="{{ $paginator->url($i).$append_url }}">{{ $i }}</a>
                                    </li>
                                    <?php $last_id = $i; ?>
                                @endfor
                                <li class="paginate_button next @if($last_id == $paginator->currentPage()) disabled  @endif"
                                    id="example_next">
                                    <a @if($last_id != $paginator->currentPage()) href="{{ $paginator->url($paginator->currentPage()+1).$append_url }}"
                                       @endif
                                       aria-controls="example" data-dt-idx="7" tabindex="0">Next</a></li>
                            </ul>
                        </div>
                    </div>


                </div>
            </div>

        </header>
    </section>

@endif
