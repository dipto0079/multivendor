@extends('frontend.master')
@section('title',__('messages.page_title.cart'))
@section('stylesheet')
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="shop">
                <form>
                    <table class="table shop_table cart">
                        <thead>
                        <tr>
                            <th class="product-remove hidden-xs">&nbsp;</th>
                            <th class="product-thumbnail hidden-xs">&nbsp;</th>
                            <th class="product-name">Product</th>
                            <th class="product-price text-center">Price</th>
                            <th class="product-quantity text-center">Quantity</th>
                            <th class="product-subtotal text-center hidden-xs">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="cart_item">
                            <td class="product-remove hidden-xs">
                                <a href="#" class="remove" title="Remove this item">×</a>
                            </td>
                            <td class="product-thumbnail hidden-xs">
                                <a href="#">
                                    <img width="100" height="150" src="{{asset('media/80x75/5.jpg')}}" alt="Product-1">
                                </a>
                            </td>
                            <td class="product-name">
                                <a href="#">Cras rhoncus duis viverra</a>
                                <dl class="variation">
                                    <dt class="variation-Color">Color:</dt>
                                    <dd class="variation-Color"><p>Green</p></dd>
                                    <dt class="variation-Size">Size:</dt>
                                    <dd class="variation-Size"><p>Extra Large</p></dd>
                                </dl>
                            </td>
                            <td class="product-price text-center">
                                <span class="amount">$22.00</span>
                            </td>
                            <td class="product-quantity text-center">
                                <div class="quantity">
                                    <input type="number" step="1" min="0" name="qunatity" value="2" title="Qty" class="input-text qty text" size="4">
                                </div>
                            </td>
                            <td class="product-subtotal hidden-xs text-center">
                                <span class="amount">$44.00</span>
                            </td>
                        </tr>
                        <tr class="cart_item">
                            <td class="product-remove hidden-xs">
                                <a href="#" class="remove" title="Remove this item">×</a>
                            </td>
                            <td class="product-thumbnail hidden-xs">
                                <a href="#">
                                    <img width="100" height="150" src="{{asset('media/80x75/5.jpg')}}" alt="Product-3">
                                </a>
                            </td>
                            <td class="product-name">
                                <a href="#">Creamy Spring Pasta</a>
                                <dl class="variation">
                                    <dt class="variation-Color">Color:</dt>
                                    <dd class="variation-Color"><p>Green</p></dd>
                                    <dt class="variation-Size">Size:</dt>
                                    <dd class="variation-Size"><p>Medium</p></dd>
                                </dl>
                            </td>
                            <td class="product-price text-center">
                                <span class="amount">$12.00</span>
                            </td>
                            <td class="product-quantity text-center">
                                <div class="quantity">
                                    <input type="number" step="1" min="0" name="quantity" value="1" title="Qty" class="input-text qty text" size="4">
                                </div>
                            </td>
                            <td class="product-subtotal hidden-xs text-center">
                                <span class="amount">$12.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="actions">
                                <div class="coupon">
                                    <label for="coupon_code">Coupon:</label>
                                    <input type="text" name="coupon_code" autocomplete="off" class="input-text" id="coupon_code" value="" placeholder="Coupon code">
                                    <input type="submit" class="btn btn-primary font-additional hvr-grow" name="apply_coupon" value="Apply Coupon">
                                </div>
                                <input type="submit" class="btn btn-primary font-additional hvr-grow" name="update_cart" value="Update Cart">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
                <div class="cart-collaterals">
                    <div class="cart_totals">
                        <h2>Cart Totals</h2>
                        <table>
                            <tbody><tr class="cart-subtotal">
                                <th>Subtotal</th>
                                <td><span class="amount">$56.00</span></td>
                            </tr>
                            <tr class="shipping">
                                <th>Shipping</th>
                                <td><span class="amount">$0.00</span></td>
                            </tr>
                            <tr class="order-total">
                                <th>Total</th>
                                <td><strong><span class="amount">$56.00</span></strong></td>
                            </tr>
                            </tbody></table>
                        <div class="wc-proceed-to-checkout">
                            <a href="#" class="btn button-additional font-additional font-weight-normal text-uppercase hvr-rectangle-out hover-focus-bg before-bg">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
@stop