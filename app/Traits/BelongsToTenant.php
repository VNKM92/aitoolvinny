<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Services\TenantManager;

trait BelongsToTenant
{
    /**
     * Boot the trait to apply the tenant scope and set tenant_id.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            $tenantManager = app(TenantManager::class);
            if (!$model->tenant_id && $tenantManager->hasTenant()) {
                $model->tenant_id = $tenantManager->getTenantId();
            }
        });
    }

    /**
     * Get the tenant that owns this model.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
