<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Post;

    public function findBySlug(string $slug): ?Post;

    public function getPublished(): Collection;

    public function getPublishedPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getPublishedByCategoryPaginated(int $categoryId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Post;

    public function update(int $id, array $data): Post;

    public function delete(int $id): bool;
}
