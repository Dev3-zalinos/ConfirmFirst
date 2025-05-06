<?php

namespace Botble\RealEstate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountIsApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('account');

        if (! auth('account')->check()) {
            return redirect()->route('public.account.login');
        }

        $enabled = (bool) setting('real_estate_enable_account_verification', false);

        abort_if(! $enabled && $request->routeIs('public.account.pending-approval'), 404);

        if ($enabled && ! $user->approved_at) {
            if ($request->routeIs('public.account.pending-approval')) {
                return $next($request);
            }

            return redirect()->route('public.account.pending-approval');
        } elseif ($request->routeIs('public.account.pending-approval')) {
            return redirect()->route('public.account.dashboard');
        }

        return $next($request);
    }
}
