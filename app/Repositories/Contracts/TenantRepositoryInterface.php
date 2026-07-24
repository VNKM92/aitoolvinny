<?php

namespace App\Repositories\Contracts;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

interface TenantRepositoryInterface
{
    public function all(): Collection;

    public function allActive(): Collection;

    public function find(int $id): ?Tenant;

    public function findBySubdomain(string $subdomain): ?Tenant;

    public function create(array $data): Tenant;

    public function update(int $id, array $data): Tenant;

    public function delete(int $id): bool;
}
