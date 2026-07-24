<?php

namespace App\Repositories\Contracts;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;

interface PageRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Page;

    public function findBySlug(string $slug): ?Page;

    public function getPublished(): Collection;

    public function create(array $data): Page;

    public function update(int $id, array $data): Page;

    public function delete(int $id): bool;
}
