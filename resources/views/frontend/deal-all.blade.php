@extends('frontend.master',['menu'=>'deals'])
@section('title',__('messages.page_title.all_deals'))
@section('stylesheet')
@stop
@section('content')
    <section id="pageTitleBox" class="paralax breadcrumb-container"  style="background-image: url('{{asset('/image/paralax/6.jpg')}}');">
        <div class="overlay"></div>
        <div class="container relative">
            <h1 class="title font-additional font-weight-normal color-main text-uppercase wow zoomIn" data-wow-delay="0.3s">Deals</h1>
            <ul class="breadcrumb-list wow zoomIn" data-wow-delay="0.3s">
                <li>
                    <a href="{{url('/')}}" class="font-additional font-weight-normal color-main text-uppercase">HOME</a>
                    <span>/</span>
                </li>
                <li class="font-additional font-weight-normal color-main text-uppercase">Deals</li>
            </ul>
        </div>
    </section>
    <section id="pageContent" class="page-content category-type_list">
        <div class="container">
            <div class="row">
                <div class="sidebar col-lg-3 col-md-3 col-sm-3 col-xs-12 clearfix">
                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp" data-wow-delay="0.3s">categories</h3>
                    <ul class="categories-tree wow fadeInUp" data-wow-delay="0.3s">
                        <li>
                            <a href="#" class="font-additional font-weight-normal hover-focus-color color-third text-uppercase">
                                <span class="pull-left">Food & Drinks</span>
                                <span class="pull-right">(8)</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="font-additional font-weight-normal hover-focus-color color-third text-uppercase">
                                <span class="pull-left">Beauty</span>
                                <span class="pull-right">(19)</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="font-additional font-weight-normal hover-focus-color color-third text-uppercase">
                                <span class="pull-left">Electronics</span>
                                <span class="pull-right">(9)</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="font-additional font-weight-normal hover-focus-color color-third text-uppercase">
                                <span class="pull-left">Mobile & Laptop</span>
                                <span class="pull-right">(7)</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="font-additional font-weight-normal hover-focus-color color-third text-uppercase">
                                <span class="pull-left">Fashion's</span>
                                <span class="pull-right">(5)</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="font-additional font-weight-normal hover-focus-color color-third text-uppercase">
                                <span class="pull-left">Gift & Jewellery</span>
                                <span class="pull-right">(7)</span>
                            </a>
                        </li><li>
                            <a href="#" class="font-additional font-weight-normal hover-focus-color color-third text-uppercase">
                                <span class="pull-left">E-Commerce</span>
                                <span class="pull-right">(7)</span>
                            </a>
                        </li>
                    </ul>
                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp" data-wow-delay="0.3s">by price</h3>
                    <div class="sidebar-slider sidebar-slider_btm_padding wow fadeInUp" data-wow-delay="0.3s">
                        <div class="slider-range" data-min="50" data-max="450" data-default-min="50" data-default-max="350" data-range="true" data-value-container-id="priceAmount"></div>
                        <div class="filter-container">
                            <div class="slider-range-value pull-left">
                                <label class="font-main font-weight-normal" for="priceAmount">Price:</label>
                                <input class="font-main font-weight-normal" type="text" id="priceAmount" readonly>
                            </div>
                            <a class="btn button-border font-additional font-weight-bold hvr-rectangle-out hover-focus-bg hover-focus-border before-bg pull-right" href="#">Filter</a>
                        </div>
                    </div>
                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp"
                        data-wow-delay="0.3s">BY city</h3>
                    @include('frontend.widget.location-side-menu')

                    <h3 class="sidebar-title font-additional font-weight-bold text-uppercase customColor wow fadeInUp" data-wow-delay="0.3s">featured products</h3>
                    <ul class="sidebar-popular-product wow fadeInUp" data-wow-delay="0.3s">
                        <li>
                            <a class="popular-product-item" href="{{url('details')}}">
                                <img src="{{asset('/image/80x75/2.jpg')}}" alt="Product">
                                <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">MEN shirts</span>
                                <span class="popular-product-item_price font-additional color-third">$105.00</span>
                            </a>
                        </li>
                        <li>
                            <a class="popular-product-item" href="{{url('details')}}">
                                <img src="{{asset('/image/80x75/2.jpg')}}" alt="Product">
                                <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">women wear</span>
                                <span class="popular-product-item_price font-additional color-third">$350.00</span>
                                <span class="product-item_sale color-main font-additional customBgColor circle">-5%</span>
                            </a>
                        </li>
                        <li>
                            <a class="popular-product-item" href="{{url('details')}}">
                                <img src="{{asset('/image/80x75/2.jpg')}}" alt="Product">
                                <span class="popular-product-item_title font-additional font-weight-bold text-uppercase">bags & packs</span>
                                <span class="popular-product-item_price font-additional color-third">$240.00</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 clearfix">
                    <div class="content-box">
                        <div class="category-filter clearfix wow fadeInUp" data-wow-delay="0.3s">
                            <div class="select pull-left">
                                <select class="select-field font-main color-third" name="sort" id="sort">
                                    <option value="">Default Sorting</option>
                                    <option value="trending">Trending items</option>
                                    <option value="sales">Best sellers</option>
                                    <option value="rating">Best rated</option>
                                    <option value="price-asc">Price: low to high</option>
                                    <option value="price-desc">Price: high to low</option>
                                </select>
                                <i class="fa fa-angle-down customColor"></i>
                            </div>
                            <div class="select pull-left">
                                <select class="select-field font-main color-third" name="items-qty" id="items-qty">
                                    <option value="12">Items On Page 12</option>
                                    <option value="24">Items On Page 24</option>
                                    <option value="36">Items On Page 36</option>
                                    <option value="48">Items On Page 48</option>
                                </select>
                                <i class="fa fa-angle-down customColor"></i>
                            </div>
                            <a href="{{url('/category')}}" class="pull-right grid-type hover-focus-border">
                                <span class="icon-list" aria-hidden="true"></span>
                            </a>
                            <a href="{{url('/category/grid')}}" class="pull-right grid-type active customBgColor color-main">
                                <span class="icon-grid" aria-hidden="true"></span>
                            </a>
                        </div>
                        <div class="products-cat clearfix">
                            <ul class="products-grid" id="grid">
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/1.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">WOMEN GOWN</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$240.00 <span>$265.00</span></div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/2.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">STYLISH WEAR</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$175.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/3.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-20%</span>
                                                <span class="product-item_outofstock color-main font-additional circle">OUT OF STOCK</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">SPRING CLOTH</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$300.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body product-border">
                                            <img alt="Product" src="{{asset('')}}/image/products/category/4.jpg" class="product-item_image">
                                            <a href="{{url('details')}}" class="product-item_link">
                                                <span class="product-item_new color-main font-additional text-uppercase circle">new</span>
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-bag"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-eye"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-heart"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a class="product-item_footer" href="{{url('details')}}">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">WOMEN CAP</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$240.00 <span>$265.00</span></div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body product-border">
                                            <img alt="Product" src="{{asset('')}}/image/products/category/5.jpg" class="product-item_image">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-bag"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-eye"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-heart"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a class="product-item_footer" href="{{url('details')}}">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">STYLISH WEAR</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$175.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body product-border">
                                            <img alt="Product" src="{{asset('')}}/image/products/category/6.jpg" class="product-item_image">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-bag"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-eye"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span aria-hidden="true" class="icon-heart"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a class="product-item_footer" href="{{url('details')}}">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">LEATHER JACKET</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$300.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/7.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">WOMEN JACKET</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$240.00 <span>$265.00</span></div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/8.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">STYLISH WEAR</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$175.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/9.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-20%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">LEATHER JACKET</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$300.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/10.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">WOMEN JACKET</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$240.00 <span>$265.00</span></div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/11.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-15%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">STYLISH WEAR</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$175.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="product-item hvr-underline-from-center">
                                        <div class="product-item_body">
                                            <img class="product-item_image" src="{{asset('')}}/image/products/category/12.jpg" alt="Product">
                                            <a class="product-item_link" href="{{url('details')}}">
                                                <span class="product-item_sale color-main font-additional customBgColor circle">-20%</span>
                                            </a>
                                            <ul class="product-item_info transition">
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-bag" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_cart')</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-eye" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">VIEW</div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#">
                                                        <span class="icon-heart" aria-hidden="true"></span>
                                                        <div class="product-item_tip font-additional font-weight-normal text-uppercase customBgColor color-main transition">@lang('messages.add_to_favorites')</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{url('details')}}" class="product-item_footer">
                                            <div class="product-item_title font-additional font-weight-bold text-center text-uppercase">LEATHER JACKET</div>
                                            <div class="product-item_price font-additional font-weight-normal customColor">$300.00</div>
                                        </a>
                                    </div>
                                </li>
                                <li class="helper-justify"></li>
                            </ul>
                        </div>
                        <div class="pagination-container wow fadeInUp" data-wow-delay="0.3s">
                            <div class="pagination-info font-additional">Items 1 to 10 of 200 total</div>
                            <ul class="pagination-list">
                                <li><a href="#" class="prev hover-focus-color">previous</a></li>
                                <li><a href="#" class="page current customBgColor">1</a></li>
                                <li><a href="#" class="page hover-focus-color">2</a></li>
                                <li><a href="#" class="page hover-focus-color">3</a></li>
                                <li><a href="#" class="page hover-focus-color">4</a></li>
                                <li><span>....</span></li>
                                <li><a href="#" class="page hover-focus-color">26</a></li>
                                <li><a href="#" class="next hover-focus-color">NEXT</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{--<section id="brandsSlider" class="brands-slider">--}}
        {{--<div class="container">--}}
            {{--<h2 class="title font-additional font-weight-bold text-uppercase wow zoomIn" data-wow-delay="0.3s">OUR brandS</h2>--}}
            {{--<div class="starSeparatorBox clearfix">--}}
                {{--<div class="starSeparator wow zoomIn" data-wow-delay="0.3s">--}}
                    {{--<span aria-hidden="true" class="icon-star"></span>--}}
                {{--</div>--}}
                {{--<div id="owl-product-slider" class="enable-owl-carousel owl-product-slider owl-bottom-pagination owl-carousel owl-theme wow fadeInUp" data-wow-delay="0.7s" data-navigation="true" data-pagination="false" data-single-item="false" data-auto-play="false" data-transition-style="false" data-main-text-animation="false" data-min600="2" data-min800="3" data-min1200="6">--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/1.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/2.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/3.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/4.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/5.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/6.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/2.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                    {{--<div class="item">--}}
                        {{--<a href="#"><img class="brands-slider_logo" src="{{asset('/image/brands/1.png')}}" alt="Brand"></a>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
@stop