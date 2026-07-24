<?php

namespace App\Http\Middleware;

use App\Services\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = SiteSettings::get('default_locale', 'en');
        $supportedLocales = SiteSettings::get('supported_locales', ['en']);

        // 1. Check if locale parameter is in the URL path (e.g. /es/posts)
        $locale = $request->segment(1);

        if (in_array($locale, $supportedLocales)) {
            app()->setLocale($locale);
        } else {
            // 2. Otherwise fallback to default locale
            app()->setLocale($defaultLocale);
        }

        return $next($request);
    }
}
