<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
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
        $tenantManager = app(TenantManager::class);

        if ($tenantManager->hasTenant()) {
            $tenant = $tenantManager->getTenant();
            $supportedLocales = $tenant->supported_locales ?? [$tenant->default_locale];

            // 1. Check if locale parameter is in the URL path (e.g. /es/posts)
            $locale = $request->segment(1);

            if (in_array($locale, $supportedLocales)) {
                app()->setLocale($locale);
            } else {
                // 2. Otherwise fallback to tenant default locale
                app()->setLocale($tenant->default_locale);
            }
        }

        return $next($request);
    }
}
