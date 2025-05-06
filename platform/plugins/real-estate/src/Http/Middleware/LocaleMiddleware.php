<?php

namespace Botble\RealEstate\Http\Middleware;

use Botble\Base\Supports\Language;
use Botble\RealEstate\Models\Account;
use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $currentLocale = Language::getDefaultLanguage();

        /**
         * @var Account $account
         */
        $account = auth('account')->user();

        $userLocale = $account->getMetaData('locale', true);

        if ($userLocale && array_key_exists($userLocale, $availableLocales = Language::getAvailableLocales())) {
            $currentLocale = $availableLocales[$userLocale];
        }

        if ($currentLocale && isset($currentLocale['locale'])) {
            app()->setLocale($currentLocale['locale']);
            $request->setLocale($currentLocale['locale']);
            $request->session()->put('locale_direction', $currentLocale['is_rtl']);
        }

        return $next($request);
    }
}
