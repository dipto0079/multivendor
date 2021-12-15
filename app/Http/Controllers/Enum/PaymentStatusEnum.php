<?php

namespace App\Http\Controllers\Enum;

class PaymentStatusEnum
{
    const PENDING = 0; //default
    const REJECTED = -1;
    const COMPLETED = 1;
}

