<?php

namespace Botble\RealEstate\Http\Middleware;

use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Theme\Facades\AdminBar;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAccount
{
    public function handle($request, Closure $next, $guard = 'account')
    {
        abort_unless(RealEstateHelper::isLoginEnabled(), 404);

        if (Auth::guard($guard)->check()) {
            return redirect(route('public.account.dashboard'));
        }

        AdminBar::setIsDisplay(false);
        OptimizerHelper::disable();

        return $next($request);
    }
}
