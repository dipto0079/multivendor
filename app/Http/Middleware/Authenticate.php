<?php

namespace App\Http\Middleware;

use App\Model\Company;
use App\UtilityFunction;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth;
use Crypt;
use Session;
use Cookie;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\BuyerStatusEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;


class Authenticate
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function isBuyerBlocked($user)
    {
        if ($user->getBuyer->status != BuyerStatusEnum::APPROVED) return 0;
        return 1;
    }

    public function isSellerBlocked($user)
    {
        if ($user->getSeller->status != SellerStatusEnum::APPROVED) return 0;
        return 1;
    }

    public function handle($request, Closure $next)
    {

        //Written by : HIRA, Nobody allowed to change the following code
        $REQUEST_URI = strtolower($_SERVER['REQUEST_URI']);
        $user = Auth::user();

        if (strpos($REQUEST_URI, '/admin') !== false) {
            if (is_null($user) || $user->user_type != UserTypeEnum::ADMIN) {
                return redirect('/admin/login');
            }
        } elseif (strpos($REQUEST_URI, '/seller') !== false) {
            if (is_null($user) || $user->user_type != UserTypeEnum::SELLER) {
                return redirect('/seller/login');
            }

            //Check whether the buyer is blocked by the admin
            if (!$this->isSellerBlocked($user)) {
                Auth::logout();
                return redirect('/seller/login')->with('message', 'The account is blocked by the Administrator.');
            }

        } elseif (strpos($REQUEST_URI, '/buyer') !== false) {
            if (is_null($user) || $user->user_type != UserTypeEnum::USER) {
                return redirect('/buyer/login');
            }

            //Check whether the buyer is blocked by the admin
            if (!$this->isBuyerBlocked($user)) {
                Auth::logout();
                return redirect('/buyer/login')->with('message', 'The account is blocked by the Administrator.');
            }

        }

        return $next($request);
    }
}
