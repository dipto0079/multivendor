<?php

namespace App\Http\Middleware;

use App\UtilityFunction;
use Closure;
use Illuminate\Support\Facades\Auth;

class Language
{
    public function handle($request, Closure $next, $guard = null)
    {
        UtilityFunction::getLocal();
        return $next($request);
    }
}
