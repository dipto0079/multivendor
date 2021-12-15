<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    protected $except = [

        //using list of links
        '/mobile-app/category-list',
        '/mobile-app/product-by/type',
        '/mobile-app/countries',
        '/mobile-app/city-by-country',
        '/mobile-app//city-by-country-id',





        //'/mobile-app/category/list',
        'admin/employee/ajax/list',
        'admin/settings/holiday-calender/save',
        'admin/product/media/save',


        '/mobile-app/category-product-list',

        '/mobile-app/user/login',
        '/mobile-app/user/facebook/login',
        '/mobile-app/user/google/plus/login',
        '/mobile-app/user-profile-info',


        '/mobile-app/shipping-tax-charge',

        '/mobile-app/user/registration',
        '/mobile-app/user/registration/varification/{varification_code}',
        '/mobile-app/user/forget/password',
        '/mobile-app/user/profile/update',
        '/mobile-app/user/transaction',
        '/mobile-app/user/track-order',
        '/mobile-app/product/type/list',

        '/mobile-app/product-by/seller',
        '/mobile-app/product-review-list',
        '/mobile-app/product-add-to-favorite',
        '/mobile-app/user-ask-question',
        '/mobile-app/user-question-list',
        '/mobile-app/user-question-details',
        '/mobile-app/user-question-reply',
        '/mobile-app/product-write-review',
        '/mobile-app/product/purchase',
        '/mobile-app/add-to-cart-mobile',
        '/mobile-app/cart-item-by-user',
        '/mobile-app/remove-cart-item',
        '/mobile-app/apply-coupon',
        '/mobile-app/cart-update',
        '/mobile-app/payment-confirm',
        '/mobile-app/product-store-info',
        '/mobile-app/product-details',
    ];
}
