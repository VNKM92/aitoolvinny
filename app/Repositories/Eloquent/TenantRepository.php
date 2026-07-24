<?php

namespace App\Repositories\Eloquent;

use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TenantRepository implements TenantRepositoryInterface
{
    public function all(): Collection
    {
        return Tenant::with('domains')->get();
    }

    public function allActive(): Collection
    {
        return Tenant::where('is_active', true)->with('domains')->get();
    }

    public function find(int $id): ?Tenant
    {
        return Tenant::with('domains')->find($id);
    }

    public function findBySubdomain(string $subdomain): ?Tenant
    {
        return Tenant::where('subdomain', $subdomain)->first();
    }

    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function update(int $id, array $data): Tenant
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update($data);
        return $tenant;
    }

    public function delete(int $id): bool
    {
        $tenant = Tenant::findOrFail($id);
        return $tenant->delete();
    }
}
