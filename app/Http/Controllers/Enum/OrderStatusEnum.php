<?php

namespace App\Http\Controllers\Enum;

class OrderStatusEnum
{
    const PENDING = 0; //default
    const ACCEPTED = 1;
    const DELIVERED = 2;
    const CLAIMED = 3;
    const REJECTED = 4;
    const FINALIZED = 5;
}

