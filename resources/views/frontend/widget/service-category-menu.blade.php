<?php
    $categories = App\Model\ProductCategory::getServiceCategoryForMenu();
?>
@if(isset($categories[0]))
<ul class="categories-tree wow fadeInUp" data-wow-delay="0.3s">
    @foreach($categories as $category)
        <?php $first_sub_category= $category->getSubCategory ?>
    <li>
        <a href="#{{$category->id}}" data-toggle="collapse" class="parent font-additional font-weight-normal hover-focus-color color-third text-uppercase">
            <span class="pull-left">{{$category->name}}</span>
            <span class="pull-right">(8) </span>
            @if(isset($first_sub_category[0]))<span class="icon fa fa-caret-right"></span>@endif
        </a>
        @if(isset($first_sub_category[0]))
        <ul class="collapse" id="{{$category->id}}">
            @foreach($first_sub_category as $first_sub)
                <?php $second_sub_category= $first_sub->getSubCategory ?>
            <li>
                <a href="#{{$first_sub->id}}" data-toggle="collapse" class="child font-additional font-weight-normal hover-focus-color color-third text-uppercase">{{$first_sub->name}}
                    <span class="pull-right">(2)</span>
                    @if(isset($second_sub_category[0])) <span class="icon fa fa-caret-right"></span> @endif
                </a>
            </li>
                @if(isset($second_sub_category[0]))
                <ul class="collapse" id="{{$first_sub->id}}">
                    @foreach($second_sub_category as $second_sub)
                    <li><a href="javascript:void(0)">{{$second_sub->name}} <span class="pull-right">(2)</span></a></li>
                    @endforeach
                </ul>
                @endif
            @endforeach
        </ul>
        @endif
    </li>
    @endforeach
</ul>
@endif