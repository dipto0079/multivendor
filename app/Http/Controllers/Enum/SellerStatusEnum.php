<?php

namespace App\Http\Controllers\Enum;

class SellerStatusEnum
{
    const PENDING = 0; // DEFAULT
    const APPROVED = 1;
    const REJECTED = 2;
    const BLOCKED = 3;
    const ARCHIVE = 4;
}
