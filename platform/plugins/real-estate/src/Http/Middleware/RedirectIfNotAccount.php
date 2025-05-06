<?php

namespace Botble\RealEstate\Http\Middleware;

use Botble\RealEstate\Facades\RealEstateHelper;
use Closure;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAccount implements AuthenticatesRequests
{
    public function handle($request, Closure $next, $guard = 'account')
    {
        abort_unless(RealEstateHelper::isLoginEnabled(), 404);

        if (! Auth::guard($guard)->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(route('public.account.login'));
        }

        return $next($request);
    }
}
