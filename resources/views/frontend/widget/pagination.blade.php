
<?php
$append_url = '';

if (isset($appends)) {
    foreach ($appends as $key => $value) {

        $append_url = $append_url . '&' . $key . '=' . $value;
    }
}
$radius = 3;
?>

@if ($paginator->lastPage() > 1)

    <div class="pagination-container wow fadeInUp" data-wow-delay="0.3s">
        <div class="pagination-info font-additional">
            <?php
            $from = ($paginator->currentPage() - 1) * ($paginator->perPage()) + 1;
            $to = $from + $paginator->count() - 1;
            echo "Items " . $from . " to " . $to . " of " . $paginator->total() . " total";
            ?>
        </div>
        <ul class="pagination-list">
            @if ($paginator->onFirstPage())
              <li class="disabled"><a class="prev hover-focus-color">PREVIOUS</a></li>
            @else
              <li><a class="prev hover-focus-color"  href="{{ $paginator->url(1).$append_url }}">PREVIOUS</a></li>
            @endif

            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                @if(($i >= 1 && $i <= $radius) || ($i > $paginator->currentPage() - $radius && $i < $paginator->currentPage() + $radius) || ($i <= $paginator->total() && $i > $paginator->total() - $radius))
                  <li >
                      <a class="page{{ ($paginator->currentPage() == $i) ? ' current customBgColor' : ' hover-focus-color' }}" href="{{ $paginator->url($i).$append_url }}">{{ $i }}</a>
                  </li>
                @elseif($i == $paginator->currentPage() - $radius || $i == $paginator->currentPage() + $radius)
                  <li >... </li>
                @endif
            @endfor


            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a class="next hover-focus-color" href="{{ $paginator->url($paginator->currentPage()+1).$append_url }}">NEXT</a></li>
            @else
                <li class="disabled"><a class="next hover-focus-color">NEXT</a></li>
            @endif

        </ul>
    </div>

@endif
