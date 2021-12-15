<?php

namespace App\Http\Controllers\Enum;

class ShippingTypeEnum
{
    const FREE_SHIPPING = 1;
    const FLAT_RATE = 2;
    const RATE_BY_WEIGHT = 3;
    const RATE_BY_ORDER_PRICE = 4;
    const ALLOW_STORE_PICKUP_ONLY = 5;
}

