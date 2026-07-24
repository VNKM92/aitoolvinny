<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $tenant = $this->tenantManager->resolveFromHost($host);

        // Check if we are trying to access a tenant site domain, but it couldn't be resolved
        $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1', 'central.local']);
        $isCentral = in_array(preg_replace('/:\d+$/', '', $host), $centralDomains);

        if (!$isCentral && !$tenant) {
            abort(404, 'Website not found or has been suspended.');
        }

        // If a tenant is resolved, configure tenant local settings
        if ($tenant) {
            // Set locale to tenant default locale
            app()->setLocale($tenant->default_locale);
            
            // Share the tenant with all Blade views automatically
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }
}
