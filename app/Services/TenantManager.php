<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Support\Facades\Cache;

class TenantManager
{
    protected ?Tenant $currentTenant = null;

    /**
     * Set the current tenant.
     */
    public function setTenant(?Tenant $tenant): void
    {
        $this->currentTenant = $tenant;
    }

    /**
     * Get the currently resolved tenant.
     */
    public function getTenant(): ?Tenant
    {
        return $this->currentTenant;
    }

    /**
     * Check if a tenant has been resolved.
     */
    public function hasTenant(): bool
    {
        return !is_null($this->currentTenant);
    }

    /**
     * Get the ID of the current tenant, or null.
     */
    public function getTenantId(): ?int
    {
        return $this->currentTenant?->id;
    }

    /**
     * Resolve tenant from request hostname.
     */
    public function resolveFromHost(string $host): ?Tenant
    {
        // 1. Remove port if exists (e.g. localhost:8000 -> localhost)
        $host = preg_replace('/:\d+$/', '', $host);

        // 2. Identify Central domains (do not resolve tenant)
        $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1', 'central.local']);
        if (in_array($host, $centralDomains)) {
            return null;
        }

        // 3. Cache key based on host
        $cacheKey = "tenant_resolution:{$host}";

        // 4. Resolve and Cache (indefinitely, cleared on tenant save/update)
        $tenantId = Cache::remember($cacheKey, now()->addDays(7), function () use ($host) {
            // First check if it matches a custom domain directly
            $domainMapping = TenantDomain::where('domain', $host)->first();
            if ($domainMapping) {
                return $domainMapping->tenant_id;
            }

            // Next check if it's a subdomain (e.g., site1.localhost or site1.cms.local)
            // Assumes central domains could be something like central.local or localhost
            // We parse the subdomain
            $parts = explode('.', $host);
            if (count($parts) > 1) {
                // If it's something.localhost, the subdomain is the first part
                $subdomain = $parts[0];
                $tenant = Tenant::where('subdomain', $subdomain)->first();
                if ($tenant) {
                    return $tenant->id;
                }
            }

            return null;
        });

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant && $tenant->is_active) {
                $this->setTenant($tenant);
                return $tenant;
            }
        }

        return null;
    }

    /**
     * Clear resolution cache for a specific domain/subdomain.
     */
    public function clearCache(string $host): void
    {
        $host = preg_replace('/:\d+$/', '', $host);
        Cache::forget("tenant_resolution:{$host}");
    }
}
